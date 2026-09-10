<?php
/**
 * What the two headers share.
 *
 * The design draws the bar once. Both widgets are that bar, and each is its own
 * widget so a page picks one and cannot hold both: one stands as the design
 * draws it from the first pixel, the other starts clear over whatever it is
 * laid on and becomes the design as soon as the page moves. Everything but the
 * name, the title, the variant class and that second state is the same, and
 * lives here rather than twice. It sits outside the widgets folder, which is
 * the register: only a section of the design belongs in there.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The header section.
 */
abstract class Header_Widget extends Base_Widget {

	/**
	 * The menu location the bar renders.
	 *
	 * Registered by the parent theme, so nothing here registers it again. The
	 * location renders nothing until a menu is assigned to it.
	 */

	/**
	 * The bar the design draws: its ground, its own colour, and what reads
	 * against that colour.
	 */
	const GROUND = '#FAF6EA';
	const INK    = '#3A2114';
	const PAPER  = '#FAF6EA';

	/**
	 * What the bar shows over a picture, before the page has moved.
	 */
	const CLEAR     = 'rgba(0, 0, 0, 0)';
	const TOP_INK   = '#FFFFFF';
	const TOP_PAPER = '#3A2114';

	/**
	 * The class that tells the two bars apart.
	 *
	 * @return string
	 */
	abstract protected function variant_class();

	/**
	 * Whether this bar starts clear and turns into the design on scroll.
	 *
	 * @return bool
	 */
	protected function is_scroll() {
		return false;
	}

	/**
	 * Whether the band gives back the height the bar takes out of the flow.
	 *
	 * @return bool
	 */
	protected function has_spacer() {
		return false;
	}

	/**
	 * The stylesheet the two bars share, declared beside the section's own.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		return array_merge( parent::get_style_depends(), $this->shared_handle( 'style' ) );
	}

	/**
	 * The script that tells the band what the bar came to, and the changing bar
	 * when the page has moved.
	 *
	 * @return array
	 */
	public function get_script_depends(): array {
		return array_merge( parent::get_script_depends(), $this->shared_handle( 'script' ) );
	}

	/**
	 * The handle the two bars share, where it is registered.
	 *
	 * @param string $kind Either style or script.
	 * @return array
	 */
	private function shared_handle( $kind ) {
		$handle = Widgets_Loader::HANDLE_PREFIX . 'header-widget';

		if ( 'style' === $kind ) {
			return wp_style_is( $handle, 'registered' ) ? array( $handle ) : array();
		}

		return wp_script_is( $handle, 'registered' ) ? array( $handle ) : array();
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-header';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'header', 'navbar', 'menu', 'logo' );
	}

	/**
	 * The logo slots this bar asks for.
	 *
	 * The bar that changes shows a different mark in each of its two states, so
	 * it asks for both. Either left empty takes the other; both left empty
	 * leave the box the design gives the mark.
	 *
	 * @return array Control name to label.
	 */
	private function logo_slots() {
		if ( ! $this->is_scroll() ) {
			return array( 'logo' => esc_html__( 'Image', 'custom-elementor-widgets' ) );
		}

		return array(
			'logo'          => esc_html__( 'Image, over the page', 'custom-elementor-widgets' ),
			'logo_scrolled' => esc_html__( 'Image, once scrolled', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_logo_controls();
		$this->register_menu_controls();
		$this->register_language_controls();
		$this->register_action_controls();
		$this->register_bar_style_controls();
		$this->register_menu_style_controls();
		$this->register_button_style_controls();

		if ( $this->is_scroll() ) {
			$this->register_top_style_controls();
		}
	}

	/**
	 * Content → Logo.
	 */
	private function register_logo_controls() {
		$this->start_controls_section(
			'section_logo',
			array(
				'label' => esc_html__( 'Logo', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( $this->logo_slots() as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label' => $label,
					'type'  => Controls_Manager::MEDIA,
				)
			);
		}

		$this->add_link_controls( $this, 'logo_link', esc_html__( 'Link', 'custom-elementor-widgets' ) );

		$this->end_controls_section();
	}

	/**
	 * Content → Menu.
	 */
	private function register_menu_controls() {
		$this->start_controls_section(
			'section_menu',
			array(
				'label' => esc_html__( 'Menu', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'menu',
			array(
				'label'   => esc_html__( 'Menu', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->menu_options(),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Language.
	 *
	 * The switch between the site's languages, drawn by the file (6916:1720,
	 * 11282:6582, 11282:5363) and answered by Polylang: the languages it
	 * knows are the ones offered, each to its own translation of the page.
	 */
	private function register_language_controls() {
		$this->start_controls_section(
			'section_language',
			array(
				'label' => esc_html__( 'Language', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'language_switch',
			array(
				'label'       => esc_html__( 'Show the language switch', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'description' => esc_html__( 'Offers every language Polylang has. Shown only when Polylang is active and has more than one.', 'custom-elementor-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Actions.
	 */
	private function register_action_controls() {
		$this->start_controls_section(
			'section_actions',
			array(
				'label' => esc_html__( 'Actions', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'button_one_text',
			array(
				'label'       => esc_html__( 'First button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Membership', 'custom-elementor-widgets' ),
			)
		);

		$this->add_link_controls( $this, 'button_one_link', esc_html__( 'First button link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'button_two_text',
			array(
				'label'       => esc_html__( 'Second button text', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Reserve', 'custom-elementor-widgets' ),
				'separator'   => 'before',
			)
		);

		$this->add_link_controls( $this, 'button_two_link', esc_html__( 'Second button link', 'custom-elementor-widgets' ) );

		$this->end_controls_section();
	}

	/**
	 * Style → Bar.
	 */
	private function register_bar_style_controls() {
		$this->start_controls_section(
			'section_bar_style',
			array(
				'label' => esc_html__( 'Bar', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'bar_background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::GROUND,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Menu.
	 */
	private function register_menu_style_controls() {
		$this->start_controls_section(
			'section_menu_style',
			array(
				'label' => esc_html__( 'Menu', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'menu_typography',
				'selector'       => '{{WRAPPER}} .custom-header__menu a',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				),
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => esc_html__( 'Colour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__menu a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_color_hover',
			array(
				'label'     => esc_html__( 'Colour on hover', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__menu a:hover'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-header__menu a:hover::after' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Buttons.
	 */
	private function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'Buttons', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'button_typography',
				'selector'       => '{{WRAPPER}} .custom-header__button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 14 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 12 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 9 ),
					),
				),
			)
		);

		$this->add_control(
			'button_one_color',
			array(
				'label'     => esc_html__( 'First button text and outline', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__button--outline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_one_background',
			array(
				'label'     => esc_html__( 'First button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::CLEAR,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__button--outline' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_two_color',
			array(
				'label'     => esc_html__( 'Second button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::PAPER,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-header__button--solid' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_two_background',
			array(
				'label'     => esc_html__( 'Second button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__button--solid' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-header__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'language_color',
			array(
				'label'     => esc_html__( 'Language switch', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-header__language-toggle' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'language_menu_color',
			array(
				'label'     => esc_html__( 'Language list text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::INK,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__language-menu a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'language_menu_background',
			array(
				'label'     => esc_html__( 'Language list background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-header__language-menu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'language_menu_current',
			array(
				'label'     => esc_html__( 'Language list, the current one', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3EEDC',
				'selectors' => array(
					'{{WRAPPER}} .custom-header__language-menu .is-current a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Over the page.
	 *
	 * The bar that changes is two bars to look at, so it is two bars to colour.
	 * These are the same controls again, held to the state before the page has
	 * moved; the section above is what the bar becomes.
	 */
	private function register_top_style_controls() {
		$at_top = '{{WRAPPER}} .custom-header:not( .is-scrolled ) ';

		$this->start_controls_section(
			'section_top_style',
			array(
				'label' => esc_html__( 'Over the page', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'top_bar_background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::CLEAR,
				'selectors' => array(
					$at_top . '.custom-header__bar' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_menu_color',
			array(
				'label'     => esc_html__( 'Menu', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::TOP_INK,
				'separator' => 'before',
				'selectors' => array(
					$at_top . '.custom-header__menu a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_menu_color_hover',
			array(
				'label'     => esc_html__( 'Menu on hover', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::TOP_INK,
				'selectors' => array(
					$at_top . '.custom-header__menu a:hover'        => 'color: {{VALUE}};',
					$at_top . '.custom-header__menu a:hover::after' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_button_one_color',
			array(
				'label'     => esc_html__( 'First button text and outline', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::TOP_INK,
				'separator' => 'before',
				'selectors' => array(
					$at_top . '.custom-header__button--outline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_button_one_background',
			array(
				'label'     => esc_html__( 'First button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::CLEAR,
				'selectors' => array(
					$at_top . '.custom-header__button--outline' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_button_two_color',
			array(
				'label'     => esc_html__( 'Second button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::TOP_PAPER,
				'separator' => 'before',
				'selectors' => array(
					$at_top . '.custom-header__button--solid' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_button_two_background',
			array(
				'label'     => esc_html__( 'Second button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::TOP_INK,
				'selectors' => array(
					$at_top . '.custom-header__button--solid' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_icon_color',
			array(
				'label'     => esc_html__( 'Icon', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => self::TOP_INK,
				'separator' => 'before',
				'selectors' => array(
					$at_top . '.custom-header__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_language_color',
			array(
				'label'     => esc_html__( 'Language switch', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3EEDC',
				'separator' => 'before',
				'selectors' => array(
					$at_top . '.custom-header__language-toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'top_language_border',
			array(
				'label'     => esc_html__( 'Language switch edge', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E1D8B1',
				'selectors' => array(
					$at_top . '.custom-header__language-toggle' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * What the design settles, for a control the client has left alone.
	 *
	 * The control carries none of this as a stored value — it shows it as a
	 * hint and stands empty — so the value is read from here on the way out.
	 *
	 * @return array
	 */
	private function design_text() {
		return array(
			'button_one_text' => esc_html__( 'Membership', 'custom-elementor-widgets' ),
			'button_two_text' => esc_html__( 'Reserve', 'custom-elementor-widgets' ),
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
	 * Print the section.
	 *
	 * The bar is out of the flow, so what it stands over reaches the top of the
	 * window. The bar the design draws from the first pixel hands that height
	 * back as a band of its own rather than asking every section under it to
	 * carry the allowance.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="custom-header <?php echo esc_attr( $this->variant_class() ); ?>">
			<div class="custom-header__bar">
				<div class="custom-header__inner">
					<div class="custom-header__row">
						<?php $this->render_logo( $settings ); ?>
						<?php $this->render_menu( $settings ); ?>
						<?php $this->render_actions( $settings ); ?>
						<?php $this->render_toggle(); ?>
					</div>
					<?php
					if ( ! $this->menu_chosen( $settings ) ) {
						$this->editor_hint( __( 'The bar is empty: build a menu in Appearance → Menus, then choose it on the Content tab.', 'custom-elementor-widgets' ) );
					}
					?>
				</div>
			</div>

			<?php if ( $this->has_spacer() ) : ?>
				<span class="custom-header__spacer" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * The logo slot — a box first, whether or not a picture has been uploaded.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_logo( $settings ) {
		$top      = isset( $settings['logo']['url'] ) ? trim( (string) $settings['logo']['url'] ) : '';
		$scrolled = isset( $settings['logo_scrolled']['url'] ) ? trim( (string) $settings['logo_scrolled']['url'] ) : '';

		// One mark given where two were asked for stands in both states.
		if ( $this->is_scroll() ) {
			$top      = '' !== $top ? $top : $scrolled;
			$scrolled = '' !== $scrolled ? $scrolled : $top;
		} else {
			$scrolled = $top;
		}

		$link = isset( $settings['logo_link'] ) ? $settings['logo_link'] : '';
		$tag  = '' === trim( (string) $link ) ? 'span' : 'a';
		$name = get_bloginfo( 'name' );
		?>
		<<?php echo esc_attr( $tag ); ?> class="custom-header__logo"<?php
			echo 'a' === $tag ? $this->link_from( $settings, 'logo_link' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
		?>>
			<?php if ( $top === $scrolled ) : ?>
				<span class="custom-header__logo-box">
					<?php $this->media( $top, $name ); ?>
				</span>
			<?php else : ?>
				<span class="custom-header__logo-box custom-header__logo-box--top">
					<?php $this->media( $top, $name ); ?>
				</span>
				<span class="custom-header__logo-box custom-header__logo-box--scrolled" aria-hidden="true">
					<?php $this->media( $scrolled, '' ); ?>
				</span>
			<?php endif; ?>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}

	/**
	 * The bar — whichever menu the client chose for it.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_menu( $settings ) {
		?>
		<div class="custom-header__middle">
			<?php if ( $this->menu_chosen( $settings ) ) : ?>
				<nav class="custom-header__menu">
					<?php $this->menu( $settings['menu'] ); ?>
				</nav>
			<?php endif; ?>
			<?php $this->render_language( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * The languages Polylang offers, each with the words it is shown by.
	 *
	 * A language whose own name is written in its own script is shown by that
	 * name, as the file shows Thai; one written in Latin letters is shown by
	 * its code, as the file shows English.
	 *
	 * @return array Each with slug, locale, url, current and words.
	 */
	private function languages() {
		if ( ! function_exists( 'pll_the_languages' ) ) {
			return array();
		}

		$raw = pll_the_languages(
			array(
				'raw'                    => 1,
				'hide_if_empty'          => 0,
				'hide_if_no_translation' => 0,
				'hide_current'           => 0,
			)
		);

		$languages = array();

		foreach ( (array) $raw as $language ) {
			$slug = isset( $language['slug'] ) ? (string) $language['slug'] : '';
			$name = isset( $language['name'] ) ? (string) $language['name'] : '';

			if ( '' === $slug ) {
				continue;
			}

			$languages[] = array(
				'slug'    => $slug,
				'locale'  => isset( $language['locale'] ) ? (string) $language['locale'] : $slug,
				'url'     => isset( $language['url'] ) ? (string) $language['url'] : '',
				'current' => ! empty( $language['current_lang'] ),
				'words'   => preg_match( '/[^\x00-\x7F]/', $name ) ? $name : strtoupper( $slug ),
			);
		}

		return $languages;
	}

	/**
	 * The switch between languages: the current one on a pill, the rest in a
	 * list that opens from it (6916:1720, 11282:6582, 11282:5363).
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_language( $settings ) {
		if ( isset( $settings['language_switch'] ) && 'yes' !== $settings['language_switch'] ) {
			return;
		}

		$languages = $this->languages();

		if ( count( $languages ) < 2 ) {
			if ( $this->is_editing() ) {
				$this->editor_hint( __( 'The language switch shows once Polylang is active with more than one language.', 'custom-elementor-widgets' ) );
			}

			return;
		}

		$current = $languages[0];

		foreach ( $languages as $language ) {
			if ( $language['current'] ) {
				$current = $language;
			}
		}
		?>
		<div class="custom-header__language">
			<button class="custom-header__language-toggle" type="button" aria-expanded="false" aria-haspopup="true">
				<span class="screen-reader-text"><?php esc_html_e( 'Language', 'custom-elementor-widgets' ); ?></span>
				<svg class="custom-header__language-globe" focusable="false" aria-hidden="true" viewBox="0 0 14.5125 14.5125" fill="none" xmlns="http://www.w3.org/2000/svg"><g><path d="M0.50625 7.25625C0.50625 10.9843 3.52823 14.0063 7.25625 14.0063C10.9843 14.0063 14.0063 10.9843 14.0063 7.25625C14.0063 3.52823 10.9843 0.50625 7.25625 0.50625C3.52823 0.50625 0.50625 3.52823 0.50625 7.25625Z" stroke="currentColor" stroke- stroke-linecap="round" stroke-linejoin="round"/><path d="M7.93117 0.540033C7.93117 0.540033 9.95617 3.20628 9.95617 7.25628C9.95617 11.3063 7.93117 13.9725 7.93117 13.9725M6.58117 13.9725C6.58117 13.9725 4.55617 11.3063 4.55617 7.25628C4.55617 3.20628 6.58117 0.540033 6.58117 0.540033M0.931421 9.61878H13.5809M0.931421 4.89378H13.5809" stroke="currentColor" stroke- stroke-linecap="round" stroke-linejoin="round"/></g></svg>
				<span class="custom-header__language-name"><?php echo esc_html( $current['words'] ); ?></span>
				<svg class="custom-header__language-chevron" focusable="false" aria-hidden="true" viewBox="0 0 9.02812 6.075" fill="none" xmlns="http://www.w3.org/2000/svg"><g><path fill-rule="evenodd" clip-rule="evenodd" d="M4.16275 4.59091L1.3502 1.99817L2.05321 1.3501L4.51426 3.6188L6.97531 1.3501L7.67832 1.99817L4.86577 4.59091C4.77253 4.67683 4.64609 4.7251 4.51426 4.7251C4.38242 4.7251 4.25599 4.67683 4.16275 4.59091Z" fill="currentColor"/></g></svg>
			</button>
			<ul class="custom-header__language-menu">
				<?php foreach ( $languages as $language ) : ?>
					<li<?php echo $language['current'] ? ' class="is-current"' : ''; ?>>
						<a href="<?php echo esc_url( $language['url'] ); ?>" lang="<?php echo esc_attr( $language['slug'] ); ?>" hreflang="<?php echo esc_attr( $language['slug'] ); ?>"<?php echo $language['current'] ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $language['words'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Whether a menu was chosen and still exists.
	 *
	 * @param array $settings The widget's settings.
	 * @return bool
	 */
	private function menu_chosen( $settings ) {
		$menu = isset( $settings['menu'] ) ? (int) $settings['menu'] : 0;

		return 0 !== $menu && (bool) wp_get_nav_menu_object( $menu );
	}

	/**
	 * The mark that opens the menu on the narrow tiers, and the one that
	 * shuts it: both the design's, carried by the build (11189:2794,
	 * 11282:5307). The wide tier does not show it.
	 */
	private function render_toggle() {
		?>
		<button class="custom-header__toggle" type="button" aria-expanded="false">
			<span class="custom-header__toggle-open">
				<span class="screen-reader-text"><?php esc_html_e( 'Open the menu', 'custom-elementor-widgets' ); ?></span>
				<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M2 8C2 7.73478 2.10536 7.48043 2.29289 7.29289C2.48043 7.10536 2.73478 7 3 7H21C21.2652 7 21.5196 7.10536 21.7071 7.29289C21.8946 7.48043 22 7.73478 22 8C22 8.26522 21.8946 8.51957 21.7071 8.70711C21.5196 8.89464 21.2652 9 21 9H3C2.73478 9 2.48043 8.89464 2.29289 8.70711C2.10536 8.51957 2 8.26522 2 8ZM2 12C2 11.7348 2.10536 11.4804 2.29289 11.2929C2.48043 11.1054 2.73478 11 3 11H21C21.2652 11 21.5196 11.1054 21.7071 11.2929C21.8946 11.4804 22 11.7348 22 12C22 12.2652 21.8946 12.5196 21.7071 12.7071C21.5196 12.8946 21.2652 13 21 13H3C2.73478 13 2.48043 12.8946 2.29289 12.7071C2.10536 12.5196 2 12.2652 2 12ZM3 15C2.73478 15 2.48043 15.1054 2.29289 15.2929C2.10536 15.4804 2 15.7348 2 16C2 16.2652 2.10536 16.5196 2.29289 16.7071C2.48043 16.8946 2.73478 17 3 17H15C15.2652 17 15.5196 16.8946 15.7071 16.7071C15.8946 16.5196 16 16.2652 16 16C16 15.7348 15.8946 15.4804 15.7071 15.2929C15.5196 15.1054 15.2652 15 15 15H3Z"/></svg>
			</span>
			<span class="custom-header__toggle-shut">
				<span class="screen-reader-text"><?php esc_html_e( 'Close the menu', 'custom-elementor-widgets' ); ?></span>
				<svg viewBox="0 0 24.616 24.616" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M6.708 18.616L6 17.908L11.6 12.308L6 6.708L6.708 6L12.308 11.6L17.908 6L18.616 6.708L13.016 12.308L18.616 17.908L17.908 18.616L12.308 13.016L6.708 18.616Z"/></svg>
			</span>
		</button>
		<?php
	}

	/**
	 * The two buttons at the end of the bar.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_actions( $settings ) {
		$one_text = $this->text( $settings, 'button_one_text' );
		$two_text = $this->text( $settings, 'button_two_text' );
		if ( '' === $one_text && '' === $two_text ) {
			return;
		}
		?>
		<div class="custom-header__actions">
			<?php if ( '' !== $one_text ) : ?>
				<a class="custom-header__button custom-header__button--outline"<?php
					echo $this->link_from( $settings, 'button_one_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $one_text ); ?></a>
			<?php endif; ?>

			<?php if ( '' !== $two_text ) : ?>
				<a class="custom-header__button custom-header__button--solid"<?php
					echo $this->link_from( $settings, 'button_two_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $two_text ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
}
