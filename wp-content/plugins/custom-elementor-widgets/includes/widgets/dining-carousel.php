<?php
/**
 * Dining, carousel — one section of the design.
 *
 * How many slides there are is the client's, so they are a repeater, and it
 * ships with none. The mark the section ends on is the design's own and is
 * carried by the widget rather than being asked for.
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
 * The Dining page's carousel.
 */
class Dining_Carousel extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dining-carousel';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dining — Carousel', 'custom-elementor-widgets' );
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
		return array( 'dining', 'carousel', 'slider', 'bartender', 'gallery' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_slide_controls();
		$this->register_style_controls();
	}

	/**
	 * Content → Slides.
	 */
	private function register_slide_controls() {
		$this->start_controls_section(
			'section_slides',
			array(
				'label' => esc_html__( 'Slides', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Bartender in action', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'Title level', 'custom-elementor-widgets' ),
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
			'slide_picture',
			array(
				'label' => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'     => esc_html__( 'Slides', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::REPEATER,
				'fields'    => $repeater->get_controls(),
				'separator' => 'before',
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

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-carousel__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 24 ) ),
					'font_weight' => array( 'default' => '400' ),
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-carousel__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EBE4CA',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-carousel__arrow'        => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-dining-carousel__arrow:hover'  => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-dining-carousel__arrow:focus'  => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-dining-carousel__arrow:active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3D3426',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-carousel__arrow svg' => 'fill: {{VALUE}};',
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

		$slides = isset( $settings['slides'] ) ? (array) $settings['slides'] : array();

		$title = isset( $settings['title'] ) ? trim( (string) $settings['title'] ) : '';
		$title = '' !== $title ? $title : esc_html__( 'Bartender in action', 'custom-elementor-widgets' );

		$tag = isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-dining-carousel">
			<<?php echo esc_attr( $tag ); ?> class="custom-dining-carousel__title"><?php
				echo esc_html( $title );
			?></<?php echo esc_attr( $tag ); ?>>

			<div class="custom-dining-carousel__stage">
				<div class="custom-dining-carousel__track" data-many="<?php echo esc_attr( count( $slides ) ); ?>">
					<?php
					// The run is written three times where there is more than one
					// slide, so the reader always has a picture either side and
					// never reaches an end. The copies carry no meaning of their
					// own and are hidden from anyone being read to.
					$runs = count( $slides ) > 1 ? 3 : 1;

					for ( $run = 0; $run < $runs; $run++ ) :
						foreach ( $slides as $slide ) :
							?>
							<div class="custom-dining-carousel__slide"<?php echo 1 === $run || 1 === $runs ? '' : ' aria-hidden="true"'; ?>>
								<?php $this->media( isset( $slide['slide_picture']['url'] ) ? $slide['slide_picture']['url'] : '' ); ?>
							</div>
							<?php
						endforeach;
					endfor;
					?>
				</div>

				<?php
				// An arrow means nothing until there is somewhere else to go.
				if ( count( $slides ) > 1 ) {
					$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
					$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<span class="custom-dining-carousel__mark" aria-hidden="true"></span>

			<?php
			if ( empty( $slides ) ) {
				$this->editor_hint( __( 'This carousel is waiting for its slides, on the Content tab.', 'custom-elementor-widgets' ) );
			}
			?>
		</div>
		<?php
	}

	/**
	 * One of the two arrows.
	 *
	 * @param string $side     Which arrow this is.
	 * @param string $label    What a reader who cannot see it is told.
	 */
	private function render_arrow( $side, $label ) {
		?>
		<button
			type="button"
			class="custom-dining-carousel__arrow custom-dining-carousel__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><svg viewBox="10 6 56 56" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M50.0008 34.0006C50.0008 34.2658 49.8954 34.5201 49.7079 34.7077C49.5204 34.8952 49.266 35.0006 49.0008 35.0006H29.4145L36.7083 42.2931C36.8012 42.386 36.8749 42.4963 36.9252 42.6177C36.9755 42.7391 37.0013 42.8692 37.0013 43.0006C37.0013 43.132 36.9755 43.2621 36.9252 43.3835C36.8749 43.5048 36.8012 43.6151 36.7083 43.7081C36.6154 43.801 36.5051 43.8747 36.3837 43.9249C36.2623 43.9752 36.1322 44.0011 36.0008 44.0011C35.8694 44.0011 35.7393 43.9752 35.6179 43.9249C35.4965 43.8747 35.3862 43.801 35.2933 43.7081L26.2933 34.7081C26.2003 34.6152 26.1266 34.5049 26.0762 34.3835C26.0259 34.2621 26 34.132 26 34.0006C26 33.8691 26.0259 33.739 26.0762 33.6176C26.1266 33.4962 26.2003 33.3859 26.2933 33.2931L35.2933 24.2931C35.4809 24.1054 35.7354 24 36.0008 24C36.2662 24 36.5206 24.1054 36.7083 24.2931C36.8959 24.4807 37.0013 24.7352 37.0013 25.0006C37.0013 25.2659 36.8959 25.5204 36.7083 25.7081L29.4145 33.0006H49.0008C49.266 33.0006 49.5204 33.1059 49.7079 33.2934C49.8954 33.481 50.0008 33.7353 50.0008 34.0006Z" fill="#121212"/></svg></button>
		<?php
	}
}
