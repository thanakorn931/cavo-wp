<?php
/**
 * 404, page not found — one section, the whole of the page it stands on.
 *
 * The file does not draw this page. It is arranged to suit the others: the
 * ground, the ink, the compressed face for what is large and Roboto for what
 * is read, and the one filled button the site presses with.
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
 * The page shown where there is no page.
 */
class Not_Found extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'not-found';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( '404 — Page not found', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-alert';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( '404', 'not found', 'error', 'missing' );
	}

	/**
	 * What each field says when the client has said nothing.
	 *
	 * @return array
	 */
	private function words() {
		return array(
			'code'    => '404',
			'heading' => esc_html__( 'Page not found', 'custom-elementor-widgets' ),
			'body'    => esc_html__( 'The page you are looking for has moved, or was never here.', 'custom-elementor-widgets' ),
			'button'  => esc_html__( 'Back to home', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$words = $this->words();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'code',
			array(
				'label'       => esc_html__( 'Number', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $words['code'],
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $words['heading'],
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
				'label'       => esc_html__( 'Body', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 3,
				'placeholder' => $words['body'],
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $words['button'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls(
			$this,
			'button_link',
			esc_html__( 'Button link', 'custom-elementor-widgets' ),
			array(
				'placeholder' => home_url( '/' ),
				'description' => esc_html__( 'Left empty, the button goes to the home page.', 'custom-elementor-widgets' ),
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
			'background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-not-found' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'code_color',
			array(
				'label'     => esc_html__( 'Number', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-not-found__code' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'code_typography',
				'label'          => esc_html__( 'Number', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-not-found__code',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 200 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 128 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 96 ),
					),
					'font_weight' => array( 'default' => '500' ),
					'line_height' => array(
						'default'        => array( 'unit' => 'px', 'size' => 160 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 104 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 80 ),
					),
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-not-found__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-not-found__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 64 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 40 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Body', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-not-found__body' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Body', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-not-found__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 14 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 12 ),
					),
					'line_height' => array(
						'default'        => array( 'unit' => 'px', 'size' => 24 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 20 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 18 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-not-found__button',
				'separator'      => 'before',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 14 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 12 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 9 ),
					),
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-not-found__button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-not-found__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * One field's words, or what it says when empty.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @param string $fallback What it says when empty.
	 * @return string
	 */
	private function say( $settings, $key, $fallback ) {
		$value = isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';

		return '' !== $value ? $value : $fallback;
	}

	/**
	 * Print the section.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$words    = $this->words();

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h1';
		$tag = in_array( $tag, array( 'h1', 'h2', 'span' ), true ) ? $tag : 'h1';

		// The button goes where it is pointed, and home where it is not.
		$link = $this->link_from( $settings, 'button_link' );
		$link = '' !== $link ? $link : ' href="' . esc_url( home_url( '/' ) ) . '"';
		?>
		<div class="custom-not-found">
			<p class="custom-not-found__code" aria-hidden="true"><?php echo esc_html( $this->say( $settings, 'code', $words['code'] ) ); ?></p>
			<<?php echo esc_attr( $tag ); ?> class="custom-not-found__heading"><?php
				echo esc_html( $this->say( $settings, 'heading', $words['heading'] ) );
			?></<?php echo esc_attr( $tag ); ?>>
			<p class="custom-not-found__body"><?php echo esc_html( $this->say( $settings, 'body', $words['body'] ) ); ?></p>
			<a class="custom-not-found__button"<?php echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes() or esc_url(). ?>><?php
				echo esc_html( $this->say( $settings, 'button_text', $words['button'] ) );
			?></a>
		</div>
		<?php
	}
}
