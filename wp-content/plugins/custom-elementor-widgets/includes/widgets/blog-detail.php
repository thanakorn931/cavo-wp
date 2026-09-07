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
use Elementor\Repeater;

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
	 * The four the design draws, in the order it draws them.
	 *
	 * @return array
	 */
	private function networks() {
		return array(
			'instagram' => esc_html__( 'Instagram', 'custom-elementor-widgets' ),
			'facebook'  => esc_html__( 'Facebook', 'custom-elementor-widgets' ),
			'tiktok'    => esc_html__( 'TikTok', 'custom-elementor-widgets' ),
			'youtube'   => esc_html__( 'YouTube', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$design = $this->design_text();

		$this->start_controls_section(
			'section_body',
			array(
				'label' => esc_html__( 'Article', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$block = new Repeater();

		$block->add_control(
			'kind',
			array(
				'label'   => esc_html__( 'Block', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => array(
					'text'    => esc_html__( 'Text', 'custom-elementor-widgets' ),
					'picture' => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				),
			)
		);

		$block->add_control(
			'body',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 6,
				'condition' => array( 'kind' => 'text' ),
			)
		);

		$block->add_control(
			'picture',
			array(
				'label'     => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => array( 'kind' => 'picture' ),
			)
		);

		$this->add_control(
			'blocks',
			array(
				'label'       => esc_html__( 'Blocks', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $block->get_controls(),
				'title_field' => '{{{ kind === "picture" ? "Picture" : ( body ? body.substring( 0, 40 ) : "Text" ) }}}',
			)
		);

		$this->end_controls_section();

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
				'placeholder' => $design['share_text'],
			)
		);

		$this->add_control(
			'share_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Facebook hands on the article the reader is standing in, whatever the site is called that day, and needs nothing here. The rest have no way to be handed a link, so each is where the reader is sent instead.', 'custom-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		foreach ( $this->networks() as $key => $label ) {
			if ( 'facebook' === $key ) {
				continue;
			}

			$this->add_link_controls( $this, 'share_' . $key, $label );
		}

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
				'default'   => '#3A2114',
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
		$blocks   = isset( $settings['blocks'] ) ? (array) $settings['blocks'] : array();
		?>
		<div class="custom-blog-detail">
			<div class="custom-blog-detail__article">
				<?php foreach ( $blocks as $block ) : ?>
					<?php $this->render_block( $block ); ?>
				<?php endforeach; ?>

				<?php
				if ( empty( $blocks ) ) {
					$this->editor_hint( __( 'The article is empty: add a block for every paragraph and every picture.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<?php $this->render_share( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * One block of the article: something said, or something shown.
	 *
	 * @param array $block The row.
	 */
	private function render_block( $block ) {
		$kind = isset( $block['kind'] ) ? (string) $block['kind'] : 'text';

		if ( 'picture' === $kind ) {
			?>
			<div class="custom-blog-detail__block">
				<span class="custom-blog-detail__shown">
					<?php $this->media( isset( $block['picture']['url'] ) ? $block['picture']['url'] : '', '', true ); ?>
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
	 * Facebook is handed the address the reader is standing at, worked out as
	 * the page is drawn, so the site can be renamed without the button going
	 * stale. The rest take no address, so they carry wherever the client set.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      Which network.
	 * @param string $label    What it is called.
	 */
	private function render_share_link( $settings, $key, $label ) {
		if ( 'facebook' === $key ) {
			printf(
				'<a class="custom-blog-detail__mark" href="%1$s" target="_blank" rel="noopener noreferrer" aria-label="%2$s">',
				esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( get_permalink() ) ),
				esc_attr( $label )
			);

			$this->render_share_mark( $key );

			echo '</a>';

			return;
		}

		$link = isset( $settings[ 'share_' . $key ] ) ? trim( (string) $settings[ 'share_' . $key ] ) : '';

		if ( '' === $link ) {
			return;
		}

		printf(
			'<a class="custom-blog-detail__mark"%1$s aria-label="%2$s">',
			$this->link_from( $settings, 'share_' . $key ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
			esc_attr( $label )
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
			'instagram' => 'M11 1.5H5C4.07205 1.50099 3.18238 1.87006 2.52622 2.52622C1.87006 3.18238 1.50099 4.07205 1.5 5V11C1.50099 11.928 1.87006 12.8176 2.52622 13.4738C3.18238 14.1299 4.07205 14.499 5 14.5H11C11.928 14.499 12.8176 14.1299 13.4738 13.4738C14.1299 12.8176 14.499 11.928 14.5 11V5C14.499 4.07205 14.1299 3.18238 13.4738 2.52622C12.8176 1.87006 11.928 1.50099 11 1.5ZM8 11C7.40666 11 6.82664 10.8241 6.33329 10.4944C5.83994 10.1648 5.45542 9.69623 5.22836 9.14805C5.0013 8.59987 4.94189 7.99667 5.05764 7.41473C5.1734 6.83279 5.45912 6.29824 5.87868 5.87868C6.29824 5.45912 6.83279 5.1734 7.41473 5.05764C7.99667 4.94189 8.59987 5.0013 9.14805 5.22836C9.69623 5.45542 10.1648 5.83994 10.4944 6.33329C10.8241 6.82664 11 7.40666 11 8C10.9992 8.7954 10.6828 9.55798 10.1204 10.1204C9.55798 10.6828 8.7954 10.9992 8 11ZM11.75 5C11.6017 5 11.4567 4.95601 11.3333 4.8736C11.21 4.79119 11.1139 4.67406 11.0571 4.53701C11.0003 4.39997 10.9855 4.24917 11.0144 4.10368C11.0433 3.9582 11.1148 3.82456 11.2197 3.71967C11.3246 3.61478 11.4582 3.54335 11.6037 3.51441C11.7492 3.48547 11.9 3.50032 12.037 3.55709C12.1741 3.61386 12.2912 3.70999 12.3736 3.83332C12.456 3.95666 12.5 4.10166 12.5 4.25C12.5 4.44891 12.421 4.63968 12.2803 4.78033C12.1397 4.92098 11.9489 5 11.75 5ZM10 8C10 8.39556 9.8827 8.78224 9.66294 9.11114C9.44318 9.44004 9.13082 9.69638 8.76537 9.84776C8.39991 9.99913 7.99778 10.0387 7.60982 9.96157C7.22186 9.8844 6.86549 9.69392 6.58579 9.41421C6.30608 9.13451 6.1156 8.77814 6.03843 8.39018C5.96126 8.00222 6.00087 7.60009 6.15224 7.23463C6.30362 6.86918 6.55996 6.55682 6.88886 6.33706C7.21776 6.1173 7.60444 6 8 6C8.53043 6 9.03914 6.21071 9.41421 6.58579C9.78929 6.96086 10 7.46957 10 8Z',
			'facebook'  => 'M14.5 8C14.498 9.58863 13.9151 11.1217 12.8612 12.3104C11.8073 13.4991 10.3551 14.2614 8.77813 14.4537C8.74301 14.4577 8.70745 14.4542 8.6738 14.4434C8.64014 14.4327 8.60915 14.4149 8.58287 14.3913C8.55659 14.3676 8.53561 14.3387 8.52133 14.3064C8.50705 14.2741 8.49978 14.2391 8.5 14.2037V9.5H10C10.0685 9.50015 10.1364 9.48621 10.1993 9.45903C10.2622 9.43186 10.3189 9.39204 10.3658 9.34204C10.4127 9.29205 10.4488 9.23294 10.4719 9.1684C10.495 9.10387 10.5045 9.03527 10.5 8.96688C10.489 8.8383 10.4296 8.71868 10.334 8.63206C10.2383 8.54545 10.1134 8.49826 9.98438 8.5H8.5V7C8.5 6.73478 8.60536 6.48043 8.79289 6.29289C8.98043 6.10536 9.23478 6 9.5 6H10.5C10.5685 6.00015 10.6364 5.98621 10.6993 5.95903C10.7622 5.93186 10.8189 5.89204 10.8658 5.84204C10.9127 5.79205 10.9488 5.73294 10.9719 5.6684C10.995 5.60387 11.0045 5.53527 11 5.46688C10.9889 5.33808 10.9294 5.21828 10.8335 5.13163C10.7376 5.04498 10.6124 4.99794 10.4831 5H9.5C8.96957 5 8.46086 5.21071 8.08579 5.58579C7.71071 5.96086 7.5 6.46957 7.5 7V8.5H6C5.93146 8.49985 5.86361 8.51379 5.80069 8.54097C5.73776 8.56814 5.68109 8.60796 5.6342 8.65796C5.58731 8.70795 5.5512 8.76706 5.52811 8.8316C5.50503 8.89613 5.49546 8.96473 5.5 9.03313C5.51106 9.16192 5.57055 9.28172 5.66648 9.36837C5.76241 9.45502 5.88763 9.50206 6.01687 9.5H7.5V14.205C7.50021 14.2403 7.49296 14.2752 7.47871 14.3075C7.46447 14.3398 7.44355 14.3686 7.41734 14.3923C7.39113 14.4159 7.36022 14.4337 7.32664 14.4445C7.29305 14.4553 7.25756 14.4589 7.2225 14.455C5.60335 14.2578 4.11723 13.4599 3.0583 12.2193C1.99937 10.9786 1.4448 9.38565 1.50437 7.75562C1.62937 4.38062 4.36312 1.63688 7.74063 1.50563C8.61506 1.47175 9.48734 1.61459 10.3053 1.9256C11.1232 2.23661 11.8701 2.7094 12.5011 3.31569C13.1321 3.92197 13.6344 4.6493 13.9778 5.45417C14.3213 6.25904 14.4989 7.12491 14.5 8Z',
			'tiktok'    => 'M14.5 5V7.5C14.5 7.63261 14.4473 7.75979 14.3536 7.85355C14.2598 7.94732 14.1326 8 14 8C12.9555 8.00243 11.926 7.75128 11 7.26813V9.75C11 11.0098 10.4996 12.218 9.60876 13.1088C8.71796 13.9996 7.50978 14.5 6.25 14.5C4.99022 14.5 3.78204 13.9996 2.89124 13.1088C2.00044 12.218 1.5 11.0098 1.5 9.75C1.5 7.44375 3.18187 5.405 5.4125 5.0075C5.48446 4.99471 5.55835 4.99785 5.62896 5.0167C5.69958 5.03555 5.7652 5.06966 5.82121 5.11661C5.87722 5.16356 5.92226 5.22223 5.95315 5.28847C5.98404 5.35471 6.00003 5.42691 6 5.5V8.16813C6.00003 8.26275 5.97321 8.35545 5.92265 8.43544C5.87209 8.51543 5.79987 8.57943 5.71438 8.62C5.51227 8.71585 5.33959 8.86417 5.21434 9.04949C5.08909 9.23481 5.01587 9.45035 5.0023 9.67361C4.98873 9.89688 5.03533 10.1197 5.13721 10.3188C5.2391 10.5179 5.39255 10.6861 5.58156 10.8057C5.77057 10.9253 5.98822 10.992 6.21179 10.9988C6.43536 11.0057 6.65667 10.9524 6.85264 10.8446C7.04861 10.7367 7.21205 10.5783 7.32591 10.3858C7.43978 10.1932 7.4999 9.97368 7.5 9.75V1.5C7.5 1.36739 7.55268 1.24021 7.64645 1.14645C7.74021 1.05268 7.86739 1 8 1H10.5C10.6326 1 10.7598 1.05268 10.8536 1.14645C10.9473 1.24021 11 1.36739 11 1.5C11.0008 2.2954 11.3172 3.05798 11.8796 3.62041C12.442 4.18284 13.2046 4.49917 14 4.5C14.1326 4.5 14.2598 4.55268 14.3536 4.64645C14.4473 4.74021 14.5 4.86739 14.5 5Z',
			'youtube'   => 'M14.6456 4.345C14.5867 4.11459 14.4739 3.90149 14.3164 3.72327C14.159 3.54505 13.9614 3.40683 13.74 3.32C11.5975 2.4925 8.1875 2.5 8 2.5C7.8125 2.5 4.4025 2.4925 2.26 3.32C2.0386 3.40683 1.84102 3.54505 1.68356 3.72327C1.5261 3.90149 1.41327 4.11459 1.35438 4.345C1.1925 4.96875 1 6.10875 1 8C1 9.89125 1.1925 11.0313 1.35438 11.655C1.41318 11.8855 1.52597 12.0988 1.68344 12.2771C1.8409 12.4554 2.03853 12.5937 2.26 12.6806C4.3125 13.4725 7.525 13.5 7.95875 13.5H8.04125C8.475 13.5 11.6894 13.4725 13.74 12.6806C13.9615 12.5937 14.1591 12.4554 14.3166 12.2771C14.474 12.0988 14.5868 11.8855 14.6456 11.655C14.8075 11.03 15 9.89125 15 8C15 6.10875 14.8075 4.96875 14.6456 4.345ZM10.0369 8.4075L7.53687 10.1575C7.46209 10.2099 7.37439 10.2408 7.28328 10.2469C7.19218 10.253 7.10115 10.234 7.02006 10.192C6.93898 10.15 6.87095 10.0866 6.82334 10.0087C6.77573 9.93077 6.75037 9.84131 6.75 9.75V6.25C6.75003 6.15853 6.77514 6.06883 6.82261 5.99064C6.87008 5.91246 6.93809 5.84879 7.01923 5.80658C7.10038 5.76436 7.19155 5.74522 7.28282 5.75122C7.37409 5.75722 7.46196 5.78815 7.53687 5.84063L10.0369 7.59063C10.1026 7.63674 10.1563 7.69802 10.1934 7.76928C10.2304 7.84054 10.2498 7.91968 10.2498 8C10.2498 8.08032 10.2304 8.15946 10.1934 8.23072C10.1563 8.30198 10.1026 8.36326 10.0369 8.40938V8.4075Z',
		);

		if ( ! isset( $marks[ $which ] ) ) {
			return;
		}

		printf(
			'<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="%s"/></svg>',
			esc_attr( $marks[ $which ] )
		);
	}
}
