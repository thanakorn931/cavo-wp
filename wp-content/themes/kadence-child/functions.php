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
