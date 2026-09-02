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
		$handle = $this->asset_handle();

		return wp_style_is( $handle, 'registered' ) ? array( $handle ) : array();
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
	 * The attributes for one link, from the value the client typed.
	 *
	 * A link field takes a path, an anchor or a whole address. Where it opens
	 * follows from where it goes, not from a checkbox: a value with no scheme is
	 * this site and opens in the same tab; otherwise the host decides. Settled
	 * here once, for every link the plugin prints.
	 *
	 * @param string $value Raw control value.
	 * @return string Escaped href, and off the domain a target and rel with it.
	 */
	protected function link_attributes( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		$attributes = ' href="' . esc_url( $value ) . '"';
		$host       = wp_parse_url( $value, PHP_URL_HOST );
		$site       = wp_parse_url( home_url(), PHP_URL_HOST );

		if ( $host && strtolower( $host ) !== strtolower( (string) $site ) ) {
			$attributes .= ' target="_blank" rel="noopener noreferrer"';
		}

		return $attributes;
	}
}
