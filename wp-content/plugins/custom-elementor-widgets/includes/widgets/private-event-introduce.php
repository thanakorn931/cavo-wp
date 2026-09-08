<?php
/**
 * Private events, introduction — one section of the design.
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
 * The Private events page's introduction.
 */
class Private_Event_Introduce extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'private-event-introduce';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Private Events — Introduce', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-text-align-center';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'private', 'events', 'introduce', 'tour' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'body'        => esc_html__( "Whether it's an intimate celebration, corporate gathering, product launch, or exclusive buyout, CAVO offers a collection of versatile spaces designed to create exceptional events. Our team will work closely with you to curate every detail, from bespoke menus and cocktails to entertainment and venue styling.", 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'Virtual tour', 'custom-elementor-widgets' ),
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
			'body',
			array(
				'label'       => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 5,
				'placeholder' => $design['body'],
			)
		);

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

		$this->add_link_controls(
			$this,
			'button_link',
			esc_html__( 'Button link', 'custom-elementor-widgets' ),
			array(
				'description' => esc_html__( 'The tour section on this page answers to #virtual-tour.', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'picture',
			array(
				'label'     => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
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
			'text_color',
			array(
				'label'     => esc_html__( 'Text and button', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-private-introduce' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-private-introduce__body',
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
				'selector'       => '{{WRAPPER}} .custom-private-introduce__button',
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
		?>
		<div class="custom-private-introduce">
			<span class="custom-private-introduce__shape" aria-hidden="true"></span>

			<div class="custom-private-introduce__words">
				<p class="custom-private-introduce__body"><?php
					echo esc_html( $this->text( $settings, 'body' ) );
				?></p>

				<a class="custom-private-introduce__button"<?php
					echo $this->link_from( $settings, 'button_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php
					echo esc_html( $this->text( $settings, 'button_text' ) );
					$this->render_play_mark();
				?></a>
			</div>

			<div class="custom-private-introduce__picture"><?php $this->media( $picture ); ?></div>
		</div>
		<?php
	}

	/**
	 * The mark beside the words — the design's own, carried by the widget.
	 */
	private function render_play_mark() {
		?>
		<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M15.3122 11.5313L10.8122 8.53125C10.7274 8.4747 10.6289 8.44223 10.5271 8.43732C10.4253 8.43241 10.3241 8.45524 10.2343 8.50337C10.1445 8.55151 10.0694 8.62313 10.0172 8.7106C9.96488 8.79807 9.93735 8.8981 9.9375 9V15C9.93735 15.1019 9.96488 15.2019 10.0172 15.2894C10.0694 15.3769 10.1445 15.4485 10.2343 15.4966C10.3241 15.5448 10.4253 15.5676 10.5271 15.5627C10.6289 15.5578 10.7274 15.5253 10.8122 15.4688L15.3122 12.4688C15.3895 12.4174 15.453 12.3478 15.4969 12.266C15.5408 12.1842 15.5638 12.0928 15.5638 12C15.5638 11.9072 15.5408 11.8158 15.4969 11.734C15.453 11.6522 15.3895 11.5826 15.3122 11.5313ZM11.0625 13.9491V10.0509L13.9856 12L11.0625 13.9491ZM21.7875 6.5625C21.7061 6.24629 21.5508 5.95391 21.3344 5.70944C21.1179 5.46496 20.8465 5.27536 20.5425 5.15625C17.3437 3.91781 12.2128 3.9375 12 3.9375C11.7872 3.9375 6.65625 3.91781 3.45375 5.15625C3.15043 5.27585 2.87978 5.46566 2.66402 5.71011C2.44825 5.95456 2.29351 6.24668 2.2125 6.5625C1.97344 7.48969 1.6875 9.1875 1.6875 12C1.6875 14.8125 1.97344 16.5103 2.2125 17.4375C2.29389 17.7537 2.4492 18.0461 2.66564 18.2906C2.88208 18.535 3.15348 18.7246 3.4575 18.8438C6.65625 20.0822 11.7863 20.0625 12 20.0625H12.0666C12.7144 20.0625 17.5041 20.0213 20.5463 18.8438C20.8503 18.7246 21.1217 18.535 21.3381 18.2906C21.5546 18.0461 21.7099 17.7537 21.7913 17.4375C22.0303 16.5122 22.3162 14.8191 22.3162 12C22.3162 9.18094 22.0312 7.48969 21.7875 6.5625ZM20.6981 17.1563C20.662 17.3003 20.5922 17.4338 20.4944 17.5456C20.3966 17.6574 20.2736 17.7444 20.1356 17.7994C17.1356 18.9553 12.0544 18.9375 12 18.9375C11.9456 18.9375 6.87094 18.9563 3.86719 17.7966C3.72918 17.7416 3.60621 17.6546 3.50841 17.5428C3.41062 17.431 3.34079 17.2975 3.30469 17.1534C3.07875 16.2919 2.8125 14.6981 2.8125 12C2.8125 9.30188 3.07875 7.70813 3.30188 6.84375C3.33798 6.69965 3.4078 6.5662 3.5056 6.45438C3.6034 6.34257 3.72637 6.25559 3.86438 6.20063C6.75563 5.08688 11.5809 5.0625 11.9775 5.0625H12C12.0506 5.0625 17.1291 5.04375 20.1328 6.20344C20.2708 6.2584 20.3938 6.34538 20.4916 6.4572C20.5894 6.56901 20.6592 6.70247 20.6953 6.84656C20.9184 7.70813 21.1847 9.30188 21.1847 12.0028C21.1847 14.7038 20.9212 16.2919 20.6981 17.1563Z"/></svg>
		<?php
	}
}
