<?php
/**
 * The forms and the subscriber list, brought up.
 *
 * None of this needs Elementor: a widget draws a form, but the record of what
 * was said and who asked to hear from the site outlives whatever draws it. So
 * this boots on its own rather than behind the editor's gate.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

use Custom_Elementor_Widgets\Admin\Menu;
use Custom_Elementor_Widgets\Form\Messages;
use Custom_Elementor_Widgets\Form\Signup;
use Custom_Elementor_Widgets\Form\Subscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * What to load, and when.
 */
final class Form_Loader {

	/**
	 * The one instance.
	 *
	 * @var Form_Loader|null
	 */
	private static $instance = null;

	/**
	 * Bring it up once.
	 *
	 * @return Form_Loader
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Require the classes and hook them up.
	 */
	private function __construct() {
		$path = CUSTOM_ELEMENTOR_WIDGETS_PATH . 'includes/';

		require_once $path . 'form/class-forms.php';
		require_once $path . 'form/class-settings.php';
		require_once $path . 'form/class-messages.php';
		require_once $path . 'form/class-subscribers.php';
		require_once $path . 'form/class-signup.php';

		add_action( 'init', array( Messages::class, 'register' ) );
		add_action( 'init', array( Subscribers::class, 'register' ) );

		Signup::listen();

		if ( is_admin() ) {
			require_once $path . 'admin/class-menu.php';
			require_once $path . 'admin/class-subscribers-table.php';
			require_once $path . 'admin/class-subscribers-screen.php';

			Menu::listen();
		}
	}

	/**
	 * One instance, and no waking a second from a stored one.
	 */
	private function __clone() {}

	/**
	 * Nothing here is restored from a string.
	 */
	public function __wakeup() {
		throw new \RuntimeException( 'Form_Loader is not restorable.' );
	}
}
