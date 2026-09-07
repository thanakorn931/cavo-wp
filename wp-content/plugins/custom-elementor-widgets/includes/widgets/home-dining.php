<?php
/**
 * Home, dining — one section of the design.
 *
 * The invitation, and five pictures laid out the way the file lays them.
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
 * The Home page's gallery.
 */
class Home_Dining extends Base_Widget {

	/**
	 * The five slots the file draws, in the order it draws them.
	 *
	 * @return array
	 */
	private function slots() {
		return array(
			'tall'   => esc_html__( 'Picture, upper left', 'custom-elementor-widgets' ),
			'wide'   => esc_html__( 'Picture, across the top', 'custom-elementor-widgets' ),
			'left'   => esc_html__( 'Picture, lower left', 'custom-elementor-widgets' ),
			'middle' => esc_html__( 'Picture, middle', 'custom-elementor-widgets' ),
			'right'  => esc_html__( 'Picture, right', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'home-dining';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Home — Dining', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'home', 'dining', 'gallery', 'experience' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'     => esc_html__( 'Experience it yourself at CAVO', 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'Reserve a table', 'custom-elementor-widgets' ),
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
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['button_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'button_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pictures',
			array(
				'label' => esc_html__( 'Pictures', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( $this->slots() as $key => $label ) {
			$this->add_control(
				'picture_' . $key,
				array(
					'label' => $label,
					'type'  => Controls_Manager::MEDIA,
				)
			);
		}

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

		$grounds = array(
			'ground_one'   => array( esc_html__( 'Background, centre', 'custom-elementor-widgets' ), '#29180E' ),
			'ground_two'   => array( esc_html__( 'Background, second', 'custom-elementor-widgets' ), '#3A2114' ),
			'ground_three' => array( esc_html__( 'Background, third', 'custom-elementor-widgets' ), '#5C2F1A' ),
			'ground_four'  => array( esc_html__( 'Background, edge', 'custom-elementor-widgets' ), '#7D3D20' ),
		);

		$slot = 1;

		foreach ( $grounds as $name => $ground ) {
			$this->add_control(
				$name,
				array(
					'label'     => $ground[0],
					'type'      => Controls_Manager::COLOR,
					'default'   => $ground[1],
					'selectors' => array(
						'{{WRAPPER}} .custom-home-dining' => '--custom-home-dining-' . $slot . ': {{VALUE}};',
					),
				)
			);

			++$slot;
		}

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-dining__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-dining__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-dining__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-dining__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-dining__button',
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

		$button = $this->text( $settings, 'button_text' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-home-dining">
			<div class="custom-home-dining__head">
				<<?php echo esc_attr( $tag ); ?> class="custom-home-dining__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>

				<?php if ( '' !== $button ) : ?>
					<a class="custom-home-dining__button"<?php
						echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>><?php echo esc_html( $button ); ?></a>
				<?php endif; ?>
			</div>

			<div class="custom-home-dining__grid">
				<?php foreach ( array_keys( $this->slots() ) as $key ) : ?>
					<span class="custom-home-dining__slot custom-home-dining__slot--<?php echo esc_attr( $key ); ?>">
						<?php $this->media( isset( $settings[ 'picture_' . $key ]['url'] ) ? $settings[ 'picture_' . $key ]['url'] : '', '', true ); ?>
					</span>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
