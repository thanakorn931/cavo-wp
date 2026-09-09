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
			'arrow_disc',
			array(
				'label'     => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__arrow:not( [disabled] )'       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-home-event__arrow:not( [disabled] ):hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_mark',
			array(
				'label'     => esc_html__( 'Arrow mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-event__arrow:not( [disabled] ) svg' => 'fill: {{VALUE}};',
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

		$items = $this->items( $settings );
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
	 * The nights still to come, in the order of the day each falls on.
	 *
	 * The day is the event's own, so neither the order nor what is left out can
	 * be read off the query — the source is asked for its list and the list is
	 * made from what came back. A day itself is still to come, since it has not
	 * finished while it is being read.
	 *
	 * @param array $settings The widget's settings.
	 * @return array
	 */
	private function items( $settings ) {
		$posts = get_posts(
			$this->source_query(
				$settings,
				array(
					'posts_per_page'      => 100,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			)
		);

		$today = (int) strtotime( 'today', current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- the client's day, not UTC's.

		$coming = array();

		foreach ( $posts as $place => $post ) {
			$when = $this->item_starts( $post );

			if ( $when < $today ) {
				continue;
			}

			$coming[] = array(
				'when'  => $when,
				'place' => $place,
				'post'  => $post,
			);
		}

		$way = isset( $settings['order'] ) && 'ASC' === $settings['order'] ? 1 : -1;

		usort(
			$coming,
			function ( $a, $b ) use ( $way ) {
				if ( $a['when'] === $b['when'] ) {
					return $a['place'] - $b['place'];
				}

				return $a['when'] < $b['when'] ? -$way : $way;
			}
		);

		return array_slice( wp_list_pluck( $coming, 'post' ), 0, self::HOW_MANY );
	}

	/**
	 * When an item is, as a moment.
	 *
	 * The day the client set for it. A day they have not set is the day the post
	 * appeared, so an item is never left without a place in the order.
	 *
	 * @param \WP_Post $post The item.
	 * @return int
	 */
	private function item_starts( $post ) {
		$fact = $this->item_settings( $post );
		$date = isset( $fact['date'] ) ? trim( (string) $fact['date'] ) : '';
		$when = '' !== $date ? strtotime( $date ) : false;

		return (int) ( $when ? $when : strtotime( $post->post_date ) );
	}

	/**
	 * One item: the card, then what is known about it.
	 *
	 * @param array    $settings The widget's settings.
	 * @param \WP_Post $item     The post.
	 */
	private function render_item( $settings, $item ) {
		$meta = $this->meta( $item );

		// Read as this item: pointed at a field, the words on the button and the
		// place it leads are each card's own. Typed instead, every card carries
		// what the section was given.
		$fact  = $this->item_settings( $item );
		$more  = $this->text( $fact, 'more_text' );
		$leads = isset( $fact['card_link'] ) ? trim( (string) $fact['card_link'] ) : '';
		$where = $this->link_from( $fact, 'card_link' );
		?>
		<div class="custom-home-event__item">
			<?php if ( '' !== $leads ) : ?>
				<a class="custom-home-event__card"<?php
					echo $where; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>>
					<span class="custom-home-event__picture">
						<?php $this->media( get_the_post_thumbnail_url( $item, 'large' ), '', true ); ?>
					</span>
					<span class="custom-home-event__mark" aria-hidden="true"></span>
				</a>
			<?php else : ?>
				<span class="custom-home-event__card">
					<span class="custom-home-event__picture">
						<?php $this->media( get_the_post_thumbnail_url( $item, 'large' ), '', true ); ?>
					</span>
					<span class="custom-home-event__mark" aria-hidden="true"></span>
				</span>
			<?php endif; ?>

			<div class="custom-home-event__words">
				<div class="custom-home-event__lines">
					<?php if ( '' !== $meta ) : ?>
						<p class="custom-home-event__meta"><?php echo esc_html( $meta ); ?></p>
					<?php endif; ?>

					<p class="custom-home-event__title"><?php echo esc_html( get_the_title( $item ) ); ?></p>
				</div>

				<?php if ( '' !== $more ) : ?>
					<a class="custom-home-event__more"<?php
						echo $where; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>><?php echo esc_html( $more ); ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * When an item is, as the design writes it: the hour, then the day. Both are
	 * the event's own fields, not when the post was published.
	 *
	 * @param \WP_Post $item The post.
	 * @return string
	 */
	private function meta( $item ) {
		$fact = $this->item_settings( $item );

		$hour = isset( $fact['time'] ) ? $fact['time'] : '';
		$day  = $this->as_day( isset( $fact['date'] ) ? $fact['date'] : '' );

		return trim( '' !== $hour ? $hour . ' - ' . $day : $day );
	}

	/**
	 * The two facts the card states.
	 *
	 * Typed, a fact is the section's and every card states the same thing.
	 * Pointed at a field, it is the item's and each card states its own.
	 */
	protected function register_more_source_controls() {
		$this->add_control(
			'date',
			array(
				'label'          => esc_html__( 'Date', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::DATE_TIME,
				'dynamic'        => array( 'active' => true ),
				'picker_options' => array( 'enableTime' => false ),
			)
		);

		$this->add_control(
			'time',
			array(
				'label'   => esc_html__( 'Time', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$design = $this->design_text();

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

		$this->add_link_controls( $this, 'card_link', esc_html__( 'Button url', 'custom-elementor-widgets' ) );

	}

	/**
	 * One arrow.
	 *
	 * @param string $way   Which way it goes.
	 * @param string $label What a reader who cannot see it is told.
	 */
	private function render_arrow( $way, $label ) {
		?>
		<button
			type="button"
			class="custom-home-event__arrow custom-home-event__arrow--<?php echo esc_attr( $way ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><?php $this->render_arrow_mark( $way ); ?></button>
		<?php
	}
}
