<?php
/**
 * Plugin Name: Custom Elementor Widgets
 * Description: The Custom project's Elementor widgets — one widget per section of the design.
 * Version:     1.0.0
 * Author:      Yes
 * Author URI:  https://yeswebdesignstudio.com/
 * Text Domain: custom-elementor
 * Requires PHP: 7.4
 *
 * @package Custom_Elementor
 */

namespace Custom_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CUSTOM_ELEMENTOR_VERSION', '1.0.0' );
define( 'CUSTOM_ELEMENTOR_FILE', __FILE__ );
define( 'CUSTOM_ELEMENTOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'CUSTOM_ELEMENTOR_URL', plugin_dir_url( __FILE__ ) );

/**
 * The bootstrap: constants above, then nothing until `plugins_loaded`.
 *
 * On `plugins_loaded` it checks that Elementor has loaded, that Elementor is
 * new enough and that PHP is. Failing any of the three it prints an admin
 * notice and stops — the base class the widgets extend does not exist until
 * Elementor is there, so activating without this gate would be fatal rather
 * than merely inert. Passing, it hands over on `elementor/init`.
 */
final class Bootstrap {

	/**
	 * Elementor 3.5.0 is the version that introduced `elementor/widgets/register`
	 * and `$widgets_manager->register()`, which is what the loader calls.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

	/**
	 * The PHP the project requires.
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Nothing happens until `plugins_loaded`.
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'boot' ) );
	}

	/**
	 * The compatibility gate, then the hand-over.
	 */
	public static function boot() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'notice_missing_elementor' ) );
			return;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'notice_old_elementor' ) );
			return;
		}

		if ( ! version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'notice_old_php' ) );
			return;
		}

		add_action( 'elementor/init', array( __CLASS__, 'load_widgets' ) );
	}

	/**
	 * Elementor's classes exist from `elementor/init` onwards, and not before.
	 */
	public static function load_widgets() {
		require_once CUSTOM_ELEMENTOR_PATH . 'includes/class-widgets-loader.php';

		Widgets_Loader::instance();
	}

	/**
	 * Elementor is not active.
	 */
	public static function notice_missing_elementor() {
		self::notice(
			sprintf(
				/* translators: 1: this plugin's name, 2: Elementor. */
				esc_html__( '%1$s needs %2$s to be installed and active.', 'custom-elementor' ),
				'<strong>' . esc_html__( 'Custom Elementor Widgets', 'custom-elementor' ) . '</strong>',
				'<strong>' . esc_html__( 'Elementor', 'custom-elementor' ) . '</strong>'
			)
		);
	}

	/**
	 * Elementor is active but too old.
	 */
	public static function notice_old_elementor() {
		self::notice(
			sprintf(
				/* translators: 1: this plugin's name, 2: Elementor, 3: the minimum Elementor version. */
				esc_html__( '%1$s needs %2$s version %3$s or newer.', 'custom-elementor' ),
				'<strong>' . esc_html__( 'Custom Elementor Widgets', 'custom-elementor' ) . '</strong>',
				'<strong>' . esc_html__( 'Elementor', 'custom-elementor' ) . '</strong>',
				esc_html( self::MINIMUM_ELEMENTOR_VERSION )
			)
		);
	}

	/**
	 * PHP is too old.
	 */
	public static function notice_old_php() {
		self::notice(
			sprintf(
				/* translators: 1: this plugin's name, 2: PHP, 3: the minimum PHP version. */
				esc_html__( '%1$s needs %2$s version %3$s or newer.', 'custom-elementor' ),
				'<strong>' . esc_html__( 'Custom Elementor Widgets', 'custom-elementor' ) . '</strong>',
				'<strong>' . esc_html__( 'PHP', 'custom-elementor' ) . '</strong>',
				esc_html( self::MINIMUM_PHP_VERSION )
			)
		);
	}

	/**
	 * Print one admin notice.
	 *
	 * @param string $message Already-escaped message, bold tags allowed.
	 */
	private static function notice( $message ) {
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			wp_kses( $message, array( 'strong' => array() ) )
		);
	}
}

Bootstrap::init();
