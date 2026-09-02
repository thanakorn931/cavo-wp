<?php
/**
 * Category, widgets, assets.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Everything this plugin registers with Elementor and with WordPress is hooked
 * here, so a renamed hook is one edit rather than one per widget.
 */
final class Widgets_Loader {

	/**
	 * The project's own panel category — a bare slug.
	 */
	const CATEGORY = 'custom';

	/**
	 * The prefix every widget's asset handles carry.
	 */
	const HANDLE_PREFIX = 'custom-';

	/**
	 * The single instance.
	 *
	 * @var Widgets_Loader|null
	 */
	private static $instance = null;

	/**
	 * Get the single instance.
	 *
	 * @return Widgets_Loader
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Hook everything up.
	 */
	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'register_assets' ) );

		add_action( 'elementor/editor/after_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * The project's own panel category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor's category manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			self::CATEGORY,
			array(
				'title' => esc_html__( 'Custom', 'custom-elementor-widgets' ),
			)
		);
	}

	/**
	 * One widget per section of the design.
	 *
	 * Each file in includes/widgets/ holds one section's widget, named after the
	 * file in the project's capitalised form: `hero-banner.php` holds
	 * `Custom_Elementor_Widgets\Widgets\Hero_Banner`. No design has been supplied yet,
	 * so the folder is empty and the plugin ships with no widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor's widget manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once CUSTOM_ELEMENTOR_WIDGETS_PATH . 'includes/class-base-widget.php';

		// Whatever the widgets share sits beside the base, outside the register.
		foreach ( (array) glob( CUSTOM_ELEMENTOR_WIDGETS_PATH . 'includes/class-*-widget.php' ) as $shared ) {
			require_once $shared;
		}

		$files = glob( CUSTOM_ELEMENTOR_WIDGETS_PATH . 'includes/widgets/*.php' );

		if ( empty( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			require_once $file;

			$class = __NAMESPACE__ . '\\Widgets\\' . self::class_name_from_file( $file );

			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}

	/**
	 * Turn a widget file name into its class name.
	 *
	 * @param string $file Absolute path to the widget file.
	 * @return string Class name, without the namespace.
	 */
	private static function class_name_from_file( $file ) {
		$slug = basename( $file, '.php' );

		return str_replace( ' ', '_', ucwords( str_replace( '-', ' ', $slug ) ) );
	}

	/**
	 * Register the front-end assets — one style and one script handle per
	 * widget, so Elementor loads a section's CSS only on the pages that use it.
	 *
	 * A widget declares its handle through the base widget; nothing is enqueued
	 * from here. The widgets folder is the register here too: a section named
	 * `header` takes `assets/css/header.css` and `assets/js/header.js` if they
	 * are there, under the handle `custom-header`.
	 */
	public function register_assets() {
		$files = glob( CUSTOM_ELEMENTOR_WIDGETS_PATH . 'includes/widgets/*.php' );

		if ( empty( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			$slug   = basename( $file, '.php' );
			$handle = self::HANDLE_PREFIX . $slug;

			$style = CUSTOM_ELEMENTOR_WIDGETS_PATH . 'assets/css/' . $slug . '.css';

			if ( file_exists( $style ) ) {
				wp_register_style(
					$handle,
					CUSTOM_ELEMENTOR_WIDGETS_URL . 'assets/css/' . $slug . '.css',
					array(),
					(string) filemtime( $style )
				);
			}

			$script = CUSTOM_ELEMENTOR_WIDGETS_PATH . 'assets/js/' . $slug . '.js';

			if ( file_exists( $script ) ) {
				wp_register_script(
					$handle,
					CUSTOM_ELEMENTOR_WIDGETS_URL . 'assets/js/' . $slug . '.js',
					array(),
					(string) filemtime( $script ),
					true
				);
			}
		}
	}

	/**
	 * Enqueue the editor-only assets. There are none yet.
	 */
	public function enqueue_editor_assets() {
	}
}
