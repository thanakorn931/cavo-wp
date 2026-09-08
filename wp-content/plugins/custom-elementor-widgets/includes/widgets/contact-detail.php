<?php
/**
 * Contact, detail — one section of the design.
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
 * How to reach the place, and the map of it.
 */
class Contact_Detail extends Base_Widget {

	/**
	 * The ways of getting in touch the design draws, and the mark on each.
	 *
	 * @return array
	 */
	private function contact_marks() {
		return array(
			'phone' => esc_html__( 'Telephone', 'custom-elementor-widgets' ),
			'email' => esc_html__( 'Email', 'custom-elementor-widgets' ),
			'chat'  => esc_html__( 'WhatsApp', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The ways of following the design draws.
	 *
	 * @return array
	 */
	private function social_marks() {
		return array(
			'instagram' => esc_html__( 'Instagram', 'custom-elementor-widgets' ),
			'facebook'  => esc_html__( 'Facebook', 'custom-elementor-widgets' ),
			'tiktok'    => esc_html__( 'TikTok', 'custom-elementor-widgets' ),
			'youtube'   => esc_html__( 'YouTube', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'contact-detail';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Contact — Detail', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-info-box';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'contact', 'detail', 'location', 'social', 'map' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'location_title' => esc_html__( 'Location', 'custom-elementor-widgets' ),
			'getting_title'  => esc_html__( 'Getting Here', 'custom-elementor-widgets' ),
			'station_title'  => esc_html__( 'Nearest BTS Station', 'custom-elementor-widgets' ),
			'contact_title'  => esc_html__( 'Contact Info', 'custom-elementor-widgets' ),
			'social_title'   => esc_html__( 'Social media', 'custom-elementor-widgets' ),
			'button_text'    => esc_html__( 'Book now', 'custom-elementor-widgets' ),
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
			'section_place',
			array(
				'label' => esc_html__( 'Getting here', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'location_title',
			array(
				'label'       => esc_html__( 'Location heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['location_title'],
			)
		);

		$this->add_control(
			'location_body',
			array(
				'label'   => esc_html__( 'Address', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => array( 'active' => true ),
				'rows'    => 5,
			)
		);

		$this->add_control(
			'location_note',
			array(
				'label'   => esc_html__( 'Beneath the address', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		foreach ( array(
			'getting' => esc_html__( 'Getting here', 'custom-elementor-widgets' ),
			'station' => esc_html__( 'Station', 'custom-elementor-widgets' ),
		) as $which => $label ) {
			$this->add_control(
				$which . '_title',
				array(
					/* translators: %s: which block. */
					'label'       => sprintf( esc_html__( '%s heading', 'custom-elementor-widgets' ), $label ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'placeholder' => $design[ $which . '_title' ],
					'separator'   => 'before',
				)
			);

			$this->add_control(
				$which . '_body',
				array(
					'label'   => esc_html__( 'Text', 'custom-elementor-widgets' ),
					'type'    => Controls_Manager::TEXTAREA,
					'dynamic' => array( 'active' => true ),
					'rows'    => 5,
				)
			);
		}

		$this->add_control(
			'map',
			array(
				'label'       => esc_html__( 'Map', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 5,
				'separator'   => 'before',
				'description' => esc_html__( 'The embed code from Google Maps’ Share dialog, the link from inside it, or just the address.', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'map_zoom',
			array(
				'label'     => esc_html__( 'Map zoom', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 21,
				'default'   => 15,
				'condition' => array( 'map!' => '' ),
			)
		);

		$this->end_controls_section();

		$this->register_contact_controls();
		$this->register_style_controls();
	}

	/**
	 * Content → Getting in touch.
	 */
	private function register_contact_controls() {
		$design = $this->design_text();

		$this->start_controls_section(
			'section_contact',
			array(
				'label' => esc_html__( 'Getting in touch', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'contact_title',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['contact_title'],
			)
		);

		foreach ( $this->contact_marks() as $which => $label ) {
			$this->add_control(
				$which . '_text',
				array(
					'label'     => $label,
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array( 'active' => true ),
					'separator' => 'before',
				)
			);

			$this->add_link_controls(
				$this,
				$which . '_link',
				/* translators: %s: which way of getting in touch. */
				sprintf( esc_html__( '%s link', 'custom-elementor-widgets' ), $label )
			);
		}

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['button_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'button_link', esc_html__( 'Button link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'social_title',
			array(
				'label'       => esc_html__( 'Social heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['social_title'],
				'separator'   => 'before',
			)
		);

		foreach ( $this->social_marks() as $which => $label ) {
			$this->add_control(
				$which . '_text',
				array(
					'label'     => $label,
					'type'      => Controls_Manager::TEXT,
					'dynamic'   => array( 'active' => true ),
					'separator' => 'before',
				)
			);

			$this->add_link_controls(
				$this,
				$which . '_link',
				/* translators: %s: which way of following. */
				sprintf( esc_html__( '%s link', 'custom-elementor-widgets' ), $label )
			);
		}

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
			'background',
			array(
				'label'     => esc_html__( 'Behind the words', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9BCA6',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-detail__column' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-detail__column' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Headings', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-contact-detail__block h3',
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
				'selector'       => '{{WRAPPER}} .custom-contact-detail__column p, {{WRAPPER}} .custom-contact-detail__item',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-contact-detail__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				),
			)
		);

		$this->add_control(
			'mark_background',
			array(
				'label'     => esc_html__( 'Marks', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-detail__mark' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mark_color',
			array(
				'label'     => esc_html__( 'Marks, inside', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-detail__mark svg' => 'fill: {{VALUE}};',
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

		$map  = isset( $settings['map']['url'] ) ? $settings['map']['url'] : '';
		$note = isset( $settings['location_note'] ) ? trim( (string) $settings['location_note'] ) : '';
		?>
		<div class="custom-contact-detail">
			<div class="custom-contact-detail__column">
				<div class="custom-contact-detail__block">
					<h3><?php echo esc_html( $this->text( $settings, 'location_title' ) ); ?></h3>
					<p class="custom-contact-detail__block-body"><?php
						echo esc_html( isset( $settings['location_body'] ) ? $settings['location_body'] : '' );
					?></p>
					<?php if ( '' !== $note ) : ?>
						<p><?php echo esc_html( $note ); ?></p>
					<?php endif; ?>
				</div>

				<?php foreach ( array( 'getting', 'station' ) as $which ) : ?>
					<div class="custom-contact-detail__block">
						<h3><?php echo esc_html( $this->text( $settings, $which . '_title' ) ); ?></h3>
						<p class="custom-contact-detail__block-body"><?php
							echo esc_html( isset( $settings[ $which . '_body' ] ) ? $settings[ $which . '_body' ] : '' );
						?></p>
					</div>
				<?php endforeach; ?>

				<div class="custom-contact-detail__block">
					<h3><?php echo esc_html( $this->text( $settings, 'contact_title' ) ); ?></h3>

					<div class="custom-contact-detail__row">
						<?php $this->render_items( $settings, array_keys( $this->contact_marks() ) ); ?>

						<?php
						$button = isset( $settings['button_text'] ) ? trim( (string) $settings['button_text'] ) : '';

						if ( '' !== $button ) :
							?>
							<a class="custom-contact-detail__button"<?php
								echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
							?>><?php echo esc_html( $button ); ?></a>
						<?php endif; ?>
					</div>
				</div>

				<div class="custom-contact-detail__block">
					<h3><?php echo esc_html( $this->text( $settings, 'social_title' ) ); ?></h3>

					<div class="custom-contact-detail__row">
						<?php $this->render_items( $settings, array_keys( $this->social_marks() ) ); ?>
					</div>
				</div>
			</div>

			<div class="custom-contact-detail__map"><?php $this->map( $map, esc_attr__( 'Where CAVO is', 'custom-elementor-widgets' ), (int) ( isset( $settings['map_zoom'] ) ? $settings['map_zoom'] : 15 ) ); ?></div>
		</div>
		<?php
	}

	/**
	 * A row of ways to reach the place, each behind its own mark.
	 *
	 * @param array $settings The widget's settings.
	 * @param array $names    Which of them to print.
	 */
	private function render_items( $settings, $names ) {
		foreach ( $names as $which ) {
			$line = isset( $settings[ $which . '_text' ] ) ? trim( (string) $settings[ $which . '_text' ] ) : '';

			if ( '' === $line ) {
				continue;
			}
			?>
			<a class="custom-contact-detail__item"<?php
				echo $this->link_from( $settings, $which . '_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
			?>>
				<span class="custom-contact-detail__mark" aria-hidden="true"><?php $this->render_mark( $which ); ?></span>
				<?php echo esc_html( $line ); ?>
			</a>
			<?php
		}
	}

	/**
	 * One of the seven marks — the design's own, carried by the widget.
	 *
	 * @param string $which Which mark.
	 */
	private function render_mark( $which ) {
		$paths = array(
			'phone'     => 'M14.4925 10.9425C14.3811 11.7894 13.9651 12.5668 13.3224 13.1294C12.6797 13.6921 11.8542 14.0015 11 14C6.0375 14 2 9.9625 2 5C1.99847 4.14581 2.30793 3.32026 2.87058 2.67756C3.43324 2.03486 4.21061 1.61894 5.0575 1.5075C5.27166 1.48135 5.48853 1.52516 5.67574 1.6324C5.86295 1.73963 6.01046 1.90454 6.09625 2.1025L7.41625 5.04937V5.05688C7.48193 5.20841 7.50905 5.37385 7.4952 5.53842C7.48135 5.703 7.42696 5.86158 7.33688 6C7.32563 6.01688 7.31375 6.0325 7.30125 6.04812L6 7.59062C6.46813 8.54187 7.46313 9.52813 8.42688 9.9975L9.94813 8.70313C9.96306 8.69057 9.97872 8.67888 9.995 8.66813C10.1333 8.57588 10.2924 8.51957 10.458 8.50429C10.6235 8.48901 10.7903 8.51525 10.9431 8.58062L10.9513 8.58438L13.8956 9.90375C14.0939 9.98923 14.2592 10.1366 14.3668 10.3239C14.4744 10.5111 14.5185 10.7281 14.4925 10.9425Z',
			'email'     => 'M14 3H2C1.86739 3 1.74021 3.05268 1.64645 3.14645C1.55268 3.24021 1.5 3.36739 1.5 3.5V12C1.5 12.2652 1.60536 12.5196 1.79289 12.7071C1.98043 12.8946 2.23478 13 2.5 13H13.5C13.7652 13 14.0196 12.8946 14.2071 12.7071C14.3946 12.5196 14.5 12.2652 14.5 12V3.5C14.5 3.36739 14.4473 3.24021 14.3536 3.14645C14.2598 3.05268 14.1326 3 14 3ZM6.16937 8L2.5 11.3631V4.63688L6.16937 8ZM6.90938 8.67813L7.65938 9.36875C7.75162 9.45343 7.87228 9.50041 7.9975 9.50041C8.12272 9.50041 8.24338 9.45343 8.33562 9.36875L9.08562 8.67813L12.7106 12H3.28562L6.90938 8.67813ZM9.83062 8L13.5 4.63625V11.3638L9.83062 8Z',
			'chat'      => 'M9.53625 9.07687L10.9738 9.79438C10.9056 10.1349 10.7215 10.4411 10.4527 10.661C10.1839 10.8809 9.84723 11.0007 9.5 11C8.30693 10.9987 7.16311 10.5241 6.31948 9.68052C5.47585 8.83689 5.00132 7.69307 5 6.5C4.9999 6.1532 5.11998 5.81708 5.33979 5.54884C5.5596 5.2806 5.86557 5.09681 6.20563 5.02875L6.92313 6.46625L6.3125 7.375C6.26687 7.44345 6.23883 7.52209 6.23087 7.60397C6.22292 7.68585 6.23528 7.76842 6.26688 7.84438C6.62462 8.69461 7.30101 9.37101 8.15125 9.72875C8.22743 9.76175 8.31063 9.77525 8.39334 9.76805C8.47605 9.76085 8.55567 9.73317 8.625 9.6875L9.53625 9.07687ZM14.5 8C14.5002 9.12221 14.2099 10.2254 13.6574 11.2021C13.1048 12.1788 12.3087 12.9958 11.3467 13.5736C10.3847 14.1514 9.28942 14.4703 8.16759 14.4993C7.04575 14.5282 5.93554 14.2662 4.945 13.7388L2.81688 14.4481C2.64068 14.5069 2.4516 14.5154 2.27083 14.4728C2.09006 14.4301 1.92474 14.3379 1.79341 14.2066C1.66207 14.0753 1.56991 13.9099 1.52725 13.7292C1.48459 13.5484 1.49312 13.3593 1.55188 13.1831L2.26125 11.055C1.79759 10.1833 1.53862 9.21737 1.504 8.23061C1.46937 7.24385 1.66 6.26217 2.06142 5.36008C2.46283 4.458 3.06448 3.65922 3.8207 3.02437C4.57691 2.38952 5.46782 1.9353 6.42579 1.69617C7.38376 1.45705 8.38362 1.4393 9.34948 1.64429C10.3153 1.84928 11.2218 2.27161 12.0001 2.87923C12.7783 3.48685 13.4079 4.26378 13.8411 5.15106C14.2743 6.03834 14.4996 7.01263 14.5 8ZM12 9.5C12.0001 9.40711 11.9743 9.31604 11.9255 9.237C11.8767 9.15795 11.8068 9.09407 11.7238 9.0525L9.72375 8.0525C9.64502 8.01327 9.55734 7.9955 9.46955 8.00099C9.38177 8.00647 9.29698 8.03502 9.22375 8.08375L8.30563 8.69625C7.88416 8.46456 7.53732 8.11772 7.30563 7.69625L7.91813 6.77812C7.96685 6.7049 7.9954 6.62011 8.00089 6.53232C8.00637 6.44453 7.98861 6.35685 7.94938 6.27812L6.94938 4.27812C6.90793 4.19442 6.84386 4.124 6.76444 4.07484C6.68501 4.02568 6.59341 3.99976 6.5 4C5.83696 4 5.20107 4.26339 4.73223 4.73223C4.26339 5.20107 4 5.83696 4 6.5C4.00165 7.95818 4.58165 9.35617 5.61274 10.3873C6.64383 11.4184 8.04182 11.9983 9.5 12C9.82831 12 10.1534 11.9353 10.4567 11.8097C10.76 11.6841 11.0356 11.4999 11.2678 11.2678C11.4999 11.0356 11.6841 10.76 11.8097 10.4567C11.9353 10.1534 12 9.8283 12 9.5Z',
			'instagram' => 'M11 1.5H5C4.07205 1.50099 3.18238 1.87006 2.52622 2.52622C1.87006 3.18238 1.50099 4.07205 1.5 5V11C1.50099 11.928 1.87006 12.8176 2.52622 13.4738C3.18238 14.1299 4.07205 14.499 5 14.5H11C11.928 14.499 12.8176 14.1299 13.4738 13.4738C14.1299 12.8176 14.499 11.928 14.5 11V5C14.499 4.07205 14.1299 3.18238 13.4738 2.52622C12.8176 1.87006 11.928 1.50099 11 1.5ZM8 11C7.40666 11 6.82664 10.8241 6.33329 10.4944C5.83994 10.1648 5.45542 9.69623 5.22836 9.14805C5.0013 8.59987 4.94189 7.99667 5.05764 7.41473C5.1734 6.83279 5.45912 6.29824 5.87868 5.87868C6.29824 5.45912 6.83279 5.1734 7.41473 5.05764C7.99667 4.94189 8.59987 5.0013 9.14805 5.22836C9.69623 5.45542 10.1648 5.83994 10.4944 6.33329C10.8241 6.82664 11 7.40666 11 8C10.9992 8.7954 10.6828 9.55798 10.1204 10.1204C9.55798 10.6828 8.7954 10.9992 8 11ZM11.75 5C11.6017 5 11.4567 4.95601 11.3333 4.8736C11.21 4.79119 11.1139 4.67406 11.0571 4.53701C11.0003 4.39997 10.9855 4.24917 11.0144 4.10368C11.0433 3.9582 11.1148 3.82456 11.2197 3.71967C11.3246 3.61478 11.4582 3.54335 11.6037 3.51441C11.7492 3.48547 11.9 3.50032 12.037 3.55709C12.1741 3.61386 12.2912 3.70999 12.3736 3.83332C12.456 3.95666 12.5 4.10166 12.5 4.25C12.5 4.44891 12.421 4.63968 12.2803 4.78033C12.1397 4.92098 11.9489 5 11.75 5ZM10 8C10 8.39556 9.8827 8.78224 9.66294 9.11114C9.44318 9.44004 9.13082 9.69638 8.76537 9.84776C8.39991 9.99913 7.99778 10.0387 7.60982 9.96157C7.22186 9.8844 6.86549 9.69392 6.58579 9.41421C6.30608 9.13451 6.1156 8.77814 6.03843 8.39018C5.96126 8.00222 6.00087 7.60009 6.15224 7.23463C6.30362 6.86918 6.55996 6.55682 6.88886 6.33706C7.21776 6.1173 7.60444 6 8 6C8.53043 6 9.03914 6.21071 9.41421 6.58579C9.78929 6.96086 10 7.46957 10 8Z',
			'facebook'  => 'M14.5 8C14.498 9.58863 13.9151 11.1217 12.8612 12.3104C11.8073 13.4991 10.3551 14.2614 8.77813 14.4537C8.74301 14.4577 8.70745 14.4542 8.6738 14.4434C8.64014 14.4327 8.60915 14.4149 8.58287 14.3913C8.55659 14.3676 8.53561 14.3387 8.52133 14.3064C8.50705 14.2741 8.49978 14.2391 8.5 14.2037V9.5H10C10.0685 9.50015 10.1364 9.48621 10.1993 9.45903C10.2622 9.43186 10.3189 9.39204 10.3658 9.34204C10.4127 9.29205 10.4488 9.23294 10.4719 9.1684C10.495 9.10387 10.5045 9.03527 10.5 8.96688C10.489 8.8383 10.4296 8.71868 10.334 8.63206C10.2383 8.54545 10.1134 8.49826 9.98438 8.5H8.5V7C8.5 6.73478 8.60536 6.48043 8.79289 6.29289C8.98043 6.10536 9.23478 6 9.5 6H10.5C10.5685 6.00015 10.6364 5.98621 10.6993 5.95903C10.7622 5.93186 10.8189 5.89204 10.8658 5.84204C10.9127 5.79205 10.9488 5.73294 10.9719 5.6684C10.995 5.60387 11.0045 5.53527 11 5.46688C10.9889 5.33808 10.9294 5.21828 10.8335 5.13163C10.7376 5.04498 10.6124 4.99794 10.4831 5H9.5C8.96957 5 8.46086 5.21071 8.08579 5.58579C7.71071 5.96086 7.5 6.46957 7.5 7V8.5H6C5.93146 8.49985 5.86361 8.51379 5.80069 8.54097C5.73776 8.56814 5.68109 8.60796 5.6342 8.65796C5.58731 8.70795 5.5512 8.76706 5.52811 8.8316C5.50503 8.89613 5.49546 8.96473 5.5 9.03313C5.51106 9.16192 5.57055 9.28172 5.66648 9.36837C5.76241 9.45502 5.88763 9.50206 6.01687 9.5H7.5V14.205C7.50021 14.2403 7.49296 14.2752 7.47871 14.3075C7.46447 14.3398 7.44355 14.3686 7.41734 14.3923C7.39113 14.4159 7.36022 14.4337 7.32664 14.4445C7.29305 14.4553 7.25756 14.4589 7.2225 14.455C5.60335 14.2578 4.11723 13.4599 3.0583 12.2193C1.99937 10.9786 1.4448 9.38565 1.50437 7.75562C1.62937 4.38062 4.36312 1.63688 7.74063 1.50563C8.61506 1.47175 9.48734 1.61459 10.3053 1.9256C11.1232 2.23661 11.8701 2.7094 12.5011 3.31569C13.1321 3.92197 13.6344 4.6493 13.9778 5.45417C14.3213 6.25904 14.4989 7.12491 14.5 8Z',
			'tiktok'    => 'M14.5 5V7.5C14.5 7.63261 14.4473 7.75979 14.3536 7.85355C14.2598 7.94732 14.1326 8 14 8C12.9555 8.00243 11.926 7.75128 11 7.26813V9.75C11 11.0098 10.4996 12.218 9.60876 13.1088C8.71796 13.9996 7.50978 14.5 6.25 14.5C4.99022 14.5 3.78204 13.9996 2.89124 13.1088C2.00044 12.218 1.5 11.0098 1.5 9.75C1.5 7.44375 3.18187 5.405 5.4125 5.0075C5.48446 4.99471 5.55835 4.99785 5.62896 5.0167C5.69958 5.03555 5.7652 5.06966 5.82121 5.11661C5.87722 5.16356 5.92226 5.22223 5.95315 5.28847C5.98404 5.35471 6.00003 5.42691 6 5.5V8.16813C6.00003 8.26275 5.97321 8.35545 5.92265 8.43544C5.87209 8.51543 5.79987 8.57943 5.71438 8.62C5.51227 8.71585 5.33959 8.86417 5.21434 9.04949C5.08909 9.23481 5.01587 9.45035 5.0023 9.67361C4.98873 9.89688 5.03533 10.1197 5.13721 10.3188C5.2391 10.5179 5.39255 10.6861 5.58156 10.8057C5.77057 10.9253 5.98822 10.992 6.21179 10.9988C6.43536 11.0057 6.65667 10.9524 6.85264 10.8446C7.04861 10.7367 7.21205 10.5783 7.32591 10.3858C7.43978 10.1932 7.4999 9.97368 7.5 9.75V1.5C7.5 1.36739 7.55268 1.24021 7.64645 1.14645C7.74021 1.05268 7.86739 1 8 1H10.5C10.6326 1 10.7598 1.05268 10.8536 1.14645C10.9473 1.24021 11 1.36739 11 1.5C11.0008 2.2954 11.3172 3.05798 11.8796 3.62041C12.442 4.18284 13.2046 4.49917 14 4.5C14.1326 4.5 14.2598 4.55268 14.3536 4.64645C14.4473 4.74021 14.5 4.86739 14.5 5Z',
			'youtube'   => 'M14.6456 4.345C14.5867 4.11459 14.4739 3.90149 14.3164 3.72327C14.159 3.54505 13.9614 3.40683 13.74 3.32C11.5975 2.4925 8.1875 2.5 8 2.5C7.8125 2.5 4.4025 2.4925 2.26 3.32C2.0386 3.40683 1.84102 3.54505 1.68356 3.72327C1.5261 3.90149 1.41327 4.11459 1.35438 4.345C1.1925 4.96875 1 6.10875 1 8C1 9.89125 1.1925 11.0313 1.35438 11.655C1.41318 11.8855 1.52597 12.0988 1.68344 12.2771C1.8409 12.4554 2.03853 12.5937 2.26 12.6806C4.3125 13.4725 7.525 13.5 7.95875 13.5H8.04125C8.475 13.5 11.6894 13.4725 13.74 12.6806C13.9615 12.5937 14.1591 12.4554 14.3166 12.2771C14.474 12.0988 14.5868 11.8855 14.6456 11.655C14.8075 11.03 15 9.89125 15 8C15 6.10875 14.8075 4.96875 14.6456 4.345ZM10.0369 8.4075L7.53687 10.1575C7.46209 10.2099 7.37439 10.2408 7.28328 10.2469C7.19218 10.253 7.10115 10.234 7.02006 10.192C6.93898 10.15 6.87095 10.0866 6.82334 10.0087C6.77573 9.93077 6.75037 9.84131 6.75 9.75V6.25C6.75003 6.15853 6.77514 6.06883 6.82261 5.99064C6.87008 5.91246 6.93809 5.84879 7.01923 5.80658C7.10038 5.76436 7.19155 5.74522 7.28282 5.75122C7.37409 5.75722 7.46196 5.78815 7.53687 5.84063L10.0369 7.59063C10.1026 7.63674 10.1563 7.69802 10.1934 7.76928C10.2304 7.84054 10.2498 7.91968 10.2498 8C10.2498 8.08032 10.2304 8.15946 10.1934 8.23072C10.1563 8.30198 10.1026 8.36326 10.0369 8.40938V8.4075Z',
		);

		if ( ! isset( $paths[ $which ] ) ) {
			return;
		}

		printf(
			'<svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="%s"/></svg>',
			esc_attr( $paths[ $which ] )
		);
	}
}
