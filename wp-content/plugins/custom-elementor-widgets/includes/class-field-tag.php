<?php
/**
 * A field of the post being shown, offered to any control that takes one.
 *
 * Elementor's own tags are its paid plugin's; the engine that runs them is not.
 * Registering one here is what lets a control be pointed at a field rather than
 * typed into, without the project owing anything to a licence.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One field of whatever post is being rendered.
 */
final class Field_Tag extends Tag {

	/**
	 * How Elementor stores it.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'custom-field';
	}

	/**
	 * How the client reads it.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Field', 'custom-elementor-widgets' );
	}

	/**
	 * Which heading it sits under in the panel.
	 *
	 * @return string
	 */
	public function get_group() {
		return Widgets_Loader::CATEGORY;
	}

	/**
	 * Which controls may be pointed at it.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array(
			Module::TEXT_CATEGORY,
			Module::POST_META_CATEGORY,
			Module::DATETIME_CATEGORY,
		);
	}

	/**
	 * Which field, chosen rather than typed.
	 */
	protected function register_controls() {
		$this->add_control(
			'key',
			array(
				'label'   => esc_html__( 'Field', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'options' => Base_Widget::field_options(),
			)
		);
	}

	/**
	 * What the field holds on the post being rendered.
	 */
	public function render() {
		$key = (string) $this->get_settings( 'key' );

		if ( '' === $key ) {
			return;
		}

		$value = self::value( $key );

		if ( '' === $value ) {
			return;
		}

		echo wp_kses_post( $value );
	}

	/**
	 * One field of the current post, however it is stored.
	 *
	 * A field holding more than a word — a repeater, a relationship — has no
	 * single reading, so it is left to the section that knows its shape.
	 *
	 * @param string $key The field.
	 * @return string
	 */
	private static function value( $key ) {
		$id = get_the_ID();

		if ( ! $id ) {
			return '';
		}

		$value = function_exists( 'get_field' ) ? get_field( $key, $id ) : get_post_meta( $id, $key, true );

		if ( is_bool( $value ) ) {
			return $value ? '1' : '';
		}

		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = trim( (string) $value );

		return '' === $value ? '' : self::as_moment( $key, $id, $value );
	}

	/**
	 * A day answered as a moment rather than as the words it is shown in.
	 *
	 * A field the client reads as a date is also a field a section counts by,
	 * and words in whatever order the client chose them cannot be compared. Only
	 * a field that holds a day is turned; everything else is left as it is.
	 *
	 * @param string $key   The field.
	 * @param int    $id    The post it is on.
	 * @param string $value What the field says.
	 * @return string
	 */
	private static function as_moment( $key, $id, $value ) {
		if ( ! function_exists( 'get_field_object' ) ) {
			return $value;
		}

		$field = get_field_object( $key, $id );

		if ( ! is_array( $field ) || ! isset( $field['type'] ) ) {
			return $value;
		}

		if ( ! in_array( $field['type'], array( 'date_picker', 'date_time_picker' ), true ) ) {
			return $value;
		}

		$when = strtotime( $value );

		return $when ? gmdate( 'Y-m-d H:i:s', $when ) : $value;
	}
}
