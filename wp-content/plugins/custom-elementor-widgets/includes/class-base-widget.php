<?php
/**
 * What every widget shares.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The two questions every widget in this plugin answers the same way: which
 * category it belongs to, and which registered style and script handles it
 * depends on.
 *
 * The handles are the widget's own — one per widget, never one for the plugin.
 * Declaring them here rather than enqueuing globally is what makes Elementor
 * load a section's CSS only on the pages that use that section.
 *
 * Name, title, icon, keywords, `register_controls()` and `render()` belong to
 * the widget itself.
 */
abstract class Base_Widget extends \Elementor\Widget_Base {

	/**
	 * The project's own panel category.
	 *
	 * @return array
	 */
	public function get_categories(): array {
		return array( Widgets_Loader::CATEGORY );
	}

	/**
	 * The style handles this widget depends on.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		$handle = $this->asset_handle();

		return wp_style_is( $handle, 'registered' ) ? array( $handle ) : array();
	}

	/**
	 * The script handles this widget depends on.
	 *
	 * @return array
	 */
	public function get_script_depends(): array {
		$handle = $this->asset_handle();

		return wp_script_is( $handle, 'registered' ) ? array( $handle ) : array();
	}

	/**
	 * This widget's own asset handle, taken from its name.
	 *
	 * @return string
	 */
	protected function asset_handle() {
		return Widgets_Loader::HANDLE_PREFIX . $this->get_name();
	}
}
