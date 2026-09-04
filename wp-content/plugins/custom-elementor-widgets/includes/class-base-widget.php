<?php
/**
 * What every widget shares.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The two questions every widget in this plugin answers the same way: which
 * category it belongs to, and which registered style and script handles it
 * depends on.
 *
 * The handles are the widget's own — one per widget, never one for the plugin.
 * Declaring them here rather than enqueuing globally is what makes Elementor
 * load a section's CSS only on the pages that use that section.
 *
 * Name, title, icon, keywords, `register_controls()` and `render()` belong to
 * the widget itself.
 */
abstract class Base_Widget extends \Elementor\Widget_Base {

	/**
	 * The project's own panel category.
	 *
	 * @return array
	 */
	public function get_categories(): array {
		return array( Widgets_Loader::CATEGORY );
	}

	/**
	 * The style handles this widget depends on.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		$shared = Widgets_Loader::HANDLE_PREFIX . 'base-widget';
		$handle = $this->asset_handle();

		$depends = wp_style_is( $shared, 'registered' ) ? array( $shared ) : array();

		if ( wp_style_is( $handle, 'registered' ) ) {
			$depends[] = $handle;
		}

		return $depends;
	}

	/**
	 * The script handles this widget depends on.
	 *
	 * @return array
	 */
	public function get_script_depends(): array {
		$handle = $this->asset_handle();

		return wp_script_is( $handle, 'registered' ) ? array( $handle ) : array();
	}

	/**
	 * This widget's own asset handle, taken from its name.
	 *
	 * @return string
	 */
	protected function asset_handle() {
		return Widgets_Loader::HANDLE_PREFIX . $this->get_name();
	}

	/**
	 * One media slot's contents: what was uploaded, or the box saying nothing
	 * has been.
	 *
	 * A slot the client has not filled is still a slot, and is seen as one. The
	 * box itself belongs to the section around it; only what stands inside it
	 * while it is empty is settled here, for every section at once.
	 *
	 * @param string $url  What the client uploaded, if anything.
	 * @param string $alt  What a reader who cannot see it is told.
	 * @param bool   $lazy Whether it waits until it is on screen to load.
	 */
	protected function media( $url, $alt = '', $lazy = false ) {
		$url = trim( (string) $url );

		if ( '' !== $url ) {
			printf(
				'<img src="%s" alt="%s"%s />',
				esc_url( $url ),
				esc_attr( $alt ),
				$lazy ? ' loading="lazy"' : ''
			);

			return;
		}

		printf(
			'<span class="custom-media-empty"><span>%s</span></span>',
			esc_html__( 'no content', 'custom-elementor-widgets' )
		);
	}

	/**
	 * Whether Elementor is showing this in its editor rather than on the site.
	 *
	 * A section with nothing in it yet prints nothing, which on the site is
	 * right and in the editor is a blank the client cannot act on. Asked here
	 * so a widget file does not reach for Elementor itself.
	 *
	 * @return bool
	 */
	protected function is_editing() {
		return \Elementor\Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * Say what the section is waiting for, in the editor only.
	 *
	 * @param string $message What the client has still to give it.
	 */
	protected function editor_hint( $message ) {
		if ( ! $this->is_editing() ) {
			return;
		}

		printf(
			'<p class="custom-editor-hint">%s</p>',
			esc_html( $message )
		);
	}

	/**
	 * One address control and the two toggles that travel with it.
	 *
	 * Every address in the build is one field and two switches, on a widget or
	 * on a repeater's row alike, so the client meets the same three controls
	 * wherever a link is asked for.
	 *
	 * @param object $target The widget, or the repeater the row belongs to.
	 * @param string $key    The address control's name.
	 * @param string $label  What the address control is called.
	 * @param array  $args   Anything else the address control carries.
	 */
	protected function add_link_controls( $target, $key, $label, $args = array() ) {
		$target->add_control(
			$key,
			array_merge(
				array(
					'label' => $label,
					'type'  => \Elementor\Controls_Manager::TEXT,
				),
				$args
			)
		);

		$target->add_control(
			$key . '_blank',
			array(
				'label'   => esc_html__( 'Open in a new tab', 'custom-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$target->add_control(
			$key . '_nofollow',
			array(
				'label'   => esc_html__( 'nofollow', 'custom-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
			)
		);
	}

	/**
	 * One address and its two toggles, read together.
	 *
	 * @param array  $settings The widget's settings, or one repeater row.
	 * @param string $key      The address control's name.
	 * @return string The attributes for that link.
	 */
	protected function link_from( $settings, $key ) {
		return $this->link_attributes(
			isset( $settings[ $key ] ) ? $settings[ $key ] : '',
			isset( $settings[ $key . '_blank' ] ) ? $settings[ $key . '_blank' ] : '',
			isset( $settings[ $key . '_nofollow' ] ) ? $settings[ $key . '_nofollow' ] : ''
		);
	}

	/**
	 * The attributes for one link.
	 *
	 * A link field takes a path, an anchor or a whole address; a value with no
	 * scheme is this site. Which tab it opens in is the toggle's answer, never
	 * the address's. Settled here once, for every link the plugin prints.
	 *
	 * @param string $value    Raw control value.
	 * @param string $blank    The new-tab toggle.
	 * @param string $nofollow The nofollow toggle.
	 * @return string Escaped href, and whatever the toggles add to it.
	 */
	protected function link_attributes( $value, $blank = '', $nofollow = '' ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		$attributes = ' href="' . esc_url( $value ) . '"';
		$rel        = array();

		if ( 'yes' === $blank ) {
			$attributes .= ' target="_blank"';
			$rel[]       = 'noopener';
			$rel[]       = 'noreferrer';
		}

		if ( 'yes' === $nofollow ) {
			$rel[] = 'nofollow';
		}

		if ( ! empty( $rel ) ) {
			$attributes .= ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"';
		}

		return $attributes;
	}
}
