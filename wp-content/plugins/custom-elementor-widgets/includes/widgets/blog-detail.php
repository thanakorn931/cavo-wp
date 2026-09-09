<?php
/**
 * Blog, detail — one section of the design.
 *
 * The article, written as a run of blocks that are each either something said
 * or something shown, and the card beside it that hands the article on.
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
 * The Blog post's body.
 */
class Blog_Detail extends Blog_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'blog-detail';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Blog - detail', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-post-content';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'blog', 'post', 'article', 'body', 'share' );
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
			'share_text' => esc_html__( 'Share this article', 'custom-elementor-widgets' ),
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
	 * The four ways the design draws of handing the article on, in the order
	 * it draws them: LINE, Facebook, X, and the address itself, copied.
	 *
	 * @return array
	 */
	private function networks() {
		return array(
			'line'     => esc_html__( 'Share on LINE', 'custom-elementor-widgets' ),
			'facebook' => esc_html__( 'Share on Facebook', 'custom-elementor-widgets' ),
			'twitter'  => esc_html__( 'Share on X', 'custom-elementor-widgets' ),
			'copy'     => esc_html__( 'Copy the link', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$design = $this->design_text();

		$this->start_controls_section(
			'section_share',
			array(
				'label' => esc_html__( 'Share', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'share_text',
			array(
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => $design['share_text'],
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
					'{{WRAPPER}} .custom-blog-detail' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Article', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-detail__article' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Article', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-detail__said',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
				),
			)
		);

		$this->add_control(
			'share_background',
			array(
				'label'     => esc_html__( 'Share, behind', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-detail__share' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'share_color',
			array(
				'label'     => esc_html__( 'Share, heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3F1ED',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-detail__share-heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'share_typography',
				'label'          => esc_html__( 'Share, heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-blog-detail__share-heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'mark_background',
			array(
				'label'     => esc_html__( 'Share, behind each mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-detail__mark' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'mark_color',
			array(
				'label'     => esc_html__( 'Share, each mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#BCA48B',
				'selectors' => array(
					'{{WRAPPER}} .custom-blog-detail__mark' => 'color: {{VALUE}};',
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
		$blocks   = $this->article();
		?>
		<div class="custom-blog-detail">
			<div class="custom-blog-detail__article">
				<?php foreach ( $blocks as $block ) : ?>
					<?php $this->render_block( $block ); ?>
				<?php endforeach; ?>

				<?php
				if ( empty( $blocks ) ) {
					$this->editor_hint( __( 'The article is empty: its blocks are added on the post, under Article.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<?php $this->render_share( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * The article's blocks, as the post carries them.
	 *
	 * The article is the post's own, added under Article on the post rather
	 * than on this section, so the section reads it from the post that is
	 * standing. It is the one field this section names.
	 *
	 * @return array
	 */
	private function article() {
		$id = get_the_ID();

		if ( ! $id || ! function_exists( 'get_field' ) ) {
			return array();
		}

		$rows = get_field( 'article', $id );

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * One block of the article: something said, or something shown.
	 *
	 * @param array $block The row.
	 */
	private function render_block( $block ) {
		$kind = isset( $block['kind'] ) ? (string) $block['kind'] : 'paragraph';

		if ( 'picture' === $kind ) {
			$picture = isset( $block['picture'] ) ? $block['picture'] : '';
			$picture = is_array( $picture ) ? ( isset( $picture['url'] ) ? $picture['url'] : '' ) : (string) $picture;
			?>
			<div class="custom-blog-detail__block">
				<span class="custom-blog-detail__shown">
					<?php $this->media( $picture, '', true ); ?>
				</span>
			</div>
			<?php

			return;
		}

		$said = isset( $block['body'] ) ? trim( (string) $block['body'] ) : '';

		if ( '' === $said ) {
			return;
		}
		?>
		<div class="custom-blog-detail__block">
			<p class="custom-blog-detail__said"><?php echo nl2br( esc_html( $said ) ); ?></p>
		</div>
		<?php
	}

	/**
	 * The card that hands the article on.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_share( $settings ) {
		?>
		<div class="custom-blog-detail__share">
			<div class="custom-blog-detail__share-title">
				<span class="custom-blog-detail__share-heading"><?php
					echo esc_html( $this->text( $settings, 'share_text' ) );
				?></span>
			</div>

			<div class="custom-blog-detail__marks">
				<?php foreach ( $this->networks() as $key => $label ) : ?>
					<?php $this->render_share_link( $settings, $key, $label ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * One of the four.
	 *
	 * Each is handed the address the reader is standing at, worked out as the
	 * page is drawn, so the site can be renamed without a button going stale;
	 * the last hands that address to the reader instead. None of them is
	 * asked for: a mark that works the section is the section's.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      Which way.
	 * @param string $label    What it is called.
	 */
	private function render_share_link( $settings, $key, $label ) {
		$here = get_permalink();

		if ( 'copy' === $key ) {
			printf(
				'<button class="custom-blog-detail__mark custom-blog-detail__mark--copy" type="button" data-copy="%1$s" aria-label="%2$s">',
				esc_url( $here ),
				esc_attr( $label )
			);

			$this->render_share_mark( $key );

			echo '</button>';

			return;
		}

		$ways = array(
			'line'     => 'https://social-plugins.line.me/lineit/share?url=',
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=',
			'twitter'  => 'https://twitter.com/intent/tweet?url=',
		);

		if ( ! isset( $ways[ $key ] ) ) {
			return;
		}

		printf(
			'<a class="custom-blog-detail__mark custom-blog-detail__mark--%3$s" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">',
			esc_url( $ways[ $key ] . rawurlencode( $here ) ),
			esc_attr( $label ),
			esc_attr( $key )
		);

		$this->render_share_mark( $key );

		echo '</a>';
	}

	/**
	 * The mark one of the four is known by, which the widget carries itself.
	 *
	 * @param string $which Which network.
	 */
	private function render_share_mark( $which ) {
		$marks = array(
			'line' => array( '0 0 16 15.333', 'M8 0C3.59 0 0 2.932 0 6.53533C0 9.76267 2.84667 12.4667 6.69 12.9807C6.95067 13.036 7.30533 13.154 7.39533 13.3767C7.47533 13.5787 7.448 13.8907 7.42067 14.1013L7.31133 14.7853C7.28133 14.9873 7.15133 15.5807 8.01067 15.218C8.87133 14.8567 12.6213 12.482 14.3013 10.5387C15.4507 9.272 16 7.974 16 6.53533C16 2.932 12.41 0 8 0ZM4.86867 8.678H3.278C3.048 8.678 2.858 8.48667 2.858 8.256V5.05533C2.858 4.824 3.048 4.63267 3.278 4.63267C3.51 4.63267 3.698 4.824 3.698 5.05533V7.83333H4.86867C5.10067 7.83333 5.288 8.02333 5.288 8.256C5.288 8.48667 5.1 8.678 4.86867 8.678ZM6.51267 8.256C6.51267 8.48667 6.32467 8.678 6.092 8.678C5.862 8.678 5.674 8.48667 5.674 8.256V5.05533C5.674 4.824 5.862 4.63267 6.094 4.63267C6.32467 4.63267 6.51267 4.824 6.51267 5.05533V8.256ZM10.34 8.256C10.34 8.43733 10.224 8.598 10.052 8.656C10.0093 8.67 9.96333 8.67667 9.91933 8.67667C9.77867 8.67667 9.65867 8.616 9.57933 8.50867L7.95067 6.28333V8.25533C7.95067 8.486 7.76467 8.67733 7.53 8.67733C7.29933 8.67733 7.11267 8.486 7.11267 8.25533V5.05533C7.11267 4.874 7.228 4.71333 7.39933 4.656C7.43933 4.64067 7.49 4.634 7.52867 4.634C7.65867 4.634 7.77867 4.704 7.85867 4.80467L9.5 7.03867V5.05533C9.5 4.824 9.688 4.63267 9.92 4.63267C10.15 4.63267 10.34 4.824 10.34 5.05533V8.256ZM12.91 6.23267C13.1427 6.23267 13.33 6.424 13.33 6.656C13.33 6.88733 13.1427 7.07867 12.91 7.07867H11.74V7.83333H12.91C13.1427 7.83333 13.33 8.02333 13.33 8.256C13.33 8.48667 13.1427 8.678 12.91 8.678H11.3193C11.0893 8.678 10.9013 8.48667 10.9013 8.256V5.05533C10.9013 4.824 11.0893 4.63267 11.3213 4.63267H12.912C13.1427 4.63267 13.33 4.824 13.33 5.05533C13.33 5.28933 13.1427 5.478 12.91 5.478H11.74V6.23267H12.91Z' ),
			'facebook' => array( '0 0 16 16', 'M15 8.00187C14.9978 9.71271 14.3701 11.3637 13.2351 12.6439C12.1001 13.924 10.5362 14.745 8.83795 14.9521C8.80014 14.9564 8.76185 14.9526 8.7256 14.941C8.68935 14.9294 8.65598 14.9102 8.62768 14.8848C8.59937 14.8593 8.57679 14.8282 8.5614 14.7934C8.54602 14.7586 8.53819 14.7209 8.53843 14.6828V9.61726H10.1538C10.2276 9.61742 10.3007 9.60241 10.3685 9.57314C10.4362 9.54388 10.4973 9.50099 10.5478 9.44715C10.5983 9.39331 10.6371 9.32966 10.662 9.26016C10.6869 9.19065 10.6972 9.11678 10.6923 9.04312C10.6804 8.90466 10.6165 8.77584 10.5135 8.68256C10.4105 8.58928 10.276 8.53846 10.137 8.54033H8.53843V6.92495C8.53843 6.63933 8.65189 6.36541 8.85386 6.16345C9.05582 5.96148 9.32974 5.84802 9.61536 5.84802H10.6923C10.7661 5.84818 10.8392 5.83317 10.9069 5.8039C10.9747 5.77464 11.0357 5.73176 11.0862 5.67791C11.1367 5.62407 11.1756 5.56042 11.2005 5.49092C11.2253 5.42141 11.2356 5.34754 11.2307 5.27389C11.2188 5.13519 11.1548 5.00617 11.0515 4.91285C10.9482 4.81954 10.8133 4.76888 10.6741 4.7711H9.61536C9.04412 4.7711 8.49628 4.99802 8.09236 5.40194C7.68843 5.80587 7.46151 6.35371 7.46151 6.92495V8.54033H5.84612C5.7723 8.54017 5.69924 8.55519 5.63147 8.58445C5.5637 8.61372 5.50268 8.6566 5.45218 8.71044C5.40168 8.76429 5.3628 8.82793 5.33793 8.89744C5.31307 8.96694 5.30277 9.04082 5.30766 9.11447C5.31957 9.25317 5.38364 9.38219 5.48694 9.4755C5.59025 9.56882 5.7251 9.61947 5.86429 9.61726H7.46151V14.6842C7.46174 14.7222 7.45393 14.7598 7.43858 14.7946C7.42324 14.8293 7.40071 14.8604 7.37249 14.8859C7.34426 14.9113 7.31097 14.9305 7.27481 14.9421C7.23864 14.9537 7.20042 14.9576 7.16266 14.9534C5.41895 14.7411 3.81851 13.8818 2.67812 12.5457C1.53773 11.2096 0.940512 9.49412 1.00467 7.7387C1.13928 4.10408 4.08333 1.14926 7.72064 1.00791C8.66234 0.971438 9.60172 1.12527 10.4826 1.4602C11.3635 1.79513 12.1677 2.30429 12.8473 2.95721C13.5269 3.61014 14.0678 4.39342 14.4377 5.2602C14.8075 6.12699 14.9988 7.05947 15 8.00187Z' ),
			'twitter' => array( '0 0 13.7143 13.7143', 'M0.0868571 0H4.12571L7.72 5.14057L12.0343 0H13.3246L8.21143 5.84457L13.7143 13.7143H9.67772L5.92343 8.34629L1.28914 13.7143H0L5.432 7.64343L0.0868571 0Z' ),
			'copy' => array( '0 0 16 16', 'M1 6.5C1 5.57174 1.36875 4.6815 2.02513 4.02513C2.6815 3.36875 3.57174 3 4.5 3H8.5C9.34711 2.99986 10.1655 3.30696 10.8034 3.86433C11.4414 4.42171 11.8555 5.19153 11.969 6.031C12.0374 6.5336 11.9958 7.04508 11.847 7.53C11.6267 8.24556 11.1827 8.8716 10.5803 9.31617C9.97782 9.76075 9.24871 10.0004 8.5 10H7.75C7.55109 10 7.36032 9.92098 7.21967 9.78033C7.07902 9.63968 7 9.44891 7 9.25C7 9.05109 7.07902 8.86032 7.21967 8.71967C7.36032 8.57902 7.55109 8.5 7.75 8.5H8.5C9.03043 8.5 9.53914 8.28929 9.91421 7.91421C10.2893 7.53914 10.5 7.03043 10.5 6.5C10.5 5.96957 10.2893 5.46086 9.91421 5.08579C9.53914 4.71071 9.03043 4.5 8.5 4.5H4.5C4.08685 4.49989 3.6838 4.62773 3.34625 4.86596C3.00869 5.10419 2.7532 5.44111 2.61488 5.83042C2.47656 6.21973 2.4622 6.64232 2.57378 7.04013C2.68536 7.43793 2.91739 7.79141 3.238 8.052C3.06213 8.57088 2.98249 9.11751 3.003 9.665C2.4036 9.38138 1.89711 8.93331 1.5425 8.37298C1.18789 7.81265 0.999754 7.16311 1 6.5ZM9 6.75C9 6.94891 8.92098 7.13968 8.78033 7.28033C8.63968 7.42098 8.44891 7.5 8.25 7.5H7.5C6.96957 7.5 6.46086 7.71071 6.08579 8.08579C5.71071 8.46086 5.5 8.96957 5.5 9.5C5.5 10.0304 5.71071 10.5391 6.08579 10.9142C6.46086 11.2893 6.96957 11.5 7.5 11.5H11.5C11.913 11.5001 12.3159 11.3723 12.6533 11.1342C12.9908 10.8961 13.2463 10.5593 13.3847 10.1702C13.5231 9.7811 13.5376 9.35868 13.4263 8.96096C13.315 8.56325 13.0833 8.20975 12.763 7.949C12.9389 7.43012 13.0185 6.88349 12.998 6.336C13.7168 6.67595 14.2985 7.25054 14.6471 7.96515C14.9958 8.67977 15.0908 9.49182 14.9164 10.2676C14.742 11.0434 14.3087 11.7367 13.6877 12.2333C13.0667 12.73 12.2952 13.0004 11.5 13H7.5C7.04037 13 6.58525 12.9095 6.16061 12.7336C5.73597 12.5577 5.35013 12.2999 5.02513 11.9749C4.70012 11.6499 4.44231 11.264 4.26642 10.8394C4.09053 10.4148 4 9.95963 4 9.5C4 9.04037 4.09053 8.58525 4.26642 8.16061C4.44231 7.73597 4.70012 7.35013 5.02513 7.02513C5.35013 6.70012 5.73597 6.44231 6.16061 6.26642C6.58525 6.09053 7.04037 6 7.5 6H8.25C8.44891 6 8.63968 6.07902 8.78033 6.21967C8.92098 6.36032 9 6.55109 9 6.75Z' ),
		);

		if ( ! isset( $marks[ $which ] ) ) {
			return;
		}

		printf(
			'<svg viewBox="%1$s" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="%2$s"/></svg>',
			esc_attr( $marks[ $which ][0] ),
			esc_attr( $marks[ $which ][1] )
		);
	}
}
