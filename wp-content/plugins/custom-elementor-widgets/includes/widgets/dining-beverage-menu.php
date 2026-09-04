<?php
/**
 * Dining, beverage menu — one section of the design.
 *
 * The button opens a menu the client has uploaded, in a tab of its own.
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
 * The Dining page's beverage menu section.
 */
class Dining_Beverage_Menu extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dining-beverage-menu';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dining — Beverage Menu', 'custom-elementor-widgets' );
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
		return array( 'dining', 'beverage', 'cocktail', 'menu', 'pdf' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'     => esc_html__( 'For cocktail', 'custom-elementor-widgets' ),
			'body'        => esc_html__( "At CAVO, every cocktail is part of The Journey of Discovery. Inspired by the Mediterranean Cultural Triad, our cocktail program explores the bright citrus traditions of Southern Europe, the aromatic botanicals and spices of Western Asia, and the bold flavours of North Africa.\n\nCrafted with premium spirits, seasonal ingredients, and refined techniques, every cocktail is designed to complement the journey from aperitif to celebration.", 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'View beverage menu', 'custom-elementor-widgets' ),
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
			'body',
			array(
				'label'       => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 8,
				'placeholder' => $design['body'],
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

		$this->add_control(
			'menu_file',
			array(
				'label'       => esc_html__( 'Menu (PDF)', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'application' ),
				'description' => esc_html__( 'One file. The button opens it in a tab of its own.', 'custom-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pictures',
			array(
				'label' => esc_html__( 'Pictures', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'background_picture',
			array(
				'label' => esc_html__( 'Behind the section', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		foreach ( array(
			'picture_one'  => esc_html__( 'Upper left', 'custom-elementor-widgets' ),
			'picture_two'  => esc_html__( 'Upper right', 'custom-elementor-widgets' ),
			'picture_wide' => esc_html__( 'Across the bottom', 'custom-elementor-widgets' ),
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
			'text_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-beverage__column' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-beverage__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-beverage__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-beverage__button',
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
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-beverage__button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-dining-beverage__button' => 'background-color: {{VALUE}};',
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

		$background = isset( $settings['background_picture']['url'] ) ? $settings['background_picture']['url'] : '';
		$file       = isset( $settings['menu_file']['url'] ) ? $settings['menu_file']['url'] : '';
		$tag        = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag        = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-dining-beverage">
			<div class="custom-dining-beverage__picture" aria-hidden="true">
				<?php if ( '' !== $background ) : ?>
					<img src="<?php echo esc_url( $background ); ?>" alt="" />
				<?php endif; ?>
			</div>

			<div class="custom-dining-beverage__veil" aria-hidden="true"></div>

			<div class="custom-dining-beverage__row">
				<div class="custom-dining-beverage__column">
					<div class="custom-dining-beverage__words">
						<<?php echo esc_attr( $tag ); ?> class="custom-dining-beverage__heading"><?php
							echo esc_html( $this->text( $settings, 'heading' ) );
						?></<?php echo esc_attr( $tag ); ?>>

						<p class="custom-dining-beverage__body"><?php
							echo esc_html( $this->text( $settings, 'body' ) );
						?></p>
					</div>

					<a
						class="custom-dining-beverage__button"
						<?php if ( '' !== $file ) : ?>
							href="<?php echo esc_url( $file ); ?>" target="_blank" rel="noopener noreferrer"
						<?php endif; ?>
					><?php echo esc_html( $this->text( $settings, 'button_text' ) ); ?></a>
				</div>

				<div class="custom-dining-beverage__gallery">
					<?php
					$this->render_tile( $settings, 'picture_one', 'one' );
					$this->render_tile( $settings, 'picture_two', 'two' );
					$this->render_tile( $settings, 'picture_wide', 'wide' );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * One picture slot — a tile first, whether or not a picture is in it.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @param string $which    Which tile, for the corners it is cut on.
	 */
	private function render_tile( $settings, $key, $which ) {
		$url = isset( $settings[ $key ]['url'] ) ? $settings[ $key ]['url'] : '';
		?>
		<div class="custom-dining-beverage__tile custom-dining-beverage__tile--<?php echo esc_attr( $which ); ?>">
			<?php if ( '' !== $url ) : ?>
				<img src="<?php echo esc_url( $url ); ?>" alt="" />
			<?php endif; ?>
		</div>
		<?php
	}
}
