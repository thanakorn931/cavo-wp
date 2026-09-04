<?php
/**
 * CTA — one section of the design.
 *
 * The design draws this band once and uses it on more than one page, so it
 * carries no page's name: the words, the picture and where the button goes are
 * the page's to give it.
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
 * The call-to-action band.
 */
class Cta extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'cta';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'CTA', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-call-to-action';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'cta', 'banner', 'call to action', 'button' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_band_style_controls();
		$this->register_button_style_controls();
	}

	/**
	 * Content → Band.
	 */
	private function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Band', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Your party, our vibe. Unforgettable', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => esc_html__( 'Heading level', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h1'   => 'H1',
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
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Reserve a Table', 'custom-elementor-widgets' ),
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'button_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

		$this->end_controls_section();
	}

	/**
	 * Style → Band.
	 */
	private function register_band_style_controls() {
		$this->start_controls_section(
			'section_band_style',
			array(
				'label' => esc_html__( 'Band', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'veil_color',
			array(
				'label'     => esc_html__( 'Over the picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-cta__veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-cta__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading colour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-cta__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Button.
	 */
	private function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .custom-cta__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Text and outline', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-cta__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-cta__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * What the design settles, for a control the client has left alone.
	 *
	 * @return array
	 */
	private function design_text() {
		return array(
			'heading'     => esc_html__( 'Your party, our vibe. Unforgettable', 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'Reserve a Table', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * One control's text: what the client typed, or what the design settles.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @return string
	 */
	private function text( $settings, $key ) {
		$typed = isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';

		if ( '' !== $typed ) {
			return $typed;
		}

		$design = $this->design_text();

		return isset( $design[ $key ] ) ? $design[ $key ] : '';
	}

	/**
	 * Print the section.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$heading  = $this->text( $settings, 'heading' );
		$button   = $this->text( $settings, 'button_text' );
		$picture  = isset( $settings['picture']['url'] ) ? $settings['picture']['url'] : '';
		$tag      = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$allowed  = array( 'h1', 'h2', 'h3', 'span' );
		$tag      = in_array( $tag, $allowed, true ) ? $tag : 'h2';
		?>
		<div class="custom-cta">
			<span class="custom-cta__picture" aria-hidden="true">
				<?php if ( '' !== $picture ) : ?>
					<img src="<?php echo esc_url( $picture ); ?>" alt="" />
				<?php endif; ?>
			</span>
			<span class="custom-cta__veil" aria-hidden="true"></span>

			<?php if ( '' !== $heading ) : ?>
				<<?php echo esc_attr( $tag ); ?> class="custom-cta__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $tag ); ?>>
			<?php endif; ?>

			<?php if ( '' !== $button ) : ?>
				<a class="custom-cta__button"<?php
					echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $button ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
}
