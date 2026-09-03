<?php
/**
 * The Space, content — one section of the design.
 *
 * The statement the page opens with, and the three pictures under it.
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
 * The Space's content section.
 */
class The_Space_Content extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'the-space-content';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'The Space — Content', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'space', 'content', 'gallery', 'statement' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_statement_controls();
		$this->register_picture_controls();
		$this->register_style_controls();
	}

	/**
	 * Content → Statement.
	 */
	private function register_statement_controls() {
		$this->start_controls_section(
			'section_statement',
			array(
				'label' => esc_html__( 'Statement', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'statement',
			array(
				'label'       => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'placeholder' => esc_html__( 'The heart of CAVO, where refined dining evolves into vibrant nightlife. Designed around the central bar and DJ booth, it brings together music, conversation, and celebration in one dynamic setting.', 'custom-elementor-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Pictures.
	 */
	private function register_picture_controls() {
		$this->start_controls_section(
			'section_pictures',
			array(
				'label' => esc_html__( 'Pictures', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( array(
			'picture_wide' => esc_html__( 'Top left', 'custom-elementor-widgets' ),
			'picture_tall' => esc_html__( 'Top right', 'custom-elementor-widgets' ),
			'picture_full' => esc_html__( 'Below', 'custom-elementor-widgets' ),
		) as $name => $label ) {
			$this->add_control(
				$name,
				array(
					'label' => $label,
					'type'  => Controls_Manager::MEDIA,
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Style → Statement.
	 */
	private function register_style_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Statement', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'statement_background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7D6B50',
				'selectors' => array(
					'{{WRAPPER}} .custom-space-content__statement' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'statement_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-space-content__statement' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'statement_typography',
				'selector'       => '{{WRAPPER}} .custom-space-content__statement p',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 24 ) ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print the section.
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$statement = isset( $settings['statement'] ) ? trim( (string) $settings['statement'] ) : '';

		if ( '' === $statement ) {
			$statement = esc_html__( 'The heart of CAVO, where refined dining evolves into vibrant nightlife. Designed around the central bar and DJ booth, it brings together music, conversation, and celebration in one dynamic setting.', 'custom-elementor-widgets' );
		}
		?>
		<div class="custom-space-content">
			<div class="custom-space-content__statement">
				<p><?php echo esc_html( $statement ); ?></p>
			</div>

			<div class="custom-space-content__gallery">
				<div class="custom-space-content__grid">
					<div class="custom-space-content__row">
						<?php
						$this->render_slot( $settings, 'picture_wide', 'wide' );
						$this->render_slot( $settings, 'picture_tall', 'tall' );
						?>
					</div>
					<?php $this->render_slot( $settings, 'picture_full', 'full' ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * One picture slot — a box first, whether or not a picture is in it.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @param string $shape    Which of the three boxes this is.
	 */
	private function render_slot( $settings, $key, $shape ) {
		$url = isset( $settings[ $key ]['url'] ) ? $settings[ $key ]['url'] : '';
		?>
		<span class="custom-space-content__slot custom-space-content__slot--<?php echo esc_attr( $shape ); ?>">
			<?php if ( '' !== $url ) : ?>
				<img src="<?php echo esc_url( $url ); ?>" alt="" />
			<?php endif; ?>
		</span>
		<?php
	}
}
