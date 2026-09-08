<?php
/**
 * Home, events — one section of the design.
 *
 * What is coming, drawn from whatever list the section is pointed at, and run
 * off the right edge of the band a card at a time. The mark on each card is the
 * build's rather than the client's.
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
 * The Home page's events.
 */
class Home_Event extends Base_Widget {

	/**
	 * How many the section draws at most.
	 */
	const HOW_MANY = 12;

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'home-event';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Home — Event', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-carousel';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'home', 'events', 'upcoming', 'carousel' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'   => esc_html__( 'Upcoming events', 'custom-elementor-widgets' ),
			'more_text' => esc_html__( 'Discover More', 'custom-elementor-widgets' ),
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

		$this->register_source_controls();

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
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 5,
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
			'more_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['more_text'],
				'separator'   => 'before',
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
			'ground_near',
			array(
				'label'     => esc_html__( 'Background, lighter', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9D5434',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event' => '--custom-home-event-near: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ground_far',
			array(
				'label'     => esc_html__( 'Background, deeper', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#29180E',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event' => '--custom-home-event-far: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EBE4CA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-event__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'words_color',
			array(
				'label'     => esc_html__( 'Card text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F5E9E4',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__words' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'words_typography',
				'label'          => esc_html__( 'Card text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-event__meta, {{WRAPPER}} .custom-home-event__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
				),
			)
		);

		$this->add_control(
			'more_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4C2513',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'more_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F5E9E4',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__more' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'more_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-event__more',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F5E9E4',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__arrow' => 'color: {{VALUE}};',
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

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';

		$items = get_posts(
			$this->source_query(
				$settings,
				array(
					'posts_per_page'      => self::HOW_MANY,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			)
		);
		?>
		<div class="custom-home-event">
			<div class="custom-home-event__aside">
				<<?php echo esc_attr( $tag ); ?> class="custom-home-event__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>

				<?php if ( count( $items ) > 1 ) : ?>
					<div class="custom-home-event__arrows">
						<?php
						$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
						$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
						?>
					</div>
				<?php endif; ?>
			</div>

			<div class="custom-home-event__rail">
				<div class="custom-home-event__track">
					<?php foreach ( $items as $item ) : ?>
						<?php $this->render_item( $settings, $item ); ?>
					<?php endforeach; ?>
				</div>
			</div>

			<?php
			if ( empty( $items ) ) {
				$this->editor_hint( __( 'Nothing is coming up yet: the cards are whatever the list on the Source tab holds.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}

	/**
	 * One item: the card, then what is known about it.
	 *
	 * @param array    $settings The widget's settings.
	 * @param \WP_Post $item     The post.
	 */
	private function render_item( $settings, $item ) {
		$more = $this->text( $settings, 'more_text' );
		$meta = $this->meta( $item );
		?>
		<div class="custom-home-event__item">
			<a class="custom-home-event__card" href="<?php echo esc_url( (string) get_permalink( $item ) ); ?>">
				<span class="custom-home-event__picture">
					<?php $this->media( get_the_post_thumbnail_url( $item, 'large' ), '', true ); ?>
				</span>
				<span class="custom-home-event__mark" aria-hidden="true"></span>
			</a>

			<div class="custom-home-event__words">
				<div class="custom-home-event__lines">
					<?php if ( '' !== $meta ) : ?>
						<p class="custom-home-event__meta"><?php echo esc_html( $meta ); ?></p>
					<?php endif; ?>

					<p class="custom-home-event__title"><?php echo esc_html( get_the_title( $item ) ); ?></p>
				</div>

				<?php if ( '' !== $more ) : ?>
					<a class="custom-home-event__more" href="<?php echo esc_url( (string) get_permalink( $item ) ); ?>"><?php
						echo esc_html( $more );
					?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * When an item is, as the design writes it: the hour, then the day. Both
	 * are when the post is published, which is the one place an event's time
	 * is set.
	 *
	 * @param \WP_Post $item The post.
	 * @return string
	 */
	private function meta( $item ) {
		$hour = (string) get_the_time( 'h : i A', $item );
		$day  = (string) get_the_date( 'd M Y', $item );

		return trim( '' !== $hour ? $hour . ' - ' . $day : $day );
	}

	/**
	 * One arrow.
	 *
	 * @param string $way   Which way it goes.
	 * @param string $label What a reader who cannot see it is told.
	 */
	private function render_arrow( $way, $label ) {
		$path = 'prev' === $way ? 'M34 20L24 28L34 36' : 'M24 20L34 28L24 36';

		printf(
			'<button class="custom-home-event__arrow custom-home-event__arrow--%1$s" type="button" aria-label="%2$s"><svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><circle cx="28" cy="28" r="27" stroke="currentColor" stroke-width="1.5"/><path d="%3$s" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
			esc_attr( $way ),
			esc_attr( $label ),
			esc_attr( $path )
		);
	}
}
