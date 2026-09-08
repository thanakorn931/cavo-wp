<?php
/**
 * Contact, hero — one section of the design.
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
 * The Contact page's name.
 */
class Contact_Hero extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'contact-hero';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Contact — Hero', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-heading';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'contact', 'hero', 'heading' );
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
				'placeholder' => esc_html__( 'Contact us', 'custom-elementor-widgets' ),
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
					'{{WRAPPER}} .custom-contact-hero' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-contact-hero__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-contact-hero__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
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

		$heading = isset( $settings['heading'] ) ? trim( (string) $settings['heading'] ) : '';
		$heading = '' !== $heading ? $heading : esc_html__( 'Contact us', 'custom-elementor-widgets' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h1';
		$tag = in_array( $tag, array( 'h1', 'h2', 'span' ), true ) ? $tag : 'h1';
		?>
		<div class="custom-contact-hero">
			<<?php echo esc_attr( $tag ); ?> class="custom-contact-hero__heading"><?php
				echo esc_html( $heading );
			?></<?php echo esc_attr( $tag ); ?>>
		</div>
		<?php
	}
}
