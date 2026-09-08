<?php
/**
 * What every widget shares.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

use Elementor\Controls_Manager;

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
		$shared = Widgets_Loader::HANDLE_PREFIX . 'base-widget';
		$handle = $this->asset_handle();

		$depends = wp_script_is( $shared, 'registered' ) ? array( $shared ) : array();

		if ( wp_script_is( $handle, 'registered' ) ) {
			$depends[] = $handle;
		}

		return $depends;
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
	 * A slot the client has not filled is still a slot, and is seen as one: the
	 * box stands, and it stands white. The box itself belongs to the section
	 * around it; only what fills it while it is empty is settled here, for
	 * every section at once.
	 *
	 * @param string $url  What the client uploaded, if anything.
	 * @param string $alt  What a reader who cannot see it is told.
	 * @param bool   $lazy Whether it waits until it is on screen to load.
	 */
	protected function media( $url, $alt = '', $lazy = false ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			echo '<span class="custom-media-empty" aria-hidden="true"></span>';

			return;
		}

		// A video standing where a picture would stands muted, loops, and
		// starts without being asked. Whether it holds still for a reader who
		// wants less movement is settled by the shared script, which finds it
		// by the attribute printed here.
		if ( $this->is_film( $url ) ) {
			printf(
				'<video src="%s" autoplay loop muted playsinline preload="auto" data-custom-plays="1"></video>',
				esc_url( $url )
			);

			return;
		}

		printf(
			'<img src="%s" alt="%s"%s />',
			esc_url( $url ),
			esc_attr( $alt ),
			$lazy ? ' loading="lazy"' : ''
		);
	}

	/**
	 * Whether what the client chose is a video rather than a picture.
	 *
	 * @param string $url What the client chose.
	 * @return bool
	 */
	protected function is_film( $url ) {
		$type = wp_check_filetype( (string) $url );

		return isset( $type['type'] ) && 0 === strpos( (string) $type['type'], 'video/' );
	}

	/**
	 * A map is a map, not a picture of one.
	 *
	 * Three things get pasted into a map box and all three are reasonable, so
	 * all three are taken: the whole frame copied out of the Share dialog, the
	 * bare address bar from inside it, or the place itself written out.
	 *
	 * Whatever arrives, only a Google host survives. What comes back is written
	 * straight into a frame's source, and an unchecked one is somebody else's
	 * page standing inside ours — the box is filled by people who are trusted,
	 * which is not the same as people who never paste the wrong thing.
	 *
	 * @param string $raw  What was pasted.
	 * @param int    $zoom How close the place stands, when an address was given.
	 * @return string Empty where it cannot be trusted, so the caller falls back.
	 */
	protected function map_src( $raw, $zoom = 15 ) {
		$raw = trim( (string) $raw );

		if ( '' === $raw ) {
			return '';
		}

		if ( false !== stripos( $raw, '<iframe' ) && preg_match( '/\ssrc\s*=\s*["\']([^"\']+)["\']/i', $raw, $found ) ) {
			$raw = html_entity_decode( $found[1], ENT_QUOTES, 'UTF-8' );
		}

		// A place written out rather than a link: build the frame Google serves
		// without a key.
		if ( ! preg_match( '#^https?://#i', $raw ) ) {
			return add_query_arg(
				array(
					'q'      => rawurlencode( $raw ),
					'z'      => (int) $zoom,
					'output' => 'embed',
				),
				'https://maps.google.com/maps'
			);
		}

		$host = strtolower( (string) wp_parse_url( $raw, PHP_URL_HOST ) );

		// Matched on the whole label, so `google.com.somewhere-else.net` fails.
		if ( ! preg_match( '/(^|\.)google(\.[a-z]{2,3}){1,2}$/', $host ) ) {
			return '';
		}

		// A maps link that is not already a frame shows a consent page inside
		// one instead of the place. This is what turns it into a frame.
		if ( false === strpos( $raw, 'output=embed' ) && false === strpos( $raw, '/maps/embed' ) ) {
			$raw = add_query_arg( 'output', 'embed', $raw );
		}

		return esc_url_raw( set_url_scheme( $raw, 'https' ) );
	}

	/**
	 * One map slot's contents: the place, or the box saying none was given.
	 *
	 * @param string $raw   What was pasted.
	 * @param string $title What a reader who cannot see it is told.
	 * @param int    $zoom  How close the place stands.
	 */
	protected function map( $raw, $title = '', $zoom = 15 ) {
		$src = $this->map_src( $raw, $zoom );

		if ( '' === $src ) {
			echo '<span class="custom-media-empty" aria-hidden="true"></span>';

			return;
		}

		printf(
			'<iframe src="%s" title="%s" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>',
			esc_url( $src ),
			esc_attr( '' !== $title ? $title : __( 'Map', 'custom-elementor-widgets' ) )
		);
	}

	/**
	 * The menus the site has, for a control to choose one from.
	 *
	 * A widget is placed on a page, not registered with the theme, so it is
	 * pointed at a menu by name rather than at a location the theme would have
	 * had to declare on its behalf.
	 *
	 * @return array
	 */
	protected function menu_options() {
		$options = array( '' => esc_html__( 'None', 'custom-elementor-widgets' ) );

		foreach ( wp_get_nav_menus() as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}

	/**
	 * One menu, printed flat.
	 *
	 * @param int|string $menu  Which menu the client chose.
	 * @param string     $class The list's class.
	 * @return bool Whether anything was printed.
	 */
	protected function menu( $menu, $class = '' ) {
		$menu = (int) $menu;

		if ( 0 === $menu || ! wp_get_nav_menu_object( $menu ) ) {
			return false;
		}

		wp_nav_menu(
			array(
				'menu'        => $menu,
				'container'   => false,
				'menu_class'  => $class,
				'depth'       => 1,
				'fallback_cb' => false,
			)
		);

		return true;
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
	 * The section's settings, read as one item of its list rather than as the
	 * page the list is on.
	 *
	 * A control pointed at a field answers about the post that is standing, and
	 * Elementor asks once and keeps the answer. Asked again with an item
	 * standing, a typed value comes back the same for every item and a field
	 * comes back as that item's own.
	 *
	 * @param \WP_Post $post The item.
	 * @return array
	 */
	protected function item_settings( $post ) {
		global $wp_query;

		$keep = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		// What is standing has two answers: the post being printed, and the post
		// the page was asked for. A tag may read either, so both are the item
		// while it is read, and both are put back after.
		$was_object = isset( $wp_query->queried_object ) ? $wp_query->queried_object : null;
		$was_id     = isset( $wp_query->queried_object_id ) ? $wp_query->queried_object_id : null;

		$GLOBALS['post'] = $post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- put back below.
		setup_postdata( $post );

		$wp_query->queried_object    = $post;
		$wp_query->queried_object_id = $post->ID;

		$settings = $this->parse_dynamic_settings( $this->get_settings() );

		$wp_query->queried_object    = $was_object;
		$wp_query->queried_object_id = $was_id;

		$GLOBALS['post'] = $keep; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- what it was.

		if ( $keep ) {
			setup_postdata( $keep );
		} else {
			wp_reset_postdata();
		}

		return array_map(
			function ( $value ) {
				return is_scalar( $value ) ? trim( (string) $value ) : $value;
			},
			$settings
		);
	}

	/**
	 * A day as the design writes it.
	 *
	 * What a field answers is a moment, so that a section can count by it. What
	 * a card states is a day. Anything that is not a moment is left as it was
	 * typed, since then it is words the client chose.
	 *
	 * @param string $value The setting.
	 * @return string
	 */
	protected function as_day( $value ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		// Read and written in the one frame. Read in the server's and written in
		// another, a day at midnight lands on the day before.
		try {
			$day = new \DateTimeImmutable( $value, new \DateTimeZone( 'UTC' ) );
		} catch ( \Exception $e ) {
			return $value;
		}

		return $day->format( 'd M Y' );
	}

	/**
	 * Content → Source.
	 *
	 * Three controls, each one narrowing the last: the post type, one of its
	 * taxonomies, and terms of that taxonomy. A taxonomy left alone means the
	 * whole post type; terms left alone mean the whole taxonomy.
	 */
	protected function register_source_controls() {
		$this->start_controls_section(
			'section_source',
			array(
				'label' => esc_html__( 'Source', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => self::post_type_options(),
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'       => esc_html__( 'Taxonomy', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array_merge(
					array( '' => esc_html__( 'All', 'custom-elementor-widgets' ) ),
					self::taxonomy_options()
				),
				'description' => esc_html__( 'Left on All, the whole source is shown. Choose one and an item with no term in it is not shown at all.', 'custom-elementor-widgets' ),
			)
		);

		// One term control per taxonomy, shown only for the taxonomy chosen.
		foreach ( self::taxonomy_options() as $name => $label ) {
			$this->add_control(
				'terms_' . $name,
				array(
					'label'       => esc_html__( 'Terms', 'custom-elementor-widgets' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => self::term_options( $name ),
					'condition'   => array( 'taxonomy' => $name ),
					'description' => esc_html__( 'Left empty, every term is shown.', 'custom-elementor-widgets' ),
				)
			);
		}

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'Descending', 'custom-elementor-widgets' ),
					'ASC'  => esc_html__( 'Ascending', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->register_more_source_controls();

		$this->end_controls_section();
	}

	/**
	 * What a section wants to ask about its source beyond where it comes from.
	 *
	 * Asked here so it stands with the rest of the questions about the list
	 * rather than in a section of its own.
	 */
	protected function register_more_source_controls() {}

	/**
	 * Every field the client has, named as they named it.
	 *
	 * Read from the field plugin where it is active. Without it there is no list
	 * to offer, and a box to type a key into would be a box to mistype one into.
	 *
	 * @return array
	 */
	public static function field_options() {
		$options = array( '' => esc_html__( 'None', 'custom-elementor-widgets' ) );

		if ( ! function_exists( 'acf_get_field_groups' ) || ! function_exists( 'acf_get_fields' ) ) {
			return $options;
		}

		foreach ( (array) acf_get_field_groups() as $group ) {
			foreach ( (array) acf_get_fields( $group ) as $field ) {
				if ( empty( $field['name'] ) ) {
					continue;
				}

				$options[ $field['name'] ] = sprintf(
					/* translators: 1: the field's label, 2: the group it belongs to. */
					esc_html__( '%1$s — %2$s', 'custom-elementor-widgets' ),
					isset( $field['label'] ) ? $field['label'] : $field['name'],
					isset( $group['title'] ) ? $group['title'] : ''
				);
			}
		}

		return $options;
	}

	/**
	 * What the three controls come to, as a query.
	 *
	 * A taxonomy left alone is the whole source; terms left alone are the whole
	 * taxonomy. Nothing here names a post type, so the same section serves
	 * another list later.
	 *
	 * @param array $settings The widget's settings.
	 * @param array $extra    What the section adds of its own.
	 * @return array
	 */
	protected function source_query( $settings, $extra = array() ) {
		$source   = isset( $settings['source'] ) ? (string) $settings['source'] : 'post';
		$taxonomy = isset( $settings['taxonomy'] ) ? (string) $settings['taxonomy'] : '';
		$order    = isset( $settings['order'] ) && 'ASC' === $settings['order'] ? 'ASC' : 'DESC';

		$query = array(
			'post_type'   => '' !== $source ? $source : 'post',
			'post_status' => 'publish',
			'orderby'     => 'date',
			'order'       => $order,
		);

		if ( '' !== $taxonomy ) {
			$terms = isset( $settings[ 'terms_' . $taxonomy ] ) ? (array) $settings[ 'terms_' . $taxonomy ] : array();
			$terms = array_filter( $terms );

			// Terms left alone are the whole taxonomy: whatever has a term in
			// it, and nothing that has none.
			$query['tax_query'] = array(
				empty( $terms )
					? array(
						'taxonomy' => $taxonomy,
						'operator' => 'EXISTS',
					)
					: array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms,
					),
			);
		}

		return array_merge( $query, $extra );
	}

	/**
	 * The post types a client may draw from.
	 *
	 * @return array
	 */
	protected static function post_type_options() {
		$options = array();

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) {
			if ( 'attachment' === $type->name ) {
				continue;
			}

			$options[ $type->name ] = $type->labels->singular_name;
		}

		return $options;
	}

	/**
	 * The taxonomies a client may narrow by.
	 *
	 * @return array
	 */
	protected static function taxonomy_options() {
		$options = array();

		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
			if ( ! $taxonomy->show_ui ) {
				continue;
			}

			$options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
		}

		return $options;
	}

	/**
	 * The terms of one taxonomy.
	 *
	 * @param string $taxonomy The taxonomy's name.
	 * @return array
	 */
	protected static function term_options( $taxonomy ) {
		$options = array();
		$terms   = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $options;
		}

		foreach ( $terms as $term ) {
			$options[ $term->slug ] = $term->name;
		}

		return $options;
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
					'label'   => $label,
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array( 'active' => true ),
				),
				$args
			)
		);

		$target->add_control(
			$key . '_blank',
			array(
				'label'   => esc_html__( 'Open in a new tab', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$target->add_control(
			$key . '_nofollow',
			array(
				'label'   => esc_html__( 'nofollow', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SWITCHER,
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
