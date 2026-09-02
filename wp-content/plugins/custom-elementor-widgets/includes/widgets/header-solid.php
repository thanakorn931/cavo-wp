<?php
/**
 * Header (solid) — one section of the design.
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
 * The solid header.
 */
class Header_Solid extends Header_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'header-solid';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Header (Solid)', 'custom-elementor-widgets' );
	}

	/**
	 * The class that tells the two bars apart.
	 *
	 * @return string
	 */
	protected function variant_class() {
		return 'custom-header--solid';
	}

	/**
	 * The background the design gives this bar. Cream 100, as the design fills it.
	 *
	 * @return string
	 */
	protected function background_default() {
		return '#FAF6EA';
	}

	/**
	 * The bar's own colour.
	 *
	 * @return string
	 */
	protected function ink() {
		return '#3A2114';
	}

	/**
	 * What reads against the ink.
	 *
	 * @return string
	 */
	protected function paper() {
		return '#FAF6EA';
	}
}
