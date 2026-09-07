<?php
/**
 * The list of addresses that asked to hear from the site.
 *
 * A row here is not a message. A message is read once and done with; an address
 * carries a state that keeps changing, so it is stored where it can be found by
 * the address itself, moved between states, and removed on the word of whoever
 * owns it.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The store, and the few things done to it.
 */
class Subscribers {

	/**
	 * The post type holding the list.
	 */
	const POST_TYPE = 'cavo_subscriber';

	/**
	 * The address, lowered, which is what a lookup matches on.
	 */
	const EMAIL = '_cavo_email';

	/**
	 * Where the address stands: pending, confirmed or unsubscribed.
	 */
	const STATUS = '_cavo_status';

	/**
	 * The secret in the confirm and unsubscribe links.
	 */
	const TOKEN = '_cavo_token';

	/**
	 * Which form the address came in through.
	 */
	const SOURCE = '_cavo_source';

	/**
	 * Register the post type, locked down.
	 */
	public static function register() {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => esc_html__( 'Subscribers', 'custom-elementor-widgets' ),
					'singular_name' => esc_html__( 'Subscriber', 'custom-elementor-widgets' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => false,
				'show_in_menu'        => false,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'supports'            => array( 'title' ),
			)
		);
	}

	/**
	 * The three states an address can stand in.
	 *
	 * @return array
	 */
	public static function states() {
		return array(
			'pending'      => esc_html__( 'Waiting to confirm', 'custom-elementor-widgets' ),
			'confirmed'    => esc_html__( 'Confirmed', 'custom-elementor-widgets' ),
			'unsubscribed' => esc_html__( 'Unsubscribed', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The row for one address, or 0.
	 *
	 * @param string $email The address.
	 * @return int
	 */
	public static function find( $email ) {
		$found = get_posts(
			array(
				'post_type'        => self::POST_TYPE,
				'post_status'      => 'any',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => false,
				'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- the list is looked up by address and nothing else.
					array(
						'key'   => self::EMAIL,
						'value' => self::normalise( $email ),
					),
				),
			)
		);

		return empty( $found ) ? 0 : (int) $found[0];
	}

	/**
	 * An address as it is compared: trimmed and lowered.
	 *
	 * @param string $email The address.
	 * @return string
	 */
	public static function normalise( $email ) {
		return strtolower( trim( (string) $email ) );
	}

	/**
	 * Put an address on the list, or bring one back that had left.
	 *
	 * Somebody signing up again after unsubscribing is asking to be back on,
	 * so the row is moved rather than refused. A confirmed address that signs
	 * up twice keeps the day it first asked.
	 *
	 * @param string $email  The address.
	 * @param string $source Which form it came through.
	 * @param string $status Where it should stand.
	 * @return int|\WP_Error The row, or why not.
	 */
	public static function add( $email, $source = '', $status = 'confirmed' ) {
		$email = trim( (string) $email );

		if ( ! is_email( $email ) ) {
			return new \WP_Error( 'cavo_bad_email', esc_html__( 'That address does not look right.', 'custom-elementor-widgets' ) );
		}

		$states = self::states();
		$status = isset( $states[ $status ] ) ? $status : 'confirmed';
		$id     = self::find( $email );

		if ( 0 === $id ) {
			$id = wp_insert_post(
				array(
					'post_type'   => self::POST_TYPE,
					'post_status' => 'publish',
					'post_title'  => $email,
				),
				true
			);

			if ( is_wp_error( $id ) ) {
				return $id;
			}

			update_post_meta( $id, self::EMAIL, self::normalise( $email ) );
			update_post_meta( $id, self::TOKEN, wp_generate_password( 32, false ) );
			update_post_meta( $id, self::SOURCE, sanitize_key( $source ) );
			update_post_meta( $id, self::STATUS, $status );

			return $id;
		}

		if ( 'confirmed' !== get_post_meta( $id, self::STATUS, true ) ) {
			update_post_meta( $id, self::STATUS, $status );
		}

		return $id;
	}

	/**
	 * Move one row to a state.
	 *
	 * @param int    $id     The row.
	 * @param string $status Where it should stand.
	 * @return bool
	 */
	public static function set_status( $id, $status ) {
		$states = self::states();

		if ( ! isset( $states[ $status ] ) || self::POST_TYPE !== get_post_type( $id ) ) {
			return false;
		}

		update_post_meta( $id, self::STATUS, $status );

		return true;
	}

	/**
	 * One row's state.
	 *
	 * @param int $id The row.
	 * @return string
	 */
	public static function status( $id ) {
		$status = (string) get_post_meta( $id, self::STATUS, true );
		$states = self::states();

		return isset( $states[ $status ] ) ? $status : 'pending';
	}

	/**
	 * The row a token names, or 0.
	 *
	 * @param string $token The secret from a link.
	 * @return int
	 */
	public static function by_token( $token ) {
		$token = trim( (string) $token );

		if ( '' === $token ) {
			return 0;
		}

		$found = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- a link carries the token and nothing else.
					array(
						'key'   => self::TOKEN,
						'value' => $token,
					),
				),
			)
		);

		return empty( $found ) ? 0 : (int) $found[0];
	}

	/**
	 * How many addresses stand in one state.
	 *
	 * @param string $status Which state, or '' for the whole list.
	 * @return int
	 */
	public static function count( $status = '' ) {
		$query = array(
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		);

		if ( '' !== $status ) {
			$query['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- counted by state.
				array(
					'key'   => self::STATUS,
					'value' => $status,
				),
			);
		}

		$found = new \WP_Query( $query );

		return (int) $found->found_posts;
	}
}
