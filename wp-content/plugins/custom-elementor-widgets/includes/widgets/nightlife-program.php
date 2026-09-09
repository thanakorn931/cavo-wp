<?php
/**
 * Nightlife, weekly program — one section of the design.
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
 * The Nightlife page's weekly program.
 */
class Nightlife_Program extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nightlife-program';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Nightlife — Program', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-album';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'nightlife', 'program', 'weekly', 'ticket', 'carousel' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
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
				'placeholder' => esc_html__( 'Weekly program', 'custom-elementor-widgets' ),
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

		$this->register_source_controls();

		$this->start_controls_section(
			'section_program',
			array(
				'label' => esc_html__( 'Program', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'ticket_text',
			array(
				'label'   => esc_html__( 'First button text', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_link_controls( $this, 'ticket_link', esc_html__( 'First button link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'vip_text',
			array(
				'label'     => esc_html__( 'Second button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'separator' => 'before',
			)
		);

		$this->add_link_controls( $this, 'vip_link', esc_html__( 'Second button link', 'custom-elementor-widgets' ) );

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
					'{{WRAPPER}} .custom-nightlife-program__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-program__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 64 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 24 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 24 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => esc_html__( 'Ticket', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EBE4CA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-program__card' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-nightlife-program__perforation' => 'background-image: radial-gradient( 5.5px 5px at 5.5px 5px, {{VALUE}} 100%, rgba( 255, 255, 255, 0 ) 100% );',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Ticket text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-program__card' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-program__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 64 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 24 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 24 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'label_typography',
				'label'          => esc_html__( 'Genre and when', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-program__genre, {{WRAPPER}} .custom-nightlife-program__when',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 20 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 12 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 12 ),
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
				'selector'       => '{{WRAPPER}} .custom-nightlife-program__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 18 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 12 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 12 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Buttons', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-program__button',
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
				'label'     => esc_html__( 'First button', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-program__button--solid' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-nightlife-program__button--outline' => 'color: {{VALUE}}; box-shadow: inset 0 0 0 1px {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_ink',
			array(
				'label'     => esc_html__( 'First button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-program__button--solid' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-program__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-program__arrow svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The facts a night's card states.
	 *
	 * Typed, a fact is the section's and every card states the same thing.
	 * Pointed at a field, it is the night's own. The date is a date rather than
	 * words, so what is typed into it is picked from a calendar.
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

		foreach ( array(
			'time_from' => esc_html__( 'Time from', 'custom-elementor-widgets' ),
			'time_to'   => esc_html__( 'Time to', 'custom-elementor-widgets' ),
			'genre'     => esc_html__( 'Genre', 'custom-elementor-widgets' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'   => $label,
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array( 'active' => true ),
				)
			);
		}
	}

	/**
	 * The nights the section shows.
	 *
	 * A weekly program is the week ahead, counted on the night's own date rather
	 * than on when the post appeared: a night set for Friday is written today
	 * and is still Friday's. Which night each is only becomes readable once the
	 * section is standing on it, so the source is asked for its list and the
	 * week is taken out of that list rather than out of the query.
	 *
	 * @param array $settings The widget's settings.
	 * @return array
	 */
	private function nights( $settings ) {
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

		$now  = current_time( 'timestamp' ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested -- the client's day, not UTC's.
		$from = (int) strtotime( 'today', $now );
		$to   = $from + ( 8 * DAY_IN_SECONDS );

		$week = array();

		foreach ( $posts as $post ) {
			$when = $this->night_starts( $post );

			if ( 0 === $when || $when < $from || $when >= $to ) {
				continue;
			}

			$week[] = array(
				'when' => $when,
				'post' => $post,
			);
		}

		usort(
			$week,
			function ( $a, $b ) {
				return $a['when'] - $b['when'];
			}
		);

		if ( isset( $settings['order'] ) && 'DESC' === $settings['order'] ) {
			$week = array_reverse( $week );
		}

		return wp_list_pluck( $week, 'post' );
	}

	/**
	 * The day a night falls on, as a moment rather than as words.
	 *
	 * @param \WP_Post $post The night.
	 * @return int
	 */
	private function night_starts( $post ) {
		$night = $this->item_settings( $post );
		$date  = isset( $night['date'] ) ? trim( (string) $night['date'] ) : '';

		if ( '' === $date ) {
			return 0;
		}

		return (int) strtotime( $date );
	}

	/**
	 * When a night is, as the design writes it: the day, then the hours.
	 *
	 * @param \WP_Post $post  The night.
	 * @param array    $night The settings read as this night.
	 * @return string
	 */
	private function when( $post, $night ) {
		$starts = $this->night_starts( $post );
		$day    = $starts ? date_i18n( 'D', $starts ) : '';

		$from = isset( $night['time_from'] ) ? $night['time_from'] : '';
		$to   = isset( $night['time_to'] ) ? $night['time_to'] : '';

		$hours = trim( '' !== $to ? $from . ' - ' . $to : $from );

		if ( '' === $day ) {
			return $hours;
		}

		return '' !== $hours ? strtoupper( $day ) . ' | ' . $hours : strtoupper( $day );
	}

	/**
	 * Print the section.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$nights  = $this->nights( $settings );
		$heading = isset( $settings['heading'] ) ? trim( (string) $settings['heading'] ) : '';
		$heading = '' !== $heading ? $heading : esc_html__( 'Weekly program', 'custom-elementor-widgets' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-nightlife-program">
			<<?php echo esc_attr( $tag ); ?> class="custom-nightlife-program__heading"><?php
				echo esc_html( $heading );
			?></<?php echo esc_attr( $tag ); ?>>

			<div class="custom-nightlife-program__card">
				<?php foreach ( $nights as $index => $post ) : ?>
					<?php $night = $this->item_settings( $post ); ?>
					<div class="custom-nightlife-program__slide<?php echo 0 === $index ? ' is-current' : ''; ?>">
						<div class="custom-nightlife-program__words">
							<p class="custom-nightlife-program__genre"><?php
								echo esc_html( isset( $night['genre'] ) ? $night['genre'] : '' );
							?></p>

							<h3 class="custom-nightlife-program__title"><?php
								echo esc_html( get_the_title( $post ) );
							?></h3>

							<p class="custom-nightlife-program__when"><?php
								echo esc_html( $this->when( $post, $night ) );
							?></p>

							<p class="custom-nightlife-program__body"><?php
								echo esc_html( get_the_excerpt( $post ) );
							?></p>

							<div class="custom-nightlife-program__actions">
								<a class="custom-nightlife-program__button custom-nightlife-program__button--solid"<?php
									echo $this->link_from( $night, 'ticket_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
								?>><?php
									$ticket = isset( $night['ticket_text'] ) ? trim( (string) $night['ticket_text'] ) : '';
									echo esc_html( '' !== $ticket ? $ticket : __( 'Buy a Ticket', 'custom-elementor-widgets' ) );
								?></a>

								<a class="custom-nightlife-program__button custom-nightlife-program__button--outline"<?php
									echo $this->link_from( $night, 'vip_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
								?>><?php
									$vip = isset( $night['vip_text'] ) ? trim( (string) $night['vip_text'] ) : '';
									echo esc_html( '' !== $vip ? $vip : __( 'Book a VIP Ticket', 'custom-elementor-widgets' ) );
								?></a>
							</div>
						</div>

						<div class="custom-nightlife-program__stamp"><?php
							$this->media( (string) get_the_post_thumbnail_url( $post, 'large' ), get_the_title( $post ) );
						?></div>

						<span class="custom-nightlife-program__perforation" aria-hidden="true"></span>
					</div>
				<?php endforeach; ?>

				<?php
				// An arrow means nothing until there is somewhere else to go.
				if ( count( $nights ) > 1 ) {
					$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
					$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<?php
			if ( empty( $nights ) ) {
				$this->editor_hint( __( 'Nothing in the source is dated in the week ahead.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}

	/**
	 * One of the two arrows — the design's own mark, carried by the widget.
	 *
	 * @param string $side  Which arrow this is.
	 * @param string $label What a reader who cannot see it is told.
	 */
	private function render_arrow( $side, $label ) {
		?>
		<button
			type="button"
			class="custom-nightlife-program__arrow custom-nightlife-program__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><svg viewBox="10 6 56 56" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M50.0008 34.0006C50.0008 34.2658 49.8954 34.5201 49.7079 34.7077C49.5204 34.8952 49.266 35.0006 49.0008 35.0006H29.4145L36.7083 42.2931C36.8012 42.386 36.8749 42.4963 36.9252 42.6177C36.9755 42.7391 37.0013 42.8692 37.0013 43.0006C37.0013 43.132 36.9755 43.2621 36.9252 43.3835C36.8749 43.5048 36.8012 43.6151 36.7083 43.7081C36.6154 43.801 36.5051 43.8747 36.3837 43.9249C36.2623 43.9752 36.1322 44.0011 36.0008 44.0011C35.8694 44.0011 35.7393 43.9752 35.6179 43.9249C35.4965 43.8747 35.3862 43.801 35.2933 43.7081L26.2933 34.7081C26.2003 34.6152 26.1266 34.5049 26.0762 34.3835C26.0259 34.2621 26 34.132 26 34.0006C26 33.8691 26.0259 33.739 26.0762 33.6176C26.1266 33.4962 26.2003 33.3859 26.2933 33.2931L35.2933 24.2931C35.4809 24.1054 35.7354 24 36.0008 24C36.2662 24 36.5206 24.1054 36.7083 24.2931C36.8959 24.4807 37.0013 24.7352 37.0013 25.0006C37.0013 25.2659 36.8959 25.5204 36.7083 25.7081L29.4145 33.0006H49.0008C49.266 33.0006 49.5204 33.1059 49.7079 33.2934C49.8954 33.481 50.0008 33.7353 50.0008 34.0006Z" fill="#121212"/></svg></button>
		<?php
	}
}
