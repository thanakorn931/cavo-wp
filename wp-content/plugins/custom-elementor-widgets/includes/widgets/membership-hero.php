<?php
/**
 * Membership, hero — one section of the design.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Membership page's opening words and its plans.
 */
class Membership_Hero extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'membership-hero';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Membership — Hero', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'membership', 'hero', 'plans', 'compare' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'    => esc_html__( 'Membership', 'custom-elementor-widgets' ),
			'body'       => esc_html__( 'Membership at CAVO offers access to a more personal way of experiencing the venue. Designed for guests who return often, members enjoy exclusive privileges, priority access, and curated benefits throughout the year. More than a membership, it is an invitation to become part of the CAVO community.', 'custom-elementor-widgets' ),
			'title'      => esc_html__( 'Compare paid plans', 'custom-elementor-widgets' ),
			'join_text'  => esc_html__( 'Join Membership', 'custom-elementor-widgets' ),
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
			'section_intro',
			array(
				'label' => esc_html__( 'Words', 'custom-elementor-widgets' ),
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
				'default' => 'h1',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'body',
			array(
				'label'       => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 5,
				'placeholder' => $design['body'],
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Plans heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['title'],
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'join_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['join_text'],
			)
		);

		$this->add_link_controls( $this, 'join_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

		$this->end_controls_section();

		$this->register_plan_controls();
		$this->register_style_controls();
	}

	/**
	 * Content → Plans.
	 */
	private function register_plan_controls() {
		$this->start_controls_section(
			'section_plans',
			array(
				'label' => esc_html__( 'Plans', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Name', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Card picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		// The design gives each plan a colour of its own, so the colours belong
		// to the row rather than to the section.
		$repeater->add_control(
			'background_from',
			array(
				'label'     => esc_html__( 'Background, from', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E1D8B1',
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'background_to',
			array(
				'label'   => esc_html__( 'Background, to', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#EBE4CA',
			)
		);

		$repeater->add_control(
			'ink',
			array(
				'label'   => esc_html__( 'Text and mark', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#3A2114',
			)
		);

		$repeater->add_control(
			'muted',
			array(
				'label'       => esc_html__( 'Text, where the plan does not carry it', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#D1D1D1',
			)
		);

		foreach ( array( 'one', 'two', 'three' ) as $index => $which ) {
			$repeater->add_control(
				'feature_' . $which,
				array(
					'label'     => sprintf(
						/* translators: %d: which of the three lines. */
						esc_html__( 'Line %d', 'custom-elementor-widgets' ),
						$index + 1
					),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array( 'active' => true ),
					'separator' => 'before',
				)
			);

			$repeater->add_control(
				'feature_' . $which . '_on',
				array(
					'label'   => esc_html__( 'The plan carries it', 'custom-elementor-widgets' ),
					'type'    => Controls_Manager::SWITCHER,
					'default' => 'yes',
				)
			);
		}

		$this->add_control(
			'plans',
			array(
				'label'       => esc_html__( 'Plans', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();
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
			'intro_background',
			array(
				'label'     => esc_html__( 'Behind the words', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-hero__intro' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-hero__heading' => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-membership-hero__title'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-hero__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-hero__heading',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-hero__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 24 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 16 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 16 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Plans heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-hero__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 48 ),
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
				'name'           => 'name_typography',
				'label'          => esc_html__( 'Plan name', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-hero__name',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 24 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 14 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 14 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'feature_typography',
				'label'          => esc_html__( 'Plan lines', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-hero__feature',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 9 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 9 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'join_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-hero__join' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'join_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-hero__join' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'join_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-hero__join',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 14 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 8 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 8 ),
					),
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
					'{{WRAPPER}} .custom-membership-hero__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_off_background',
			array(
				'label'     => esc_html__( 'Arrows, with nowhere to go', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9BCA6',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-hero__arrow[disabled]' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-membership-hero__arrow svg' => 'fill: {{VALUE}};',
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

		$plans = isset( $settings['plans'] ) ? (array) $settings['plans'] : array();
		$tag   = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h1';
		$tag   = in_array( $tag, array( 'h1', 'h2', 'span' ), true ) ? $tag : 'h1';
		?>
		<div class="custom-membership-hero">
			<div class="custom-membership-hero__intro">
				<<?php echo esc_attr( $tag ); ?> class="custom-membership-hero__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>

				<p class="custom-membership-hero__body"><?php echo esc_html( $this->text( $settings, 'body' ) ); ?></p>
			</div>

			<div class="custom-membership-hero__plans">
				<div class="custom-membership-hero__inner">
					<div class="custom-membership-hero__aside">
						<div class="custom-membership-hero__aside-top">
							<p class="custom-membership-hero__title"><?php echo esc_html( $this->text( $settings, 'title' ) ); ?></p>

							<?php if ( count( $plans ) > 1 ) : ?>
								<div class="custom-membership-hero__arrows">
									<?php
									$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
									$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
									?>
								</div>
							<?php endif; ?>
						</div>

						<a class="custom-membership-hero__join"<?php
							echo $this->link_from( $settings, 'join_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
						?>><?php echo esc_html( $this->text( $settings, 'join_text' ) ); ?></a>
					</div>

					<?php if ( ! empty( $plans ) ) : ?>
						<div class="custom-membership-hero__stage">
							<div class="custom-membership-hero__track">
								<?php foreach ( $plans as $plan ) : ?>
									<?php $this->render_plan( $plan ); ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>

				<?php
				if ( empty( $plans ) ) {
					$this->editor_hint( __( 'This section is waiting for its plans, on the Content tab.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * One plan.
	 *
	 * The colours are the row's own, so they are written onto the card rather
	 * than through the Style tab, which knows only the section.
	 *
	 * @param array $plan The row.
	 */
	private function render_plan( $plan ) {
		$from  = isset( $plan['background_from'] ) ? $plan['background_from'] : '';
		$to    = isset( $plan['background_to'] ) ? $plan['background_to'] : '';
		$ink   = isset( $plan['ink'] ) ? $plan['ink'] : '';
		$muted = isset( $plan['muted'] ) ? $plan['muted'] : '';

		$style = sprintf(
			'background-image: radial-gradient( 312px 388px at 31%% 21%%, %s 0%%, %s 100%% ); color: %s;',
			esc_attr( $from ),
			esc_attr( $to ),
			esc_attr( $ink )
		);
		?>
		<article class="custom-membership-hero__card" style="<?php echo esc_attr( $style ); ?>">
			<div class="custom-membership-hero__words">
				<div class="custom-membership-hero__head">
					<p class="custom-membership-hero__name"><?php
						echo esc_html( isset( $plan['name'] ) ? $plan['name'] : '' );
					?></p>

					<span class="custom-membership-hero__badge" aria-hidden="true"></span>
				</div>

				<span class="custom-membership-hero__rule" aria-hidden="true"></span>

				<ul class="custom-membership-hero__features">
					<?php foreach ( array( 'one', 'two', 'three' ) as $which ) : ?>
						<?php
						$line = isset( $plan[ 'feature_' . $which ] ) ? trim( (string) $plan[ 'feature_' . $which ] ) : '';

						if ( '' === $line ) {
							continue;
						}

						$on = ! isset( $plan[ 'feature_' . $which . '_on' ] ) || 'yes' === $plan[ 'feature_' . $which . '_on' ];
						?>
						<li class="custom-membership-hero__feature"<?php
							echo $on ? '' : ' style="color: ' . esc_attr( $muted ) . ';"';
						?>>
							<?php $this->render_check(); ?>
							<span class="custom-membership-hero__feature-text"><?php echo esc_html( $line ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="custom-membership-hero__picture"><?php
				$this->media( isset( $plan['picture']['url'] ) ? $plan['picture']['url'] : '' );
			?></div>
		</article>
		<?php
	}

	/**
	 * The mark beside a line — the design's own, carried by the widget.
	 */
	private function render_check() {
		?>
		<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M12 2.25C10.0716 2.25 8.18657 2.82183 6.58319 3.89317C4.97981 4.96452 3.73013 6.48726 2.99217 8.26884C2.25422 10.0504 2.06114 12.0108 2.43734 13.9021C2.81355 15.7934 3.74215 17.5307 5.10571 18.8943C6.46927 20.2579 8.20655 21.1865 10.0979 21.5627C11.9892 21.9389 13.9496 21.7458 15.7312 21.0078C17.5127 20.2699 19.0355 19.0202 20.1068 17.4168C21.1782 15.8134 21.75 13.9284 21.75 12C21.7473 9.41498 20.7192 6.93661 18.8913 5.10872C17.0634 3.28084 14.585 2.25273 12 2.25ZM16.2806 10.2806L11.0306 15.5306C10.961 15.6004 10.8783 15.6557 10.7872 15.6934C10.6962 15.7312 10.5986 15.7506 10.5 15.7506C10.4014 15.7506 10.3038 15.7312 10.2128 15.6934C10.1217 15.6557 10.039 15.6004 9.96937 15.5306L7.71937 13.2806C7.57864 13.1399 7.49958 12.949 7.49958 12.75C7.49958 12.551 7.57864 12.3601 7.71937 12.2194C7.86011 12.0786 8.05098 11.9996 8.25 11.9996C8.44902 11.9996 8.63989 12.0786 8.78063 12.2194L10.5 13.9397L15.2194 9.21937C15.2891 9.14969 15.3718 9.09442 15.4628 9.0567C15.5539 9.01899 15.6515 8.99958 15.75 8.99958C15.8485 8.99958 15.9461 9.01899 16.0372 9.0567C16.1282 9.09442 16.2109 9.14969 16.2806 9.21937C16.3503 9.28906 16.4056 9.37178 16.4433 9.46283C16.481 9.55387 16.5004 9.65145 16.5004 9.75C16.5004 9.84855 16.481 9.94613 16.4433 10.0372C16.4056 10.1282 16.3503 10.2109 16.2806 10.2806Z"/></svg>
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
			class="custom-membership-hero__arrow custom-membership-hero__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><svg viewBox="10 6 56 56" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M50.0008 34.0006C50.0008 34.2658 49.8954 34.5201 49.7079 34.7077C49.5204 34.8952 49.266 35.0006 49.0008 35.0006H29.4145L36.7083 42.2931C36.8012 42.386 36.8749 42.4963 36.9252 42.6177C36.9755 42.7391 37.0013 42.8692 37.0013 43.0006C37.0013 43.132 36.9755 43.2621 36.9252 43.3835C36.8749 43.5048 36.8012 43.6151 36.7083 43.7081C36.6154 43.801 36.5051 43.8747 36.3837 43.9249C36.2623 43.9752 36.1322 44.0011 36.0008 44.0011C35.8694 44.0011 35.7393 43.9752 35.6179 43.9249C35.4965 43.8747 35.3862 43.801 35.2933 43.7081L26.2933 34.7081C26.2003 34.6152 26.1266 34.5049 26.0762 34.3835C26.0259 34.2621 26 34.132 26 34.0006C26 33.8691 26.0259 33.739 26.0762 33.6176C26.1266 33.4962 26.2003 33.3859 26.2933 33.2931L35.2933 24.2931C35.4809 24.1054 35.7354 24 36.0008 24C36.2662 24 36.5206 24.1054 36.7083 24.2931C36.8959 24.4807 37.0013 24.7352 37.0013 25.0006C37.0013 25.2659 36.8959 25.5204 36.7083 25.7081L29.4145 33.0006H49.0008C49.266 33.0006 49.5204 33.1059 49.7079 33.2934C49.8954 33.481 50.0008 33.7353 50.0008 34.0006Z" fill="#121212"/></svg></button>
		<?php
	}
}
