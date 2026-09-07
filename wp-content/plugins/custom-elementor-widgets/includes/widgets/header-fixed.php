<?php
/**
 * Header, fixed — one section of the design.
 *
 * The bar as the design draws it, from the first pixel and for as long as the
 * page is open. It stands out of the flow, so it hands back the height it took
 * rather than asking the section under it to leave room.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Header_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The bar that does not change.
 */
class Header_Fixed extends Header_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'header-fixed';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Header - fixed', 'custom-elementor-widgets' );
	}

	/**
	 * The class that tells the two bars apart.
	 *
	 * @return string
	 */
	protected function variant_class() {
		return 'custom-header--fixed';
	}

	/**
	 * Nothing stands under this bar, so the band gives its height back.
	 *
	 * @return bool
	 */
	protected function has_spacer() {
		return true;
	}
}
