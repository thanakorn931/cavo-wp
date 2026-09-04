<?php
/**
 * Kadence Child functions.
 *
 * This file runs before the parent's, so nothing here calls a parent function
 * at file scope. Everything hooks.
 *
 * @package kadence-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the child stylesheet.
 *
 * Nothing enqueues it for us. It goes out after the parent's and declares the
 * parent's own handle ('kadence-global', registered in the parent's
 * inc/components/styles/component.php) as its dependency, so it always lands
 * last. The parent's styles and scripts are left alone.
 */
function kadence_child_enqueue_styles() {
	$style = get_stylesheet_directory() . '/style.css';

	wp_enqueue_style(
		'kadence-child',
		get_stylesheet_uri(),
		array( 'kadence-global' ),
		file_exists( $style ) ? (string) filemtime( $style ) : false
	);
}
add_action( 'wp_enqueue_scripts', 'kadence_child_enqueue_styles', 20 );

/**
 * Give every post type the classic editor.
 *
 * Pages are built in Elementor, so the block canvas is a second place to put
 * content nothing renders. Switched for every post type rather than a named
 * list, so types registered later follow.
 */
add_filter( 'use_block_editor_for_post_type', '__return_false', 100 );

/**
 * Give the widgets screen the classic widgets.
 *
 * A separate switch from the filter above: theme support, added by core on
 * after_setup_theme, so this runs later and takes it back off.
 */
function kadence_child_classic_widgets() {
	remove_theme_support( 'widgets-block-editor' );
}
add_action( 'after_setup_theme', 'kadence_child_classic_widgets', 20 );

/**
 * Let the media library take SVG.
 *
 * WordPress already carries PDF and every video format a site has reason to
 * use. SVG is the one it refuses, and the refusal is deliberate: an SVG is XML
 * that can hold a script, and it is served from this site's own domain, so what
 * it holds runs in the browser of whoever opens it.
 *
 * The type joins the allowed list here, for everyone the media library already
 * opens to. kadence_child_svg_filetype() answers the check core makes against
 * the file's own bytes, and kadence_child_reject_unsafe_svg() turns away
 * anything executable — that last one is what keeps the type safe, rather than
 * a narrower door. All three are needed; the first on its own changes nothing.
 *
 * @param array $mimes Extension-to-MIME map.
 * @return array
 */
function kadence_child_allow_svg( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';

	return $mimes;
}
add_filter( 'upload_mimes', 'kadence_child_allow_svg' );

/**
 * Answer core's own check on the file.
 *
 * `wp_check_filetype_and_ext()` reads the bytes with finfo, which calls an SVG
 * text rather than an image, and refuses the upload on the mismatch between
 * that and the extension.
 *
 * @param array       $data      Ext, type and proper filename, or empties.
 * @param string      $file      Full path to the file.
 * @param string      $filename  The name of the file.
 * @param array|null  $mimes     Allowed mime types.
 * @param string|bool $real_mime What finfo made of the file.
 * @return array
 */
function kadence_child_svg_filetype( $data, $file, $filename, $mimes = null, $real_mime = false ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$ext = strtolower( (string) pathinfo( $filename, PATHINFO_EXTENSION ) );

	if ( 'svg' === $ext || 'svgz' === $ext ) {
		$data['ext']  = $ext;
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'kadence_child_svg_filetype', 10, 5 );

/**
 * Turn away an SVG that carries anything executable.
 *
 * An icon holds shapes and nothing else. A file holding a script, an event
 * handler, an embedded document or an XML entity was not drawn to be an icon,
 * and it is refused whole with the reason said rather than stripped and stored.
 *
 * @param array $file One entry of $_FILES, as wp_handle_upload() received it.
 * @return array
 */
function kadence_child_reject_unsafe_svg( $file ) {
	if ( ! empty( $file['error'] ) ) {
		return $file;
	}

	$ext = strtolower( (string) pathinfo( $file['name'], PATHINFO_EXTENSION ) );

	if ( 'svg' !== $ext && 'svgz' !== $ext ) {
		return $file;
	}

	$markup = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	if ( 'svgz' === $ext && false !== $markup ) {
		$markup = @gzdecode( $markup ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	if ( false === $markup || '' === $markup || false === stripos( $markup, '<svg' ) ) {
		$file['error'] = __( 'This file could not be read as an SVG.', 'kadence-child' );

		return $file;
	}

	$forbidden = array(
		'#<\s*script#i',
		'#<\s*(?:iframe|embed|object|foreignObject|handler|listener|set|animate)#i',
		'#\son[a-z]+\s*=#i',
		'#javascript\s*:#i',
		'#data\s*:\s*text/html#i',
		'#<!ENTITY#i',
		'#<!DOCTYPE#i',
		'#<\?php#i',
	);

	foreach ( $forbidden as $pattern ) {
		if ( preg_match( $pattern, $markup ) ) {
			$file['error'] = __( 'This SVG carries a script or an embedded document and was not uploaded. An icon holds shapes only.', 'kadence-child' );

			return $file;
		}
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'kadence_child_reject_unsafe_svg' );

/**
 * Give an SVG a size in the admin.
 *
 * An SVG carries no pixel size for WordPress to read, so the media grid and an
 * image field render it collapsed until the theme gives it one.
 *
 * @param array    $response   The attachment as the admin's JavaScript sees it.
 * @param \WP_Post $attachment The attachment itself.
 * @return array
 */
function kadence_child_svg_admin_size( $response, $attachment ) {
	if ( 'image/svg+xml' !== ( isset( $response['mime'] ) ? $response['mime'] : '' ) ) {
		return $response;
	}

	$response['sizes'] = array(
		'full' => array(
			'url'         => $response['url'],
			'width'       => 1024,
			'height'      => 1024,
			'orientation' => 'landscape',
		),
	);

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'kadence_child_svg_admin_size', 10, 2 );

/**
 * Let the sized SVG fill its thumbnail rather than sit at its own scale.
 */
function kadence_child_svg_admin_css() {
	echo '<style>.attachment .thumbnail img[src$=".svg"], .media-icon img[src$=".svg"], .attachment-preview .thumbnail img[src$=".svg"] { width: 100%; height: auto; }</style>';
}
add_action( 'admin_head', 'kadence_child_svg_admin_css' );

/**
 * Register the menu location the parent does not.
 *
 * The footer draws two lists of links side by side. The parent registers one
 * footer location, so only the second is ours to add. A location renders
 * nothing until a template — here, the footer widget — calls for it.
 */
function kadence_child_menu_locations() {
	register_nav_menus(
		array(
			'footer_secondary' => esc_html__( 'Footer Secondary', 'kadence-child' ),
		)
	);
}
add_action( 'after_setup_theme', 'kadence_child_menu_locations', 20 );

/**
 * The Events post type.
 *
 * What the client puts on the page is a list they add to and remove from, so it
 * is content of its own rather than fields on the page that shows it. The title,
 * the words and the picture are WordPress's own; only what WordPress has no
 * field for is added beside them.
 */
function kadence_child_event_post_type() {
	register_post_type(
		'event',
		array(
			'labels'        => array(
				'name'               => esc_html__( 'Events', 'kadence-child' ),
				'singular_name'      => esc_html__( 'Event', 'kadence-child' ),
				'add_new_item'       => esc_html__( 'Add Event', 'kadence-child' ),
				'edit_item'          => esc_html__( 'Edit Event', 'kadence-child' ),
				'new_item'           => esc_html__( 'New Event', 'kadence-child' ),
				'view_item'          => esc_html__( 'View Event', 'kadence-child' ),
				'search_items'       => esc_html__( 'Search Events', 'kadence-child' ),
				'not_found'          => esc_html__( 'No events yet', 'kadence-child' ),
				'all_items'          => esc_html__( 'All Events', 'kadence-child' ),
				'menu_name'          => esc_html__( 'Events', 'kadence-child' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'menu_position' => 21,
			'menu_icon'     => 'dashicons-calendar-alt',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			'show_in_rest'  => true,
			'rewrite'       => array( 'slug' => 'event' ),
		)
	);

	register_taxonomy(
		'event_category',
		'event',
		array(
			'labels'            => array(
				'name'          => esc_html__( 'Event Categories', 'kadence-child' ),
				'singular_name' => esc_html__( 'Event Category', 'kadence-child' ),
				'menu_name'     => esc_html__( 'Categories', 'kadence-child' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'event-category' ),
		)
	);
}
add_action( 'init', 'kadence_child_event_post_type' );

/**
 * The two categories the design draws, put there once.
 *
 * A term the client has to create before the page works is a page that arrives
 * broken. Renaming or adding to them afterwards is theirs; these two are only
 * ever created where they are missing.
 */
function kadence_child_event_terms() {
	if ( get_option( 'kadence_child_event_terms' ) ) {
		return;
	}

	foreach ( array(
		'current-events' => esc_html__( 'Current Events', 'kadence-child' ),
		'past-events'    => esc_html__( 'Past Events', 'kadence-child' ),
	) as $slug => $name ) {
		if ( ! term_exists( $slug, 'event_category' ) ) {
			wp_insert_term( $name, 'event_category', array( 'slug' => $slug ) );
		}
	}

	update_option( 'kadence_child_event_terms', 1 );
}
add_action( 'init', 'kadence_child_event_terms', 20 );

/**
 * What an event carries that WordPress has no field for.
 *
 * The date, the hour and the kind of music. Registered in code so the fields
 * travel with the theme rather than being imported into each environment, and
 * only where the plugin that renders them is active.
 */
function kadence_child_event_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_event_detail',
			'title'    => esc_html__( 'Event', 'kadence-child' ),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'event',
					),
				),
			),
			'position' => 'normal',
			'fields'   => array(
				array(
					'key'           => 'field_event_date',
					'label'         => esc_html__( 'Date', 'kadence-child' ),
					'name'          => 'event_date',
					'type'          => 'date_picker',
					'display_format' => 'd M Y',
					'return_format' => 'd M Y',
					'first_day'     => 1,
				),
				array(
					'key'            => 'field_event_time',
					'label'          => esc_html__( 'Time', 'kadence-child' ),
					'name'           => 'event_time',
					'type'           => 'time_picker',
					'display_format' => 'h : i A',
					'return_format'  => 'h : i A',
				),
				array(
					'key'   => 'field_event_genre',
					'label' => esc_html__( 'Genre', 'kadence-child' ),
					'name'  => 'event_genre',
					'type'  => 'text',
				),
			),
		)
	);
}
add_action( 'acf/init', 'kadence_child_event_fields' );

/**
 * The edit screen is a form, not a dashboard.
 *
 * A plugin drops its panel wherever it registered, opened, and WordPress lets
 * anyone drag the boxes into whatever order they like and remembers it. Neither
 * is the screen the client should meet. Which template a page is set to has no
 * bearing on any of this: the screen is shaped for the post type.
 */

/**
 * The plugin's boxes, per column, in the order they are wanted.
 *
 * @return array
 */
function kadence_child_plugin_box_order() {
	return array(
		'normal' => array( 'rank_math_metabox' ),
		'side'   => array( 'rank_math_metabox_content_ai', 'rank_math_metabox_link_suggestions' ),
	);
}

/**
 * Whether a box belongs to the plugin whose panels are about the page without
 * being the page.
 *
 * Matched by the prefix rather than by a list of names, so a box the plugin
 * adds in a later version is covered too, and so nothing happens at all where
 * the plugin is absent.
 *
 * @param string $id The box's id.
 * @return bool
 */
function kadence_child_is_plugin_box( $id ) {
	return 0 === strpos( $id, 'rank_math' );
}

/**
 * Move the plugin's boxes, each definition handed back in whole.
 *
 * Passing null for a title or a callback does not mean "leave what is
 * registered": `do_meta_boxes()` skips a box with an empty title, so the box
 * disappears instead of moving.
 */
function kadence_child_move_plugin_boxes() {
	global $wp_meta_boxes;

	$screen = get_current_screen();

	if ( ! $screen || 'post' !== $screen->base || empty( $wp_meta_boxes[ $screen->id ] ) ) {
		return;
	}

	$found = array();

	foreach ( array_keys( $wp_meta_boxes[ $screen->id ] ) as $context ) {
		foreach ( (array) $wp_meta_boxes[ $screen->id ][ $context ] as $boxes ) {
			foreach ( (array) $boxes as $id => $box ) {
				if ( $box && kadence_child_is_plugin_box( $id ) ) {
					$found[ $id ] = $box;
					remove_meta_box( $id, $screen, $context );
				}
			}
		}
	}

	if ( empty( $found ) ) {
		return;
	}

	foreach ( kadence_child_plugin_box_order() as $context => $order ) {
		foreach ( $order as $id ) {
			if ( isset( $found[ $id ] ) ) {
				kadence_child_place_box( $id, $found[ $id ], $screen, $context );
				unset( $found[ $id ] );
			}
		}
	}

	// A box the plugin adds later is not left where it fell.
	foreach ( $found as $id => $box ) {
		kadence_child_place_box( $id, $box, $screen, 'side' );
	}

	// Registering a box at the top priority puts it after whatever was there
	// already, so the head of each column is set here rather than by the order
	// the boxes happened to be registered in.
	foreach ( kadence_child_plugin_box_order() as $context => $order ) {
		if ( empty( $wp_meta_boxes[ $screen->id ][ $context ]['high'] ) ) {
			continue;
		}

		$group = $wp_meta_boxes[ $screen->id ][ $context ]['high'];
		$head  = array();

		foreach ( $order as $id ) {
			if ( isset( $group[ $id ] ) ) {
				$head[ $id ] = $group[ $id ];
				unset( $group[ $id ] );
			}
		}

		$wp_meta_boxes[ $screen->id ][ $context ]['high'] = $head + $group;
	}
}
add_action( 'add_meta_boxes', 'kadence_child_move_plugin_boxes', 999 );

/**
 * One box, put back with everything it was registered with.
 *
 * @param string     $id      The box's id.
 * @param array      $box     The box as it was registered.
 * @param \WP_Screen $screen  The screen it belongs to.
 * @param string     $context The column it goes in.
 */
function kadence_child_place_box( $id, $box, $screen, $context ) {
	if ( empty( $box['title'] ) || empty( $box['callback'] ) ) {
		return;
	}

	add_meta_box(
		$id,
		$box['title'],
		$box['callback'],
		$screen,
		$context,
		'high',
		isset( $box['args'] ) ? $box['args'] : null
	);
}

/**
 * The order is not the reader's to keep, and the plugin's panels load shut.
 *
 * @param \WP_Screen $screen The screen being shown.
 */
function kadence_child_edit_screen( $screen ) {
	if ( ! $screen || 'post' !== $screen->base ) {
		return;
	}

	// Discarding the order WordPress stored is what enforces the order.
	// Hiding the buttons that set it is not.
	add_filter( 'get_user_option_meta-box-order_' . $screen->id, '__return_empty_array' );

	add_filter( 'get_user_option_closedpostboxes_' . $screen->id, 'kadence_child_closed_boxes' );

	add_action( 'admin_head', 'kadence_child_edit_screen_css' );
	add_action( 'admin_print_footer_scripts', 'kadence_child_edit_screen_script' );
}
add_action( 'current_screen', 'kadence_child_edit_screen' );

/**
 * Only the plugin's panels come back shut. The page's own boxes open every
 * visit, whatever anyone left them as.
 *
 * @return array
 */
function kadence_child_closed_boxes() {
	$closed = array();

	foreach ( kadence_child_plugin_box_order() as $order ) {
		$closed = array_merge( $closed, $order );
	}

	return $closed;
}

/**
 * The move arrows go; the collapse toggle stays.
 */
function kadence_child_edit_screen_css() {
	echo '<style>#poststuff .postbox .handle-order-higher, #poststuff .postbox .handle-order-lower { display: none; } #poststuff .postbox .hndle { cursor: default; }</style>';
}

/**
 * And the drag with them.
 */
function kadence_child_edit_screen_script() {
	echo '<script>jQuery(function($){$(".meta-box-sortables").each(function(){if($(this).data("ui-sortable")){$(this).sortable("disable");}});});</script>';
}

/**
 * The plugin's primary-term radios, turned off through its own filter.
 */
add_filter( 'rank_math/admin/disable_primary_term', '__return_true' );
