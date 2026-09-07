<?php
/**
 * The inbox's post type.
 *
 * The list is the menu: every other screen in WP Form hangs under this one and
 * is reached through it, so the type is registered whether or not a form has
 * yet been wired to file anything in it.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * What a form files when somebody writes in.
 */
class Messages {

	/**
	 * The post type holding the messages.
	 */
	const POST_TYPE = 'cavo_message';

	/**
	 * Register it, locked down.
	 *
	 * What was said is printed on the message rather than opened for editing,
	 * so nothing here is creatable and the editor never appears.
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => esc_html__( 'WP Form', 'custom-elementor-widgets' ),
					'singular_name' => esc_html__( 'Message', 'custom-elementor-widgets' ),
					'menu_name'     => esc_html__( 'WP Form', 'custom-elementor-widgets' ),
					'all_items'     => esc_html__( 'Inbox', 'custom-elementor-widgets' ),
					'not_found'     => esc_html__( 'No messages yet.', 'custom-elementor-widgets' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'menu_icon'           => 'dashicons-email-alt',
				'menu_position'       => 26,
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'capabilities'        => array(
					'create_posts' => 'do_not_allow',
				),
				'supports'            => array( 'title' ),
			)
		);
	}
}
