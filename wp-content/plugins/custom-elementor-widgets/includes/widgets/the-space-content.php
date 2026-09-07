<?php
/**
 * The Space, content — one section of the design.
 *
 * The three pictures on the gradient the file gives them.
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
 * The Space's content section.
 */
class The_Space_Content extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'the-space-content';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'The Space — Content', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-image-box';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'space', 'content', 'gallery', 'pictures' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_picture_controls();
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
			'picture_wide' => esc_html__( 'Top left', 'custom-elementor-widgets' ),
			'picture_tall' => esc_html__( 'Top right', 'custom-elementor-widgets' ),
			'picture_full' => esc_html__( 'Below', 'custom-elementor-widgets' ),
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
	 * Print the section.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="custom-space-content">
			<div class="custom-space-content__gallery">
				<div class="custom-space-content__grid">
					<div class="custom-space-content__row">
						<?php
						$this->render_slot( $settings, 'picture_wide', 'wide' );
						$this->render_slot( $settings, 'picture_tall', 'tall' );
						?>
					</div>
					<?php $this->render_slot( $settings, 'picture_full', 'full' ); ?>
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
	 * @param string $shape    Which of the three boxes this is.
	 */
	private function render_slot( $settings, $key, $shape ) {
		$url = isset( $settings[ $key ]['url'] ) ? $settings[ $key ]['url'] : '';
		?>
		<span class="custom-space-content__slot custom-space-content__slot--<?php echo esc_attr( $shape ); ?>">
			<?php $this->media( $url ); ?>
		</span>
		<?php
	}
}
