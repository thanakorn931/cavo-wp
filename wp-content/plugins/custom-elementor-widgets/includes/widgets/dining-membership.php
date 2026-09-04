<?php
/**
 * Dining, membership — one section of the design.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Dining page's membership band.
 */
class Dining_Membership extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dining-membership';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dining — Membership', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image-before-after';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'dining', 'membership', 'panels' );
	}

	/**
	 * The Content tab and the Style tab.
	 *
	 * The design draws three panels and not a fourth, so each is its own pair
	 * of controls rather than a repeater.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Panels', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Wide panel picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Become a membership', 'custom-elementor-widgets' ),
			)
		);

		$this->add_link_controls( $this, 'button_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

		foreach ( array( 'one', 'two' ) as $index => $which ) {
			$this->add_control(
				'side_' . $which . '_picture',
				array(
					'label'     => sprintf(
						/* translators: %d: which of the two narrow panels. */
						esc_html__( 'Narrow panel %d picture', 'custom-elementor-widgets' ),
						$index + 1
					),
					'type'      => Controls_Manager::MEDIA,
					'separator' => 'before',
				)
			);

			$this->add_link_controls(
				$this,
				'side_' . $which . '_link',
				sprintf(
					/* translators: %d: which of the two narrow panels. */
					esc_html__( 'Narrow panel %d link', 'custom-elementor-widgets' ),
					$index + 1
				)
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'veil_color',
			array(
				'label'     => esc_html__( 'Over the narrow panels', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-membership__veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-membership__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-membership__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-membership__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print the section.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$button = isset( $settings['button_text'] ) ? trim( (string) $settings['button_text'] ) : '';
		$button = '' !== $button ? $button : esc_html__( 'Become a membership', 'custom-elementor-widgets' );
		?>
		<div class="custom-dining-membership">
			<div class="custom-dining-membership__panel custom-dining-membership__panel--wide">
				<?php $this->render_picture( $settings, 'picture' ); ?>

				<a class="custom-dining-membership__button"<?php
					echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $button ); ?></a>
			</div>

			<?php foreach ( array( 'one', 'two' ) as $which ) : ?>
				<a
					class="custom-dining-membership__panel custom-dining-membership__panel--side"
					<?php
					echo $this->link_from( $settings, 'side_' . $which . '_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>
				>
					<?php $this->render_picture( $settings, 'side_' . $which . '_picture' ); ?>
					<span class="custom-dining-membership__veil" aria-hidden="true"></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * One picture slot — a box first, whether or not a picture is in it.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 */
	private function render_picture( $settings, $key ) {
		$url = isset( $settings[ $key ]['url'] ) ? $settings[ $key ]['url'] : '';
		?>
		<span class="custom-dining-membership__picture" aria-hidden="true">
			<?php if ( '' !== $url ) : ?>
				<img src="<?php echo esc_url( $url ); ?>" alt="" />
			<?php endif; ?>
		</span>
		<?php
	}
}
