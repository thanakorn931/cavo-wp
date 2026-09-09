<?php
/**
 * Event, what is coming — one section of the design.
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
 * The Event page's list of what is coming.
 */
class Event_Events extends Event_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'event-events';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Event — Events', 'custom-elementor-widgets' );
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
		return array( 'event', 'events', 'coming', 'list' );
	}

	/**
	 * What is still to come.
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

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'See More', 'custom-elementor-widgets' ),
			)
		);

		$this->add_responsive_control(
			'step',
			array(
				'label'          => esc_html__( 'How many stand at a time', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'default'        => 6,
				'tablet_default' => 4,
				'mobile_default' => 3,
				'description'    => esc_html__( 'Beneath the newest, before the button is pressed, and again with each press. The design shows two rows of three; on a tablet, where the newest stands with the rest, four in all, and on a phone three.', 'custom-elementor-widgets' ),
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
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-event-list__band' => 'color: {{VALUE}};',
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
						'tablet_default' => array( 'unit' => 'px', 'size' => 13 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 13 ),
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
						'tablet_default' => array( 'unit' => 'px', 'size' => 17 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 17 ),
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
						'tablet_default' => array( 'unit' => 'px', 'size' => 17 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 17 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-event-more',
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
				'default'   => '#121212',
				'separator' => 'before',
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
				'default'   => '#FAF6EA',
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

		// The first item runs the width of the band, as the design draws it. It
		// is the same list, not a slot of its own.
		$items = $this->items( $settings );
		$lead  = ! empty( $items ) ? array_shift( $items ) : null;

		$steps   = $this->per_tier( $settings, 'step', array( 'desktop' => 6, 'tablet' => 4, 'mobile' => 3 ) );
		$step    = $steps['desktop'];
		$button  = isset( $settings['button_text'] ) ? trim( (string) $settings['button_text'] ) : '';
		$button  = '' !== $button ? $button : esc_html__( 'See More', 'custom-elementor-widgets' );
		// The button stands whenever any screen would have something left to
		// bring; which screen this is, and so whether it has, is settled there.
		$waiting = count( $items ) > min( $steps );
		?>
		<div class="custom-event-list" data-event-feed<?php $this->tier_attributes( 'shown', $steps ); ?><?php $this->tier_attributes( 'step', $steps ); ?>>
			<div class="custom-event-list__band">
				<?php if ( $lead ) : ?>
					<?php $this->render_lead( $lead ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $items ) ) : ?>
					<div class="custom-event-list__grid">
						<?php foreach ( $items as $index => $item ) : ?>
							<?php $this->render_card( $item, $index >= $step ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $waiting ) : ?>
					<div class="custom-event-list__actions">
						<?php $this->render_more_button( $button, $step ); ?>
					</div>
				<?php endif; ?>

				<?php
				if ( empty( $items ) && ! $lead ) {
					$this->editor_hint( __( 'This section is waiting for its source, on the Content tab.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<span class="custom-event-list__strip" aria-hidden="true"></span>
		</div>
		<?php
	}

	/**
	 * The newest item, across the width of the band.
	 *
	 * @param \WP_Post $post The item.
	 */
	private function render_lead( $post ) {
		$picture = get_the_post_thumbnail_url( $post, 'full' );
		?>
		<div class="custom-event-list__lead">
			<span class="custom-event-list__lead-picture" aria-hidden="true">
				<?php $this->media( $picture ); ?>
				<span class="custom-event-card__badge" aria-hidden="true"></span>
			</span>

			<span class="custom-event-list__lead-veil" aria-hidden="true"></span>

			<div class="custom-event-card__words">
				<?php $this->render_meta( $post ); ?>

				<h3 class="custom-event-card__title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>

				<p class="custom-event-card__body"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
			</div>
		</div>
		<?php
	}
}
