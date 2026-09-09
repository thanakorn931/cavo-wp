<?php
/**
 * Event, what has been — one section of the design.
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
 * The Event page's list of what has been.
 */
class Event_Past_Events extends Event_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'event-past-events';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Event — Past Events', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'event', 'past', 'archive', 'list' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'     => esc_html__( 'Past Events', 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'See All Past Events', 'custom-elementor-widgets' ),
			'action_text' => esc_html__( 'Reserve a Table', 'custom-elementor-widgets' ),
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
	 * What has already been.
	 *
	 * @return string
	 */
	protected function shows() {
		return 'past';
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
			'action_text',
			array(
				'label'       => esc_html__( 'Link text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['action_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'action_link', esc_html__( 'Link', 'custom-elementor-widgets' ) );

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

		$this->add_control(
			'shown',
			array(
				'label'       => esc_html__( 'How many stand before the button is pressed', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'default'     => 3,
				'description' => esc_html__( 'The button then brings the rest on screen, all of them at once.', 'custom-elementor-widgets' ),
			)
		);

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
			'text_color',
			array(
				'label'     => esc_html__( 'Heading, facts and title', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-event-past' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#454545',
				'selectors' => array(
					'{{WRAPPER}} .custom-event-card__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-event-past__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
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
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
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
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
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
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'action_typography',
				'label'          => esc_html__( 'Link and button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-event-past__action, {{WRAPPER}} .custom-event-more',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				),
			)
		);

		$this->add_control(
			'action_color',
			array(
				'label'     => esc_html__( 'Link', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-event-past__action' => 'color: {{VALUE}}; border: 1px solid {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-event-more' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-event-more' => 'background-color: {{VALUE}};',
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

		$items = $this->items( $settings );
		$shown = isset( $settings['shown'] ) ? max( 1, (int) $settings['shown'] ) : 3;
		$tag   = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag   = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-event-past" data-event-feed>
			<div class="custom-event-past__head">
				<<?php echo esc_attr( $tag ); ?> class="custom-event-past__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>

				<a class="custom-event-past__action"<?php
					echo $this->link_from( $settings, 'action_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $this->text( $settings, 'action_text' ) ); ?></a>
			</div>

			<?php if ( ! empty( $items ) ) : ?>
				<div class="custom-event-past__grid">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php $this->render_card( $item, $index >= $shown ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( count( $items ) > $shown ) : ?>
				<div class="custom-event-past__actions">
					<?php $this->render_more_button( $this->text( $settings, 'button_text' ), 0 ); ?>
				</div>
			<?php endif; ?>

			<?php
			if ( empty( $items ) ) {
				$this->editor_hint( __( 'This section is waiting for its source, on the Content tab.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}
}
