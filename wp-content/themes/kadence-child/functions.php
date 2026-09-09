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
			'labels'              => array(
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
			// An event is drawn by the sections that list it and is read nowhere
			// else, so it is given no address of its own. Written, it appears in
			// those lists and in no other place.
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-calendar-alt',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'show_in_rest'        => true,
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
			// The terms sort the lists; they are not a place to be read either.
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'kadence_child_event_post_type' );

/**
 * A post is written once, not summarised beside itself.
 *
 * The words the client types are the description, and a card that shows less of
 * them shows the beginning of them. A second box asking for the same thing in
 * fewer words is a second thing to keep in step with the first.
 */
function kadence_child_one_description() {
	remove_post_type_support( 'post', 'excerpt' );
}
add_action( 'init', 'kadence_child_one_description', 20 );

/**
 * What an event carries that WordPress has no field for.
 *
 * When it is, from when until when, and the kind of music. When a post is
 * published is when it appeared on the site, which is not when the night is;
 * an event set for next week would have to be held back until then to say so.
 * Registered in code so the fields travel with the theme rather than being
 * imported into each environment, and only where the plugin that renders them
 * is active.
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
					'key'                     => 'field_event_date',
					'label'                   => esc_html__( 'Date', 'kadence-child' ),
					'name'                    => 'event_date',
					'type'                    => 'date_picker',
					'display_format'          => 'd M Y',
					'return_format'           => 'd M Y',
					'first_day'               => 1,
					// An empty date opens on today, filled into the box and no
					// further: it is saved when the post is, like anything else
					// typed into the screen.
					'default_to_current_date' => 1,
				),
				array(
					'key'            => 'field_event_time_from',
					'label'          => esc_html__( 'Time from', 'kadence-child' ),
					'name'           => 'event_time_from',
					'type'           => 'time_picker',
					'display_format' => 'h : i A',
					'return_format'  => 'h : i A',
				),
				array(
					'key'            => 'field_event_time_to',
					'label'          => esc_html__( 'Time to', 'kadence-child' ),
					'name'           => 'event_time_to',
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
		'private-event' => esc_html__( 'Private event', 'kadence-child' ),
		'contact'       => esc_html__( 'Contact', 'kadence-child' ),
		'newsletter'    => esc_html__( 'Newsletter', 'kadence-child' ),
	);
}

/**
 * Whether a form asks to be written to rather than written back to.
 *
 * A sign-up asks one thing and files no message, so it carries neither a field
 * list nor anybody to notify. It is still one of the forms: it has a tab, its
 * words are the client's, and the captcha in front of it is the domain's.
 *
 * @param string $slug The form's slug.
 * @return bool
 */
function kadence_child_form_is_signup( $slug ) {
	return 'newsletter' === $slug;
}

/**
 * The forms that ask questions and file the answers.
 *
 * What a section can be pointed at: a widget that draws a list of questions has
 * nothing to draw for a form that asks one.
 *
 * @return array
 */
function kadence_child_enquiry_forms() {
	$kept = array();

	foreach ( kadence_child_forms() as $slug => $name ) {
		if ( ! kadence_child_form_is_signup( $slug ) ) {
			$kept[ $slug ] = $name;
		}
	}

	return $kept;
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

	return (array) get_field( 'form_fields_' . $slug, 'option' );
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
		'subscribers' => array(
			'label' => esc_html__( 'Subscribers', 'kadence-child' ),
			'url'   => kadence_child_form_url( 'cavo-subscribers' ),
			'page'  => 'cavo-subscribers',
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
		// The inbox is the post type's own list, and the subscriber list is a
		// list of rows rather than a page of fields. Neither is an options page.
		if ( in_array( $tab['page'], array( 'edit-cavo_message', 'cavo-subscribers' ), true ) ) {
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
	$editor = array();

	foreach ( $forms as $slug => $name ) {
		$editor[] = array(
			'key'   => 'field_cavo_editor_tab_' . $slug,
			/* translators: %s: the form's name. */
			'label' => sprintf( esc_html__( '%s form', 'kadence-child' ), $name ),
			'type'  => 'tab',
		);

		$editor[] = kadence_child_form_is_signup( $slug )
			? kadence_child_signup_field_group( $slug )
			: kadence_child_form_fields_repeater( $slug );
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_cavo_editor',
			'title'    => esc_html__( 'Form', 'kadence-child' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-editor' ) ) ),
			'fields'   => $editor,
		)
	);

	acf_add_local_field_group( kadence_child_form_settings_group( $forms ) );

	acf_add_local_field_group(
		array(
			'key'      => 'group_cavo_recaptcha',
			'title'    => esc_html__( 'reCAPTCHA', 'kadence-child' ),
			'location' => array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'cavo-form-recaptcha' ) ) ),
			'fields'   => array(
				array(
					'key'        => 'field_cavo_v2',
					'label'      => esc_html__( 'reCAPTCHA v2', 'kadence-child' ),
					'name'       => 'recaptcha_v2',
					'type'       => 'group',
					'sub_fields' => array(
						array( 'key' => 'field_cavo_v2_site', 'label' => esc_html__( 'Site key', 'kadence-child' ), 'name' => 'site', 'type' => 'text' ),
						array( 'key' => 'field_cavo_v2_secret', 'label' => esc_html__( 'Secret key', 'kadence-child' ), 'name' => 'secret', 'type' => 'text' ),
					),
				),
				array(
					'key'        => 'field_cavo_v3',
					'label'      => esc_html__( 'reCAPTCHA v3', 'kadence-child' ),
					'name'       => 'recaptcha_v3',
					'type'       => 'group',
					'sub_fields' => array(
						array( 'key' => 'field_cavo_v3_site', 'label' => esc_html__( 'Site key', 'kadence-child' ), 'name' => 'site', 'type' => 'text' ),
						array( 'key' => 'field_cavo_v3_secret', 'label' => esc_html__( 'Secret key', 'kadence-child' ), 'name' => 'secret', 'type' => 'text' ),
						array(
							'key'           => 'field_cavo_v3_threshold',
							'label'         => esc_html__( 'Score threshold', 'kadence-child' ),
							'name'          => 'threshold',
							'type'          => 'number',
							'min'           => 0,
							'max'           => 1,
							'step'          => 0.1,
							'default_value' => 0.5,
						),
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'kadence_child_form_field_groups', 20 );

/**
 * The fields one form asks for.
 *
 * @param string $key The form's slug: its fields are stored under it.
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
					'textarea' => esc_html__( 'Text area', 'kadence-child' ),
					'email'    => esc_html__( 'Email', 'kadence-child' ),
					'tel'      => esc_html__( 'Tel', 'kadence-child' ),
					'select'   => esc_html__( 'Select', 'kadence-child' ),
					'date'     => esc_html__( 'Date', 'kadence-child' ),
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
				'label'             => esc_html__( 'Options', 'kadence-child' ),
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
 * @return array
 */
function kadence_child_form_settings_group( $forms ) {
	$fields = array();

	foreach ( $forms as $slug => $name ) {
		$fields[] = array(
			'key'   => 'field_cavo_tab_' . $slug,
			/* translators: %s: the form's name. */
			'label' => sprintf( esc_html__( '%s form', 'kadence-child' ), $name ),
			'type'  => 'tab',
		);

		$fields = array_merge(
			$fields,
			kadence_child_form_is_signup( $slug )
				? kadence_child_signup_settings_fields( $slug )
				: kadence_child_form_settings_fields( $slug )
		);
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
 * @param string $key The form's slug: its settings are stored under it.
 * @return array
 */

/**
 * Who hears about a message, for whichever form filed it.
 *
 * Both emails live here, in two groups named after who receives them. The team
 * has a switch of its own: a form nobody is watching should be able to say so
 * without its addresses being deleted to mean it.
 *
 * @param string $key The form's slug: its settings are stored under it.
 * @return array
 */
function kadence_child_form_notification_fields( $key ) {
	return array(
		array(
			'key'          => 'field_cavo_team_group_' . $key,
			'label'        => esc_html__( 'Notifications › Team', 'kadence-child' ),
			'name'         => 'team_' . $key,
			'type'         => 'group',
			'sub_fields'   => array(
				array(
					'key'           => 'field_cavo_team_send_' . $key,
					'label'         => esc_html__( 'Send notifications', 'kadence-child' ),
					'name'          => 'send',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
				),
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
	);
}

function kadence_child_form_settings_fields( $key ) {
	return array_merge(
		kadence_child_form_notification_fields( $key ),
		array(
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
		)
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

	return (array) get_field( $group . '_' . $slug, 'option' );
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

	$keys = kadence_child_captcha_keys( $version );

	return ( '' !== $keys['site'] && '' !== $keys['secret'] ) ? $version : 'off';
}

/**
 * One version's pair of keys, and what v3 answers against.
 *
 * A version is a section of its own on the reCAPTCHA screen, so it is read as
 * one thing rather than as loose keys sharing a prefix.
 *
 * @param string $version `v2` or `v3`.
 * @return array Site, secret and threshold.
 */
function kadence_child_captcha_keys( $version ) {
	$keys = function_exists( 'get_field' ) ? (array) get_field( 'recaptcha_' . $version, 'option' ) : array();

	return array(
		'site'      => isset( $keys['site'] ) ? trim( (string) $keys['site'] ) : '',
		'secret'    => isset( $keys['secret'] ) ? trim( (string) $keys['secret'] ) : '',
		'threshold' => isset( $keys['threshold'] ) ? (float) $keys['threshold'] : 0,
	);
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

	$keys = kadence_child_captcha_keys( $version );
	$site = $keys['site'];

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
				'secret'   => kadence_child_captcha_keys( $version )['secret'],
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

	$threshold = kadence_child_captcha_keys( 'v3' )['threshold'];
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

	// A switch that is off is not an address list left empty: the addresses stay
	// where they were written, and nothing goes to them until it is on again.
	if ( ! empty( $team['send'] ) ) {
		foreach ( (array) ( isset( $team['to'] ) ? $team['to'] : array() ) as $row ) {
			if ( ! empty( $row['email'] ) && is_email( $row['email'] ) ) {
				$to[] = $row['email'];
			}
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


/**
 * Where the addresses live.
 *
 * A message is read once and done with; an address carries a state that keeps
 * changing, so it is stored where it can be found by the address itself, moved
 * between states, and removed on the word of whoever owns it.
 */
function kadence_child_subscriber_post_type() {
	register_post_type(
		'cavo_subscriber',
		array(
			'labels'              => array(
				'name'          => esc_html__( 'Subscribers', 'kadence-child' ),
				'singular_name' => esc_html__( 'Subscriber', 'kadence-child' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array( 'title' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'kadence_child_subscriber_post_type' );

/**
 * The three states an address can stand in.
 *
 * @return array
 */
function kadence_child_subscriber_states() {
	return array(
		'pending'      => esc_html__( 'Waiting to confirm', 'kadence-child' ),
		'confirmed'    => esc_html__( 'Confirmed', 'kadence-child' ),
		'unsubscribed' => esc_html__( 'Unsubscribed', 'kadence-child' ),
	);
}

/**
 * An address as it is compared: trimmed and lowered.
 *
 * @param string $email The address.
 * @return string
 */
function kadence_child_subscriber_key( $email ) {
	return strtolower( trim( (string) $email ) );
}

/**
 * The row for one address, or 0.
 *
 * @param string $email The address.
 * @return int
 */
function kadence_child_subscriber_find( $email ) {
	$found = get_posts(
		array(
			'post_type'      => 'cavo_subscriber',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => 'cavo_email', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- the list is looked up by address and nothing else.
			'meta_value'     => kadence_child_subscriber_key( $email ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
		)
	);

	return empty( $found ) ? 0 : (int) $found[0];
}

/**
 * The row a token names, or 0.
 *
 * @param string $token The secret from a link.
 * @return int
 */
function kadence_child_subscriber_by_token( $token ) {
	$token = trim( (string) $token );

	if ( '' === $token ) {
		return 0;
	}

	$found = get_posts(
		array(
			'post_type'      => 'cavo_subscriber',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_key'       => 'cavo_token', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- a link carries the token and nothing else.
			'meta_value'     => $token, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
		)
	);

	return empty( $found ) ? 0 : (int) $found[0];
}

/**
 * Put an address on the list, or bring one back that had left.
 *
 * Somebody signing up again after unsubscribing is asking to be back on, so the
 * row is moved rather than refused, and keeps the day it first asked.
 *
 * @param string $email  The address.
 * @param string $source Which form it came through.
 * @param string $status Where it should stand.
 * @return int|WP_Error The row, or why not.
 */
function kadence_child_subscriber_add( $email, $source = '', $status = 'confirmed' ) {
	$email = trim( (string) $email );

	if ( ! is_email( $email ) ) {
		return new WP_Error( 'cavo_bad_email', esc_html__( 'That address does not look right.', 'kadence-child' ) );
	}

	$states = kadence_child_subscriber_states();
	$status = isset( $states[ $status ] ) ? $status : 'confirmed';
	$id     = kadence_child_subscriber_find( $email );

	if ( 0 !== $id ) {
		if ( 'confirmed' !== get_post_meta( $id, 'cavo_status', true ) ) {
			update_post_meta( $id, 'cavo_status', $status );
		}

		return $id;
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'cavo_subscriber',
			'post_status' => 'publish',
			'post_title'  => $email,
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		return $id;
	}

	update_post_meta( $id, 'cavo_email', kadence_child_subscriber_key( $email ) );
	update_post_meta( $id, 'cavo_token', wp_generate_password( 32, false ) );
	update_post_meta( $id, 'cavo_source', sanitize_key( $source ) );
	update_post_meta( $id, 'cavo_status', $status );

	return $id;
}

/**
 * One row's state.
 *
 * @param int $id The row.
 * @return string
 */
function kadence_child_subscriber_status( $id ) {
	$status = (string) get_post_meta( $id, 'cavo_status', true );
	$states = kadence_child_subscriber_states();

	return isset( $states[ $status ] ) ? $status : 'pending';
}

/**
 * Move one row to a state.
 *
 * @param int    $id     The row.
 * @param string $status Where it should stand.
 * @return bool
 */
function kadence_child_subscriber_set_status( $id, $status ) {
	$states = kadence_child_subscriber_states();

	if ( ! isset( $states[ $status ] ) || 'cavo_subscriber' !== get_post_type( $id ) ) {
		return false;
	}

	update_post_meta( $id, 'cavo_status', $status );

	return true;
}

/**
 * How many addresses stand in one state.
 *
 * @param string $status Which state, or '' for the whole list.
 * @return int
 */
function kadence_child_subscriber_count( $status = '' ) {
	$query = array(
		'post_type'      => 'cavo_subscriber',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
	);

	if ( '' !== $status ) {
		$query['meta_key']   = 'cavo_status'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- counted by state.
		$query['meta_value'] = $status; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
	}

	$found = new WP_Query( $query );

	return (int) $found->found_posts;
}

/**
 * The two cheapest defences, printed once for every form that wants them.
 *
 * A field the eye cannot see and a hand will not fill, then the time between
 * the page opening and the press. Written here rather than in each form: a
 * trap a form can be built without is a trap that is sooner or later missing.
 */
function kadence_child_form_trap() {
	printf(
		'<input type="hidden" name="cavo_opened" value="%d" />',
		absint( time() )
	);

	printf(
		'<div class="cavo-trap" aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">' .
		'<label>%1$s<input type="text" name="cavo_website" value="" tabindex="-1" autocomplete="off" /></label></div>',
		esc_html__( 'Leave this field empty', 'kadence-child' )
	);
}

/**
 * Whether a POST was made by something other than a reader.
 *
 * @param array $post The posted values.
 * @return bool
 */
function kadence_child_form_trapped( $post ) {
	if ( '' !== trim( (string) ( isset( $post['cavo_website'] ) ? $post['cavo_website'] : '' ) ) ) {
		return true;
	}

	$opened = isset( $post['cavo_opened'] ) ? (int) $post['cavo_opened'] : 0;

	return $opened > 0 && ( time() - $opened ) < 3;
}

/**
 * What the list does, as opposed to who is on it.
 *
 * Recipients are never settled here — the recipients are the list. Sending
 * starts off: a site mid-build must not answer its first publish by mailing
 * everybody who signed up while it was being tested.
 *
 * @return array
 */
function kadence_child_subscription_defaults() {
	return array(
		'sending'    => 0,
		'confirms'   => 0,
		'post_types' => array( 'post' ),
		'subject'    => '{title}',
	);
}

/**
 * The subscription's settings.
 *
 * @return array
 */
function kadence_child_subscription_settings() {
	$stored = get_option( 'cavo_subscription', array() );

	return array_merge( kadence_child_subscription_defaults(), is_array( $stored ) ? $stored : array() );
}

/**
 * Write them back.
 *
 * @param array $settings What to keep.
 */
function kadence_child_subscription_save( $settings ) {
	update_option( 'cavo_subscription', array_merge( kadence_child_subscription_defaults(), (array) $settings ) );
}

/**
 * The hidden fields a sign-up form prints.
 *
 * @param string $slug Which form is asking.
 */
function kadence_child_subscribe_fields( $slug = 'newsletter' ) {
	wp_nonce_field( 'cavo_subscribe', 'cavo_nonce' );

	printf( '<input type="hidden" name="action" value="cavo_subscribe" />' );
	printf( '<input type="hidden" name="cavo_form" value="%s" />', esc_attr( sanitize_key( $slug ) ) );

	kadence_child_form_trap();
}

/**
 * Store the answer, redirect past the POST, and stop.
 *
 * The address is handed back through the store rather than the address bar:
 * what somebody typed is theirs, and a query string is written down by every
 * proxy between here and them.
 *
 * @param string $back  Where the reader was.
 * @param string $state What to tell them.
 * @param string $email What to put back in the field.
 */
function kadence_child_subscribe_back( $back, $state, $email = '' ) {
	$key = wp_generate_password( 12, false );

	set_transient(
		'cavo_signup_' . $key,
		array(
			'state' => $state,
			'email' => $email,
		),
		5 * MINUTE_IN_SECONDS
	);

	wp_safe_redirect( add_query_arg( 'cavo_signup', $key, $back ) );
	exit;
}

/**
 * Take the sign-up, store the address, and send the reader back.
 */
function kadence_child_subscribe_submit() {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = wp_validate_redirect( $back, home_url( '/' ) );

	if ( ! isset( $_POST['cavo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cavo_nonce'] ) ), 'cavo_subscribe' ) ) {
		kadence_child_subscribe_back( $back, 'expired' );
	}

	// A robot learns nothing from being told the same thing a reader is told.
	if ( kadence_child_form_trapped( wp_unslash( $_POST ) ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read as presence and elapsed time only.
		kadence_child_subscribe_back( $back, 'ok' );
	}

	$slug = isset( $_POST['cavo_form'] ) ? sanitize_key( wp_unslash( $_POST['cavo_form'] ) ) : 'newsletter';

	// Last of the three, and the only one the reader can see.
	$token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

	if ( ! kadence_child_form_captcha_passed( $slug, $token ) ) {
		kadence_child_subscribe_back( $back, 'expired' );
	}

	$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';

	if ( ! is_email( $email ) ) {
		kadence_child_subscribe_back( $back, 'invalid', $email );
	}

	$settings = kadence_child_subscription_settings();
	$status   = empty( $settings['confirms'] ) ? 'confirmed' : 'pending';
	$added    = kadence_child_subscriber_add( $email, $slug, $status );

	if ( is_wp_error( $added ) ) {
		kadence_child_subscribe_back( $back, 'invalid', $email );
	}

	// The row is the record and the email a courtesy on top of it, so these
	// happen after the address is stored and never instead of storing it.
	if ( 'pending' === $status && 'pending' === kadence_child_subscriber_status( $added ) ) {
		kadence_child_subscribe_confirm_mail( $added );
	}

	kadence_child_subscribe_notify( $slug, $email );

	kadence_child_subscribe_back( $back, 'pending' === $status ? 'confirm' : 'ok' );
}
add_action( 'admin_post_nopriv_cavo_subscribe', 'kadence_child_subscribe_submit' );
add_action( 'admin_post_cavo_subscribe', 'kadence_child_subscribe_submit' );

/**
 * The stored answer for this request.
 *
 * @return array
 */
function kadence_child_subscribe_result() {
	if ( ! isset( $_GET['cavo_signup'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a key naming this reader's own stored answer.
		return array();
	}

	$key    = sanitize_key( wp_unslash( $_GET['cavo_signup'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- as above.
	$stored = get_transient( 'cavo_signup_' . $key );

	return is_array( $stored ) ? $stored : array();
}

/**
 * Follow a confirm or unsubscribe link.
 *
 * Whether the token was known or not, the same page comes back: a link that
 * says which addresses exist is a way of asking.
 */
function kadence_child_subscribe_follow() {
	$links = array(
		'cavo_confirm' => 'confirmed',
		'cavo_unsub'   => 'unsubscribed',
	);

	foreach ( $links as $arg => $status ) {
		if ( ! isset( $_GET[ $arg ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the token in the link is the proof.
			continue;
		}

		$token = sanitize_text_field( wp_unslash( $_GET[ $arg ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- as above.
		$id    = kadence_child_subscriber_by_token( $token );

		if ( 0 !== $id ) {
			kadence_child_subscriber_set_status( $id, $status );
		}

		kadence_child_subscribe_back( home_url( '/' ), 'confirmed' === $status ? 'welcome' : 'gone' );
	}
}
add_action( 'init', 'kadence_child_subscribe_follow' );

/**
 * The Subscribers screen, hung under the inbox like the rest.
 *
 * It is a list of rows rather than a page of fields, so it is a screen of its
 * own rather than one more options page.
 */
function kadence_child_subscribers_page() {
	add_submenu_page(
		'edit.php?post_type=cavo_message',
		esc_html__( 'Subscribers', 'kadence-child' ),
		esc_html__( 'Subscribers', 'kadence-child' ),
		'manage_options',
		'cavo-subscribers',
		'kadence_child_subscribers_render'
	);
}
add_action( 'admin_menu', 'kadence_child_subscribers_page' );

/**
 * The sub-tabs of the Subscribers screen.
 *
 * @return array
 */
function kadence_child_subscribers_views() {
	return array(
		'list'       => esc_html__( 'List', 'kadence-child' ),
		'broadcasts' => esc_html__( 'Broadcasts', 'kadence-child' ),
		'settings'   => esc_html__( 'Settings', 'kadence-child' ),
	);
}

/**
 * Which sub-tab is open.
 *
 * @return string
 */
function kadence_child_subscribers_view() {
	$asked = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- which sub-tab to draw.
	$views = kadence_child_subscribers_views();

	return isset( $views[ $asked ] ) ? $asked : 'list';
}

/**
 * Where a sub-tab lives.
 *
 * @param string $view Which one.
 * @return string
 */
function kadence_child_subscribers_url( $view = '' ) {
	$url = kadence_child_form_url( 'cavo-subscribers' );

	return '' === $view ? $url : add_query_arg( 'view', $view, $url );
}

/**
 * A row action changes something on a GET, so it redirects after itself.
 *
 * This runs before the screen is drawn: a redirect decided halfway down a page
 * has nowhere to go.
 */
function kadence_child_subscribers_act() {
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- which screen was asked for.

	if ( 'cavo-subscribers' !== $page || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$what = isset( $_GET['cavo_do'] ) ? sanitize_key( wp_unslash( $_GET['cavo_do'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the nonce is checked below.
	$id   = isset( $_GET['cavo_who'] ) ? absint( wp_unslash( $_GET['cavo_who'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the nonce is checked below.

	if ( '' !== $what && 0 !== $id ) {
		check_admin_referer( 'cavo_subscriber_' . $id );

		if ( 'remove' === $what ) {
			wp_delete_post( $id, true );
		} elseif ( 'unsubscribe' === $what ) {
			kadence_child_subscriber_set_status( $id, 'unsubscribed' );
		}

		wp_safe_redirect( add_query_arg( 'done', $what, kadence_child_subscribers_url( 'list' ) ) );
		exit;
	}

	$bulk = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the nonce is checked below.
	$bulk = '-1' === $bulk || '' === $bulk ? ( isset( $_REQUEST['action2'] ) ? sanitize_key( wp_unslash( $_REQUEST['action2'] ) ) : '' ) : $bulk; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- as above.

	if ( in_array( $bulk, array( 'remove', 'unsubscribe' ), true ) ) {
		check_admin_referer( 'bulk-subscribers' );

		$chosen = isset( $_REQUEST['cavo_who'] ) ? array_map( 'absint', (array) wp_unslash( $_REQUEST['cavo_who'] ) ) : array();

		foreach ( $chosen as $one ) {
			if ( 0 === $one ) {
				continue;
			}

			if ( 'remove' === $bulk ) {
				wp_delete_post( $one, true );
			} else {
				kadence_child_subscriber_set_status( $one, 'unsubscribed' );
			}
		}

		wp_safe_redirect( add_query_arg( 'done', $bulk, kadence_child_subscribers_url( 'list' ) ) );
		exit;
	}

	if ( isset( $_POST['cavo_add_subscriber'] ) ) {
		check_admin_referer( 'cavo_add_subscriber' );

		$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
		$added = kadence_child_subscriber_add( $email, '', 'confirmed' );

		wp_safe_redirect( add_query_arg( 'done', is_wp_error( $added ) ? 'invalid' : 'added', kadence_child_subscribers_url( 'list' ) ) );
		exit;
	}

	if ( isset( $_POST['cavo_subscription_settings'] ) ) {
		check_admin_referer( 'cavo_subscription_settings' );

		kadence_child_subscription_save(
			array(
				'sending'    => isset( $_POST['sending'] ) ? 1 : 0,
				'confirms'   => isset( $_POST['confirms'] ) ? 1 : 0,
				'post_types' => isset( $_POST['post_types'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['post_types'] ) ) : array(),
				'subject'    => isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '',
			)
		);

		wp_safe_redirect( add_query_arg( 'done', 'saved', kadence_child_subscribers_url( 'settings' ) ) );
		exit;
	}
}
add_action( 'admin_init', 'kadence_child_subscribers_act' );

/**
 * Say what just happened.
 */
function kadence_child_subscribers_notice() {
	$what = isset( $_GET['done'] ) ? sanitize_key( wp_unslash( $_GET['done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- printing what just happened.

	$words = array(
		'remove'      => esc_html__( 'Removed from the list.', 'kadence-child' ),
		'unsubscribe' => esc_html__( 'Marked unsubscribed.', 'kadence-child' ),
		'added'       => esc_html__( 'Added to the list.', 'kadence-child' ),
		'invalid'     => esc_html__( 'That address does not look right.', 'kadence-child' ),
		'saved'       => esc_html__( 'Saved.', 'kadence-child' ),
	);

	if ( ! isset( $words[ $what ] ) ) {
		return;
	}

	printf(
		'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
		'invalid' === $what ? 'error' : 'success',
		esc_html( $words[ $what ] )
	);
}

/**
 * Draw the screen.
 */
function kadence_child_subscribers_render() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You cannot see this screen.', 'kadence-child' ) );
	}

	$here  = kadence_child_subscribers_view();
	$views = kadence_child_subscribers_views();
	$last  = array_key_last( $views );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Subscribers', 'kadence-child' ); ?></h1>
		<?php kadence_child_subscribers_notice(); ?>

		<ul class="subsubsub">
			<?php foreach ( $views as $slug => $label ) : ?>
				<li>
					<a href="<?php echo esc_url( kadence_child_subscribers_url( $slug ) ); ?>"<?php echo $slug === $here ? ' class="current"' : ''; ?>>
						<?php echo esc_html( $label ); ?>
					</a><?php echo $slug === $last ? '' : ' |'; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<div style="clear:both"></div>

		<?php
		if ( 'settings' === $here ) {
			kadence_child_subscribers_settings_screen();
		} elseif ( 'broadcasts' === $here ) {
			kadence_child_subscribers_broadcasts_screen();
		} else {
			kadence_child_subscribers_list_screen();
		}
		?>
	</div>
	<?php
}

/**
 * What the list does.
 */
function kadence_child_subscribers_settings_screen() {
	$settings = kadence_child_subscription_settings();
	$types    = kadence_child_news_post_types();
	?>
	<form method="post">
		<?php wp_nonce_field( 'cavo_subscription_settings' ); ?>
		<input type="hidden" name="cavo_subscription_settings" value="1" />

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><?php esc_html_e( 'Sending', 'kadence-child' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="sending" value="1" <?php checked( ! empty( $settings['sending'] ) ); ?> />
						<?php esc_html_e( 'Email the list when something is published', 'kadence-child' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Signing up', 'kadence-child' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="confirms" value="1" <?php checked( ! empty( $settings['confirms'] ) ); ?> />
						<?php esc_html_e( 'Ask the address to confirm itself before it counts', 'kadence-child' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Off, anybody can put anybody else on the list.', 'kadence-child' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'What counts as news', 'kadence-child' ); ?></th>
				<td>
					<?php foreach ( $types as $type ) : ?>
						<label style="display:block">
							<input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $type->name ); ?>"
								<?php checked( in_array( $type->name, (array) $settings['post_types'], true ) ); ?> />
							<?php echo esc_html( $type->labels->name ); ?>
						</label>
					<?php endforeach; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cavo-subject"><?php esc_html_e( 'Subject', 'kadence-child' ); ?></label></th>
				<td>
					<input type="text" id="cavo-subject" name="subject" class="regular-text"
						value="<?php echo esc_attr( $settings['subject'] ); ?>" />
					<p class="description"><?php esc_html_e( '{title} stands for what was published.', 'kadence-child' ); ?></p>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>

	<?php if ( ! empty( $settings['sending'] ) ) : ?>
		<p class="description" style="max-width:40em">
			<?php
			esc_html_e( 'A publish hands the list to WordPress’s scheduler and it is worked through 50 at a time. WordPress runs that scheduler when somebody visits the site, so on a quiet night the last batch waits for the first visitor. A real cron on the server calling wp-cron.php every few minutes settles it.', 'kadence-child' );
			?>
		</p>
	<?php endif; ?>
	<?php
}

/**
 * Who is on the list.
 */
function kadence_child_subscribers_list_screen() {
	$table = new Kadence_Child_Subscribers_Table();
	$table->prepare_items();
	?>
	<form method="post" style="margin:1em 0;padding:1em;background:#fff;border:1px solid #c3c4c7">
		<?php wp_nonce_field( 'cavo_add_subscriber' ); ?>
		<input type="hidden" name="cavo_add_subscriber" value="1" />
		<label for="cavo-add-email"><?php esc_html_e( 'Add an address', 'kadence-child' ); ?></label>
		<input type="email" id="cavo-add-email" name="email" class="regular-text" required />
		<?php submit_button( esc_html__( 'Add', 'kadence-child' ), 'secondary', 'submit', false ); ?>
	</form>

	<form method="get">
		<input type="hidden" name="post_type" value="cavo_message" />
		<input type="hidden" name="page" value="cavo-subscribers" />
		<?php
		$table->views();
		$table->search_box( esc_html__( 'Search addresses', 'kadence-child' ), 'subscribers' );
		?>
	</form>

	<form method="post">
		<?php
		wp_nonce_field( 'bulk-subscribers' );
		$table->display();
		?>
	</form>
	<?php
}

/**
 * The list of addresses, as the screen draws it.
 */
function kadence_child_subscribers_table_class() {
	if ( class_exists( 'Kadence_Child_Subscribers_Table' ) ) {
		return;
	}

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	/**
	 * Who is on the list.
	 */
	class Kadence_Child_Subscribers_Table extends WP_List_Table {

		/**
		 * Name the thing being listed.
		 */
		public function __construct() {
			parent::__construct(
				array(
					'singular' => 'subscriber',
					'plural'   => 'subscribers',
					'ajax'     => false,
				)
			);
		}

		/**
		 * The columns.
		 *
		 * The column naming where an address came from appears once a second
		 * form can put one there, and not before: until then every row would
		 * say the same word.
		 *
		 * @return array
		 */
		public function get_columns() {
			$columns = array(
				'cb'     => '<input type="checkbox" />',
				'email'  => esc_html__( 'Address', 'kadence-child' ),
				'status' => esc_html__( 'Standing', 'kadence-child' ),
			);

			if ( count( kadence_child_subscriber_sources() ) > 1 ) {
				$columns['source'] = esc_html__( 'Form', 'kadence-child' );
			}

			$columns['date'] = esc_html__( 'Signed up', 'kadence-child' );

			return $columns;
		}

		/**
		 * Which columns can be sorted on.
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return array(
				'email' => array( 'title', false ),
				'date'  => array( 'date', true ),
			);
		}

		/**
		 * What can be done to a selection.
		 *
		 * @return array
		 */
		public function get_bulk_actions() {
			return array(
				'unsubscribe' => esc_html__( 'Mark unsubscribed', 'kadence-child' ),
				'remove'      => esc_html__( 'Remove from the list', 'kadence-child' ),
			);
		}

		/**
		 * Which standing is being looked at.
		 *
		 * @return string
		 */
		private function standing() {
			$asked  = isset( $_GET['standing'] ) ? sanitize_key( wp_unslash( $_GET['standing'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- narrowing a list.
			$states = kadence_child_subscriber_states();

			return isset( $states[ $asked ] ) ? $asked : '';
		}

		/**
		 * The counts across the top.
		 *
		 * @return array
		 */
		protected function get_views() {
			$here = $this->standing();
			$base = kadence_child_subscribers_url( 'list' );

			$views = array(
				'' => sprintf(
					'<a href="%s"%s>%s <span class="count">(%s)</span></a>',
					esc_url( $base ),
					'' === $here ? ' class="current"' : '',
					esc_html__( 'All', 'kadence-child' ),
					esc_html( number_format_i18n( kadence_child_subscriber_count() ) )
				),
			);

			foreach ( kadence_child_subscriber_states() as $slug => $label ) {
				$views[ $slug ] = sprintf(
					'<a href="%s"%s>%s <span class="count">(%s)</span></a>',
					esc_url( add_query_arg( 'standing', $slug, $base ) ),
					$slug === $here ? ' class="current"' : '',
					esc_html( $label ),
					esc_html( number_format_i18n( kadence_child_subscriber_count( $slug ) ) )
				);
			}

			return $views;
		}

		/**
		 * Fetch the page being looked at.
		 */
		public function prepare_items() {
			$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

			$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ordering a list.
			$order   = isset( $_GET['order'] ) && 'asc' === strtolower( sanitize_key( wp_unslash( $_GET['order'] ) ) ) ? 'ASC' : 'DESC'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ordering a list.
			$search  = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- searching a list.

			$query = array(
				'post_type'      => 'cavo_subscriber',
				'post_status'    => 'any',
				'posts_per_page' => 50,
				'paged'          => $this->get_pagenum(),
				'orderby'        => in_array( $orderby, array( 'title', 'date' ), true ) ? $orderby : 'date',
				'order'          => $order,
			);

			if ( '' !== $search ) {
				$query['s'] = $search;
			}

			$standing = $this->standing();

			if ( '' !== $standing ) {
				$query['meta_key']   = 'cavo_status'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- narrowed by standing.
				$query['meta_value'] = $standing; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- as above.
			}

			$found = new WP_Query( $query );

			$this->items = $found->posts;

			$this->set_pagination_args(
				array(
					'total_items' => (int) $found->found_posts,
					'per_page'    => 50,
					'total_pages' => (int) $found->max_num_pages,
				)
			);
		}

		/**
		 * The checkbox.
		 *
		 * @param WP_Post $item One row.
		 * @return string
		 */
		public function column_cb( $item ) {
			return sprintf( '<input type="checkbox" name="cavo_who[]" value="%d" />', (int) $item->ID );
		}

		/**
		 * The address, and what can be done to it.
		 *
		 * @param WP_Post $item One row.
		 * @return string
		 */
		public function column_email( $item ) {
			$base    = kadence_child_subscribers_url( 'list' );
			$actions = array();

			if ( 'unsubscribed' !== kadence_child_subscriber_status( $item->ID ) ) {
				$actions['unsubscribe'] = sprintf(
					'<a href="%s">%s</a>',
					esc_url(
						wp_nonce_url(
							add_query_arg(
								array(
									'cavo_do'  => 'unsubscribe',
									'cavo_who' => (int) $item->ID,
								),
								$base
							),
							'cavo_subscriber_' . $item->ID
						)
					),
					esc_html__( 'Mark unsubscribed', 'kadence-child' )
				);
			}

			$actions['remove'] = sprintf(
				'<a href="%s" class="submitdelete">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'cavo_do'  => 'remove',
								'cavo_who' => (int) $item->ID,
							),
							$base
						),
						'cavo_subscriber_' . $item->ID
					)
				),
				esc_html__( 'Remove', 'kadence-child' )
			);

			return sprintf( '<strong>%s</strong>%s', esc_html( $item->post_title ), $this->row_actions( $actions ) );
		}

		/**
		 * Where the address stands.
		 *
		 * @param WP_Post $item One row.
		 * @return string
		 */
		public function column_status( $item ) {
			$states = kadence_child_subscriber_states();
			$status = kadence_child_subscriber_status( $item->ID );

			return esc_html( isset( $states[ $status ] ) ? $states[ $status ] : $status );
		}

		/**
		 * Which form it came through.
		 *
		 * @param WP_Post $item One row.
		 * @return string
		 */
		public function column_source( $item ) {
			$slug  = (string) get_post_meta( $item->ID, 'cavo_source', true );
			$forms = kadence_child_forms();

			if ( isset( $forms[ $slug ] ) ) {
				return esc_html( $forms[ $slug ] );
			}

			return 'newsletter' === $slug ? esc_html__( 'Newsletter', 'kadence-child' ) : '—';
		}

		/**
		 * When they asked.
		 *
		 * @param WP_Post $item One row.
		 * @return string
		 */
		public function column_date( $item ) {
			return esc_html( get_the_date( 'j F Y', $item ) );
		}

		/**
		 * What an empty list says.
		 */
		public function no_items() {
			esc_html_e( 'Nobody has signed up yet.', 'kadence-child' );
		}
	}
}
add_action( 'admin_init', 'kadence_child_subscribers_table_class', 5 );

/**
 * Which forms have actually put somebody on the list.
 *
 * @return array
 */
function kadence_child_subscriber_sources() {
	global $wpdb;

	$found = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one distinct read, drawn once per screen.
		$wpdb->prepare(
			"SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value <> ''",
			'cavo_source'
		)
	);

	return is_array( $found ) ? $found : array();
}

/**
 * A record of one thing having gone out to the list.
 *
 * Sent means it was accepted, not that it was received. Nothing here can tell
 * the second, and a column that implies otherwise is worse than none.
 */
function kadence_child_broadcast_post_type() {
	register_post_type(
		'cavo_broadcast',
		array(
			'labels'              => array(
				'name'          => esc_html__( 'Broadcasts', 'kadence-child' ),
				'singular_name' => esc_html__( 'Broadcast', 'kadence-child' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'supports'            => array( 'title' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'kadence_child_broadcast_post_type' );

/**
 * How many go out on one turn of the queue.
 *
 * A publish must not wait on the list, and a request must not carry it: the
 * work is handed to the scheduler and taken back a batch at a time.
 */
const KADENCE_CHILD_BATCH = 50;

/**
 * From is the domain.
 *
 * @return string
 */
function kadence_child_broadcast_from() {
	$domain = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );

	return sprintf(
		'From: %s <no-reply@%s>',
		wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		$domain
	);
}

/**
 * The link that takes an address off the list, or confirms it onto it.
 *
 * @param int    $id  The subscriber.
 * @param string $arg Which link.
 * @return string
 */
function kadence_child_subscriber_link( $id, $arg = 'cavo_unsub' ) {
	$token = (string) get_post_meta( $id, 'cavo_token', true );

	return add_query_arg( $arg, rawurlencode( $token ), home_url( '/' ) );
}

/**
 * One mail's body.
 *
 * @param WP_Post $post  What was published.
 * @param int     $who   The subscriber it is going to.
 * @return string
 */
function kadence_child_broadcast_body( $post, $who ) {
	$title   = get_the_title( $post );
	$link    = get_permalink( $post );
	$excerpt = get_the_excerpt( $post );
	$image   = get_the_post_thumbnail_url( $post, 'large' );
	$unsub   = kadence_child_subscriber_link( $who, 'cavo_unsub' );

	ob_start();
	?>
	<div style="margin:0;padding:24px;background:#faf6ea;font-family:Helvetica,Arial,sans-serif;color:#29180e">
		<div style="max-width:560px;margin:0 auto;background:#ffffff;padding:24px">
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="" width="512" style="display:block;width:100%;height:auto;margin:0 0 20px" />
			<?php endif; ?>

			<h1 style="margin:0 0 12px;font-size:24px;line-height:1.25;font-weight:500"><?php echo esc_html( $title ); ?></h1>

			<?php if ( '' !== trim( (string) $excerpt ) ) : ?>
				<p style="margin:0 0 20px;font-size:16px;line-height:1.5"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>

			<p style="margin:0 0 8px">
				<a href="<?php echo esc_url( $link ); ?>" style="display:inline-block;padding:12px 20px;background:#29180e;color:#faf6ea;text-decoration:none;font-size:14px">
					<?php echo esc_html__( 'Read it', 'kadence-child' ); ?>
				</a>
			</p>
		</div>

		<p style="max-width:560px;margin:16px auto 0;font-size:12px;line-height:1.5;color:#6b5a4c">
			<?php echo esc_html__( 'You are receiving this because you asked to hear from us.', 'kadence-child' ); ?>
			<a href="<?php echo esc_url( $unsub ); ?>" style="color:#6b5a4c"><?php echo esc_html__( 'Unsubscribe', 'kadence-child' ); ?></a>
		</p>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Publishing something is what makes it news.
 *
 * A post that has already gone out never goes out again, whatever is done to it
 * afterwards: the guard is written on the post itself, before the first batch.
 *
 * @param string  $new  The status it moved to.
 * @param string  $old  The status it came from.
 * @param WP_Post $post What moved.
 */
function kadence_child_broadcast_on_publish( $new, $old, $post ) {
	if ( 'publish' !== $new || 'publish' === $old ) {
		return;
	}

	if ( wp_is_post_revision( $post ) || wp_is_post_autosave( $post ) ) {
		return;
	}

	$settings = kadence_child_subscription_settings();

	if ( empty( $settings['sending'] ) ) {
		return;
	}

	if ( ! in_array( $post->post_type, (array) $settings['post_types'], true ) ) {
		return;
	}

	if ( get_post_meta( $post->ID, 'cavo_broadcast', true ) ) {
		return;
	}

	$broadcast = wp_insert_post(
		array(
			'post_type'   => 'cavo_broadcast',
			'post_status' => 'publish',
			'post_title'  => get_the_title( $post ),
		),
		true
	);

	if ( is_wp_error( $broadcast ) ) {
		return;
	}

	update_post_meta( $post->ID, 'cavo_broadcast', $broadcast );
	update_post_meta( $broadcast, 'cavo_post', (int) $post->ID );
	update_post_meta( $broadcast, 'cavo_cursor', 0 );
	update_post_meta( $broadcast, 'cavo_state', 'running' );

	foreach ( array( 'sent', 'nosmtp', 'failed' ) as $count ) {
		update_post_meta( $broadcast, 'cavo_' . $count, 0 );
	}

	update_post_meta( $broadcast, 'cavo_queued', kadence_child_subscriber_count( 'confirmed' ) );

	wp_schedule_single_event( time() + 30, 'cavo_broadcast_run', array( (int) $broadcast ) );
}
add_action( 'transition_post_status', 'kadence_child_broadcast_on_publish', 10, 3 );

/**
 * One turn of the queue.
 *
 * @param int $broadcast Which broadcast.
 */
function kadence_child_broadcast_run( $broadcast ) {
	$broadcast = (int) $broadcast;

	if ( 'cavo_broadcast' !== get_post_type( $broadcast ) || 'running' !== get_post_meta( $broadcast, 'cavo_state', true ) ) {
		return;
	}

	$post = get_post( (int) get_post_meta( $broadcast, 'cavo_post', true ) );

	if ( ! $post || 'publish' !== $post->post_status ) {
		update_post_meta( $broadcast, 'cavo_state', 'stopped' );

		return;
	}

	$cursor = (int) get_post_meta( $broadcast, 'cavo_cursor', true );

	global $wpdb;

	// Walked by id rather than by page. A page is counted from the start of the
	// list every time, so a row removed mid-run slides the next page up and
	// whoever was on the join is never written to.
	$people = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one indexed read per batch, and a cache would be stale by the next.
		$wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->posts} p
			 INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = 'cavo_status'
			 WHERE p.post_type = 'cavo_subscriber' AND m.meta_value = 'confirmed' AND p.ID > %d
			 ORDER BY p.ID ASC LIMIT %d",
			$cursor,
			KADENCE_CHILD_BATCH
		)
	);

	if ( empty( $people ) ) {
		update_post_meta( $broadcast, 'cavo_state', 'done' );

		return;
	}

	$settings = kadence_child_subscription_settings();
	$subject  = trim( (string) $settings['subject'] );
	$subject  = '' !== $subject ? $subject : '{title}';
	$subject  = str_replace( '{title}', get_the_title( $post ), $subject );
	$from     = kadence_child_broadcast_from();

	foreach ( $people as $who ) {
		$who   = (int) $who;
		$email = get_post_field( 'post_title', $who );

		if ( ! is_email( $email ) ) {
			update_post_meta( $broadcast, 'cavo_cursor', $who );
			continue;
		}

		$unsub = kadence_child_subscriber_link( $who, 'cavo_unsub' );

		$state = kadence_child_send(
			$email,
			$subject,
			kadence_child_broadcast_body( $post, $who ),
			array(
				'Content-Type: text/html; charset=UTF-8',
				$from,
				'List-Unsubscribe: <' . esc_url_raw( $unsub ) . '>',
				'List-Unsubscribe-Post: List-Unsubscribe=One-Click',
			)
		);

		$key = in_array( $state, array( 'sent', 'nosmtp', 'failed' ), true ) ? $state : 'failed';

		update_post_meta( $broadcast, 'cavo_' . $key, (int) get_post_meta( $broadcast, 'cavo_' . $key, true ) + 1 );
		update_post_meta( $broadcast, 'cavo_cursor', $who );
	}

	wp_schedule_single_event( time() + 60, 'cavo_broadcast_run', array( $broadcast ) );
}
add_action( 'cavo_broadcast_run', 'kadence_child_broadcast_run' );

/**
 * The one mail an address gets before it counts.
 *
 * Sent only where confirming is asked for. Nothing else goes to an address
 * that has not answered this.
 *
 * @param int $who The subscriber.
 * @return string What state the mail left in.
 */
function kadence_child_subscribe_confirm_mail( $who ) {
	$email = get_post_field( 'post_title', $who );

	if ( ! is_email( $email ) ) {
		return 'nothing';
	}

	$link = kadence_child_subscriber_link( $who, 'cavo_confirm' );
	$name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

	ob_start();
	?>
	<div style="margin:0;padding:24px;background:#faf6ea;font-family:Helvetica,Arial,sans-serif;color:#29180e">
		<div style="max-width:560px;margin:0 auto;background:#ffffff;padding:24px">
			<h1 style="margin:0 0 12px;font-size:20px;line-height:1.3;font-weight:500">
				<?php echo esc_html__( 'One press and you are on the list', 'kadence-child' ); ?>
			</h1>
			<p style="margin:0 0 20px;font-size:16px;line-height:1.5">
				<?php echo esc_html__( 'Somebody asked for this address to hear from us. If that was you, confirm it below. If it was not, do nothing and nothing will be sent.', 'kadence-child' ); ?>
			</p>
			<p style="margin:0">
				<a href="<?php echo esc_url( $link ); ?>" style="display:inline-block;padding:12px 20px;background:#29180e;color:#faf6ea;text-decoration:none;font-size:14px">
					<?php echo esc_html__( 'Confirm', 'kadence-child' ); ?>
				</a>
			</p>
		</div>
	</div>
	<?php

	return kadence_child_send(
		$email,
		/* translators: %s: the site's name. */
		sprintf( esc_html__( 'Confirm your address — %s', 'kadence-child' ), $name ),
		(string) ob_get_clean(),
		array( 'Content-Type: text/html; charset=UTF-8', kadence_child_broadcast_from() )
	);
}

/**
 * What has gone out.
 */
function kadence_child_subscribers_broadcasts_screen() {
	$rows = get_posts(
		array(
			'post_type'      => 'cavo_broadcast',
			'post_status'    => 'any',
			'posts_per_page' => 30,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		)
	);

	if ( empty( $rows ) ) {
		echo '<p>' . esc_html__( 'Nothing has gone out yet.', 'kadence-child' ) . '</p>';

		return;
	}

	$states = array(
		'running' => esc_html__( 'Going out', 'kadence-child' ),
		'done'    => esc_html__( 'Finished', 'kadence-child' ),
		'stopped' => esc_html__( 'Stopped', 'kadence-child' ),
	);
	?>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'What went out', 'kadence-child' ); ?></th>
				<th><?php esc_html_e( 'When', 'kadence-child' ); ?></th>
				<th><?php esc_html_e( 'Standing', 'kadence-child' ); ?></th>
				<th><?php esc_html_e( 'On the list', 'kadence-child' ); ?></th>
				<th><?php esc_html_e( 'Accepted', 'kadence-child' ); ?></th>
				<th><?php esc_html_e( 'No mail server', 'kadence-child' ); ?></th>
				<th><?php esc_html_e( 'Refused', 'kadence-child' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $rows as $row ) :
				$state  = (string) get_post_meta( $row->ID, 'cavo_state', true );
				$target = (int) get_post_meta( $row->ID, 'cavo_post', true );
				$nosmtp = (int) get_post_meta( $row->ID, 'cavo_nosmtp', true );
				?>
				<tr>
					<td>
						<?php if ( $target && get_post( $target ) ) : ?>
							<a href="<?php echo esc_url( (string) get_permalink( $target ) ); ?>"><?php echo esc_html( $row->post_title ); ?></a>
						<?php else : ?>
							<?php echo esc_html( $row->post_title ); ?>
						<?php endif; ?>
					</td>
					<td><?php echo esc_html( get_the_date( 'j F Y, H:i', $row ) ); ?></td>
					<td><?php echo esc_html( isset( $states[ $state ] ) ? $states[ $state ] : $state ); ?></td>
					<td><?php echo esc_html( number_format_i18n( (int) get_post_meta( $row->ID, 'cavo_queued', true ) ) ); ?></td>
					<td><?php echo esc_html( number_format_i18n( (int) get_post_meta( $row->ID, 'cavo_sent', true ) ) ); ?></td>
					<td><?php echo $nosmtp > 0 ? '<strong style="color:#b32d2e">' . esc_html( number_format_i18n( $nosmtp ) ) . '</strong>' : '0'; ?></td>
					<td><?php echo esc_html( number_format_i18n( (int) get_post_meta( $row->ID, 'cavo_failed', true ) ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<p class="description" style="margin-top:1em">
		<?php esc_html_e( 'Accepted means a mail server took it, not that it arrived. “No mail server” means PHP’s own mail() carried it because nothing else did — that mail usually arrives nowhere.', 'kadence-child' ); ?>
	</p>
	<?php
}

/**
 * What a reader could be told about.
 *
 * A page builder keeps its own posts — a template, a saved element — and an
 * upload is a post as well. None of them is news, and a list that offers them
 * invites somebody to mail everybody by saving a template.
 *
 * @return array
 */
function kadence_child_news_post_types() {
	$kept = array();

	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) {
		if ( 'attachment' === $type->name || 0 === strpos( $type->name, 'elementor' ) || 0 === strpos( $type->name, 'e-' ) ) {
			continue;
		}

		$kept[ $type->name ] = $type;
	}

	return $kept;
}

/**
 * What a sign-up form asks, on the Form editor.
 *
 * The design settles that there is one box and that it takes an address, so
 * neither is offered here. What is left is what the box says, which is the
 * client's.
 *
 * @param string $key The form's slug: its fields are stored under it.
 * @return array
 */
function kadence_child_signup_field_group( $key ) {
	return array(
		'key'        => 'field_cavo_signup_' . $key,
		'label'      => esc_html__( 'Field', 'kadence-child' ),
		'name'       => 'signup_' . $key,
		'type'       => 'group',
		'sub_fields' => array(
			array(
				'key'         => 'field_cavo_signup_label_' . $key,
				'label'       => esc_html__( 'Label', 'kadence-child' ),
				'name'        => 'label',
				'type'        => 'text',
				'placeholder' => esc_html__( 'Email address', 'kadence-child' ),
			),
			array(
				'key'         => 'field_cavo_signup_placeholder_' . $key,
				'label'       => esc_html__( 'Placeholder', 'kadence-child' ),
				'name'        => 'placeholder',
				'type'        => 'text',
				'placeholder' => esc_html__( 'Enter your email', 'kadence-child' ),
			),
			array(
				'key'         => 'field_cavo_signup_button_' . $key,
				'label'       => esc_html__( 'Button', 'kadence-child' ),
				'name'        => 'button',
				'type'        => 'text',
				'placeholder' => esc_html__( 'Submit', 'kadence-child' ),
			),
		),
	);
}

/**
 * A sign-up form's settings.
 *
 * The same three sections every form carries. What differs is the middle one:
 * a sign-up can end in a third way, waiting on an address to answer for itself.
 *
 * @param string $key The form's slug: its settings are stored under it.
 * @return array
 */
function kadence_child_signup_settings_fields( $key ) {
	return array_merge(
		kadence_child_form_notification_fields( $key ),
		array(
		array(
			'key'        => 'field_cavo_result_' . $key,
			'label'      => esc_html__( 'Submit result', 'kadence-child' ),
			'name'       => 'result_' . $key,
			'type'       => 'group',
			'sub_fields' => array(
				array(
					'key'         => 'field_cavo_success_' . $key,
					'label'       => esc_html__( 'Success', 'kadence-child' ),
					'name'        => 'success',
					'type'        => 'text',
				),
				array(
					'key'         => 'field_cavo_confirm_' . $key,
					'label'       => esc_html__( 'Sent to confirm', 'kadence-child' ),
					'name'        => 'confirm',
					'type'        => 'text',
				),
				array(
					'key'         => 'field_cavo_fail_' . $key,
					'label'       => esc_html__( 'Fail', 'kadence-child' ),
					'name'        => 'fail',
					'type'        => 'text',
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
		)
	);
}

/**
 * What a sign-up form's box says, as the client left it.
 *
 * @param string $slug The form's slug.
 * @param string $which Which word.
 * @return string
 */
function kadence_child_signup_word( $slug, $which ) {
	if ( ! function_exists( 'get_field' ) ) {
		return '';
	}

	$said = (array) get_field( 'signup_' . $slug, 'option' );

	return isset( $said[ $which ] ) ? trim( (string) $said[ $which ] ) : '';
}

/**
 * What the page says back after a press.
 *
 * @param string $slug  The form's slug.
 * @param string $state What happened.
 * @return string
 */
function kadence_child_signup_answer( $slug, $state ) {
	$said = kadence_child_form_settings( $slug, 'result' );

	$written = array(
		'ok'      => isset( $said['success'] ) ? trim( (string) $said['success'] ) : '',
		'confirm' => isset( $said['confirm'] ) ? trim( (string) $said['confirm'] ) : '',
		'invalid' => isset( $said['fail'] ) ? trim( (string) $said['fail'] ) : '',
	);

	$design = array(
		'ok'      => esc_html__( 'Thank you — you are on the list.', 'kadence-child' ),
		'confirm' => esc_html__( 'Almost there: open the email we just sent and confirm.', 'kadence-child' ),
		'invalid' => esc_html__( 'That address does not look right.', 'kadence-child' ),
		'expired' => esc_html__( 'That page had been open a while. Please try again.', 'kadence-child' ),
		'welcome' => esc_html__( 'Confirmed — you are on the list.', 'kadence-child' ),
		'gone'    => esc_html__( 'You have been taken off the list.', 'kadence-child' ),
	);

	if ( isset( $written[ $state ] ) && '' !== $written[ $state ] ) {
		return $written[ $state ];
	}

	return isset( $design[ $state ] ) ? $design[ $state ] : '';
}

/**
 * Who hears that somebody signed up.
 *
 * A sign-up says one thing, so that one thing is what both emails carry. The
 * row was written first; these are the courtesy on top of it, and neither can
 * lose the address by failing.
 *
 * @param string $slug  The form's slug.
 * @param string $email What they typed.
 */
function kadence_child_subscribe_notify( $slug, $email ) {
	$domain  = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$name    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$forms   = kadence_child_forms();
	$form    = isset( $forms[ $slug ] ) ? $forms[ $slug ] : $slug;
	$headers = array( sprintf( 'From: %s <no-reply@%s>', $name, $domain ) );

	// The address goes in a header only once it has been read as an address.
	$reply = is_email( $email ) ? array_merge( $headers, array( 'Reply-To: ' . $email ) ) : $headers;

	$team = kadence_child_form_settings( $slug, 'team' );
	$to   = array();

	if ( ! empty( $team['send'] ) ) {
		foreach ( (array) ( isset( $team['to'] ) ? $team['to'] : array() ) as $row ) {
			if ( ! empty( $row['email'] ) && is_email( $row['email'] ) ) {
				$to[] = $row['email'];
			}
		}
	}

	if ( ! empty( $to ) ) {
		$subject = isset( $team['subject'] ) ? trim( (string) $team['subject'] ) : '';
		$subject = '' !== $subject ? $subject : sprintf( '%1$s — %2$s', $form, $email );
		$body    = isset( $team['body'] ) ? trim( (string) $team['body'] ) : '';

		kadence_child_send(
			$to,
			$subject,
			( '' !== $body ? $body . "\r\n\r\n" : '' ) . $email,
			$reply
		);
	}

	$client = kadence_child_form_settings( $slug, 'client' );

	if ( empty( $client['copy'] ) || ! is_email( $email ) ) {
		return;
	}

	$subject = isset( $client['subject'] ) ? trim( (string) $client['subject'] ) : '';
	$subject = '' !== $subject ? $subject : sprintf( '%1$s — %2$s', $form, $name );
	$body    = isset( $client['body'] ) ? trim( (string) $client['body'] ) : '';

	kadence_child_send(
		$email,
		$subject,
		( '' !== $body ? $body . "\r\n\r\n" : '' ) . ( ! empty( $client['answers'] ) ? $email : '' ),
		$headers
	);
}
