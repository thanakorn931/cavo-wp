<?php
/**
 * What the list does, as opposed to who is on it.
 *
 * Recipients are never a setting here — the recipients are the list. What is
 * settled instead is whether anything goes out at all, what publishing counts
 * as news, and the words wrapped around it.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The subscription's own settings.
 */
class Settings {

	/**
	 * Where they are kept.
	 */
	const OPTION = 'cavo_subscription_settings';

	/**
	 * The settings before anybody has touched them.
	 *
	 * Sending starts off. A plugin arriving on a site mid-build must not answer
	 * the first publish by mailing everybody who signed up while it was being
	 * tested.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'sending'    => false,
			'confirms'   => false,
			'post_types' => array( 'post' ),
			'from_name'  => '',
			'subject'    => esc_html__( '{title}', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * All of them.
	 *
	 * @return array
	 */
	public static function all() {
		$stored = get_option( self::OPTION, array() );
		$stored = is_array( $stored ) ? $stored : array();

		return array_merge( self::defaults(), $stored );
	}

	/**
	 * One of them.
	 *
	 * @param string $key   Which.
	 * @param mixed  $empty What to answer if it is not there.
	 * @return mixed
	 */
	public static function get( $key, $empty = null ) {
		$all = self::all();

		return isset( $all[ $key ] ) ? $all[ $key ] : $empty;
	}

	/**
	 * Write them back.
	 *
	 * @param array $settings What to keep.
	 */
	public static function save( $settings ) {
		update_option( self::OPTION, array_merge( self::defaults(), (array) $settings ) );
	}

	/**
	 * Whether anything goes out on publish at all.
	 *
	 * @return bool
	 */
	public static function sending() {
		return (bool) self::get( 'sending', false );
	}

	/**
	 * Whether an address is asked to confirm itself before it counts.
	 *
	 * Off, anybody can put anybody else's address on the list.
	 *
	 * @return bool
	 */
	public static function confirms() {
		return (bool) self::get( 'confirms', false );
	}

	/**
	 * The post types whose publishing is news.
	 *
	 * @return array
	 */
	public static function post_types() {
		$types = (array) self::get( 'post_types', array( 'post' ) );

		return array_values( array_filter( array_map( 'sanitize_key', $types ) ) );
	}

	/**
	 * The name a mail comes from. The address is the domain's, never a reader's.
	 *
	 * @return string
	 */
	public static function from_name() {
		$name = trim( (string) self::get( 'from_name', '' ) );

		return '' !== $name ? $name : get_bloginfo( 'name' );
	}
}
