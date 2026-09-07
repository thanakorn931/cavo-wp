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
						<video
							src="<?php echo esc_url( $url ); ?>"
							autoplay
							loop
							muted
							playsinline
							preload="auto"
						></video>
					<?php else : ?>
						<img src="<?php echo esc_url( $url ); ?>" alt="" />
					<?php endif; ?>
				</div>
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

}
