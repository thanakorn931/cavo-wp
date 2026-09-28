<?php
/**
 * Blog, the list — one section of the design.
 *
 * The page's name, every post as a card three to a row, the page one is on,
 * and the two lines that close the page off.
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
 * The Blog page.
 */
class Blog_List extends Blog_Widget {

	/**
	 * How much of the list the page carries. How much of it stands on screen
	 * at once is the section's own, and the reader's.
	 */
	const MOST = 100;

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'blog-list';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Blog - list', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-posts-grid';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'blog', 'posts', 'list', 'grid', 'pagination' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading'     => esc_html__( 'Blogs', 'custom-elementor-widgets' ),
			'follow_text' => esc_html__( 'Follow us on instagram', 'custom-elementor-widgets' ),
			'handle_text' => esc_html__( '@CAVO', 'custom-elementor-widgets' ),
			'press_text'  => esc_html__( 'Press & Media Enquiries', 'custom-elementor-widgets' ),
			'button_text' => esc_html__( 'Load More', 'custom-elementor-widgets' ),
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

		$this->register_source_controls();

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
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['heading'],
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => esc_html__( 'Heading level', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
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

		$this->add_responsive_control(
			'shown',
			array(
				'label'          => esc_html__( 'How many stand before the button is pressed', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'default'        => 9,
				'tablet_default' => 6,
				'mobile_default' => 3,
			)
		);

		$this->add_responsive_control(
			'step',
			array(
				'label'          => esc_html__( 'How many more each press brings', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 0,
				'default'        => 9,
				'tablet_default' => 6,
				'mobile_default' => 3,
				'description'    => esc_html__( 'Nought brings the rest at once.', 'custom-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_close',
			array(
				'label' => esc_html__( 'Closing lines', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'follow_text',
			array(
				'label'       => esc_html__( 'Invitation', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['follow_text'],
			)
		);

		$this->add_control(
			'handle_text',
			array(
				'label'       => esc_html__( 'Handle', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['handle_text'],
			)
		);

		$this->add_link_controls( $this, 'handle_link', esc_html__( 'Handle link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'press_text',
			array(
				'label'       => esc_html__( 'Enquiries', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['press_text'],
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'press_link', esc_html__( 'Enquiries link', 'custom-elementor-widgets' ) );

		$this->end_controls_section();

		$this->register_style_controls();
	}

	/**
	 * Style → Section, Cards and Pages.
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
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-list__heading',
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
			'band_color',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list' => '--custom-blog-ground: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'band_foot_color',
			array(
				'label'     => esc_html__( 'Background, at the foot', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#BCA48B',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list' => '--custom-blog-foot: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ink_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list__cards'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-blog-list__close'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-blog-list__more'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Dates and lines', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-card__date, {{WRAPPER}} .custom-blog-list__close, {{WRAPPER}} .custom-blog-list__more',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 13 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 13 ),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Cards', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E8E4DB',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-card__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 20 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 16 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 16 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'excerpt_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-card__excerpt',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 20 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 16 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 16 ),
					),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_more_style',
			array(
				'label' => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'more_color',
			array(
				'label'     => esc_html__( 'What it says', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list__more' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'more_background',
			array(
				'label'     => esc_html__( 'Behind it', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list__more' => 'background-color: {{VALUE}};',
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

		// Three to a row and three rows on the wide screen, as the design lays
		// them out; two to a row and one to a row on the narrower ones, so a
		// press brings a screenful whichever screen it is.
		$shows = $this->per_tier( $settings, 'shown', array( 'desktop' => 9, 'tablet' => 6, 'mobile' => 3 ) );
		$steps = $this->per_tier( $settings, 'step', array( 'desktop' => 9, 'tablet' => 6, 'mobile' => 3 ), 0 );
		$query = $this->query( $settings );
		$most  = max( $shows );

		// The button stands whenever any screen would have something left to
		// bring; which screen this is, and so whether it has, is settled by the
		// script.
		$waiting = (int) $query->post_count > min( $shows );
		?>
		<div class="custom-blog-list">
			<div class="custom-blog-list__name">
				<<?php echo esc_attr( $tag ); ?> class="custom-blog-list__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>
			</div>

			<div class="custom-blog-list__band" data-feed<?php $this->tier_attributes( 'shown', $shows ); ?><?php $this->tier_attributes( 'step', $steps ); ?>>
				<div class="custom-blog-list__cards">
					<?php
					// The first row stands in the first screen at the widest
					// tier, and those load with the page; the rest wait for the
					// reader. Beyond what the widest screen shows at once, a
					// card waits for the button as well.
					$place = 0;

					while ( $query->have_posts() ) {
						$query->the_post();
						$this->render_card( $place < 3, $place >= $most );
						$place++;
					}

					wp_reset_postdata();
					?>
				</div>

				<?php if ( $waiting ) : ?>
					<div class="custom-blog-list__actions">
						<button type="button" class="custom-blog-list__more" data-feed-more>
							<span class="custom-blog-list__more-text"><?php
								echo esc_html( $this->text( $settings, 'button_text' ) );
							?></span>
							<span class="custom-blog-list__more-spinner" aria-hidden="true"></span>
						</button>
					</div>
				<?php endif; ?>

				<?php $this->render_close( $settings ); ?>

				<?php
				if ( 0 === (int) $query->post_count ) {
					$this->editor_hint( __( 'Nothing is written yet: the cards are whatever posts the site has.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * The posts the list carries.
	 *
	 * @param array $settings The widget's settings.
	 * @return \WP_Query
	 */
	private function query( $settings ) {
		return new \WP_Query(
			$this->source_query(
				$settings,
				array(
					'posts_per_page' => self::MOST,
					'no_found_rows'  => true,
				)
			)
		);
	}

	/**
	 * The two lines that close the page off.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_close( $settings ) {
		$follow = $this->text( $settings, 'follow_text' );
		$handle = $this->text( $settings, 'handle_text' );
		$press  = $this->text( $settings, 'press_text' );
		?>
		<div class="custom-blog-list__close">
			<div class="custom-blog-list__invite">
				<?php if ( '' !== $follow ) : ?>
					<p class="custom-blog-list__follow"><?php echo esc_html( $follow ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $handle ) : ?>
					<a class="custom-blog-list__handle"<?php
						echo $this->link_from( $settings, 'handle_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
					?>>
						<?php $this->render_mark( 'instagram' ); ?>
						<span><?php echo esc_html( $handle ); ?></span>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $press ) : ?>
				<a class="custom-blog-list__press"<?php
					echo $this->link_from( $settings, 'press_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $press ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}

}
