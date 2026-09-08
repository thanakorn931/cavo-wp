<?php
/**
 * Nightlife, atmosphere — one section of the design.
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
 * The Nightlife page's atmosphere carousel.
 */
class Nightlife_Carousel extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nightlife-carousel';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Nightlife — Carousel', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'nightlife', 'carousel', 'atmosphere', 'gallery' );
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
				'placeholder' => esc_html__( 'Nightlife atmosphere', 'custom-elementor-widgets' ),
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

		$repeater = new Repeater();

		$repeater->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'     => esc_html__( 'Pictures', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

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
					'{{WRAPPER}} .custom-nightlife-carousel__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-carousel__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 24 ) ),
					'font_weight' => array( 'default' => '400' ),
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

		$slides  = isset( $settings['slides'] ) ? (array) $settings['slides'] : array();
		$heading = isset( $settings['heading'] ) ? trim( (string) $settings['heading'] ) : '';
		$heading = '' !== $heading ? $heading : esc_html__( 'Nightlife atmosphere', 'custom-elementor-widgets' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-nightlife-carousel">
			<<?php echo esc_attr( $tag ); ?> class="custom-nightlife-carousel__heading"><?php
				echo esc_html( $heading );
			?></<?php echo esc_attr( $tag ); ?>>

			<div class="custom-nightlife-carousel__stage">
				<div class="custom-nightlife-carousel__track">
					<?php foreach ( $slides as $slide ) : ?>
						<div class="custom-nightlife-carousel__slide"><?php
							$this->media( isset( $slide['picture']['url'] ) ? $slide['picture']['url'] : '' );
						?></div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php
			if ( empty( $slides ) ) {
				$this->editor_hint( __( 'This carousel is waiting for its pictures, on the Content tab.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}
}
