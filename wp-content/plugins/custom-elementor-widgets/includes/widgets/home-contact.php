<?php
/**
 * Home, contact — one section of the design.
 *
 * Where the place is and how to reach it down the left, the picture down the
 * right. Only the hours are a list the client adds to; the rest is what the
 * design settles. The marks beside the three ways of reaching it are the
 * build's rather than the client's.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Home page's contact.
 */
class Home_Contact extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'home-contact';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Home — Contact', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-map-pin';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'home', 'contact', 'location', 'hours' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'       => esc_html__( 'Contact us', 'custom-elementor-widgets' ),
			'location_title' => esc_html__( 'Location', 'custom-elementor-widgets' ),
			'getting_title' => esc_html__( 'Getting Here', 'custom-elementor-widgets' ),
			'station_title' => esc_html__( 'Nearest BTS Station', 'custom-elementor-widgets' ),
			'hours_title'   => esc_html__( 'Operating format', 'custom-elementor-widgets' ),
			'contact_title' => esc_html__( 'Contact Info', 'custom-elementor-widgets' ),
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
			'location_title',
			array(
				'label'       => esc_html__( 'Location heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['location_title'],
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'location_body',
			array(
				'label' => esc_html__( 'Location', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$this->add_control(
			'getting_title',
			array(
				'label'       => esc_html__( 'Getting here heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['getting_title'],
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'getting_body',
			array(
				'label' => esc_html__( 'Getting here', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$this->add_control(
			'station_title',
			array(
				'label'       => esc_html__( 'Station heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['station_title'],
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'station_body',
			array(
				'label' => esc_html__( 'Station', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::TEXTAREA,
				'rows'  => 5,
			)
		);

		$this->add_control(
			'map',
			array(
				'label'     => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hours',
			array(
				'label' => esc_html__( 'Hours', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'hours_title',
			array(
				'label'       => esc_html__( 'Hours heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['hours_title'],
			)
		);

		$hours = new Repeater();

		$hours->add_control(
			'name',
			array(
				'label'       => esc_html__( 'Name', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$hours->add_control(
			'time',
			array(
				'label'       => esc_html__( 'Time', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'hours',
			array(
				'label'       => esc_html__( 'Hours', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $hours->get_controls(),
				'title_field' => '{{{ name }}}',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_reach',
			array(
				'label' => esc_html__( 'Contact info', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'contact_title',
			array(
				'label'       => esc_html__( 'Contact heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['contact_title'],
			)
		);

		foreach ( $this->ways() as $key => $label ) {
			$this->add_control(
				$key . '_text',
				array(
					'label'     => $label,
					'type'      => Controls_Manager::TEXT,
					'separator' => 'before',
				)
			);

			$this->add_link_controls( $this, $key . '_link', $label . ' ' . esc_html__( 'link', 'custom-elementor-widgets' ) );
		}

		$this->end_controls_section();

		$this->register_style_controls();
	}

	/**
	 * The three ways of reaching the place, in the order the file draws them.
	 *
	 * @return array
	 */
	private function ways() {
		return array(
			'phone' => esc_html__( 'Phone', 'custom-elementor-widgets' ),
			'email' => esc_html__( 'Email', 'custom-elementor-widgets' ),
			'chat'  => esc_html__( 'WhatsApp', 'custom-elementor-widgets' ),
		);
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
			'background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-contact' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-home-contact__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-contact__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-contact__column' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Block headings', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-contact__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-home-contact__body, {{WRAPPER}} .custom-home-contact__hour, {{WRAPPER}} .custom-home-contact__way',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_control(
			'mark_background',
			array(
				'label'     => esc_html__( 'Behind each mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-contact__mark' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mark_color',
			array(
				'label'     => esc_html__( 'Each mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-home-contact__mark' => 'color: {{VALUE}};',
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

		$map   = isset( $settings['map']['url'] ) ? $settings['map']['url'] : '';
		$hours = isset( $settings['hours'] ) ? (array) $settings['hours'] : array();

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-home-contact">
			<div class="custom-home-contact__column">
				<<?php echo esc_attr( $tag ); ?> class="custom-home-contact__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>

				<?php
				$this->render_block( $this->text( $settings, 'location_title' ), isset( $settings['location_body'] ) ? $settings['location_body'] : '' );
				$this->render_block( $this->text( $settings, 'getting_title' ), isset( $settings['getting_body'] ) ? $settings['getting_body'] : '' );
				$this->render_block( $this->text( $settings, 'station_title' ), isset( $settings['station_body'] ) ? $settings['station_body'] : '' );
				?>

				<div class="custom-home-contact__block">
					<p class="custom-home-contact__title"><?php
						echo esc_html( $this->text( $settings, 'hours_title' ) );
					?></p>

					<?php foreach ( $hours as $hour ) : ?>
						<p class="custom-home-contact__hour">
							<span class="custom-home-contact__hour-name"><?php
								echo esc_html( isset( $hour['name'] ) ? $hour['name'] : '' );
							?></span>
							<span class="custom-home-contact__hour-time"><?php
								echo esc_html( isset( $hour['time'] ) ? $hour['time'] : '' );
							?></span>
						</p>
					<?php endforeach; ?>
				</div>

				<div class="custom-home-contact__block">
					<p class="custom-home-contact__title"><?php
						echo esc_html( $this->text( $settings, 'contact_title' ) );
					?></p>

					<div class="custom-home-contact__ways">
						<?php foreach ( array_keys( $this->ways() ) as $key ) : ?>
							<?php $this->render_way( $settings, $key ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<span class="custom-home-contact__map"><?php $this->media( $map ); ?></span>
		</div>
		<?php
	}

	/**
	 * One block: its heading and what it says.
	 *
	 * @param string $title What it is called.
	 * @param string $body  What it says.
	 */
	private function render_block( $title, $body ) {
		$body = trim( (string) $body );

		if ( '' === $title && '' === $body ) {
			return;
		}
		?>
		<div class="custom-home-contact__block">
			<?php if ( '' !== $title ) : ?>
				<p class="custom-home-contact__title"><?php echo esc_html( $title ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $body ) : ?>
				<p class="custom-home-contact__body"><?php echo esc_html( $body ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * One way of reaching the place: its mark, and what it says.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      Which way.
	 */
	private function render_way( $settings, $key ) {
		$said = isset( $settings[ $key . '_text' ] ) ? trim( (string) $settings[ $key . '_text' ] ) : '';

		if ( '' === $said ) {
			return;
		}

		$link = isset( $settings[ $key . '_link' ] ) ? trim( (string) $settings[ $key . '_link' ] ) : '';
		$tag  = '' === $link ? 'span' : 'a';
		?>
		<<?php echo esc_attr( $tag ); ?> class="custom-home-contact__way"<?php
			echo 'a' === $tag ? $this->link_from( $settings, $key . '_link' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
		?>>
			<span class="custom-home-contact__mark"><?php $this->render_mark( $key ); ?></span>
			<span><?php echo esc_html( $said ); ?></span>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}

	/**
	 * One of the marks the design draws, which the widget carries itself.
	 *
	 * @param string $which Which mark.
	 */
	private function render_mark( $which ) {
		$paths = array(
			'phone' => 'M14.4925 10.9425C14.3811 11.7894 13.9651 12.5668 13.3224 13.1294C12.6797 13.6921 11.8542 14.0015 11 14C6.0375 14 2 9.9625 2 5C1.99847 4.14581 2.30793 3.32026 2.87058 2.67756C3.43324 2.03486 4.21061 1.61894 5.0575 1.5075C5.27166 1.48135 5.48853 1.52516 5.67574 1.6324C5.86295 1.73963 6.01046 1.90454 6.09625 2.1025L7.41625 5.04937V5.05688C7.48193 5.20841 7.50905 5.37385 7.4952 5.53842C7.48135 5.703 7.42696 5.86158 7.33688 6C7.32563 6.01688 7.31375 6.0325 7.30125 6.04812L6 7.59062C6.46813 8.54187 7.46313 9.52813 8.42688 9.9975L9.94813 8.70313C9.96306 8.69057 9.97872 8.67888 9.995 8.66813C10.1333 8.57588 10.2924 8.51957 10.458 8.50429C10.6235 8.48901 10.7903 8.51525 10.9431 8.58062L10.9513 8.58438L13.8956 9.90375C14.0939 9.98923 14.2592 10.1366 14.3668 10.3239C14.4744 10.5111 14.5185 10.7281 14.4925 10.9425Z',
			'email' => 'M14 3H2C1.86739 3 1.74021 3.05268 1.64645 3.14645C1.55268 3.24021 1.5 3.36739 1.5 3.5V12C1.5 12.2652 1.60536 12.5196 1.79289 12.7071C1.98043 12.8946 2.23478 13 2.5 13H13.5C13.7652 13 14.0196 12.8946 14.2071 12.7071C14.3946 12.5196 14.5 12.2652 14.5 12V3.5C14.5 3.36739 14.4473 3.24021 14.3536 3.14645C14.2598 3.05268 14.1326 3 14 3ZM6.16937 8L2.5 11.3631V4.63688L6.16937 8ZM6.90938 8.67813L7.65938 9.36875C7.75162 9.45343 7.87228 9.50041 7.9975 9.50041C8.12272 9.50041 8.24338 9.45343 8.33562 9.36875L9.08562 8.67813L12.7106 12H3.28562L6.90938 8.67813ZM9.83062 8L13.5 4.63625V11.3638L9.83062 8Z',
			'chat'  => 'M9.53625 9.07687L10.9738 9.79438C10.9056 10.1349 10.7215 10.4411 10.4527 10.661C10.1839 10.8809 9.84723 11.0007 9.5 11C8.30693 10.9987 7.16311 10.5241 6.31948 9.68052C5.47585 8.83689 5.00132 7.69307 5 6.5C4.9999 6.1532 5.11998 5.81708 5.33979 5.54884C5.5596 5.2806 5.86557 5.09681 6.20563 5.02875L6.92313 6.46625L6.3125 7.375C6.26687 7.44345 6.23883 7.52209 6.23087 7.60397C6.22292 7.68585 6.23528 7.76842 6.26688 7.84438C6.62462 8.69461 7.30101 9.37101 8.15125 9.72875C8.22743 9.76175 8.31063 9.77525 8.39334 9.76805C8.47605 9.76085 8.55567 9.73317 8.625 9.6875L9.53625 9.07687ZM14.5 8C14.5002 9.12221 14.2099 10.2254 13.6574 11.2021C13.1048 12.1788 12.3087 12.9958 11.3467 13.5736C10.3847 14.1514 9.28942 14.4703 8.16759 14.4993C7.04575 14.5282 5.93554 14.2662 4.945 13.7388L2.81688 14.4481C2.64068 14.5069 2.4516 14.5154 2.27083 14.4728C2.09006 14.4301 1.92474 14.3379 1.79341 14.2066C1.66207 14.0753 1.56991 13.9099 1.52725 13.7292C1.48459 13.5484 1.49312 13.3593 1.55188 13.1831L2.26125 11.055C1.79759 10.1833 1.53862 9.21737 1.504 8.23061C1.46937 7.24385 1.66 6.26217 2.06142 5.36008C2.46283 4.458 3.06448 3.65922 3.8207 3.02437C4.57691 2.38952 5.46782 1.9353 6.42579 1.69617C7.38376 1.45705 8.38362 1.4393 9.34948 1.64429C10.3153 1.84928 11.2218 2.27161 12.0001 2.87923C12.7783 3.48685 13.4079 4.26378 13.8411 5.15106C14.2743 6.03834 14.4996 7.01263 14.5 8ZM12 9.5C12.0001 9.40711 11.9743 9.31604 11.9255 9.237C11.8767 9.15795 11.8068 9.09407 11.7238 9.0525L9.72375 8.0525C9.64502 8.01327 9.55734 7.9955 9.46955 8.00099C9.38177 8.00647 9.29698 8.03502 9.22375 8.08375L8.30563 8.69625C7.88416 8.46456 7.53732 8.11772 7.30563 7.69625L7.91813 6.77812C7.96685 6.7049 7.9954 6.62011 8.00089 6.53232C8.00637 6.44453 7.98861 6.35685 7.94938 6.27812L6.94938 4.27812C6.90793 4.19442 6.84386 4.124 6.76444 4.07484C6.68501 4.02568 6.59341 3.99976 6.5 4C5.83696 4 5.20107 4.26339 4.73223 4.73223C4.26339 5.20107 4 5.83696 4 6.5C4.00165 7.95818 4.58165 9.35617 5.61274 10.3873C6.64383 11.4184 8.04182 11.9983 9.5 12C9.82831 12 10.1534 11.9353 10.4567 11.8097C10.76 11.6841 11.0356 11.4999 11.2678 11.2678C11.4999 11.0356 11.6841 10.76 11.8097 10.4567C11.9353 10.1534 12 9.8283 12 9.5Z',
		);

		if ( ! isset( $paths[ $which ] ) ) {
			return;
		}

		printf(
			'<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="%s"/></svg>',
			esc_attr( $paths[ $which ] )
		);
	}
}
