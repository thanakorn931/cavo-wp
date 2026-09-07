<?php
/**
 * Home, introduce — one section of the design.
 *
 * What the place is, written in two colours, and the picture it is said
 * against. The soft mark behind the words is the build's rather than the
 * client's.
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
 * The Home page's introduction.
 */
class Home_Introduce extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'home-introduce';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Home — Introduce', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-text-area';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'home', 'introduce', 'about', 'statement' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'button_text' => esc_html__( 'Get to know CAVO', 'custom-elementor-widgets' ),
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
			'section_content',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		// Two colours run through one sentence, so the words are written where
		// a colour can be given to a phrase rather than to the whole of them.
		$this->add_control(
			'body',
			array(
				'label' => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::WYSIWYG,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['button_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'button_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'picture',
			array(
				'label'     => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
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
				'label'     => esc_html__( 'Background, lightest', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E8E4DB',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-introduce' => '--custom-home-ground-near: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ground_mid',
			array(
				'label'     => esc_html__( 'Background, middle', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C8BCA9',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-introduce' => '--custom-home-ground-mid: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ground_far',
			array(
				'label'     => esc_html__( 'Background, deepest', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#A89376',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-introduce' => '--custom-home-ground-far: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-introduce__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-introduce__body',
				'fields_options' => array(
					'typography'      => array( 'default' => 'yes' ),
					'font_family'     => array( 'default' => 'Roboto' ),
					'font_size'       => array( 'default' => array( 'unit' => 'px', 'size' => 36 ) ),
					'line_height'     => array( 'default' => array( 'unit' => 'px', 'size' => 45 ) ),
					'text_transform'  => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-introduce__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-introduce__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-introduce__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
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

		$picture = isset( $settings['picture']['url'] ) ? $settings['picture']['url'] : '';
		$button  = $this->text( $settings, 'button_text' );
		$body    = isset( $settings['body'] ) ? $settings['body'] : '';
		?>
		<div class="custom-home-introduce">
			<div class="custom-home-introduce__column">
				<span class="custom-home-introduce__mark" aria-hidden="true"></span>

				<div class="custom-home-introduce__body"><?php
					echo wp_kses_post( $body );
				?></div>

				<?php if ( '' !== $button ) : ?>
					<a class="custom-home-introduce__button"<?php
						echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>><?php echo esc_html( $button ); ?></a>
				<?php endif; ?>

				<?php
				if ( '' === trim( wp_strip_all_tags( (string) $body ) ) ) {
					$this->editor_hint( __( 'This section is waiting for its words, on the Content tab.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<span class="custom-home-introduce__picture"><?php $this->media( $picture ); ?></span>
		</div>
		<?php
	}
}
