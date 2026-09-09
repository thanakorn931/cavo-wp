<?php
/**
 * Dining, detail — one section of the design.
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
 * The Dining page's detail band.
 */
class Dining_Detail extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'dining-detail';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Dining — Detail', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-text-align-left';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'dining', 'detail', 'hours' );
	}

	/**
	 * The words the design settles.
	 *
	 * The two headings and the two paragraphs are lorem in the file, which
	 * settles their place and their size but not what they say, so nothing
	 * stands behind those.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'hours_label' => esc_html__( 'Dining hours', 'custom-elementor-widgets' ),
			'hours_value' => esc_html__( '17:00 - 21:30 daily', 'custom-elementor-widgets' ),
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
		$this->register_picture_controls();
		$this->register_word_controls();
		$this->register_hours_controls();
		$this->register_style_controls();
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
			'picture_one' => esc_html__( 'Left picture', 'custom-elementor-widgets' ),
			'picture_two' => esc_html__( 'Right picture', 'custom-elementor-widgets' ),
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
	 * Content → Words.
	 */
	private function register_word_controls() {
		$this->start_controls_section(
			'section_words',
			array(
				'label' => esc_html__( 'Words', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
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

		foreach ( array( 'one', 'two' ) as $index => $which ) {
			$this->add_control(
				'heading_' . $which,
				array(
					'label'     => sprintf(
						/* translators: %d: which of the two blocks. */
						esc_html__( 'Heading %d', 'custom-elementor-widgets' ),
						$index + 1
					),
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array( 'active' => true ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'body_' . $which,
				array(
					'label'   => sprintf(
						/* translators: %d: which of the two blocks. */
						esc_html__( 'Text %d', 'custom-elementor-widgets' ),
						$index + 1
					),
					'type'    => Controls_Manager::TEXTAREA,
					'dynamic' => array( 'active' => true ),
					'rows'    => 5,
				)
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Content → Hours.
	 */
	private function register_hours_controls() {
		$design = $this->design_text();

		$this->start_controls_section(
			'section_hours',
			array(
				'label' => esc_html__( 'Hours', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'hours_label',
			array(
				'label'       => esc_html__( 'Label', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['hours_label'],
			)
		);

		$this->add_control(
			'hours_value',
			array(
				'label'       => esc_html__( 'Hours', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['hours_value'],
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

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-detail__column' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-detail__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 24 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 20 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 20 ),
					),
					'font_weight' => array( 'default' => '400' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-detail__body',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 12 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 12 ),
					),
				),
			)
		);

		$this->add_control(
			'hours_background',
			array(
				'label'     => esc_html__( 'Hours background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-detail__hours' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hours_label_color',
			array(
				'label'     => esc_html__( 'Hours label', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-detail__hours' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'hours_label_typography',
				'label'          => esc_html__( 'Hours label', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-detail__hours-label',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 18 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 9 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 9 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'hours_value_color',
			array(
				'label'     => esc_html__( 'Hours', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6D6D6D',
				'selectors' => array(
					'{{WRAPPER}} .custom-dining-detail__hours-value' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'hours_value_typography',
				'label'          => esc_html__( 'Hours', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-dining-detail__hours-value',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 8 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 8 ),
					),
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

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-dining-detail">
			<div class="custom-dining-detail__pictures">
				<?php
				$this->render_picture( $settings, 'picture_one', 'one' );
				$this->render_picture( $settings, 'picture_two', 'two' );
				?>
			</div>

			<div class="custom-dining-detail__column">
				<?php foreach ( array( 'one', 'two' ) as $which ) : ?>
					<div class="custom-dining-detail__block">
						<<?php echo esc_attr( $tag ); ?> class="custom-dining-detail__heading"><?php
							echo esc_html( isset( $settings[ 'heading_' . $which ] ) ? $settings[ 'heading_' . $which ] : '' );
						?></<?php echo esc_attr( $tag ); ?>>

						<p class="custom-dining-detail__body"><?php
							echo esc_html( isset( $settings[ 'body_' . $which ] ) ? $settings[ 'body_' . $which ] : '' );
						?></p>
					</div>
				<?php endforeach; ?>

				<div class="custom-dining-detail__hours">
					<span class="custom-dining-detail__hours-icon" aria-hidden="true"><?php $this->render_timer(); ?></span>

					<span class="custom-dining-detail__hours-words">
						<span class="custom-dining-detail__hours-label"><?php
							echo esc_html( $this->text( $settings, 'hours_label' ) );
						?></span>
						<span class="custom-dining-detail__hours-value"><?php
							echo esc_html( $this->text( $settings, 'hours_value' ) );
						?></span>
					</span>
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
	 * @param string $which    Which of the two, for the corners it is cut on.
	 */
	private function render_picture( $settings, $key, $which ) {
		$url = isset( $settings[ $key ]['url'] ) ? $settings[ $key ]['url'] : '';
		?>
		<div class="custom-dining-detail__picture custom-dining-detail__picture--<?php echo esc_attr( $which ); ?>">
			<?php $this->media( $url ); ?>
		</div>
		<?php
	}

	/**
	 * The timer beside the hours — the section's own mark, not the client's.
	 */
	private function render_timer() {
		?>
		<svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M17 7.4375C13.7124 7.4375 10.5595 8.74349 8.23482 11.0682C5.91015 13.3928 4.60417 16.5458 4.60417 19.8333C4.60417 23.1209 5.91015 26.2738 8.23482 28.5985C10.5595 30.9232 13.7124 32.2292 17 32.2292C20.2876 32.2292 23.4405 30.9232 25.7652 28.5985C28.0898 26.2738 29.3958 23.1209 29.3958 19.8333C29.3958 16.5458 28.0898 13.3928 25.7652 11.0682C23.4405 8.74349 20.2876 7.4375 17 7.4375ZM18.0625 14.1667C18.0625 13.8849 17.9506 13.6146 17.7513 13.4154C17.552 13.2161 17.2818 13.1042 17 13.1042C16.7182 13.1042 16.448 13.2161 16.2487 13.4154C16.0494 13.6146 15.9375 13.8849 15.9375 14.1667V19.8333C15.9375 20.1151 16.0494 20.3854 16.2487 20.5846C16.448 20.7839 16.7182 20.8958 17 20.8958C17.2818 20.8958 17.552 20.7839 17.7513 20.5846C17.9506 20.3854 18.0625 20.1151 18.0625 19.8333V14.1667ZM11.6875 2.83333C11.6875 2.55154 11.7994 2.28129 11.9987 2.08203C12.198 1.88278 12.4682 1.77083 12.75 1.77083H21.25C21.5318 1.77083 21.802 1.88278 22.0013 2.08203C22.2006 2.28129 22.3125 2.55154 22.3125 2.83333C22.3125 3.11513 22.2006 3.38538 22.0013 3.58463C21.802 3.78389 21.5318 3.89583 21.25 3.89583H12.75C12.4682 3.89583 12.198 3.78389 11.9987 3.58463C11.7994 3.38538 11.6875 3.11513 11.6875 2.83333Z" fill="currentColor"/></svg>
		<?php
	}
}
