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
use Elementor\Icons_Manager;
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
		$this->register_arrow_controls();
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
	 * Content → Arrows.
	 */
	private function register_arrow_controls() {
		$this->start_controls_section(
			'section_arrows',
			array(
				'label' => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( array(
			'arrow_previous' => esc_html__( 'Previous', 'custom-elementor-widgets' ),
			'arrow_next'     => esc_html__( 'Next', 'custom-elementor-widgets' ),
		) as $name => $label ) {
			$this->add_control(
				$name,
				array(
					'label'       => $label,
					'type'        => Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
				)
			);
		}

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
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-carousel__arrow'     => 'color: {{VALUE}};',
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
					$this->render_arrow( $settings, 'arrow_previous', 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
					$this->render_arrow( $settings, 'arrow_next', 'next', __( 'Next', 'custom-elementor-widgets' ) );
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
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @param string $side     Which arrow this is.
	 * @param string $label    What a reader who cannot see it is told.
	 */
	private function render_arrow( $settings, $key, $side, $label ) {
		?>
		<button
			type="button"
			class="custom-dining-carousel__arrow custom-dining-carousel__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		>
			<?php
			if ( ! empty( $settings[ $key ]['value'] ) ) {
				Icons_Manager::render_icon( $settings[ $key ], array( 'aria-hidden' => 'true' ) );
			}
			?>
		</button>
		<?php
	}
}
