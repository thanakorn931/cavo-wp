<?php
/**
 * Header, scroll — one section of the design.
 *
 * The same bar, laid over whatever opens the page: clear to begin with, and the
 * design itself the moment the page moves. It leaves no height behind, because
 * what it stands over is meant to reach the top of the window.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Header_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The bar that changes as the page moves.
 */
class Header_Scroll extends Header_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'header-scroll';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Header - scroll', 'custom-elementor-widgets' );
	}

	/**
	 * The class that tells the two bars apart.
	 *
	 * @return string
	 */
	protected function variant_class() {
		return 'custom-header--scroll';
	}

	/**
	 * This bar has two states to look at, and so two of every colour.
	 *
	 * @return bool
	 */
	protected function is_scroll() {
		return true;
	}
}
