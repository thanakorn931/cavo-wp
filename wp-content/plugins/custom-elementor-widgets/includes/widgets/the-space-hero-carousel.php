<?php
/**
 * The Space, hero carousel — one section of the design.
 *
 * The title and the tour link belong to whichever slide is in the middle, so
 * they are the slide's and travel with it. How many slides there are is the
 * client's, so they are a repeater, and it ships with none.
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
 * The Space's hero carousel.
 */
class The_Space_Hero_Carousel extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'the-space-hero-carousel';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'The Space — Hero Carousel', 'custom-elementor-widgets' );
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
		return array( 'space', 'hero', 'carousel', 'slider', 'gallery' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_slide_controls();
		$this->register_action_controls();
		$this->register_title_style_controls();
		$this->register_action_style_controls();
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

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_title',
			array(
				'label'   => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'slide_words',
			array(
				'label'   => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'rows'    => 5,
			)
		);

		$repeater->add_control(
			'slide_picture',
			array(
				'label' => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_link_controls( $repeater, 'slide_host_link', esc_html__( 'Host the area link', 'custom-elementor-widgets' ) );

		$this->add_link_controls( $repeater, 'slide_link', esc_html__( 'Virtual tour link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'Slides', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ slide_title }}}',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'Title level', 'custom-elementor-widgets' ),
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
	}

	/**
	 * Content → Actions.
	 */
	private function register_action_controls() {
		$this->start_controls_section(
			'section_actions',
			array(
				'label' => esc_html__( 'Actions', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Host The Area', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'link_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The words and the icon are the same on every slide. Where each of the two links goes is the slide\'s own, on the Slides tab.', 'custom-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->add_control(
			'tour_text',
			array(
				'label'       => esc_html__( 'Tour link text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Virtual tour', 'custom-elementor-widgets' ),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'tour_icon',
			array(
				'label'       => esc_html__( 'Tour icon', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Title.
	 */
	private function register_title_style_controls() {
		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'band_background',
			array(
				'label'       => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#FAF6EA',
				'description' => esc_html__( 'The file leaves this band clear and puts the colour on the page behind it. The band carries it here so the page needs no setting of its own; clear this to give it back to the page.', 'custom-elementor-widgets' ),
				'selectors'   => array(
					'{{WRAPPER}} .custom-space-hero' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'selector'       => '{{WRAPPER}} .custom-space-hero__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Colour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-space-hero__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Actions.
	 */
	private function register_action_style_controls() {
		$this->start_controls_section(
			'section_action_style',
			array(
				'label' => esc_html__( 'Actions', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'action_typography',
				'selector'       => '{{WRAPPER}} .custom-space-hero__button, {{WRAPPER}} .custom-space-hero__tour-link',
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
					'{{WRAPPER}} .custom-space-hero__button' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-space-hero__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tour_color',
			array(
				'label'     => esc_html__( 'Tour link', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-space-hero__tour'      => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-space-hero__tour-link' => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-space-hero__tour svg'  => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'banner_background',
			array(
				'label'     => esc_html__( 'Banner background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7D6B50',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-space-hero__banner' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'words_color',
			array(
				'label'     => esc_html__( 'Banner text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-space-hero__words' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'words_typography',
				'label'          => esc_html__( 'Banner text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-space-hero__words',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 24 ) ),
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
					'{{WRAPPER}} .custom-space-hero__arrow'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-space-hero__arrow svg' => 'fill: {{VALUE}};',
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

		// The repeater is the slides. The title, the actions and the band the
		// slides run through are the section, and stand whether or not any
		// slides have been added yet.
		$first = ! empty( $slides ) ? reset( $slides ) : array();
		$tag   = isset( $settings['title_tag'] ) ? $settings['title_tag'] : 'h1';
		$tag   = in_array( $tag, array( 'h1', 'h2', 'span' ), true ) ? $tag : 'h1';
		?>
		<div class="custom-space-hero">
			<div class="custom-space-hero__band">
			<<?php echo esc_attr( $tag ); ?> class="custom-space-hero__title"><?php
				echo esc_html( isset( $first['slide_title'] ) ? $first['slide_title'] : '' );
			?></<?php echo esc_attr( $tag ); ?>>

			<?php $this->render_actions( $settings, $first ); ?>

			<div class="custom-space-hero__stage">
				<div class="custom-space-hero__track">
					<?php foreach ( $slides as $slide ) : ?>
						<div
							class="custom-space-hero__slide"
							data-title="<?php echo esc_attr( isset( $slide['slide_title'] ) ? $slide['slide_title'] : '' ); ?>"
							data-words="<?php echo esc_attr( isset( $slide['slide_words'] ) ? $slide['slide_words'] : '' ); ?>"
							data-host="<?php echo esc_attr( isset( $slide['slide_host_link'] ) ? $slide['slide_host_link'] : '' ); ?>"
							data-host-blank="<?php echo esc_attr( isset( $slide['slide_host_link_blank'] ) ? $slide['slide_host_link_blank'] : '' ); ?>"
							data-host-nofollow="<?php echo esc_attr( isset( $slide['slide_host_link_nofollow'] ) ? $slide['slide_host_link_nofollow'] : '' ); ?>"
							data-link="<?php echo esc_attr( isset( $slide['slide_link'] ) ? $slide['slide_link'] : '' ); ?>"
							data-link-blank="<?php echo esc_attr( isset( $slide['slide_link_blank'] ) ? $slide['slide_link_blank'] : '' ); ?>"
							data-link-nofollow="<?php echo esc_attr( isset( $slide['slide_link_nofollow'] ) ? $slide['slide_link_nofollow'] : '' ); ?>"
						>
							<?php $this->media(
								isset( $slide['slide_picture']['url'] ) ? $slide['slide_picture']['url'] : '',
								isset( $slide['slide_title'] ) ? $slide['slide_title'] : ''
							); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php
				// An arrow means nothing until there is somewhere else to go.
				if ( count( $slides ) > 1 ) {
					$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
					$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<?php
			if ( empty( $slides ) ) {
				$this->editor_hint( __( 'This carousel is waiting for its slides, on the Content tab.', 'custom-elementor-widgets' ) );
			}
			?>
			</div>

			<div class="custom-space-hero__banner">
				<p class="custom-space-hero__words"><?php
					echo esc_html( isset( $first['slide_words'] ) ? $first['slide_words'] : '' );
				?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * The button and the tour link above the slides.
	 *
	 * @param array $settings The widget's settings.
	 * @param array $first    The slide the title starts on.
	 */
	private function render_actions( $settings, $first ) {
		$button = isset( $settings['button_text'] ) ? trim( (string) $settings['button_text'] ) : '';
		$button = '' !== $button ? $button : esc_html__( 'Host The Area', 'custom-elementor-widgets' );

		$tour = isset( $settings['tour_text'] ) ? trim( (string) $settings['tour_text'] ) : '';
		$tour = '' !== $tour ? $tour : esc_html__( 'Virtual tour', 'custom-elementor-widgets' );

		?>
		<div class="custom-space-hero__actions">
			<a class="custom-space-hero__button"<?php
				echo $this->link_from( $first, 'slide_host_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
			?>><?php echo esc_html( $button ); ?></a>

			<span class="custom-space-hero__tour">
				<a class="custom-space-hero__tour-link"<?php
					echo $this->link_from( $first, 'slide_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $tour ); ?></a>
				<?php if ( ! empty( $settings['tour_icon']['value'] ) ) : ?>
					<span class="custom-space-hero__tour-icon">
						<?php Icons_Manager::render_icon( $settings['tour_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					</span>
				<?php endif; ?>
			</span>
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
			class="custom-the-space-hero-carousel__arrow custom-the-space-hero-carousel__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><svg viewBox="10 6 56 56" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M50.0008 34.0006C50.0008 34.2658 49.8954 34.5201 49.7079 34.7077C49.5204 34.8952 49.266 35.0006 49.0008 35.0006H29.4145L36.7083 42.2931C36.8012 42.386 36.8749 42.4963 36.9252 42.6177C36.9755 42.7391 37.0013 42.8692 37.0013 43.0006C37.0013 43.132 36.9755 43.2621 36.9252 43.3835C36.8749 43.5048 36.8012 43.6151 36.7083 43.7081C36.6154 43.801 36.5051 43.8747 36.3837 43.9249C36.2623 43.9752 36.1322 44.0011 36.0008 44.0011C35.8694 44.0011 35.7393 43.9752 35.6179 43.9249C35.4965 43.8747 35.3862 43.801 35.2933 43.7081L26.2933 34.7081C26.2003 34.6152 26.1266 34.5049 26.0762 34.3835C26.0259 34.2621 26 34.132 26 34.0006C26 33.8691 26.0259 33.739 26.0762 33.6176C26.1266 33.4962 26.2003 33.3859 26.2933 33.2931L35.2933 24.2931C35.4809 24.1054 35.7354 24 36.0008 24C36.2662 24 36.5206 24.1054 36.7083 24.2931C36.8959 24.4807 37.0013 24.7352 37.0013 25.0006C37.0013 25.2659 36.8959 25.5204 36.7083 25.7081L29.4145 33.0006H49.0008C49.266 33.0006 49.5204 33.1059 49.7079 33.2934C49.8954 33.481 50.0008 33.7353 50.0008 34.0006Z" fill="#121212"/></svg></button>
		<?php
	}
}
