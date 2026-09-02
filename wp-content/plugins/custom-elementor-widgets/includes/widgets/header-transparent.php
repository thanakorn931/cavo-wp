<?php
/**
 * Header (transparent) — one section of the design.
 *
 * The design draws the bar two ways and this is one of them. What the two share
 * is in Header_Widget; a page picks one and cannot hold both.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Header_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The transparent header.
 */
class Header_Transparent extends Header_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'header-transparent';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Header (Transparent)', 'custom-elementor-widgets' );
	}

	/**
	 * The class that tells the two bars apart.
	 *
	 * @return string
	 */
	protected function variant_class() {
		return 'custom-header--transparent';
	}

	/**
	 * The background the design gives this bar. Nothing: it shows what is behind it.
	 *
	 * @return string
	 */
	protected function background_default() {
		return '';
	}
}
