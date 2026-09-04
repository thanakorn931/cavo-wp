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

/**
 * The forms, and the inbox behind them.
 *
 * One menu holds all of it: the messages that have come in, the editor that
 * says what a form asks, the settings that say what happens when one is sent,
 * and the captcha keys, which are the domain's rather than any one form's.
 */

/**
 * Where the messages live.
 *
 * A message is a record, not something anybody writes: the post type is shut to
 * the public, to queries, to REST and to search, and the screen that lists them
 * adds nothing.
 */
function kadence_child_message_post_type() {
	register_post_type(
		'cavo_message',
		array(
			'labels'              => array(
				'name'          => esc_html__( 'Inbox', 'kadence-child' ),
				'singular_name' => esc_html__( 'Message', 'kadence-child' ),
				'menu_name'     => esc_html__( 'Inbox', 'kadence-child' ),
				'search_items'  => esc_html__( 'Search messages', 'kadence-child' ),
				'not_found'     => esc_html__( 'No messages yet', 'kadence-child' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => 'cavo-form',
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array( 'title' ),
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'kadence_child_message_post_type' );

/**
 * The forms the site has, by name.
 *
 * Everything else on the menu is registered from this list, so adding a form is
 * adding a name here and nothing more.
 *
 * @return array Slug to name.
 */
function kadence_child_forms() {
	$forms = array();

	if ( ! function_exists( 'get_field' ) ) {
		return $forms;
	}

	foreach ( (array) get_field( 'forms', 'option' ) as $row ) {
		$name = isset( $row['form_name'] ) ? trim( (string) $row['form_name'] ) : '';

		if ( '' === $name ) {
			continue;
		}

		$forms[ sanitize_title( $name ) ] = $name;
	}

	return $forms;
}

/**
 * The menu: the inbox, an editor, the settings, and the keys.
 *
 * With one form the editor and the settings are a page each. With more than one
 * the editor becomes a page per form and the settings grow a tab per form, so
 * the shape of the menu follows the number of forms rather than the other way
 * round.
 */
function kadence_child_form_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => esc_html__( 'WP Form', 'kadence-child' ),
			'menu_title' => esc_html__( 'WP Form', 'kadence-child' ),
			'menu_slug'  => 'cavo-form',
			'capability' => 'manage_options',
			'icon_url'   => 'dashicons-email-alt',
			'position'   => 26,
			'redirect'   => false,
		)
	);

	$forms = kadence_child_forms();

	if ( count( $forms ) > 1 ) {
		foreach ( $forms as $slug => $name ) {
			acf_add_options_sub_page(
				array(
					/* translators: %s: the form's name. */
					'page_title'  => sprintf( esc_html__( '%s form editor', 'kadence-child' ), $name ),
					'menu_title'  => sprintf( esc_html__( '%s form editor', 'kadence-child' ), $name ),
					'menu_slug'   => 'cavo-form-editor-' . $slug,
					'parent_slug' => 'cavo-form',
					'capability'  => 'manage_options',
				)
			);
		}
	} else {
		acf_add_options_sub_page(
			array(
				'page_title'  => esc_html__( 'Form editor', 'kadence-child' ),
				'menu_title'  => esc_html__( 'Form editor', 'kadence-child' ),
				'menu_slug'   => 'cavo-form-editor',
				'parent_slug' => 'cavo-form',
				'capability'  => 'manage_options',
			)
		);
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => esc_html__( 'Settings', 'kadence-child' ),
			'menu_title'  => esc_html__( 'Settings', 'kadence-child' ),
			'menu_slug'   => 'cavo-form-settings',
			'parent_slug' => 'cavo-form',
			'capability'  => 'manage_options',
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title'  => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'menu_title'  => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'menu_slug'   => 'cavo-form-recaptcha',
			'parent_slug' => 'cavo-form',
			'capability'  => 'manage_options',
		)
	);
}
add_action( 'acf/init', 'kadence_child_form_pages' );

/**
 * The inbox stands first on the menu, whatever order the pages registered in.
 */
function kadence_child_form_menu_order() {
	global $submenu;

	if ( empty( $submenu['cavo-form'] ) ) {
		return;
	}

	$inbox = array();
	$rest  = array();

	foreach ( $submenu['cavo-form'] as $item ) {
		if ( isset( $item[2] ) && false !== strpos( $item[2], 'post_type=cavo_message' ) ) {
			$inbox[] = $item;
		} elseif ( isset( $item[2] ) && 'cavo-form' !== $item[2] ) {
			$rest[] = $item;
		}
	}

	$submenu['cavo-form'] = array_merge( $inbox, $rest ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- the menu is ours to order.
}
add_action( 'admin_menu', 'kadence_child_form_menu_order', 999 );

/**
 * What a form asks, and what happens when one is sent.
 *
 * The fields are the client's to arrange; the settings are the client's to
 * word. Nothing here is a field on the page that shows the form.
 */
function kadence_child_form_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$forms = kadence_child_forms();

	// The list every other screen is built from.
	acf_add_local_field_group(
		array(
			'key'      => 'group_cavo_forms',
			'title'    => esc_html__( 'Forms', 'kadence-child' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form' ) ) ),
			'fields'   => array(
				array(
					'key'          => 'field_cavo_forms',
					'label'        => esc_html__( 'Forms', 'kadence-child' ),
					'name'         => 'forms',
					'type'         => 'repeater',
					'layout'       => 'block',
					'collapsed'    => 'field_cavo_form_name',
					'button_label' => esc_html__( 'Add form', 'kadence-child' ),
					'sub_fields'   => array(
						array(
							'key'      => 'field_cavo_form_name',
							'label'    => esc_html__( 'Name', 'kadence-child' ),
							'name'     => 'form_name',
							'type'     => 'text',
							'required' => 1,
						),
					),
				),
			),
		)
	);

	// One editor for the one form, or one for each of several.
	if ( count( $forms ) > 1 ) {
		foreach ( $forms as $slug => $name ) {
			acf_add_local_field_group( kadence_child_form_editor_group( $slug, $name, 'cavo-form-editor-' . $slug ) );
		}
	} else {
		$slug = key( $forms );
		$slug = $slug ? $slug : 'form';

		acf_add_local_field_group( kadence_child_form_editor_group( $slug, current( $forms ), 'cavo-form-editor' ) );
	}

	acf_add_local_field_group( kadence_child_form_settings_group( $forms ) );

	// The keys are the domain's, once for the site.
	acf_add_local_field_group(
		array(
			'key'      => 'group_cavo_recaptcha',
			'title'    => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-recaptcha' ) ) ),
			'fields'   => array(
				array(
					'key'   => 'field_cavo_v2_site',
					'label' => esc_html__( 'v2 site key', 'kadence-child' ),
					'name'  => 'recaptcha_v2_site',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_cavo_v2_secret',
					'label' => esc_html__( 'v2 secret key', 'kadence-child' ),
					'name'  => 'recaptcha_v2_secret',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_cavo_v3_site',
					'label' => esc_html__( 'v3 site key', 'kadence-child' ),
					'name'  => 'recaptcha_v3_site',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_cavo_v3_secret',
					'label' => esc_html__( 'v3 secret key', 'kadence-child' ),
					'name'  => 'recaptcha_v3_secret',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_cavo_v3_threshold',
					'label'         => esc_html__( 'v3 threshold', 'kadence-child' ),
					'name'          => 'recaptcha_v3_threshold',
					'type'          => 'number',
					'min'           => 0,
					'max'           => 1,
					'step'          => 0.1,
					'default_value' => 0.5,
				),
			),
		)
	);
}
add_action( 'acf/init', 'kadence_child_form_fields', 20 );

/**
 * One form's fields, on its own editor page.
 *
 * @param string $slug The form's slug.
 * @param string $name The form's name.
 * @param string $page The options page it sits on.
 * @return array
 */
function kadence_child_form_editor_group( $slug, $name, $page ) {
	return array(
		'key'      => 'group_cavo_editor_' . $slug,
		'title'    => $name ? $name : esc_html__( 'Fields', 'kadence-child' ),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => $page ) ) ),
		'fields'   => array(
			array(
				'key'          => 'field_cavo_fields_' . $slug,
				'label'        => esc_html__( 'Fields', 'kadence-child' ),
				'name'         => 'form_fields_' . $slug,
				'type'         => 'repeater',
				'layout'       => 'block',
				'collapsed'    => 'field_cavo_label_' . $slug,
				'button_label' => esc_html__( 'Add field', 'kadence-child' ),
				'sub_fields'   => array(
					array(
						'key'      => 'field_cavo_label_' . $slug,
						'label'    => esc_html__( 'Label', 'kadence-child' ),
						'name'     => 'label',
						'type'     => 'text',
						'required' => 1,
					),
					array(
						'key'           => 'field_cavo_type_' . $slug,
						'label'         => esc_html__( 'Type', 'kadence-child' ),
						'name'          => 'type',
						'type'          => 'select',
						'default_value' => 'text',
						'choices'       => array(
							'text'     => esc_html__( 'Text', 'kadence-child' ),
							'email'    => esc_html__( 'Email', 'kadence-child' ),
							'tel'      => esc_html__( 'Phone', 'kadence-child' ),
							'select'   => esc_html__( 'Choice', 'kadence-child' ),
							'date'     => esc_html__( 'Date', 'kadence-child' ),
							'textarea' => esc_html__( 'Message', 'kadence-child' ),
						),
					),
					array(
						'key'           => 'field_cavo_width_' . $slug,
						'label'         => esc_html__( 'Width', 'kadence-child' ),
						'name'          => 'width',
						'type'          => 'select',
						'default_value' => '100',
						'choices'       => array(
							'50'  => '50%',
							'100' => '100%',
						),
					),
					array(
						'key'           => 'field_cavo_required_' . $slug,
						'label'         => esc_html__( 'Required', 'kadence-child' ),
						'name'          => 'required',
						'type'          => 'true_false',
						'ui'            => 1,
						'default_value' => 0,
					),
					array(
						'key'   => 'field_cavo_placeholder_' . $slug,
						'label' => esc_html__( 'Placeholder', 'kadence-child' ),
						'name'  => 'placeholder',
						'type'  => 'text',
					),
					array(
						'key'               => 'field_cavo_choices_' . $slug,
						'label'             => esc_html__( 'Choices', 'kadence-child' ),
						'name'              => 'choices',
						'type'              => 'textarea',
						'rows'              => 5,
						'conditional_logic' => array(
							array(
								array(
									'field'    => 'field_cavo_type_' . $slug,
									'operator' => '==',
									'value'    => 'select',
								),
							),
						),
					),
				),
			),
		),
	);
}

/**
 * What happens when a form is sent: three sections, in this order.
 *
 * @param array $forms Slug to name.
 * @return array
 */
function kadence_child_form_settings_group( $forms ) {
	$fields = array();
	$many   = count( $forms ) > 1;

	if ( empty( $forms ) ) {
		$forms = array( 'form' => '' );
	}

	foreach ( $forms as $slug => $name ) {
		if ( $many ) {
			$fields[] = array(
				'key'   => 'field_cavo_tab_' . $slug,
				/* translators: %s: the form's name. */
				'label' => sprintf( esc_html__( '%s form', 'kadence-child' ), $name ),
				'type'  => 'tab',
			);
		}

		$fields = array_merge( $fields, kadence_child_form_settings_fields( $slug ) );
	}

	return array(
		'key'      => 'group_cavo_settings',
		'title'    => esc_html__( 'Settings', 'kadence-child' ),
		'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-settings' ) ) ),
		'fields'   => $fields,
	);
}

/**
 * One form's three sections.
 *
 * @param string $slug The form's slug.
 * @return array
 */
function kadence_child_form_settings_fields( $slug ) {
	return array(
		array(
			'key'   => 'field_cavo_notifications_' . $slug,
			'label' => esc_html__( 'Notifications', 'kadence-child' ),
			'type'  => 'message',
			'message' => esc_html__( 'The team always receives the answers. The copy to whoever wrote them is a courtesy on top of that.', 'kadence-child' ),
		),
		array(
			'key'          => 'field_cavo_team_' . $slug,
			'label'        => esc_html__( 'Team', 'kadence-child' ),
			'name'         => 'team_to_' . $slug,
			'type'         => 'text',
			'instructions' => '',
			'placeholder'  => 'name@example.com',
		),
		array(
			'key'           => 'field_cavo_client_copy_' . $slug,
			'label'         => esc_html__( 'Client — send a copy', 'kadence-child' ),
			'name'          => 'client_copy_' . $slug,
			'type'          => 'true_false',
			'ui'            => 1,
			'default_value' => 1,
		),
		array(
			'key'           => 'field_cavo_client_answers_' . $slug,
			'label'         => esc_html__( 'Client — attach their answers', 'kadence-child' ),
			'name'          => 'client_answers_' . $slug,
			'type'          => 'true_false',
			'ui'            => 1,
			'default_value' => 0,
		),
		array(
			'key'   => 'field_cavo_result_' . $slug,
			'label' => esc_html__( 'Submit result', 'kadence-child' ),
			'type'  => 'message',
			'message' => '',
		),
		array(
			'key'   => 'field_cavo_success_' . $slug,
			'label' => esc_html__( 'Success', 'kadence-child' ),
			'name'  => 'success_' . $slug,
			'type'  => 'text',
		),
		array(
			'key'   => 'field_cavo_fail_' . $slug,
			'label' => esc_html__( 'Fail', 'kadence-child' ),
			'name'  => 'fail_' . $slug,
			'type'  => 'text',
		),
		array(
			'key'   => 'field_cavo_captcha_' . $slug,
			'label' => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'type'  => 'message',
			'message' => esc_html__( 'A version chosen with no keys behind it means off.', 'kadence-child' ),
		),
		array(
			'key'           => 'field_cavo_version_' . $slug,
			'label'         => esc_html__( 'Version', 'kadence-child' ),
			'name'          => 'captcha_version_' . $slug,
			'type'          => 'select',
			'default_value' => 'off',
			'choices'       => array(
				'off' => esc_html__( 'Off', 'kadence-child' ),
				'v2'  => esc_html__( 'v2 checkbox', 'kadence-child' ),
				'v3'  => esc_html__( 'v3', 'kadence-child' ),
			),
		),
	);
}

/**
 * One form's definition, as the client left it.
 *
 * @param string $slug The form's slug.
 * @return array
 */
function kadence_child_form_definition( $slug ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	return (array) get_field( 'form_fields_' . $slug, 'option' );
}

/**
 * What comes in when a form is sent.
 *
 * The row is the record and the email a courtesy on top of it, so the answers
 * are stored before anything is sent and a notification that never leaves
 * cannot lose the enquiry.
 */
function kadence_child_form_submit() {
	$slug  = isset( $_POST['cavo_form'] ) ? sanitize_title( wp_unslash( $_POST['cavo_form'] ) ) : '';
	$forms = kadence_child_forms();
	$name  = isset( $forms[ $slug ] ) ? $forms[ $slug ] : '';
	$back  = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( '' === $slug || ! isset( $_POST['cavo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cavo_nonce'] ) ), 'cavo_form_' . $slug ) ) {
		wp_safe_redirect( add_query_arg( 'sent', 'no', $back ) );
		exit;
	}

	// A field the form does not ask for is not an answer.
	$definition = kadence_child_form_definition( $slug );
	$answers    = array();
	$sender     = '';

	foreach ( $definition as $index => $field ) {
		$label = isset( $field['label'] ) ? trim( (string) $field['label'] ) : '';

		if ( '' === $label ) {
			continue;
		}

		$key   = 'field_' . $index;
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$value = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );

		if ( ! empty( $field['required'] ) && '' === $value ) {
			wp_safe_redirect( add_query_arg( 'sent', 'no', $back ) );
			exit;
		}

		if ( '' === $sender && 'email' === $field['type'] && is_email( $value ) ) {
			$sender = $value;
		}

		$answers[] = array(
			'label' => $label,
			'value' => $value,
		);
	}

	// Store first.
	$message = wp_insert_post(
		array(
			'post_type'   => 'cavo_message',
			'post_status' => 'publish',
			'post_title'  => trim( $name . ' — ' . ( '' !== $sender ? $sender : __( 'no address', 'kadence-child' ) ) ),
		),
		true
	);

	if ( is_wp_error( $message ) ) {
		wp_safe_redirect( add_query_arg( 'sent', 'no', $back ) );
		exit;
	}

	update_post_meta( $message, 'cavo_form_slug', $slug );
	update_post_meta( $message, 'cavo_form_name', $name );
	update_post_meta( $message, 'cavo_sender', $sender );
	update_post_meta( $message, 'cavo_answers', wp_json_encode( $answers ) );
	update_post_meta( $message, 'cavo_unread', 1 );

	// Mail second.
	kadence_child_form_mail( $slug, $name, $sender, $answers, $message );

	wp_safe_redirect( add_query_arg( 'sent', 'yes', $back ) );
	exit;
}
add_action( 'admin_post_nopriv_cavo_form', 'kadence_child_form_submit' );
add_action( 'admin_post_cavo_form', 'kadence_child_form_submit' );

/**
 * The two emails. They are not one email with different words.
 *
 * The team always receives the answers, and its subject carries the form's name
 * and whoever wrote them. The copy back is a courtesy, and carries the answers
 * only where that has been asked for.
 *
 * @param string $slug    The form's slug.
 * @param string $name    The form's name.
 * @param string $sender  Whoever wrote them, where they gave an address.
 * @param array  $answers What they wrote.
 * @param int    $message The record already stored.
 */
function kadence_child_form_mail( $slug, $name, $sender, $answers, $message ) {
	$domain = wp_parse_url( home_url(), PHP_URL_HOST );
	$domain = preg_replace( '/^www\./', '', (string) $domain );

	// From is the domain. The sender's own address goes in Reply-To, where it
	// does not stop the mail arriving at all.
	$headers = array( sprintf( 'From: %s <no-reply@%s>', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $domain ) );

	if ( '' !== $sender ) {
		$headers[] = 'Reply-To: ' . $sender;
	}

	$body = '';

	foreach ( $answers as $answer ) {
		$body .= $answer['label'] . ': ' . $answer['value'] . "\r\n";
	}

	$team = function_exists( 'get_field' ) ? trim( (string) get_field( 'team_to_' . $slug, 'option' ) ) : '';
	$team = '' !== $team ? $team : get_option( 'admin_email' );

	$sent = wp_mail(
		$team,
		/* translators: 1: the form's name, 2: whoever sent it. */
		sprintf( __( '%1$s — %2$s', 'kadence-child' ), $name, '' !== $sender ? $sender : __( 'no address', 'kadence-child' ) ),
		$body,
		$headers
	);

	update_post_meta( $message, 'cavo_team_mail', $sent ? 1 : 0 );

	if ( '' === $sender || ! function_exists( 'get_field' ) || ! get_field( 'client_copy_' . $slug, 'option' ) ) {
		return;
	}

	$copy = get_field( 'client_answers_' . $slug, 'option' ) ? $body : '';

	update_post_meta(
		$message,
		'cavo_client_mail',
		wp_mail(
			$sender,
			/* translators: %s: the form's name. */
			sprintf( __( '%s — we have your message', 'kadence-child' ), $name ),
			$copy,
			$headers
		) ? 1 : 0
	);
}

/**
 * The inbox is a list of messages, not a list of posts.
 *
 * One inbox holds every form's, so it says which form each came from. What is
 * unread is said with a dot and a count, and reading one is a row action and a
 * bulk action rather than an edit.
 */
function kadence_child_inbox_columns( $columns ) {
	return array(
		'cb'          => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'cavo_unread' => '<span class="screen-reader-text">' . esc_html__( 'Unread', 'kadence-child' ) . '</span>',
		'title'       => esc_html__( 'Message', 'kadence-child' ),
		'cavo_form'   => esc_html__( 'Form', 'kadence-child' ),
		'cavo_sender' => esc_html__( 'From', 'kadence-child' ),
		'date'        => esc_html__( 'Received', 'kadence-child' ),
	);
}
add_filter( 'manage_cavo_message_posts_columns', 'kadence_child_inbox_columns' );

/**
 * What each column says.
 *
 * @param string $column  The column.
 * @param int    $post_id The message.
 */
function kadence_child_inbox_column( $column, $post_id ) {
	if ( 'cavo_unread' === $column ) {
		echo get_post_meta( $post_id, 'cavo_unread', true ) ? '<span class="cavo-unread" aria-label="' . esc_attr__( 'Unread', 'kadence-child' ) . '"></span>' : '';
	}

	if ( 'cavo_form' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'cavo_form_name', true ) );
	}

	if ( 'cavo_sender' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'cavo_sender', true ) );
	}
}
add_action( 'manage_cavo_message_posts_custom_column', 'kadence_child_inbox_column', 10, 2 );

/**
 * The dot, and how many are still wearing one.
 */
function kadence_child_inbox_css() {
	$screen = get_current_screen();

	if ( ! $screen || 'edit-cavo_message' !== $screen->id ) {
		return;
	}

	echo '<style>.column-cavo_unread{width:24px}.cavo-unread{display:inline-block;width:8px;height:8px;border-radius:50%;background:#d63638}</style>';
}
add_action( 'admin_head', 'kadence_child_inbox_css' );

/**
 * How many are unread, beside the menu.
 */
function kadence_child_inbox_count() {
	global $menu, $submenu;

	$unread = get_posts(
		array(
			'post_type'      => 'cavo_message',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'meta_key'       => 'cavo_unread', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => 1, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'no_found_rows'  => true,
		)
	);

	if ( empty( $unread ) ) {
		return;
	}

	$bubble = ' <span class="awaiting-mod"><span class="pending-count">' . count( $unread ) . '</span></span>';

	foreach ( (array) $menu as $index => $item ) {
		if ( isset( $item[2] ) && 'cavo-form' === $item[2] ) {
			$menu[ $index ][0] .= $bubble; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- the menu is ours to mark.
		}
	}

	if ( empty( $submenu['cavo-form'] ) ) {
		return;
	}

	foreach ( $submenu['cavo-form'] as $index => $item ) {
		if ( isset( $item[2] ) && false !== strpos( $item[2], 'post_type=cavo_message' ) ) {
			$submenu['cavo-form'][ $index ][0] .= $bubble; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- the menu is ours to mark.
		}
	}
}
add_action( 'admin_menu', 'kadence_child_inbox_count', 1000 );

/**
 * Reading one is an action on the row, not an edit.
 *
 * @param array    $actions What the row offers.
 * @param \WP_Post $post    The message.
 * @return array
 */
function kadence_child_inbox_actions( $actions, $post ) {
	if ( 'cavo_message' !== $post->post_type ) {
		return $actions;
	}

	unset( $actions['inline hide-if-no-js'] );

	if ( get_post_meta( $post->ID, 'cavo_unread', true ) ) {
		$actions['cavo_read'] = sprintf(
			'<a href="%s">%s</a>',
			esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'post_type'  => 'cavo_message',
							'cavo_read'  => $post->ID,
						),
						admin_url( 'edit.php' )
					),
					'cavo_read_' . $post->ID
				)
			),
			esc_html__( 'Mark as read', 'kadence-child' )
		);
	}

	return $actions;
}
add_filter( 'post_row_actions', 'kadence_child_inbox_actions', 10, 2 );

/**
 * And a bulk action beside it.
 *
 * @param array $actions What the screen offers.
 * @return array
 */
function kadence_child_inbox_bulk( $actions ) {
	$actions['cavo_read'] = esc_html__( 'Mark as read', 'kadence-child' );

	return $actions;
}
add_filter( 'bulk_actions-edit-cavo_message', 'kadence_child_inbox_bulk' );

/**
 * Marking them, one or many.
 *
 * @param string $redirect Where the screen goes next.
 * @param string $action   What was asked for.
 * @param array  $ids      Which messages.
 * @return string
 */
function kadence_child_inbox_bulk_handle( $redirect, $action, $ids ) {
	if ( 'cavo_read' !== $action ) {
		return $redirect;
	}

	foreach ( (array) $ids as $id ) {
		delete_post_meta( (int) $id, 'cavo_unread' );
	}

	return $redirect;
}
add_filter( 'handle_bulk_actions-edit-cavo_message', 'kadence_child_inbox_bulk_handle', 10, 3 );

/**
 * The row action's own answer.
 */
function kadence_child_inbox_read() {
	if ( ! isset( $_GET['cavo_read'] ) ) {
		return;
	}

	$id = (int) $_GET['cavo_read'];

	if ( ! current_user_can( 'edit_posts' ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'cavo_read_' . $id ) ) {
		return;
	}

	delete_post_meta( $id, 'cavo_unread' );

	wp_safe_redirect( remove_query_arg( array( 'cavo_read', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'kadence_child_inbox_read' );
