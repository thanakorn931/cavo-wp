<?php
/**
 * Nightlife, what is coming — one section of the design.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Event_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Nightlife page's three upcoming items.
 */
class Nightlife_Event extends Event_Widget {

	/**
	 * How many the design draws, and all it ever draws: the way to the rest is
	 * the button, not the section.
	 */
	const SHOWN = 3;

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nightlife-event';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Nightlife — Event', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'nightlife', 'event', 'upcoming' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'     => esc_html__( 'Upcoming Events', 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'See Upcoming Events', 'custom-elementor-widgets' ),
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
	 * What is still to come — the section says so above itself.
	 *
	 * @return string
	 */
	protected function shows() {
		return 'coming';
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_source_controls();

		$design = $this->design_text();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
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

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['button_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'button_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

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
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-event__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-event__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 64 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 32 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Facts and title', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-event' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-event-card__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'meta_typography',
				'label'          => esc_html__( 'Facts', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-event-card__meta',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 15 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 15 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-event-card__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 20 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 19 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 19 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-event-card__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 20 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 19 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 19 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-event__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 14 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 9 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 9 ),
					),
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-event__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-event__button' => 'background-color: {{VALUE}};',
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

		$items = array_slice( $this->items( $settings ), 0, self::SHOWN );
		$tag   = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag   = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-nightlife-event">
			<span class="custom-nightlife-event__mark" aria-hidden="true"></span>

			<<?php echo esc_attr( $tag ); ?> class="custom-nightlife-event__heading"><?php
				echo esc_html( $this->text( $settings, 'heading' ) );
			?></<?php echo esc_attr( $tag ); ?>>

			<?php if ( ! empty( $items ) ) : ?>
				<div class="custom-nightlife-event__grid">
					<?php foreach ( $items as $item ) : ?>
						<?php $this->render_card( $item ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="custom-nightlife-event__actions">
				<a class="custom-nightlife-event__button"<?php
					echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $this->text( $settings, 'button_text' ) ); ?></a>
			</div>

			<?php
			if ( empty( $items ) ) {
				$this->editor_hint( __( 'This section is waiting for its source, on the Content tab.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}
}
