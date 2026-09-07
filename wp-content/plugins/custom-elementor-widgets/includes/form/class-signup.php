<?php
/**
 * What happens when somebody asks to hear from the site.
 *
 * The row is the record and the email a courtesy on top of it, so the address
 * is stored before anything is sent. The answer is never rendered onto the POST
 * itself: it is stored, redirected past, and printed by the page that comes
 * back, so a refresh cannot sign anybody up twice.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The POST, and the two links a mail carries.
 */
class Signup {

	/**
	 * What the form posts as.
	 */
	const ACTION = 'cavo_subscribe';

	/**
	 * The field a hand will not fill.
	 */
	const TRAP = 'cavo_website';

	/**
	 * The field carrying when the page was drawn.
	 */
	const DRAWN = 'cavo_drawn';

	/**
	 * A form filled faster than this was not filled by a reader.
	 */
	const TOO_FAST = 3;

	/**
	 * A page left open longer than this is asked for again.
	 */
	const TOO_OLD = 7200;

	/**
	 * The query argument naming a stored answer.
	 */
	const RESULT = 'cavo_signup';

	/**
	 * Listen for the POST and for the two links.
	 */
	public static function listen() {
		add_action( 'admin_post_nopriv_' . self::ACTION, array( __CLASS__, 'receive' ) );
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'receive' ) );
		add_action( 'init', array( __CLASS__, 'follow_link' ) );
	}

	/**
	 * The hidden fields every sign-up form prints.
	 */
	public static function fields() {
		wp_nonce_field( self::ACTION, '_cavo_nonce' );

		printf(
			'<input type="hidden" name="action" value="%s" />',
			esc_attr( self::ACTION )
		);

		printf(
			'<input type="hidden" name="%s" value="%d" />',
			esc_attr( self::DRAWN ),
			absint( time() )
		);

		printf(
			'<input type="hidden" name="cavo_from" value="%s" />',
			esc_attr( self::here() )
		);

		// A field the eye cannot see and a hand will not fill.
		printf(
			'<div aria-hidden="true" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">' .
			'<label>%1$s<input type="text" name="%2$s" value="" tabindex="-1" autocomplete="off" /></label></div>',
			esc_html__( 'Leave this field empty', 'custom-elementor-widgets' ),
			esc_attr( self::TRAP )
		);
	}

	/**
	 * Where the reader was standing when they pressed it.
	 *
	 * @return string
	 */
	private static function here() {
		$path = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitised -- run through esc_url_raw below.

		return esc_url_raw( home_url( $path ) );
	}

	/**
	 * Take the POST, store the address, and send the reader back.
	 */
	public static function receive() {
		$back = isset( $_POST['cavo_from'] ) ? esc_url_raw( wp_unslash( $_POST['cavo_from'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked immediately below.
		$back = wp_validate_redirect( $back, home_url( '/' ) );

		if ( ! isset( $_POST['_cavo_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_cavo_nonce'] ) ), self::ACTION ) ) {
			self::go( $back, 'expired', '' );
		}

		// Cheapest first: the trap costs nothing and the reader pays for none of it.
		$trapped = isset( $_POST[ self::TRAP ] ) && '' !== trim( (string) wp_unslash( $_POST[ self::TRAP ] ) );
		$drawn   = isset( $_POST[ self::DRAWN ] ) ? absint( wp_unslash( $_POST[ self::DRAWN ] ) ) : 0;
		$waited  = time() - $drawn;

		if ( $trapped || $waited < self::TOO_FAST ) {
			// Say the same thing a success says: a robot learns nothing from it.
			self::go( $back, 'ok', '' );
		}

		if ( 0 === $drawn || $waited > self::TOO_OLD ) {
			self::go( $back, 'expired', '' );
		}

		$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';

		if ( ! is_email( $email ) ) {
			self::go( $back, 'invalid', $email );
		}

		$slug   = isset( $_POST['form'] ) ? sanitize_key( wp_unslash( $_POST['form'] ) ) : 'newsletter';
		$slug   = Forms::exists( $slug ) ? $slug : 'newsletter';
		$status = Settings::confirms() ? 'pending' : 'confirmed';
		$id     = Subscribers::add( $email, $slug, $status );

		if ( is_wp_error( $id ) ) {
			self::go( $back, 'invalid', $email );
		}

		self::go( $back, 'pending' === $status ? 'confirm' : 'ok', '' );
	}

	/**
	 * Store the answer, redirect past the POST, and stop.
	 *
	 * The address is handed back through the store rather than the address bar:
	 * what somebody typed is theirs, and a query string is written down by
	 * every proxy between here and them.
	 *
	 * @param string $back  Where the reader was.
	 * @param string $state What to tell them.
	 * @param string $email What to put back in the field.
	 */
	private static function go( $back, $state, $email ) {
		$key = wp_generate_password( 12, false );

		set_transient(
			'cavo_signup_' . $key,
			array(
				'state' => $state,
				'email' => $email,
			),
			5 * MINUTE_IN_SECONDS
		);

		wp_safe_redirect( add_query_arg( self::RESULT, $key, $back ) );
		exit;
	}

	/**
	 * The stored answer for this request, read once and then gone.
	 *
	 * @return array
	 */
	public static function result() {
		$key = isset( $_GET[ self::RESULT ] ) ? sanitize_key( wp_unslash( $_GET[ self::RESULT ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a key naming this reader's own stored answer.

		if ( '' === $key ) {
			return array();
		}

		$stored = get_transient( 'cavo_signup_' . $key );

		return is_array( $stored ) ? $stored : array();
	}

	/**
	 * Follow a confirm or unsubscribe link.
	 */
	public static function follow_link() {
		foreach ( array( 'cavo_confirm' => 'confirmed', 'cavo_unsub' => 'unsubscribed' ) as $arg => $status ) {
			if ( ! isset( $_GET[ $arg ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the token in the link is the proof.
				continue;
			}

			$token = sanitize_text_field( wp_unslash( $_GET[ $arg ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the token in the link is the proof.
			$id    = Subscribers::by_token( $token );

			if ( 0 !== $id ) {
				Subscribers::set_status( $id, $status );
			}

			// Whether the token was known or not, the same page comes back: a
			// link that says which addresses exist is a way of asking.
			$key = wp_generate_password( 12, false );

			set_transient(
				'cavo_signup_' . $key,
				array(
					'state' => 'confirmed' === $status ? 'welcome' : 'gone',
					'email' => '',
				),
				5 * MINUTE_IN_SECONDS
			);

			wp_safe_redirect( add_query_arg( self::RESULT, $key, home_url( '/' ) ) );
			exit;
		}
	}
}
