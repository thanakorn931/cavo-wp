<?php
/**
 * Membership, benefits — one section of the design.
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
 * The Membership page's benefits.
 */
class Membership_Benefit extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'membership-benefit';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Membership — Benefit', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-device';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'membership', 'benefit', 'carousel' );
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
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Benefits', 'custom-elementor-widgets' ),
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
			'section_benefits',
			array(
				'label' => esc_html__( 'Benefits', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'wide_picture',
			array(
				'label' => esc_html__( 'Wide picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'body',
			array(
				'label'   => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'rows'    => 5,
			)
		);

		$repeater->add_control(
			'thumb_picture',
			array(
				'label' => esc_html__( 'Small picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'benefits',
			array(
				'label'       => esc_html__( 'Benefits', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
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
				'label'     => esc_html__( 'Over the section', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-benefit__veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'thumb_veil_color',
			array(
				'label'     => esc_html__( 'Over the small picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-benefit__thumb-veil' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-membership-benefit__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-benefit__heading',
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
			'title_color',
			array(
				'label'     => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-benefit__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-benefit__title',
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

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-benefit__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-membership-benefit__body',
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

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-membership-benefit__arrow' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-membership-benefit__arrow svg' => 'fill: {{VALUE}};',
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

		$benefits = isset( $settings['benefits'] ) ? (array) $settings['benefits'] : array();
		$picture  = isset( $settings['picture']['url'] ) ? $settings['picture']['url'] : '';

		$heading = isset( $settings['heading'] ) ? trim( (string) $settings['heading'] ) : '';
		$heading = '' !== $heading ? $heading : esc_html__( 'Benefits', 'custom-elementor-widgets' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-membership-benefit">
			<span class="custom-membership-benefit__picture" aria-hidden="true"><?php $this->media( $picture ); ?></span>
			<span class="custom-membership-benefit__veil" aria-hidden="true"></span>

			<<?php echo esc_attr( $tag ); ?> class="custom-membership-benefit__heading"><?php
				echo esc_html( $heading );
			?></<?php echo esc_attr( $tag ); ?>>

			<?php if ( ! empty( $benefits ) ) : ?>
				<div class="custom-membership-benefit__row">
					<?php foreach ( $benefits as $index => $benefit ) : ?>
						<div class="custom-membership-benefit__slide<?php echo 0 === $index ? ' is-current' : ''; ?>">
							<div class="custom-membership-benefit__stage"><?php
								$this->media( isset( $benefit['wide_picture']['url'] ) ? $benefit['wide_picture']['url'] : '' );
							?></div>

							<div class="custom-membership-benefit__side">
								<div class="custom-membership-benefit__words">
									<h3 class="custom-membership-benefit__title"><?php
										echo esc_html( isset( $benefit['title'] ) ? $benefit['title'] : '' );
									?></h3>

									<p class="custom-membership-benefit__body"><?php
										echo esc_html( isset( $benefit['body'] ) ? $benefit['body'] : '' );
									?></p>
								</div>

								<div class="custom-membership-benefit__thumb">
									<?php $this->media( isset( $benefit['thumb_picture']['url'] ) ? $benefit['thumb_picture']['url'] : '' ); ?>
									<span class="custom-membership-benefit__thumb-veil" aria-hidden="true"></span>
								</div>
							</div>
						</div>
					<?php endforeach; ?>

					<?php
					// An arrow means nothing until there is somewhere else to go.
					if ( count( $benefits ) > 1 ) {
						$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
						$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
					}
					?>
				</div>
			<?php endif; ?>

			<?php
			if ( empty( $benefits ) ) {
				$this->editor_hint( __( 'This section is waiting for its benefits, on the Content tab.', 'custom-elementor-widgets' ) );
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
			class="custom-membership-benefit__arrow custom-membership-benefit__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><svg viewBox="10 6 56 56" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M50.0008 34.0006C50.0008 34.2658 49.8954 34.5201 49.7079 34.7077C49.5204 34.8952 49.266 35.0006 49.0008 35.0006H29.4145L36.7083 42.2931C36.8012 42.386 36.8749 42.4963 36.9252 42.6177C36.9755 42.7391 37.0013 42.8692 37.0013 43.0006C37.0013 43.132 36.9755 43.2621 36.9252 43.3835C36.8749 43.5048 36.8012 43.6151 36.7083 43.7081C36.6154 43.801 36.5051 43.8747 36.3837 43.9249C36.2623 43.9752 36.1322 44.0011 36.0008 44.0011C35.8694 44.0011 35.7393 43.9752 35.6179 43.9249C35.4965 43.8747 35.3862 43.801 35.2933 43.7081L26.2933 34.7081C26.2003 34.6152 26.1266 34.5049 26.0762 34.3835C26.0259 34.2621 26 34.132 26 34.0006C26 33.8691 26.0259 33.739 26.0762 33.6176C26.1266 33.4962 26.2003 33.3859 26.2933 33.2931L35.2933 24.2931C35.4809 24.1054 35.7354 24 36.0008 24C36.2662 24 36.5206 24.1054 36.7083 24.2931C36.8959 24.4807 37.0013 24.7352 37.0013 25.0006C37.0013 25.2659 36.8959 25.5204 36.7083 25.7081L29.4145 33.0006H49.0008C49.266 33.0006 49.5204 33.1059 49.7079 33.2934C49.8954 33.481 50.0008 33.7353 50.0008 34.0006Z"/></svg></button>
		<?php
	}
}
