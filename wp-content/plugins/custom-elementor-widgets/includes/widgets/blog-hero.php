<?php
/**
 * Blog, hero — one section of the design.
 *
 * The way back, the post's name, what is known about it, and the picture it
 * opens with. All of it is the post's own but the way back, which is the only
 * thing on the page the post cannot know.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Blog_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Blog post's opening.
 */
class Blog_Hero extends Blog_Widget {

	/**
	 * What the list names the page it was read at.
	 */
	const ARG = 'blog_page';

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'blog-hero';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Blog - hero', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-title';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'blog', 'post', 'hero', 'title', 'back' );
	}

	/**
	 * No card is drawn here.
	 *
	 * @return bool
	 */
	protected function draws_cards() {
		return false;
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'back_text' => esc_html__( 'Back', 'custom-elementor-widgets' ),
			'read_text' => esc_html__( 'min read', 'custom-elementor-widgets' ),
			'by_text'   => esc_html__( 'By', 'custom-elementor-widgets' ),
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
			'back_text',
			array(
				'label'       => esc_html__( 'Back', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['back_text'],
			)
		);

		$this->add_link_controls( $this, 'back_link', esc_html__( 'Back link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'heading_tag',
			array(
				'label'     => esc_html__( 'Heading level', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h1',
				'separator' => 'before',
				'options'   => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'read_minutes',
			array(
				'label'       => esc_html__( 'Minutes to read', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
			)
		);

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
			'ground_color',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-hero__back' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .custom-blog-hero__name' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'picture_background',
			array(
				'label'     => esc_html__( 'Behind the picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9BCA6',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-hero__picture' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .custom-blog-hero__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-hero__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 64 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 32 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'ink_color',
			array(
				'label'     => esc_html__( 'The way back, and what is known', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-hero__back' => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-blog-hero__meta' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'meta_typography',
				'label'          => esc_html__( 'The way back, and what is known', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-hero__back, {{WRAPPER}} .custom-blog-hero__meta',
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

		$this->end_controls_section();
	}

	/**
	 * Print the section.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h1';
		$tag = in_array( $tag, array( 'h1', 'h2', 'span' ), true ) ? $tag : 'h1';
		?>
		<div class="custom-blog-hero">
			<div class="custom-blog-hero__back">
				<a class="custom-blog-hero__back-link" href="<?php echo esc_url( $this->back_url( $settings ) ); ?>">
					<?php $this->render_mark( 'back' ); ?>
					<span><?php echo esc_html( $this->text( $settings, 'back_text' ) ); ?></span>
				</a>
			</div>

			<div class="custom-blog-hero__name">
				<div class="custom-blog-hero__column">
					<<?php echo esc_attr( $tag ); ?> class="custom-blog-hero__heading"><?php
						echo esc_html( get_the_title() );
					?></<?php echo esc_attr( $tag ); ?>>

					<div class="custom-blog-hero__meta">
						<span class="custom-blog-hero__date">
							<?php $this->render_mark( 'calendar' ); ?>
							<span><?php echo esc_html( get_the_date( self::DATE_FORMAT ) ); ?></span>
						</span>

						<?php $minutes = isset( $settings['read_minutes'] ) ? (int) $settings['read_minutes'] : 0; ?>
						<?php if ( $minutes > 0 ) : ?>
							<span class="custom-blog-hero__rule" aria-hidden="true">|</span>
							<span><?php
								printf( '%1$s %2$s', (int) $minutes, esc_html( $this->text( $settings, 'read_text' ) ) );
							?></span>
						<?php endif; ?>

						<span class="custom-blog-hero__rule" aria-hidden="true">|</span>
						<span><?php
							printf( '%1$s %2$s', esc_html( $this->text( $settings, 'by_text' ) ), esc_html( get_the_author() ) );
						?></span>
					</div>
				</div>
			</div>

			<div class="custom-blog-hero__picture">
				<?php $this->media( get_the_post_thumbnail_url( null, 'full' ), get_the_title() ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Where the way back leads.
	 *
	 * The list writes the page it was read at into every card it links from, so
	 * a reader who came in from the fourth page of it is sent back to the fourth
	 * page rather than the first. Somebody who arrived another way is sent to
	 * the list itself.
	 *
	 * @param array $settings The widget's settings.
	 * @return string
	 */
	private function back_url( $settings ) {
		$link = isset( $settings['back_link'] ) ? trim( (string) $settings['back_link'] ) : '';
		$link = '' !== $link ? $link : $this->posts_page();

		// A way back that leads nowhere is the page it was pressed on. Where
		// neither the client nor WordPress has said which page holds the list,
		// the front of the site is still somewhere to go.
		$link = '' !== $link ? $link : home_url( '/' );

		$came = isset( $_GET[ self::ARG ] ) ? (int) $_GET[ self::ARG ] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading which page was left.

		if ( $came < 2 ) {
			return $link;
		}

		return add_query_arg( self::ARG, $came, $link );
	}

	/**
	 * The list, where WordPress itself knows which page holds it.
	 *
	 * @return string
	 */
	private function posts_page() {
		$page = (int) get_option( 'page_for_posts' );

		return $page > 0 ? (string) get_permalink( $page ) : '';
	}
}
