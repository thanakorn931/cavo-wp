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
 * One menu holds all of it, as a row of tabs rather than a list down the
 * sidebar: the messages that have come in, an editor per form, the settings
 * that say what happens when one is sent, and the captcha keys, which are the
 * domain's rather than any one form's.
 */

/**
 * The forms the site has, by slug.
 *
 * Every screen and every check reads this one list, so a second form costs a
 * row in it and nothing else. Which forms a site has is the build's, not the
 * client's: what each one asks is theirs, on its own editor.
 *
 * @return array Slug to name.
 */
function kadence_child_forms() {
	return array(
		'proposal' => esc_html__( 'Proposal', 'kadence-child' ),
	);
}

/**
 * One form's fields, as the client left them.
 *
 * @param string $slug The form's slug.
 * @return array
 */
function kadence_child_form_definition( $slug ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	return (array) get_field( 'form_fields_' . kadence_child_form_key( $slug ), 'option' );
}

/**
 * The key a form's own settings are stored under.
 *
 * With one form there is nothing to tell apart, so the key is fixed; with more
 * than one it is the form's.
 *
 * @param string $slug The form's slug.
 * @return string
 */
function kadence_child_form_key( $slug ) {
	$forms = kadence_child_forms();

	return count( $forms ) > 1 ? $slug : 'form';
}

/**
 * Where the parent puts a screen registered under it.
 *
 * A screen hung under the inbox is reached through the inbox. WordPress names
 * the hook it looks the screen up by after the parent the address gives it, so
 * only an address naming that same parent finds it.
 *
 * @param string $page The screen's slug.
 * @return string
 */
function kadence_child_form_url( $page ) {
	return admin_url( 'edit.php?post_type=cavo_message&page=' . $page );
}

/**
 * The tabs, and which screen each one is.
 *
 * @return array Slug to label and url.
 */
function kadence_child_form_tabs() {
	$tabs = array(
		'inbox'  => array(
			'label' => esc_html__( 'Inbox', 'kadence-child' ),
			'url'   => admin_url( 'edit.php?post_type=cavo_message' ),
			'page'  => 'edit-cavo_message',
		),
		'editor' => array(
			'label' => esc_html__( 'Form editor', 'kadence-child' ),
			'url'   => kadence_child_form_url( 'cavo-form-editor' ),
			'page'  => 'cavo-form-editor',
		),
	);

	$tabs['settings'] = array(
		'label' => esc_html__( 'Settings', 'kadence-child' ),
		'url'   => kadence_child_form_url( 'cavo-form-settings' ),
		'page'  => 'cavo-form-settings',
	);

	$tabs['recaptcha'] = array(
		'label' => esc_html__( 'reCAPTCHA', 'kadence-child' ),
		'url'   => kadence_child_form_url( 'cavo-form-recaptcha' ),
		'page'  => 'cavo-form-recaptcha',
	);

	return $tabs;
}

/**
 * Where the messages live.
 *
 * A message is a record, not something anybody writes: the post type is shut to
 * the public, to queries, to REST and to search.
 */
function kadence_child_message_post_type() {
	register_post_type(
		'cavo_message',
		array(
			'labels'              => array(
				'name'          => esc_html__( 'Inbox', 'kadence-child' ),
				'singular_name' => esc_html__( 'Message', 'kadence-child' ),
				'menu_name'     => esc_html__( 'WP Form', 'kadence-child' ),
				'search_items'  => esc_html__( 'Search messages', 'kadence-child' ),
				'not_found'     => esc_html__( 'No messages yet', 'kadence-child' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
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
 * The screens the tabs lead to.
 *
 * They hang under the inbox, which is the menu: the post type draws WP Form and
 * these are its other screens.
 */
function kadence_child_form_pages() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	foreach ( kadence_child_form_tabs() as $tab ) {
		if ( 'edit-cavo_message' === $tab['page'] ) {
			continue;
		}

		acf_add_options_sub_page(
			array(
				'page_title'    => $tab['label'],
				'menu_title'    => $tab['label'],
				'menu_slug'     => $tab['page'],
				'capability'    => 'manage_options',
				'parent_slug'   => 'edit.php?post_type=cavo_message',
				'update_button' => esc_html__( 'Save', 'kadence-child' ),
			)
		);
	}
}
add_action( 'acf/init', 'kadence_child_form_pages' );

/**
 * The sidebar carries the menu's name and nothing under it.
 *
 * The list is trimmed as the sidebar is about to be drawn, not while the menu is
 * being built: until the screen has been let in, WordPress is still reading that
 * same list to work out which parent the screen hangs from.
 */
function kadence_child_form_sidebar() {
	foreach ( kadence_child_form_tabs() as $tab ) {
		remove_submenu_page(
			'edit.php?post_type=cavo_message',
			'edit-cavo_message' === $tab['page'] ? 'edit.php?post_type=cavo_message' : $tab['page']
		);
	}

	remove_submenu_page( 'edit.php?post_type=cavo_message', 'post-new.php?post_type=cavo_message' );
}
add_action( 'admin_head', 'kadence_child_form_sidebar' );

/**
 * The address these screens were reached at before they hung under the inbox.
 *
 * A bookmark or a browser's history still holds it, so send it on rather than
 * leave it at a screen WordPress no longer knows by that name.
 */
function kadence_child_form_old_url() {
	global $pagenow;

	if ( 'admin.php' !== $pagenow || ! isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading which screen was asked for.
		return;
	}

	$page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading which screen was asked for.

	foreach ( kadence_child_form_tabs() as $tab ) {
		if ( $tab['page'] === $page ) {
			wp_safe_redirect( $tab['url'] );
			exit;
		}
	}
}
add_action( 'admin_init', 'kadence_child_form_old_url' );

/**
 * The tabs themselves, across the top of whichever screen is open.
 *
 * They print on the one hook that fires after the heading on both a post-type
 * list and an options page, so neither screen's markup is reproduced here.
 */
function kadence_child_form_tab_bar() {
	$screen = get_current_screen();

	if ( ! $screen ) {
		return;
	}

	// A tab is matched by its whole name: one form's is sooner or later the
	// beginning of another's.
	$here = 'edit-cavo_message' === $screen->id ? 'edit-cavo_message' : ( isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading which screen is open.
	$tabs = kadence_child_form_tabs();
	$mine = false;

	foreach ( $tabs as $tab ) {
		if ( $tab['page'] === $here ) {
			$mine = true;
			break;
		}
	}

	if ( ! $mine ) {
		return;
	}

	echo '<nav class="nav-tab-wrapper wp-clearfix" style="margin-bottom:16px">';

	foreach ( $tabs as $tab ) {
		printf(
			'<a class="nav-tab%s" href="%s">%s</a>',
			$tab['page'] === $here ? ' nav-tab-active' : '',
			esc_url( $tab['url'] ),
			esc_html( $tab['label'] )
		);
	}

	echo '</nav>';
}
add_action( 'all_admin_notices', 'kadence_child_form_tab_bar' );

/**
 * What each screen holds.
 */
function kadence_child_form_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$forms  = kadence_child_forms();
	$many   = count( $forms ) > 1;
	$editor = array();

	foreach ( $forms as $slug => $name ) {
		$key = $many ? $slug : 'form';

		if ( $many ) {
			$editor[] = array(
				'key'   => 'field_cavo_editor_tab_' . $key,
				/* translators: %s: the form's name. */
				'label' => sprintf( esc_html__( '%s form', 'kadence-child' ), $name ),
				'type'  => 'tab',
			);
		}

		$editor[] = kadence_child_form_fields_repeater( $key );

		if ( ! $many ) {
			break;
		}
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_cavo_editor',
			'title'    => esc_html__( 'Form', 'kadence-child' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-editor' ) ) ),
			'fields'   => $editor,
		)
	);

	acf_add_local_field_group( kadence_child_form_settings_group( $forms, $many ) );

	acf_add_local_field_group(
		array(
			'key'      => 'group_cavo_recaptcha',
			'title'    => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-recaptcha' ) ) ),
			'fields'   => array(
				array( 'key' => 'field_cavo_v2_site', 'label' => esc_html__( 'v2 site key', 'kadence-child' ), 'name' => 'recaptcha_v2_site', 'type' => 'text' ),
				array( 'key' => 'field_cavo_v2_secret', 'label' => esc_html__( 'v2 secret key', 'kadence-child' ), 'name' => 'recaptcha_v2_secret', 'type' => 'text' ),
				array( 'key' => 'field_cavo_v3_site', 'label' => esc_html__( 'v3 site key', 'kadence-child' ), 'name' => 'recaptcha_v3_site', 'type' => 'text' ),
				array( 'key' => 'field_cavo_v3_secret', 'label' => esc_html__( 'v3 secret key', 'kadence-child' ), 'name' => 'recaptcha_v3_secret', 'type' => 'text' ),
				array(
					'key'           => 'field_cavo_v3_threshold',
					'label'         => esc_html__( 'Score threshold', 'kadence-child' ),
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
add_action( 'acf/init', 'kadence_child_form_field_groups', 20 );

/**
 * The fields one form asks for.
 *
 * @param string $key The key the form's own settings are stored under.
 * @return array
 */
function kadence_child_form_fields_repeater( $key ) {
	return array(
		'key'          => 'field_cavo_fields_' . $key,
		'label'        => esc_html__( 'Fields', 'kadence-child' ),
		'name'         => 'form_fields_' . $key,
		'type'         => 'repeater',
		'layout'       => 'block',
		'collapsed'    => 'field_cavo_label_' . $key,
		'button_label' => esc_html__( 'Add field', 'kadence-child' ),
		'sub_fields'   => array(
			array(
				'key'      => 'field_cavo_label_' . $key,
				'label'    => esc_html__( 'Label', 'kadence-child' ),
				'name'     => 'label',
				'type'     => 'text',
				'required' => 1,
			),
			array(
				'key'           => 'field_cavo_type_' . $key,
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
				'key'           => 'field_cavo_width_' . $key,
				'label'         => esc_html__( 'Width', 'kadence-child' ),
				'name'          => 'width',
				'type'          => 'select',
				'default_value' => '100',
				'choices'       => array( '50' => '50%', '100' => '100%' ),
			),
			array(
				'key'           => 'field_cavo_required_' . $key,
				'label'         => esc_html__( 'Required', 'kadence-child' ),
				'name'          => 'required',
				'type'          => 'true_false',
				'ui'            => 1,
				'default_value' => 0,
			),
			array(
				'key'   => 'field_cavo_placeholder_' . $key,
				'label' => esc_html__( 'Placeholder', 'kadence-child' ),
				'name'  => 'placeholder',
				'type'  => 'text',
			),
			array(
				'key'               => 'field_cavo_choices_' . $key,
				'label'             => esc_html__( 'Choices', 'kadence-child' ),
				'name'              => 'choices',
				'type'              => 'textarea',
				'rows'              => 5,
				'conditional_logic' => array( array( array( 'field' => 'field_cavo_type_' . $key, 'operator' => '==', 'value' => 'select' ) ) ),
			),
		),
	);
}

/**
 * What happens when a form is sent: three sections, in this order and no other.
 *
 * @param array $forms Slug to name.
 * @param bool  $many  Whether the site has more than one form.
 * @return array
 */
function kadence_child_form_settings_group( $forms, $many ) {
	$fields = array();

	foreach ( $forms as $slug => $name ) {
		$key = $many ? $slug : 'form';

		if ( $many ) {
			$fields[] = array(
				'key'   => 'field_cavo_tab_' . $key,
				/* translators: %s: the form's name. */
				'label' => sprintf( esc_html__( '%s form', 'kadence-child' ), $name ),
				'type'  => 'tab',
			);
		}

		$fields = array_merge( $fields, kadence_child_form_settings_fields( $key ) );

		if ( ! $many ) {
			break;
		}
	}

	return array(
		'key'        => 'group_cavo_settings',
		'title'      => esc_html__( 'Settings', 'kadence-child' ),
		'location'   => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-settings' ) ) ),
		'menu_order' => 1,
		'fields'     => $fields,
	);
}

/**
 * One form's three sections.
 *
 * Both emails live in Notifications, in two groups named after who receives
 * them, so every label under them reads as a phrase.
 *
 * @param string $key The key the form's settings are stored under.
 * @return array
 */
function kadence_child_form_settings_fields( $key ) {
	return array(
		array(
			'key'          => 'field_cavo_team_group_' . $key,
			'label'        => esc_html__( 'Notifications › Team', 'kadence-child' ),
			'name'         => 'team_' . $key,
			'type'         => 'group',
			'sub_fields'   => array(
				array(
					'key'          => 'field_cavo_team_to_' . $key,
					'label'        => esc_html__( 'Send notifications to', 'kadence-child' ),
					'name'         => 'to',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => esc_html__( 'Add address', 'kadence-child' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_cavo_team_email_' . $key,
							'label' => esc_html__( 'Email', 'kadence-child' ),
							'name'  => 'email',
							'type'  => 'email',
						),
					),
				),
				array(
					'key'         => 'field_cavo_team_subject_' . $key,
					'label'       => esc_html__( 'Subject', 'kadence-child' ),
					'name'        => 'subject',
					'type'        => 'text',
					'placeholder' => '{form} — {name}',
				),
				array(
					'key'   => 'field_cavo_team_body_' . $key,
					'label' => esc_html__( 'Body', 'kadence-child' ),
					'name'  => 'body',
					'type'  => 'textarea',
					'rows'  => 5,
				),
			),
		),
		array(
			'key'        => 'field_cavo_client_group_' . $key,
			'label'      => esc_html__( 'Notifications › Client', 'kadence-child' ),
			'name'       => 'client_' . $key,
			'type'       => 'group',
			'sub_fields' => array(
				array(
					'key'           => 'field_cavo_client_copy_' . $key,
					'label'         => esc_html__( 'Send a copy', 'kadence-child' ),
					'name'          => 'copy',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
				),
				array(
					'key'   => 'field_cavo_client_subject_' . $key,
					'label' => esc_html__( 'Subject', 'kadence-child' ),
					'name'  => 'subject',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_cavo_client_body_' . $key,
					'label' => esc_html__( 'Body', 'kadence-child' ),
					'name'  => 'body',
					'type'  => 'textarea',
					'rows'  => 5,
				),
				array(
					'key'           => 'field_cavo_client_answers_' . $key,
					'label'         => esc_html__( 'Include their answers', 'kadence-child' ),
					'name'          => 'answers',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
				),
			),
		),
		array(
			'key'        => 'field_cavo_result_' . $key,
			'label'      => esc_html__( 'Submit result', 'kadence-child' ),
			'name'       => 'result_' . $key,
			'type'       => 'group',
			'sub_fields' => array(
				array(
					'key'   => 'field_cavo_success_' . $key,
					'label' => esc_html__( 'Success', 'kadence-child' ),
					'name'  => 'success',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_cavo_fail_' . $key,
					'label' => esc_html__( 'Fail', 'kadence-child' ),
					'name'  => 'fail',
					'type'  => 'text',
				),
			),
		),
		array(
			'key'        => 'field_cavo_captcha_' . $key,
			'label'      => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'name'       => 'captcha_' . $key,
			'type'       => 'group',
			'sub_fields' => array(
				array(
					'key'           => 'field_cavo_version_' . $key,
					'label'         => esc_html__( 'Version', 'kadence-child' ),
					'name'          => 'version',
					'type'          => 'select',
					'default_value' => 'off',
					'choices'       => array(
						'off' => esc_html__( 'Off', 'kadence-child' ),
						'v2'  => esc_html__( 'v2', 'kadence-child' ),
						'v3'  => esc_html__( 'v3', 'kadence-child' ),
					),
				),
			),
		),
	);
}

/**
 * One form's settings, whatever key they are stored under.
 *
 * Switches are read directly rather than through anything that fills in what is
 * missing: off is a value, and a helper that hands back the default cannot tell
 * the two apart.
 *
 * @param string $slug  The form's slug.
 * @param string $group Which group.
 * @return array
 */
function kadence_child_form_settings( $slug, $group ) {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	return (array) get_field( $group . '_' . kadence_child_form_key( $slug ), 'option' );
}

/**
 * Which captcha a form runs, once the keys behind it are counted.
 *
 * A version chosen with no keys behind it means off.
 *
 * @param string $slug The form's slug.
 * @return string
 */
function kadence_child_form_captcha_version( $slug ) {
	if ( ! function_exists( 'get_field' ) ) {
		return 'off';
	}

	$captcha = kadence_child_form_settings( $slug, 'captcha' );
	$version = isset( $captcha['version'] ) ? (string) $captcha['version'] : 'off';

	if ( 'off' === $version ) {
		return 'off';
	}

	$site   = trim( (string) get_field( 'recaptcha_' . $version . '_site', 'option' ) );
	$secret = trim( (string) get_field( 'recaptcha_' . $version . '_secret', 'option' ) );

	return ( '' !== $site && '' !== $secret ) ? $version : 'off';
}

/**
 * Whatever the captcha needs on the page, printed where the form is.
 *
 * @param string $slug The form's slug.
 */
function kadence_child_form_captcha_field( $slug ) {
	$version = kadence_child_form_captcha_version( $slug );

	if ( 'off' === $version ) {
		return;
	}

	$site = trim( (string) get_field( 'recaptcha_' . $version . '_site', 'option' ) );

	if ( 'v2' === $version ) {
		wp_enqueue_script( 'cavo-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google's own script carries no version.
		printf( '<div class="g-recaptcha" data-sitekey="%s"></div>', esc_attr( $site ) );

		return;
	}

	wp_enqueue_script( 'cavo-recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . rawurlencode( $site ), array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google's own script carries no version.

	printf(
		'<input type="hidden" name="g-recaptcha-response" value="" data-cavo-recaptcha="%s" />',
		esc_attr( $site )
	);

	wp_add_inline_script(
		'cavo-recaptcha',
		'grecaptcha.ready(function(){document.querySelectorAll("[data-cavo-recaptcha]").forEach(function(f){grecaptcha.execute(f.dataset.cavoRecaptcha,{action:"submit"}).then(function(t){f.value=t;});});});'
	);
}

/**
 * Whether the captcha is satisfied.
 *
 * @param string $slug  The form's slug.
 * @param string $token What came back with the message.
 * @return bool
 */
function kadence_child_form_captcha_passed( $slug, $token ) {
	$version = kadence_child_form_captcha_version( $slug );

	if ( 'off' === $version ) {
		return true;
	}

	if ( '' === $token ) {
		return false;
	}

	$answer = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => trim( (string) get_field( 'recaptcha_' . $version . '_secret', 'option' ) ),
				'response' => $token,
			),
		)
	);

	if ( is_wp_error( $answer ) ) {
		return false;
	}

	$said = json_decode( wp_remote_retrieve_body( $answer ), true );

	if ( empty( $said['success'] ) ) {
		return false;
	}

	if ( 'v3' !== $version ) {
		return true;
	}

	$threshold = (float) get_field( 'recaptcha_v3_threshold', 'option' );
	$threshold = $threshold > 0 ? $threshold : 0.5;

	return isset( $said['score'] ) && (float) $said['score'] >= $threshold;
}

/**
 * Which transport carried the last message, read after every other hook has had
 * its turn — the plugin that sends is on this hook too, and its answer is the
 * one that counts.
 *
 * @param \PHPMailer $mailer The mailer.
 */
function kadence_child_mail_transport( $mailer ) {
	$GLOBALS['cavo_transport'] = isset( $mailer->Mailer ) ? (string) $mailer->Mailer : ''; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's own property.
}
add_action( 'phpmailer_init', 'kadence_child_mail_transport', PHP_INT_MAX );

/**
 * One email, and the state it left in.
 *
 * Four states, not two. An empty transport is not a failure: a plugin sending
 * through a provider's own API never reaches PHPMailer, so the hook never fires.
 *
 * @param string|array $to      Who it goes to.
 * @param string       $subject Its subject.
 * @param string       $body    Its body.
 * @param array        $headers Its headers.
 * @return string sent, nosmtp, failed or nothing.
 */
function kadence_child_send( $to, $subject, $body, $headers ) {
	if ( empty( $to ) ) {
		return 'nothing';
	}

	$GLOBALS['cavo_transport'] = null;

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( ! $sent ) {
		return 'failed';
	}

	return 'mail' === $GLOBALS['cavo_transport'] ? 'nosmtp' : 'sent';
}

/**
 * What comes in when a form is sent.
 *
 * The row is the record and the email a courtesy on top of it, so the answers
 * are stored before anything is sent. The questions are stored beside them: a
 * label will be reworded and a field removed, and a message has to keep making
 * sense afterwards.
 */
function kadence_child_form_submit() {
	$slug  = isset( $_POST['cavo_form'] ) ? sanitize_title( wp_unslash( $_POST['cavo_form'] ) ) : '';
	$forms = kadence_child_forms();
	$back  = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	if ( ! isset( $forms[ $slug ] ) || ! isset( $_POST['cavo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cavo_nonce'] ) ), 'cavo_form_' . $slug ) ) {
		kadence_child_form_back( $back, 'no', array() );
	}

	// Cheapest first: a field the eye cannot see, then the time it took.
	if ( '' !== trim( (string) ( isset( $_POST['cavo_website'] ) ? wp_unslash( $_POST['cavo_website'] ) : '' ) ) ) {
		kadence_child_form_back( $back, 'no', array() );
	}

	$opened = isset( $_POST['cavo_opened'] ) ? (int) $_POST['cavo_opened'] : 0;

	if ( $opened && ( time() - $opened ) < 3 ) {
		kadence_child_form_back( $back, 'no', array() );
	}

	$definition = kadence_child_form_definition( $slug );
	$answers    = array();
	$typed      = array();
	$sender     = '';
	$name       = '';
	$seen       = array();

	foreach ( $definition as $index => $field ) {
		$label = isset( $field['label'] ) ? trim( (string) $field['label'] ) : '';

		if ( '' === $label ) {
			continue;
		}

		$post_key = 'field_' . (int) $index;
		$value    = isset( $_POST[ $post_key ] ) ? wp_unslash( $_POST[ $post_key ] ) : '';
		$value    = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );

		$typed[ $post_key ] = $value;

		if ( ! empty( $field['required'] ) && '' === $value ) {
			kadence_child_form_back( $back, 'no', $typed );
		}

		// Two fields that share a label are told apart before either is stored.
		$key = sanitize_title( $label );
		$key = isset( $seen[ $key ] ) ? $key . '-' . ( ++$seen[ $key ] ) : $key;

		if ( ! isset( $seen[ $key ] ) ) {
			$seen[ $key ] = 1;
		}

		if ( '' === $sender && 'email' === $field['type'] && is_email( $value ) ) {
			$sender = $value;
		}

		if ( '' === $name && 'text' === $field['type'] && '' !== $value ) {
			$name = $value;
		}

		$answers[] = array(
			'key'   => $key,
			'label' => $label,
			'type'  => (string) $field['type'],
			'value' => $value,
		);
	}

	$token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

	if ( ! kadence_child_form_captcha_passed( $slug, $token ) ) {
		kadence_child_form_back( $back, 'no', $typed );
	}

	// Store first.
	$message = wp_insert_post(
		array(
			'post_type'   => 'cavo_message',
			'post_status' => 'publish',
			'post_title'  => '' !== $name ? $name : ( '' !== $sender ? $sender : __( 'No name', 'kadence-child' ) ),
		),
		true
	);

	if ( is_wp_error( $message ) ) {
		kadence_child_form_back( $back, 'no', $typed );
	}

	// What the list needs is worked out now, not looked up later against a form
	// that has since changed.
	update_post_meta( $message, 'cavo_form_slug', $slug );
	update_post_meta( $message, 'cavo_form_name', $forms[ $slug ] );
	update_post_meta( $message, 'cavo_sender', $sender );
	update_post_meta( $message, 'cavo_answers', wp_json_encode( $answers ) );
	update_post_meta( $message, 'cavo_unread', 1 );

	// Mail second.
	kadence_child_form_mail( $slug, $forms[ $slug ], $sender, $name, $answers, $message );

	kadence_child_form_back( $back, 'yes', array() );
}
add_action( 'admin_post_nopriv_cavo_form', 'kadence_child_form_submit' );
add_action( 'admin_post_cavo_form', 'kadence_child_form_submit' );

/**
 * Post, then redirect, then render — and where it did not go through, what they
 * typed comes back with the page.
 *
 * @param string $back   Where they were.
 * @param string $result Whether it went through.
 * @param array  $typed  What they typed, where it did not.
 */
function kadence_child_form_back( $back, $result, $typed ) {
	$args = array( 'sent' => $result );

	if ( ! empty( $typed ) ) {
		$token = wp_generate_password( 12, false );

		set_transient( 'cavo_typed_' . $token, $typed, 10 * MINUTE_IN_SECONDS );

		$args['typed'] = $token;
	}

	wp_safe_redirect( add_query_arg( $args, remove_query_arg( array( 'sent', 'typed' ), $back ) ) );
	exit;
}

/**
 * The two emails. They are not one email with different words.
 *
 * @param string $slug    The form's slug.
 * @param string $form    The form's name.
 * @param string $sender  Whoever wrote them, where they gave an address.
 * @param string $name    What they called themselves, where they said.
 * @param array  $answers What they wrote.
 * @param int    $message The record already stored.
 */
function kadence_child_form_mail( $slug, $form, $sender, $name, $answers, $message ) {
	$domain = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );

	// From is the domain. The sender's own address goes in Reply-To, where it
	// does not stop the mail arriving at all.
	$headers = array( sprintf( 'From: %s <no-reply@%s>', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $domain ) );

	if ( '' !== $sender ) {
		$headers[] = 'Reply-To: ' . $sender;
	}

	$written = '';

	foreach ( $answers as $answer ) {
		$written .= $answer['label'] . ': ' . $answer['value'] . "\r\n";
	}

	$team = kadence_child_form_settings( $slug, 'team' );
	$to   = array();

	foreach ( (array) ( isset( $team['to'] ) ? $team['to'] : array() ) as $row ) {
		if ( ! empty( $row['email'] ) && is_email( $row['email'] ) ) {
			$to[] = $row['email'];
		}
	}

	$subject = isset( $team['subject'] ) ? trim( (string) $team['subject'] ) : '';
	$subject = '' !== $subject ? $subject : sprintf( '%1$s — %2$s', $form, '' !== $name ? $name : $sender );
	$body    = isset( $team['body'] ) ? trim( (string) $team['body'] ) : '';

	update_post_meta(
		$message,
		'cavo_team_mail',
		kadence_child_send(
			$to,
			kadence_child_form_tokens( $subject, $form, $name, $sender ),
			( '' !== $body ? kadence_child_form_tokens( $body, $form, $name, $sender ) . "\r\n\r\n" : '' ) . $written,
			$headers
		)
	);

	$client = kadence_child_form_settings( $slug, 'client' );

	if ( '' === $sender || empty( $client['copy'] ) ) {
		update_post_meta( $message, 'cavo_client_mail', 'nothing' );

		return;
	}

	$subject = isset( $client['subject'] ) ? trim( (string) $client['subject'] ) : '';
	$subject = '' !== $subject ? $subject : sprintf( '%s — we have your message', $form );
	$body    = isset( $client['body'] ) ? trim( (string) $client['body'] ) : '';

	update_post_meta(
		$message,
		'cavo_client_mail',
		kadence_child_send(
			$sender,
			kadence_child_form_tokens( $subject, $form, $name, $sender ),
			kadence_child_form_tokens( $body, $form, $name, $sender ) . ( empty( $client['answers'] ) ? '' : "\r\n\r\n" . $written ),
			$headers
		)
	);
}

/**
 * The few words a subject or a body may stand in for.
 *
 * @param string $said   What was written.
 * @param string $form   The form's name.
 * @param string $name   What they called themselves.
 * @param string $sender Their address.
 * @return string
 */
function kadence_child_form_tokens( $said, $form, $name, $sender ) {
	return strtr(
		$said,
		array(
			'{form}'  => $form,
			'{name}'  => '' !== $name ? $name : $sender,
			'{email}' => $sender,
		)
	);
}

/**
 * The inbox is a list of messages, not a list of posts.
 *
 * @param array $columns What the screen would show.
 * @return array
 */
function kadence_child_inbox_columns( $columns ) {
	$forms = kadence_child_forms();

	$mine = array(
		'cb'          => isset( $columns['cb'] ) ? $columns['cb'] : '',
		'cavo_unread' => '<span class="screen-reader-text">' . esc_html__( 'Unread', 'kadence-child' ) . '</span>',
		'title'       => esc_html__( 'From', 'kadence-child' ),
	);

	// A second form makes two kinds of message sit in one list with no way to
	// tell them apart.
	if ( count( $forms ) > 1 ) {
		$mine['cavo_form'] = esc_html__( 'Form', 'kadence-child' );
	}

	$mine['cavo_sender'] = esc_html__( 'Reply to', 'kadence-child' );
	$mine['cavo_said']   = esc_html__( 'Said', 'kadence-child' );
	$mine['cavo_mail']   = esc_html__( 'Notified', 'kadence-child' );
	$mine['date']        = esc_html__( 'Received', 'kadence-child' );

	return $mine;
}
add_filter( 'manage_cavo_message_posts_columns', 'kadence_child_inbox_columns' );

/**
 * What each column says.
 *
 * The Said column takes the first answer that is a message, which is a fact
 * about the field rather than a guess about the content.
 *
 * @param string $column  The column.
 * @param int    $post_id The message.
 */
function kadence_child_inbox_column( $column, $post_id ) {
	if ( 'cavo_unread' === $column ) {
		echo get_post_meta( $post_id, 'cavo_unread', true ) ? '<span class="cavo-unread" aria-label="' . esc_attr__( 'Unread', 'kadence-child' ) . '"></span>' : '';

		return;
	}

	if ( 'cavo_form' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'cavo_form_name', true ) );

		return;
	}

	if ( 'cavo_sender' === $column ) {
		$sender = get_post_meta( $post_id, 'cavo_sender', true );

		echo $sender ? '<a href="mailto:' . esc_attr( $sender ) . '">' . esc_html( $sender ) . '</a>' : '—';

		return;
	}

	if ( 'cavo_said' === $column ) {
		foreach ( kadence_child_message_answers( $post_id ) as $answer ) {
			if ( 'textarea' === $answer['type'] && '' !== $answer['value'] ) {
				echo esc_html( wp_trim_words( $answer['value'], 12 ) );

				return;
			}
		}

		echo '—';

		return;
	}

	if ( 'cavo_mail' === $column ) {
		kadence_child_inbox_mail_state( $post_id );
	}
}
add_action( 'manage_cavo_message_posts_custom_column', 'kadence_child_inbox_column', 10, 2 );

/**
 * What became of the two emails, in words rather than in glyphs alone.
 *
 * @param int $post_id The message.
 */
function kadence_child_inbox_mail_state( $post_id ) {
	$says = array(
		'sent'    => array( '✓', esc_html__( 'a mail server accepted it', 'kadence-child' ) ),
		'nosmtp'  => array( '!', esc_html__( 'sent through PHP mail — no SMTP took it, so it may have arrived nowhere', 'kadence-child' ) ),
		'failed'  => array( '✗', esc_html__( 'refused', 'kadence-child' ) ),
		'nothing' => array( '–', esc_html__( 'nothing to send', 'kadence-child' ) ),
	);

	foreach ( array(
		'cavo_team_mail'   => esc_html__( 'Team', 'kadence-child' ),
		'cavo_client_mail' => esc_html__( 'Client', 'kadence-child' ),
	) as $meta => $who ) {
		$state = (string) get_post_meta( $post_id, $meta, true );
		$state = isset( $says[ $state ] ) ? $state : 'nothing';

		printf(
			'<div>%1$s %2$s — %3$s</div>',
			esc_html( $says[ $state ][0] ),
			esc_html( $who ),
			esc_html( $says[ $state ][1] )
		);
	}
}

/**
 * What a message was asked, and what was answered.
 *
 * @param int $post_id The message.
 * @return array
 */
function kadence_child_message_answers( $post_id ) {
	$stored = json_decode( (string) get_post_meta( $post_id, 'cavo_answers', true ), true );

	return is_array( $stored ) ? $stored : array();
}

/**
 * The dot, and how many are still wearing one.
 */
function kadence_child_inbox_css() {
	$screen = get_current_screen();

	if ( ! $screen || false === strpos( $screen->id, 'cavo_message' ) ) {
		return;
	}

	echo '<style>.column-cavo_unread{width:24px}.cavo-unread{display:inline-block;width:8px;height:8px;border-radius:50%;background:#d63638}.cavo-said th{width:180px;text-align:left}</style>';
}
add_action( 'admin_head', 'kadence_child_inbox_css' );

/**
 * How many are unread, beside the menu.
 */
function kadence_child_inbox_count() {
	global $menu;

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

	foreach ( (array) $menu as $index => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=cavo_message' === $item[2] ) {
			$menu[ $index ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . count( $unread ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- the menu is ours to mark.
		}
	}
}
add_action( 'admin_menu', 'kadence_child_inbox_count', 1000 );

/**
 * Reading one is an action on the row, first in it, and Quick Edit goes.
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

	if ( ! get_post_meta( $post->ID, 'cavo_unread', true ) ) {
		return $actions;
	}

	$read = array(
		'cavo_read' => sprintf(
			'<a href="%s">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( array( 'post_type' => 'cavo_message', 'cavo_read' => $post->ID ), admin_url( 'edit.php' ) ), 'cavo_read_' . $post->ID ) ),
			esc_html__( 'Mark as read', 'kadence-child' )
		),
	);

	return $read + $actions;
}
add_filter( 'post_row_actions', 'kadence_child_inbox_actions', 10, 2 );

/**
 * Quick Edit goes from the bulk menu too, or the same panel is only harder to
 * find.
 *
 * @param array $actions What the screen offers.
 * @return array
 */
function kadence_child_inbox_bulk( $actions ) {
	unset( $actions['edit'] );

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
 * A row action changes something on a GET, so it redirects after itself.
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

/**
 * What was said, printed on the message rather than opened for editing.
 */
function kadence_child_message_box() {
	add_meta_box(
		'cavo_message_answers',
		esc_html__( 'Message', 'kadence-child' ),
		'kadence_child_message_render',
		'cavo_message',
		'normal',
		'high'
	);

	remove_meta_box( 'submitdiv', 'cavo_message', 'side' );
	remove_meta_box( 'slugdiv', 'cavo_message', 'normal' );
}
add_action( 'add_meta_boxes_cavo_message', 'kadence_child_message_box' );

/**
 * The message itself.
 *
 * @param \WP_Post $post The message.
 */
function kadence_child_message_render( $post ) {
	$answers = kadence_child_message_answers( $post->ID );

	if ( empty( $answers ) ) {
		echo '<p>' . esc_html__( 'Nothing was stored with this message.', 'kadence-child' ) . '</p>';

		return;
	}

	echo '<table class="widefat striped"><tbody>';

	foreach ( $answers as $answer ) {
		printf(
			'<tr class="cavo-said"><th scope="row">%1$s</th><td>%2$s</td></tr>',
			esc_html( $answer['label'] ),
			nl2br( esc_html( $answer['value'] ) )
		);
	}

	echo '</tbody></table>';

	echo '<p>';
	kadence_child_inbox_mail_state( $post->ID );
	echo '</p>';
}

/**
 * Opening a message is one of the three ways the dot clears.
 *
 * @param \WP_Post $post The message.
 */
function kadence_child_message_opened( $post ) {
	if ( 'cavo_message' === $post->post_type ) {
		delete_post_meta( $post->ID, 'cavo_unread' );
	}
}
add_action( 'edit_form_top', 'kadence_child_message_opened' );

