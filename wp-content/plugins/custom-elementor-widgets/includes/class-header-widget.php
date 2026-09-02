<?php
/**
 * What the two headers share.
 *
 * The design draws the bar twice — solid and transparent — and each is its own
 * widget, so a page picks one and cannot hold both. Everything but the name,
 * the title and the variant class is the same, and lives here rather than
 * twice. It sits outside the widgets folder, which is the register: only a
 * section of the design belongs in there.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The header section.
 */
abstract class Header_Widget extends Base_Widget {

	/**
	 * The class that tells the two bars apart.
	 *
	 * @return string
	 */
	abstract protected function variant_class();

	/**
	 * The background the design gives this bar.
	 *
	 * @return string
	 */
	abstract protected function background_default();

	/**
	 * The menu location the bar renders.
	 *
	 * Registered by the parent theme, so nothing here registers it again. The
	 * location renders nothing until a menu is assigned to it.
	 */
	const MENU_LOCATION = 'primary';

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
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_logo_controls();
		$this->register_menu_controls();
		$this->register_action_controls();
		$this->register_bar_style_controls();
		$this->register_menu_style_controls();
		$this->register_button_style_controls();
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

		$this->add_control(
			'logo',
			array(
				'label' => esc_html__( 'Image', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label'       => esc_html__( 'Link', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '/',
			)
		);

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
			'menu_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The bar is a menu. Build it in Appearance → Menus and assign it to the Primary location.', 'custom-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
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
				'label'   => esc_html__( 'First button text', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Membership', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'button_one_link',
			array(
				'label'       => esc_html__( 'First button link', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '/membership',
			)
		);

		$this->add_control(
			'button_two_text',
			array(
				'label'     => esc_html__( 'Second button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Reserve', 'custom-elementor-widgets' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_two_link',
			array(
				'label'       => esc_html__( 'Second button link', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '/reserve',
			)
		);

		$this->add_control(
			'icon_link_icon',
			array(
				'label'            => esc_html__( 'Icon', 'custom-elementor-widgets' ),
				'type'             => Controls_Manager::ICONS,
				'separator'        => 'before',
				'skin'             => 'inline',
				'label_block'      => false,
				'exclude_inline_options' => array( 'svg' ),
			)
		);

		$this->add_control(
			'icon_link_url',
			array(
				'label'       => esc_html__( 'Icon link', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '/account',
			)
		);

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
				'default'   => $this->background_default(),
				'selectors' => array(
					'{{WRAPPER}} .custom-header' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'bar_border_color',
			array(
				'label'     => esc_html__( 'Bottom border', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__row' => 'border-bottom: 1px solid {{VALUE}};',
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
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .custom-header__menu a',
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => esc_html__( 'Colour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
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
				'selectors' => array(
					'{{WRAPPER}} .custom-header__menu a:hover'           => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-header__menu a'                 => 'text-decoration-color: {{VALUE}};',
					'{{WRAPPER}} .custom-header__menu a:hover'           => 'text-decoration: underline;',
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
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .custom-header__button',
			)
		);

		$this->add_control(
			'button_one_color',
			array(
				'label'     => esc_html__( 'First button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-header__button--outline' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_one_background',
			array(
				'label'     => esc_html__( 'First button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-header__icon-link'      => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-header__icon-link svg'  => 'fill: {{VALUE}};',
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
		?>
		<div class="custom-header <?php echo esc_attr( $this->variant_class() ); ?>">
			<div class="custom-header__bar">
				<div class="custom-header__row">
					<?php $this->render_logo( $settings ); ?>
					<?php $this->render_menu(); ?>
					<?php $this->render_actions( $settings ); ?>
				</div>
				<?php
				if ( ! has_nav_menu( self::MENU_LOCATION ) ) {
					$this->editor_hint( __( 'The bar is empty: build a menu in Appearance → Menus and assign it to the Primary location.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * The logo slot — a box first, whether or not a picture has been uploaded.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_logo( $settings ) {
		$image = isset( $settings['logo']['url'] ) ? $settings['logo']['url'] : '';
		$link  = isset( $settings['logo_link'] ) ? $settings['logo_link'] : '';
		$tag   = '' === trim( (string) $link ) ? 'span' : 'a';
		?>
		<<?php echo esc_attr( $tag ); ?> class="custom-header__logo"<?php
			echo 'a' === $tag ? $this->link_attributes( $link ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
		?>>
			<span class="custom-header__logo-box">
				<?php if ( '' !== $image ) : ?>
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
				<?php endif; ?>
			</span>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}

	/**
	 * The bar — whatever menu is assigned to the theme's location.
	 */
	private function render_menu() {
		if ( ! has_nav_menu( self::MENU_LOCATION ) ) {
			return;
		}
		?>
		<nav class="custom-header__menu">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => self::MENU_LOCATION,
					'container'      => false,
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
		<?php
	}

	/**
	 * The two buttons and the icon beside the bar.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_actions( $settings ) {
		$one_text = isset( $settings['button_one_text'] ) ? trim( (string) $settings['button_one_text'] ) : '';
		$two_text = isset( $settings['button_two_text'] ) ? trim( (string) $settings['button_two_text'] ) : '';
		$icon     = isset( $settings['icon_link_icon'] ) ? $settings['icon_link_icon'] : array();
		$has_icon = ! empty( $icon['value'] );

		if ( '' === $one_text && '' === $two_text && ! $has_icon ) {
			return;
		}
		?>
		<div class="custom-header__actions">
			<?php if ( '' !== $one_text ) : ?>
				<a class="custom-header__button custom-header__button--outline"<?php
					echo $this->link_attributes( isset( $settings['button_one_link'] ) ? $settings['button_one_link'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $one_text ); ?></a>
			<?php endif; ?>

			<?php if ( '' !== $two_text ) : ?>
				<a class="custom-header__button custom-header__button--solid"<?php
					echo $this->link_attributes( isset( $settings['button_two_link'] ) ? $settings['button_two_link'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php echo esc_html( $two_text ); ?></a>
			<?php endif; ?>

			<?php if ( $has_icon ) : ?>
				<a class="custom-header__icon-link"<?php
					echo $this->link_attributes( isset( $settings['icon_link_url'] ) ? $settings['icon_link_url'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>><?php Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) ); ?></a>
			<?php endif; ?>
		</div>
		<?php
	}
}
