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
			'heading'          => esc_html__( 'Upcoming Events', 'custom-elementor-widgets' ),
			'button_text'      => esc_html__( 'See Upcoming Events', 'custom-elementor-widgets' ),
			'past_heading'     => esc_html__( 'Past Events', 'custom-elementor-widgets' ),
			'past_button_text' => esc_html__( 'See More Past Events', 'custom-elementor-widgets' ),
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
	 * Source → the item's own facts, and its two ways to a ticket.
	 */
	protected function register_more_source_controls() {
		parent::register_more_source_controls();
		$this->register_ticket_source_controls();
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

		$this->register_ticket_text_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_past',
			array(
				'label' => esc_html__( 'When nothing is coming', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'past_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'With nothing coming, the section shows what has been instead, from the same source, as the Past events section draws it, without its Reserve a Table link. With nothing on either side it is left out of the page.', 'custom-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'past_heading',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['past_heading'],
			)
		);

		$this->add_control(
			'past_button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['past_button_text'],
			)
		);

		$this->add_responsive_control(
			'past_shown',
			array(
				'label'          => esc_html__( 'How many stand before the button is pressed', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'default'        => 3,
				'tablet_default' => 2,
				'mobile_default' => 2,
			)
		);

		$this->add_responsive_control(
			'past_step',
			array(
				'label'          => esc_html__( 'How many more each press brings', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 0,
				'default'        => 6,
				'tablet_default' => 4,
				'mobile_default' => 4,
				'description'    => esc_html__( 'Nought brings the rest at once.', 'custom-elementor-widgets' ),
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
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-event__heading' => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-event-past__heading'      => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-event__heading, {{WRAPPER}} .custom-event-past__heading',
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
					'{{WRAPPER}} .custom-event-past'      => 'color: {{VALUE}};',
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
				'selector'       => '{{WRAPPER}} .custom-nightlife-event__button, {{WRAPPER}} .custom-event-more',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 14 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 12 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 12 ),
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
					'{{WRAPPER}} .custom-event-more'              => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-event-more'              => 'background-color: {{VALUE}};',
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

		// With nothing coming, what has been stands instead, from the same
		// source, the latest first; with nothing on either side the section is
		// left out of the page.
		if ( empty( $items ) ) {
			$past = $this->items( $settings, 'past', -1 );

			if ( ! empty( $past ) ) {
				$this->render_past( $settings, $past, $tag );

				return;
			}

			if ( ! $this->is_editing() ) {
				return;
			}
		}
		?>
		<div class="custom-nightlife-event">
			<span class="custom-nightlife-event__mark" aria-hidden="true"></span>

			<<?php echo esc_attr( $tag ); ?> class="custom-nightlife-event__heading"><?php
				echo esc_html( $this->text( $settings, 'heading' ) );
			?></<?php echo esc_attr( $tag ); ?>>

			<?php if ( ! empty( $items ) ) : ?>
				<div class="custom-nightlife-event__grid">
					<?php foreach ( $items as $item ) : ?>
						<?php $this->render_card( $item, false, false, $this->card_actions( $settings, $item ) ); ?>
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

	/**
	 * What has been, drawn as the Past events section draws it, without its
	 * Reserve a Table link: the heading, the cards three to a row, and the
	 * button that brings the rest on screen.
	 *
	 * @param array      $settings The widget's settings.
	 * @param \WP_Post[] $items    What has been, the latest first.
	 * @param string     $tag      The heading's element.
	 */
	private function render_past( $settings, $items, $tag ) {
		$shows = $this->per_tier( $settings, 'past_shown', array( 'desktop' => 3, 'tablet' => 2, 'mobile' => 2 ) );
		$steps = $this->per_tier( $settings, 'past_step', array( 'desktop' => 6, 'tablet' => 4, 'mobile' => 4 ), 0 );
		$shown = $shows['desktop'];
		?>
		<div class="custom-event-past custom-nightlife-event__past" data-feed<?php $this->tier_attributes( 'shown', $shows ); ?><?php $this->tier_attributes( 'step', $steps ); ?>>
			<div class="custom-event-past__head">
				<<?php echo esc_attr( $tag ); ?> class="custom-event-past__heading"><?php
					echo esc_html( $this->text( $settings, 'past_heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>
			</div>

			<div class="custom-event-past__grid">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php $this->render_card( $item, $index >= $shown ); ?>
				<?php endforeach; ?>
			</div>

			<?php if ( count( $items ) > min( $shows ) ) : ?>
				<div class="custom-event-past__actions">
					<?php $this->render_more_button( $this->text( $settings, 'past_button_text' ), $steps['desktop'] ); ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * The Past events section's stylesheet is this one's too, for what has
	 * been.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		$past = \Custom_Elementor_Widgets\Widgets_Loader::HANDLE_PREFIX . 'event-past-events';

		return array_merge( parent::get_style_depends(), wp_style_is( $past, 'registered' ) ? array( $past ) : array() );
	}
}
