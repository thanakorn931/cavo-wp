<?php
/**
 * Private events, virtual tour — one section of the design.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Private events page's virtual tour.
 */
class Private_Event_Virtual_Tour extends Base_Widget {

	/**
	 * What the section answers to, so a link on the page can reach it.
	 */
	const ANCHOR = 'virtual-tour';

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'private-event-virtual-tour';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Private Events — Virtual Tour', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-play';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'private', 'events', 'virtual', 'tour', 'video' );
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
			'video',
			array(
				'label'       => esc_html__( 'Video', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'video', 'image' ),
				'description' => esc_html__( 'A film or a picture; the band holds either.', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'anchor_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				/* translators: %s: the anchor the section answers to. */
				'raw'             => sprintf( esc_html__( 'This section answers to %s, so a link on the page can reach it.', 'custom-elementor-widgets' ), '<code>#' . self::ANCHOR . '</code>' ),
				'content_classes' => 'elementor-descriptor',
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
			'play_background',
			array(
				'label'     => esc_html__( 'Play mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EBE4CA',
				'selectors' => array(
					'{{WRAPPER}} .custom-private-tour__play' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'play_color',
			array(
				'label'     => esc_html__( 'Play mark ink', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .custom-private-tour__play' => 'color: {{VALUE}};',
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

		$url = isset( $settings['video']['url'] ) ? trim( (string) $settings['video']['url'] ) : '';
		?>
		<div class="custom-private-tour" id="<?php echo esc_attr( self::ANCHOR ); ?>">
			<div class="custom-private-tour__stage">
				<div class="custom-private-tour__media">
					<?php if ( '' === $url ) : ?>
						<?php $this->media( '' ); ?>
					<?php elseif ( $this->is_film( $url ) ) : ?>
						<video src="<?php echo esc_url( $url ); ?>" playsinline preload="metadata"></video>
					<?php else : ?>
						<img src="<?php echo esc_url( $url ); ?>" alt="" />
					<?php endif; ?>
				</div>

				<button type="button" class="custom-private-tour__play" aria-label="<?php
					echo esc_attr__( 'Play', 'custom-elementor-widgets' );
				?>"><?php $this->render_play_mark(); ?></button>
			</div>

			<span class="custom-private-tour__wordmark" aria-hidden="true"></span>
		</div>
		<?php
	}

	/**
	 * Whether what the client chose is a film rather than a picture.
	 *
	 * @param string $url What the client chose.
	 * @return bool
	 */
	private function is_film( $url ) {
		$type = wp_check_filetype( $url );

		return isset( $type['type'] ) && 0 === strpos( (string) $type['type'], 'video/' );
	}

	/**
	 * The play mark — the design's own, carried by the widget.
	 */
	private function render_play_mark() {
		?>
		<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M22.966 10.4415L3.31671 0.271723C2.98543 0.100096 2.606 0.00640164 2.21761 0.000316546C1.82922 -0.00576855 1.44595 0.0759762 1.10739 0.237109C0.772049 0.395738 0.492701 0.627075 0.298073 0.907328C0.103445 1.18758 0.000560319 1.50664 0 1.83168V22.1689C0.00252676 22.6565 0.233798 23.1232 0.642972 23.4666C1.05215 23.81 1.60574 24.0018 2.18205 24C2.5843 23.9998 2.97874 23.906 3.32217 23.7288L22.966 13.5591C23.2818 13.3963 23.5427 13.1675 23.7238 12.8947C23.9049 12.6219 24 12.3143 24 12.0014C24 11.6886 23.9049 11.381 23.7238 11.1082C23.5427 10.8354 23.2818 10.6066 22.966 10.4438V10.4415ZM2.18205 22.147V1.84668L21.7973 12.0003L2.18205 22.147Z"/></svg>
		<?php
	}
}
