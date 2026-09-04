<?php
/**
 * Nightlife, VIP package — one section of the design.
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
 * The Nightlife page's VIP package.
 */
class Nightlife_Membership extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nightlife-membership';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Nightlife — Membership', 'custom-elementor-widgets' );
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
		return array( 'nightlife', 'membership', 'vip', 'panels' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'     => esc_html__( 'VIP package', 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'Become a membership', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * One control's text: what the client typed, or what the design settles.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @return string
	 */
	protected function text( $settings, $key ) {
		$typed = isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';

		if ( '' !== $typed ) {
			return $typed;
		}

		$design = $this->design_text();

		return isset( $design[ $key ] ) ? $design[ $key ] : '';
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$design = $this->design_text();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Behind the section', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['heading'],
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => esc_html__( 'Heading level', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_panels',
			array(
				'label' => esc_html__( 'Panels', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'panel_picture',
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
				'placeholder' => $design['button_text'],
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

		$this->register_style_controls();
	}

	/**
	 * Style → Section.
	 */
	private function register_style_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-membership__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-membership__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'veil_color',
			array(
				'label'     => esc_html__( 'Over the section', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.33)',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-membership__veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'panel_veil_color',
			array(
				'label'     => esc_html__( 'Over the narrow panels', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-membership__panel-veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-membership__button',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-membership__button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-nightlife-membership__button' => 'background-color: {{VALUE}};',
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

		$picture = isset( $settings['picture']['url'] ) ? $settings['picture']['url'] : '';
		$tag     = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag     = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-nightlife-membership">
			<span class="custom-nightlife-membership__picture" aria-hidden="true"><?php $this->media( $picture ); ?></span>
			<span class="custom-nightlife-membership__veil" aria-hidden="true"></span>

			<<?php echo esc_attr( $tag ); ?> class="custom-nightlife-membership__heading"><?php
				echo esc_html( $this->text( $settings, 'heading' ) );
			?></<?php echo esc_attr( $tag ); ?>>

			<div class="custom-nightlife-membership__panels">
				<div class="custom-nightlife-membership__panel custom-nightlife-membership__panel--wide">
					<span class="custom-nightlife-membership__panel-picture" aria-hidden="true"><?php
						$this->media( isset( $settings['panel_picture']['url'] ) ? $settings['panel_picture']['url'] : '' );
					?></span>

					<a class="custom-nightlife-membership__button"<?php
						echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>><?php echo esc_html( $this->text( $settings, 'button_text' ) ); ?></a>
				</div>

				<?php foreach ( array( 'one', 'two' ) as $which ) : ?>
					<a
						class="custom-nightlife-membership__panel custom-nightlife-membership__panel--side"
						<?php
						echo $this->link_from( $settings, 'side_' . $which . '_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
						?>
					>
						<span class="custom-nightlife-membership__panel-picture" aria-hidden="true"><?php
							$this->media( isset( $settings[ 'side_' . $which . '_picture' ]['url'] ) ? $settings[ 'side_' . $which . '_picture' ]['url'] : '' );
						?></span>
						<span class="custom-nightlife-membership__panel-veil" aria-hidden="true"></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
