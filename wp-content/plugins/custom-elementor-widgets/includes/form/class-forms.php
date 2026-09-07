<?php
/**
 * The forms this site has.
 *
 * Which forms a site has is the build's; what each one asks is the client's.
 * The list here is the first of those, and everything else — a tab, a screen,
 * a setting — is drawn from it rather than written out again beside it.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Form;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The register every form screen reads.
 */
class Forms {

	/**
	 * Where each form's own settings are kept, one row per form.
	 */
	const OPTION = 'cavo_form_settings';

	/**
	 * The forms, in the order their sub-tabs stand.
	 *
	 * `subscribes` is the switch's position before anybody has moved it, not
	 * the answer: the answer is read through `subscribes()`.
	 *
	 * @return array
	 */
	public static function all() {
		return array(
			'contact'    => array(
				'name'       => esc_html__( 'Contact', 'custom-elementor-widgets' ),
				'subscribes' => false,
				'files'      => true,
			),
			'private'    => array(
				'name'       => esc_html__( 'Private event', 'custom-elementor-widgets' ),
				'subscribes' => false,
				'files'      => true,
			),
			'newsletter' => array(
				'name'       => esc_html__( 'Newsletter', 'custom-elementor-widgets' ),
				'subscribes' => true,
				'files'      => false,
			),
		);
	}

	/**
	 * Whether a slug names one of them.
	 *
	 * @param string $slug The form's slug.
	 * @return bool
	 */
	public static function exists( $slug ) {
		$all = self::all();

		return isset( $all[ $slug ] );
	}

	/**
	 * A form's name, as a sub-tab prints it.
	 *
	 * @param string $slug The form's slug.
	 * @return string
	 */
	public static function name( $slug ) {
		$all = self::all();

		return isset( $all[ $slug ]['name'] ) ? $all[ $slug ]['name'] : $slug;
	}

	/**
	 * The slug of whichever form stands first.
	 *
	 * @return string
	 */
	public static function first() {
		$slugs = array_keys( self::all() );

		return reset( $slugs );
	}

	/**
	 * One form's settings, the register's own values standing in for anything
	 * nobody has set.
	 *
	 * @param string $slug The form's slug.
	 * @return array
	 */
	public static function settings( $slug ) {
		$all = self::all();

		if ( ! isset( $all[ $slug ] ) ) {
			return array();
		}

		$stored = get_option( self::OPTION, array() );
		$stored = isset( $stored[ $slug ] ) && is_array( $stored[ $slug ] ) ? $stored[ $slug ] : array();

		return array_merge( $all[ $slug ], $stored );
	}

	/**
	 * Write one form's settings back.
	 *
	 * @param string $slug     The form's slug.
	 * @param array  $settings What to keep.
	 */
	public static function save( $slug, $settings ) {
		if ( ! self::exists( $slug ) ) {
			return;
		}

		$stored          = get_option( self::OPTION, array() );
		$stored          = is_array( $stored ) ? $stored : array();
		$stored[ $slug ] = $settings;

		update_option( self::OPTION, $stored );
	}

	/**
	 * Whether submitting this form puts the address on the list.
	 *
	 * @param string $slug The form's slug.
	 * @return bool
	 */
	public static function subscribes( $slug ) {
		$settings = self::settings( $slug );

		return ! empty( $settings['subscribes'] );
	}

	/**
	 * Whether submitting this form also files a message in the inbox.
	 *
	 * A sign-up is not an enquiry: a list of them would bury the enquiries the
	 * inbox exists for.
	 *
	 * @param string $slug The form's slug.
	 * @return bool
	 */
	public static function files_message( $slug ) {
		$settings = self::settings( $slug );

		return ! empty( $settings['files'] );
	}
}
