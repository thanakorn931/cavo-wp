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

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Blog page.
 */
class Blog_List extends Base_Widget {

	/**
	 * Three to a row, three rows, as the design lays them out.
	 */
	const PER_PAGE = 9;

	/**
	 * How many numbers the pages offer at once.
	 */
	const WINDOW = 4;

	/**
	 * What names the page in the address.
	 */
	const ARG = 'blog_page';

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
			'go_text'     => esc_html__( 'go', 'custom-elementor-widgets' ),
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
				'default' => 'h1',
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'     => esc_html__( 'Order', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'DESC',
				'separator' => 'before',
				'options'   => array(
					'DESC' => esc_html__( 'Newest first', 'custom-elementor-widgets' ),
					'ASC'  => esc_html__( 'Oldest first', 'custom-elementor-widgets' ),
				),
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
				'placeholder' => $design['follow_text'],
			)
		);

		$this->add_control(
			'handle_text',
			array(
				'label'       => esc_html__( 'Handle', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => $design['handle_text'],
			)
		);

		$this->add_link_controls( $this, 'handle_link', esc_html__( 'Handle link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'press_text',
			array(
				'label'       => esc_html__( 'Enquiries', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
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
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 64 ) ),
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
					'{{WRAPPER}} .custom-blog-list__pages'  => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Dates and lines', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-list__date, {{WRAPPER}} .custom-blog-list__close, {{WRAPPER}} .custom-blog-list__pages',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
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
					'{{WRAPPER}} .custom-blog-list__card' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-list__title',
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
				'name'           => 'excerpt_typography',
				'label'          => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-list__excerpt',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pages_style',
			array(
				'label' => esc_html__( 'Pages', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'page_here_color',
			array(
				'label'     => esc_html__( 'The page being read', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list__page--here' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'page_here_background',
			array(
				'label'     => esc_html__( 'The page being read, behind', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-list__page--here' => 'background-color: {{VALUE}};',
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

		$total = $this->total();
		$here  = $this->here( $total );
		$query = $this->query( $settings, $here );
		?>
		<div class="custom-blog-list">
			<div class="custom-blog-list__name">
				<<?php echo esc_attr( $tag ); ?> class="custom-blog-list__heading"><?php
					echo esc_html( $this->text( $settings, 'heading' ) );
				?></<?php echo esc_attr( $tag ); ?>>
			</div>

			<div class="custom-blog-list__band">
				<div class="custom-blog-list__cards">
					<?php
					while ( $query->have_posts() ) {
						$query->the_post();
						$this->render_card();
					}

					wp_reset_postdata();
					?>
				</div>

				<?php $this->render_pages( $settings, $here, $total ); ?>
				<?php $this->render_close( $settings ); ?>

				<?php
				if ( 0 === (int) $query->found_posts ) {
					$this->editor_hint( __( 'Nothing is written yet: the cards are whatever posts the site has.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * How many pages the list comes to.
	 *
	 * @return int
	 */
	private function total() {
		$written = wp_count_posts( 'post' );
		$written = isset( $written->publish ) ? (int) $written->publish : 0;

		return max( 1, (int) ceil( $written / self::PER_PAGE ) );
	}

	/**
	 * The posts this page of the list shows.
	 *
	 * @param array $settings The widget's settings.
	 * @param int   $here     The page being read.
	 * @return \WP_Query
	 */
	private function query( $settings, $here ) {
		$order = isset( $settings['order'] ) && 'ASC' === $settings['order'] ? 'ASC' : 'DESC';

		return new \WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => self::PER_PAGE,
				'orderby'        => 'date',
				'order'          => $order,
				'paged'          => (int) $here,
			)
		);
	}

	/**
	 * Which page is being read.
	 *
	 * A page named past either end is brought into range rather than refused.
	 *
	 * @param int $total How many pages there are.
	 * @return int
	 */
	private function here( $total ) {
		$total = max( 1, (int) $total );
		$asked = isset( $_GET[ self::ARG ] ) ? (int) $_GET[ self::ARG ] : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading which page is open.

		return max( 1, min( $total, $asked ) );
	}

	/**
	 * One post, as a card.
	 */
	private function render_card() {
		?>
		<a class="custom-blog-list__card" href="<?php echo esc_url( get_permalink() ); ?>">
			<span class="custom-blog-list__picture">
				<?php $this->media( get_the_post_thumbnail_url( null, 'large' ), '', true ); ?>
			</span>

			<span class="custom-blog-list__body">
				<span class="custom-blog-list__date">
					<?php $this->render_mark(); ?>
					<span><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
				</span>

				<span class="custom-blog-list__text">
					<span class="custom-blog-list__title"><?php echo esc_html( get_the_title() ); ?></span>
					<span class="custom-blog-list__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></span>
				</span>
			</span>
		</a>
		<?php
	}

	/**
	 * The pages, and the field that names one.
	 *
	 * @param array $settings The widget's settings.
	 * @param int   $here     The page being read.
	 * @param int   $total    How many pages there are.
	 */
	private function render_pages( $settings, $here, $total ) {
		$total = max( 1, $total );
		?>
		<nav class="custom-blog-list__pages" aria-label="<?php esc_attr_e( 'Pages', 'custom-elementor-widgets' ); ?>">
			<?php
			$this->render_step( $here - 1, $here > 1, 'M10 12L6 8L10 4', __( 'Previous page', 'custom-elementor-widgets' ) );

			foreach ( $this->window( $here, $total ) as $page ) {
				if ( 0 === $page ) {
					echo '<span class="custom-blog-list__page custom-blog-list__page--break" aria-hidden="true">…</span>';

					continue;
				}

				$this->render_number( $page, $page === $here );
			}

			$this->render_step( $here + 1, $here < $total, 'M6 4L10 8L6 12', __( 'Next page', 'custom-elementor-widgets' ) );
			?>

			<form class="custom-blog-list__go" method="get" action="<?php echo esc_url( $this->here_url() ); ?>">
				<?php $this->render_carried(); ?>
				<input
					class="custom-blog-list__field"
					type="number"
					name="<?php echo esc_attr( self::ARG ); ?>"
					inputmode="numeric"
					required
					aria-label="<?php esc_attr_e( 'Page number', 'custom-elementor-widgets' ); ?>"
				/>
				<button class="custom-blog-list__page custom-blog-list__page--go" type="submit"><?php
					echo esc_html( $this->text( $settings, 'go_text' ) );
				?></button>
			</form>
		</nav>
		<?php
	}

	/**
	 * Which numbers the pages offer.
	 *
	 * Four at most. Past four pages three of them start at the page being read
	 * and the last stands alone behind a break; within four of the end there is
	 * nothing left to break, and the four are the last four. A break is a nought.
	 *
	 * @param int $here  The page being read.
	 * @param int $total How many pages there are.
	 * @return array
	 */
	private function window( $here, $total ) {
		if ( $total <= self::WINDOW ) {
			return range( 1, $total );
		}

		if ( $here > $total - self::WINDOW ) {
			return range( $total - self::WINDOW + 1, $total );
		}

		return array( $here, $here + 1, $here + 2, 0, $total );
	}

	/**
	 * One number.
	 *
	 * @param int  $page The page it leads to.
	 * @param bool $here Whether it is the page being read.
	 */
	private function render_number( $page, $here ) {
		printf(
			'<a class="custom-blog-list__page%1$s" href="%2$s"%3$s>%4$s</a>',
			$here ? ' custom-blog-list__page--here' : '',
			esc_url( $this->page_url( $page ) ),
			$here ? ' aria-current="page"' : '',
			esc_html( number_format_i18n( $page ) )
		);
	}

	/**
	 * One step either way.
	 *
	 * A step with nowhere to go stays where it is and refuses the press: a
	 * control that disappears moves everything beside it.
	 *
	 * @param int    $page  The page it leads to.
	 * @param bool   $open  Whether there is a page that way.
	 * @param string $path  The arrow it draws.
	 * @param string $label What a reader who cannot see it is told.
	 */
	private function render_step( $page, $open, $path, $label ) {
		$arrow = sprintf(
			'<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="%s" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			esc_attr( $path )
		);

		if ( ! $open ) {
			printf(
				'<span class="custom-blog-list__page custom-blog-list__page--step custom-blog-list__page--shut" aria-hidden="true">%s</span>',
				$arrow // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a fixed mark the widget carries.
			);

			return;
		}

		printf(
			'<a class="custom-blog-list__page custom-blog-list__page--step" href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( $this->page_url( $page ) ),
			esc_attr( $label ),
			$arrow // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a fixed mark the widget carries.
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

	/**
	 * The address of one page of the list.
	 *
	 * @param int $page Which page.
	 * @return string
	 */
	private function page_url( $page ) {
		if ( $page <= 1 ) {
			return remove_query_arg( self::ARG, $this->here_url() );
		}

		return add_query_arg( self::ARG, (int) $page, $this->here_url() );
	}

	/**
	 * The address the page is being read at.
	 *
	 * @return string
	 */
	private function here_url() {
		global $wp;

		// Built from what WordPress resolved rather than from the address as it
		// arrived, so a site in a folder keeps its folder.
		return home_url( isset( $wp->request ) ? $wp->request : '' );
	}

	/**
	 * Whatever else the address was carrying, so naming a page does not drop it.
	 */
	private function render_carried() {
		foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- carrying the address forward.
			$key = sanitize_key( $key );

			if ( '' === $key || self::ARG === $key || is_array( $value ) ) {
				continue;
			}

			printf(
				'<input type="hidden" name="%s" value="%s" />',
				esc_attr( $key ),
				esc_attr( sanitize_text_field( wp_unslash( $value ) ) )
			);
		}
	}

	/**
	 * One of the marks the design draws, which the widget carries itself.
	 *
	 * @param string $which Which mark.
	 */
	private function render_mark( $which = 'calendar' ) {
		$marks = array(
			'calendar'  => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M10 2.66667V1.33333M10 2.66667V4M10 2.66667H7M2 6.66667V12.6667C2 13.0203 2.14048 13.3594 2.39052 13.6095C2.64057 13.8595 2.97971 14 3.33333 14H12.6667C13.0203 14 13.3594 13.8595 13.6095 13.6095C13.8595 13.3594 14 13.0203 14 12.6667V6.66667M2 6.66667H14M2 6.66667V4C2 3.64638 2.14048 3.30724 2.39052 3.05719C2.64057 2.80714 2.97971 2.66667 3.33333 2.66667H4.66667M14 6.66667V4C14 3.64638 13.8595 3.30724 13.6095 3.05719C13.3594 2.80714 13.0203 2.66667 12.6667 2.66667H12.3333M4.66667 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'instagram' => '<svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M11 1.5H5C4.07205 1.50099 3.18238 1.87006 2.52622 2.52622C1.87006 3.18238 1.50099 4.07205 1.5 5V11C1.50099 11.928 1.87006 12.8176 2.52622 13.4738C3.18238 14.1299 4.07205 14.499 5 14.5H11C11.928 14.499 12.8176 14.1299 13.4738 13.4738C14.1299 12.8176 14.499 11.928 14.5 11V5C14.499 4.07205 14.1299 3.18238 13.4738 2.52622C12.8176 1.87006 11.928 1.50099 11 1.5ZM8 11C7.40666 11 6.82664 10.8241 6.33329 10.4944C5.83994 10.1648 5.45542 9.69623 5.22836 9.14805C5.0013 8.59987 4.94189 7.99667 5.05764 7.41473C5.1734 6.83279 5.45912 6.29824 5.87868 5.87868C6.29824 5.45912 6.83279 5.1734 7.41473 5.05764C7.99667 4.94189 8.59987 5.0013 9.14805 5.22836C9.69623 5.45542 10.1648 5.83994 10.4944 6.33329C10.8241 6.82664 11 7.40666 11 8C10.9992 8.7954 10.6828 9.55798 10.1204 10.1204C9.55798 10.6828 8.7954 10.9992 8 11ZM11.75 5C11.6017 5 11.4567 4.95601 11.3333 4.8736C11.21 4.79119 11.1139 4.67406 11.0571 4.53701C11.0003 4.39997 10.9855 4.24917 11.0144 4.10368C11.0433 3.9582 11.1148 3.82456 11.2197 3.71967C11.3246 3.61478 11.4582 3.54335 11.6037 3.51441C11.7492 3.48547 11.9 3.50032 12.037 3.55709C12.1741 3.61386 12.2912 3.70999 12.3736 3.83332C12.456 3.95666 12.5 4.10166 12.5 4.25C12.5 4.44891 12.421 4.63968 12.2803 4.78033C12.1397 4.92098 11.9489 5 11.75 5ZM10 8C10 8.39556 9.8827 8.78224 9.66294 9.11114C9.44318 9.44004 9.13082 9.69638 8.76537 9.84776C8.39991 9.99913 7.99778 10.0387 7.60982 9.96157C7.22186 9.8844 6.86549 9.69392 6.58579 9.41421C6.30608 9.13451 6.1156 8.77814 6.03843 8.39018C5.96126 8.00222 6.00087 7.60009 6.15224 7.23463C6.30362 6.86918 6.55996 6.55682 6.88886 6.33706C7.21776 6.1173 7.60444 6 8 6C8.53043 6 9.03914 6.21071 9.41421 6.58579C9.78929 6.96086 10 7.46957 10 8Z"/></svg>',
		);

		echo isset( $marks[ $which ] ) ? $marks[ $which ] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a fixed mark the widget carries.
	}
}
