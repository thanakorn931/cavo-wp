<?php
/**
 * Home, the space — one section of the design.
 *
 * One area at a time: the tabs name them, and each carries its own two
 * pictures, its words and the two ways further in. The table is booked from the
 * section rather than from any one area.
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
 * The Home page's areas.
 */
class Home_The_Space extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'home-the-space';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Home — The Space', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-tabs';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'home', 'space', 'areas', 'tabs' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'      => esc_html__( 'Discover The Space', 'custom-elementor-widgets' ),
			'reserve_text' => esc_html__( 'Reserve a Table', 'custom-elementor-widgets' ),
			'space_text'   => esc_html__( 'View Space', 'custom-elementor-widgets' ),
			'tour_text'    => esc_html__( 'Virtual tour', 'custom-elementor-widgets' ),
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
			'section_areas',
			array(
				'label' => esc_html__( 'Areas', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$area = new Repeater();

		$area->add_control(
			'name',
			array(
				'label'       => esc_html__( 'Tab', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$area->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$area->add_control(
			'body',
			array(
				'label' => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$area->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Picture, large', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$area->add_control(
			'thumb',
			array(
				'label' => esc_html__( 'Picture, small', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_link_controls( $area, 'space_link', esc_html__( 'View space link', 'custom-elementor-widgets' ) );

		$this->add_link_controls( $area, 'tour_link', esc_html__( 'Virtual tour link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'areas',
			array(
				'label'       => esc_html__( 'Areas', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $area->get_controls(),
				'title_field' => '{{{ name || title || "Area" }}}',
			)
		);

		$this->end_controls_section();

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
			'picture',
			array(
				'label'     => esc_html__( 'Background picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'reserve_text',
			array(
				'label'       => esc_html__( 'Reserve button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['reserve_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'reserve_link', esc_html__( 'Reserve button link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'space_text',
			array(
				'label'       => esc_html__( 'View space text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['space_text'],
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'tour_text',
			array(
				'label'       => esc_html__( 'Virtual tour text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['tour_text'],
			)
		);

		$this->add_control(
			'tour_icon',
			array(
				'label'                  => esc_html__( 'Virtual tour icon', 'custom-elementor-widgets' ),
				'type'                   => Controls_Manager::ICONS,
				'skin'                   => 'inline',
				'label_block'            => false,
				'exclude_inline_options' => array( 'svg' ),
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
			'veil_color',
			array(
				'label'     => esc_html__( 'Veil', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wash_near',
			array(
				'label'     => esc_html__( 'Wash, lighter', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(157, 84, 52, 0.7)',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space' => '--custom-home-space-near: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wash_mid',
			array(
				'label'     => esc_html__( 'Wash, middle', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(100, 49, 26, 0.7)',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space' => '--custom-home-space-mid: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wash_far',
			array(
				'label'     => esc_html__( 'Wash, deeper', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(76, 37, 19, 0.7)',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space' => '--custom-home-space-far: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-space__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'tab_color',
			array(
				'label'     => esc_html__( 'Tab', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#B0B0B0',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__tab' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_color_here',
			array(
				'label'     => esc_html__( 'Tab, the one open', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__tab.is-here' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_rule_color',
			array(
				'label'     => esc_html__( 'Tab rule', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3EEDC',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__tabs' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tab_mark_color',
			array(
				'label'     => esc_html__( 'Tab mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#B9AD79',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__tab.is-here' => 'border-bottom-color: {{VALUE}}; box-shadow: 0 4px 10px 0 {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'tab_typography',
				'label'          => esc_html__( 'Tabs', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-space__tab',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Area title', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Area title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-space__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 48 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Area text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Area text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-space__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 18 ) ),
				),
			)
		);

		$this->add_control(
			'action_color',
			array(
				'label'     => esc_html__( 'View space and tour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__actions' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reserve_color',
			array(
				'label'     => esc_html__( 'Reserve button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__reserve' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'reserve_background',
			array(
				'label'     => esc_html__( 'Reserve button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__reserve' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'action_typography',
				'label'          => esc_html__( 'Buttons', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-space__reserve, {{WRAPPER}} .custom-home-space__space, {{WRAPPER}} .custom-home-space__tour-link',
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
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-space__arrow' => 'color: {{VALUE}};',
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

		$areas   = isset( $settings['areas'] ) ? (array) $settings['areas'] : array();
		$picture = isset( $settings['picture']['url'] ) ? $settings['picture']['url'] : '';
		$reserve = $this->text( $settings, 'reserve_text' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-home-space">
			<span class="custom-home-space__picture" aria-hidden="true"><?php $this->media( $picture ); ?></span>
			<span class="custom-home-space__veil" aria-hidden="true"></span>
			<span class="custom-home-space__wash" aria-hidden="true"></span>

			<<?php echo esc_attr( $tag ); ?> class="custom-home-space__heading"><?php
				echo esc_html( $this->text( $settings, 'heading' ) );
			?></<?php echo esc_attr( $tag ); ?>>

			<div class="custom-home-space__tabs" role="tablist">
				<?php foreach ( $areas as $index => $area ) : ?>
					<button
						class="custom-home-space__tab<?php echo 0 === $index ? ' is-here' : ''; ?>"
						type="button"
						role="tab"
						aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						data-area="<?php echo esc_attr( (int) $index ); ?>"
					><?php
						echo esc_html( isset( $area['name'] ) && '' !== trim( (string) $area['name'] ) ? $area['name'] : ( isset( $area['title'] ) ? $area['title'] : '' ) );
					?></button>
				<?php endforeach; ?>
			</div>

			<div class="custom-home-space__stage">
				<?php foreach ( $areas as $index => $area ) : ?>
					<?php $this->render_area( $settings, $area, 0 === (int) $index ); ?>
				<?php endforeach; ?>

				<?php if ( '' !== $reserve ) : ?>
					<a class="custom-home-space__reserve"<?php
						echo $this->link_from( $settings, 'reserve_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>><?php echo esc_html( $reserve ); ?></a>
				<?php endif; ?>

				<?php if ( count( $areas ) > 1 ) : ?>
					<?php
					$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
					$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
					?>
				<?php endif; ?>
			</div>

			<?php
			if ( empty( $areas ) ) {
				$this->editor_hint( __( 'This section is waiting for its areas, on the Content tab.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}

	/**
	 * One area: its two pictures and what is said about it.
	 *
	 * @param array $settings The widget's settings.
	 * @param array $area     The row.
	 * @param bool  $here     Whether it is the one open.
	 */
	private function render_area( $settings, $area, $here ) {
		$space = $this->text( $settings, 'space_text' );
		$tour  = $this->text( $settings, 'tour_text' );
		$icon  = isset( $settings['tour_icon'] ) ? $settings['tour_icon'] : array();
		?>
		<div class="custom-home-space__area<?php echo $here ? ' is-here' : ''; ?>"<?php echo $here ? '' : ' hidden'; ?>>
			<span class="custom-home-space__large">
				<?php $this->media( isset( $area['picture']['url'] ) ? $area['picture']['url'] : '' ); ?>
			</span>

			<span class="custom-home-space__small">
				<?php $this->media( isset( $area['thumb']['url'] ) ? $area['thumb']['url'] : '' ); ?>
				<span class="custom-home-space__small-veil" aria-hidden="true"></span>
			</span>

			<div class="custom-home-space__panel">
				<p class="custom-home-space__title"><?php
					echo esc_html( isset( $area['title'] ) ? $area['title'] : '' );
				?></p>

				<p class="custom-home-space__body"><?php
					echo esc_html( isset( $area['body'] ) ? $area['body'] : '' );
				?></p>

				<div class="custom-home-space__actions">
					<?php if ( '' !== $space ) : ?>
						<a class="custom-home-space__space"<?php
							echo $this->link_from( $area, 'space_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
						?>><?php echo esc_html( $space ); ?></a>
					<?php endif; ?>

					<?php if ( '' !== $tour ) : ?>
						<span class="custom-home-space__tour">
							<a class="custom-home-space__tour-link"<?php
								echo $this->link_from( $area, 'tour_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
							?>><?php echo esc_html( $tour ); ?></a>

							<?php if ( ! empty( $icon['value'] ) ) : ?>
								<span class="custom-home-space__tour-icon">
									<?php \Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?>
								</span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
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
			'<button class="custom-home-space__arrow custom-home-space__arrow--%1$s" type="button" aria-label="%2$s"><svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><circle cx="28" cy="28" r="27" stroke="currentColor" stroke-width="1.5"/><path d="%3$s" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
			esc_attr( $way ),
			esc_attr( $label ),
			esc_attr( $path )
		);
	}
}
