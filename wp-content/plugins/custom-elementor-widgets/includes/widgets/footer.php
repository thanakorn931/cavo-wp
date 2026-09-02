<?php
/**
 * Footer — one section of the design.
 *
 * The two lists of links are menus, rendered from the theme's registered
 * locations. Everything beside them is a control on this widget.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The footer section.
 */
class Footer extends Base_Widget {

	/**
	 * The menu locations the two lists render.
	 *
	 * The first is the parent theme's; the second the child registers, because
	 * the parent does not. Either renders nothing until a menu is assigned.
	 */
	const MENU_LOCATIONS = array( 'footer', 'footer_secondary' );

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'footer';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Footer', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-footer';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'footer', 'newsletter', 'social', 'menu', 'contact' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$this->register_signup_controls();
		$this->register_contact_controls();
		$this->register_menu_controls();
		$this->register_social_controls();
		$this->register_bottom_controls();
		$this->register_footer_style_controls();
		$this->register_signup_style_controls();
		$this->register_menu_style_controls();
	}

	/**
	 * Content → Sign-up.
	 */
	private function register_signup_controls() {
		$this->start_controls_section(
			'section_signup',
			array(
				'label' => esc_html__( 'Sign-up', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'signup_heading',
			array(
				'label'   => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Signup to our newsletter', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'signup_heading_tag',
			array(
				'label'   => esc_html__( 'Heading level', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'signup_placeholder',
			array(
				'label'   => esc_html__( 'Field placeholder', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Enter your email', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'signup_button',
			array(
				'label'   => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Submit', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'signup_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The form is a mock: it holds the submit rather than sending anywhere, until where the address goes has been settled.', 'custom-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Contact.
	 */
	private function register_contact_controls() {
		$this->start_controls_section(
			'section_contact',
			array(
				'label' => esc_html__( 'Contact', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'contact_label',
			array(
				'label'   => esc_html__( 'Label', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Contact Info', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'contact_address',
			array(
				'label'   => esc_html__( 'Address', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => esc_html__( 'Town Hall Sukhumvit 49, Sukhumvit 49, Khlong Tan Nuea, Watthana, Bangkok 10110', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'contact_details',
			array(
				'label'   => esc_html__( 'Telephone and email', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => "0xx-xxx-xxxx\nexample@gmail.com",
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Menus.
	 */
	private function register_menu_controls() {
		$this->start_controls_section(
			'section_menus',
			array(
				'label' => esc_html__( 'Menus', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'menus_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'The two lists are menus. Build them in Appearance → Menus and assign them to the Footer and Footer Secondary locations.', 'custom-elementor-widgets' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Social.
	 *
	 * How many there are is the client's, so it is a repeater, and it ships
	 * with no rows.
	 */
	private function register_social_controls() {
		$this->start_controls_section(
			'section_social',
			array(
				'label' => esc_html__( 'Social', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'social_name',
			array(
				'label' => esc_html__( 'Name', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'social_icon',
			array(
				'label'       => esc_html__( 'Icon', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

		$repeater->add_control(
			'social_link',
			array(
				'label'       => esc_html__( 'Link', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'https://',
			)
		);

		$this->add_control(
			'social_links',
			array(
				'label'       => esc_html__( 'Links', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ social_name }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content → Bottom.
	 */
	private function register_bottom_controls() {
		$this->start_controls_section(
			'section_bottom',
			array(
				'label' => esc_html__( 'Bottom', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'copyright',
			array(
				'label'   => esc_html__( 'Copyright', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '© 2026 CAVO design by Yes Web Design.', 'custom-elementor-widgets' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Footer.
	 */
	private function register_footer_style_controls() {
		$this->start_controls_section(
			'section_footer_style',
			array(
				'label' => esc_html__( 'Footer', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'footer_background',
			array(
				'label'     => esc_html__( 'Background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4C2513',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'footer_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F9F8F6',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'footer_typography',
				'selector' => '{{WRAPPER}} .custom-footer',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_typography',
				'label'    => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .custom-footer__heading',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Sign-up.
	 */
	private function register_signup_style_controls() {
		$this->start_controls_section(
			'section_signup_style',
			array(
				'label' => esc_html__( 'Sign-up', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'field_width',
			array(
				'label'      => esc_html__( 'Field width', 'custom-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 120,
						'max'  => 640,
						'step' => 2,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .custom-footer__field' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'submit_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__submit' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__submit' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style → Menus and social.
	 */
	private function register_menu_style_controls() {
		$this->start_controls_section(
			'section_menu_style',
			array(
				'label' => esc_html__( 'Menus and social', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typography',
				'selector' => '{{WRAPPER}} .custom-footer__menu a',
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => esc_html__( 'Link', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__menu a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_color_hover',
			array(
				'label'     => esc_html__( 'Link on hover', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__menu a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_color',
			array(
				'label'     => esc_html__( 'Social icon', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__social-link'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .custom-footer__social-link svg' => 'fill: {{VALUE}};',
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
		<div class="custom-footer">
			<div class="custom-footer__container">
				<div class="custom-footer__inner">
					<div class="custom-footer__content">
						<div class="custom-footer__signup-column">
							<?php
							$this->render_signup( $settings );
							$this->render_contact( $settings );
							?>
						</div>
						<?php $this->render_menus(); ?>
					</div>

					<hr class="custom-footer__rule" />

					<div class="custom-footer__bottom">
						<?php
						$this->render_copyright( $settings );
						$this->render_social( $settings );
						?>
					</div>

					<?php $this->render_editor_hint( $settings ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Say what the footer is still waiting for, in the editor only.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_editor_hint( $settings ) {
		$waiting = array();

		$text = array( 'signup_heading', 'signup_button', 'contact_address', 'copyright' );

		foreach ( $text as $key ) {
			if ( '' !== trim( (string) ( isset( $settings[ $key ] ) ? $settings[ $key ] : '' ) ) ) {
				$text = array();
				break;
			}
		}

		if ( ! empty( $text ) ) {
			$waiting[] = __( 'its text, on the Content tab', 'custom-elementor-widgets' );
		}

		if ( empty( array_filter( self::MENU_LOCATIONS, 'has_nav_menu' ) ) ) {
			$waiting[] = __( 'a menu on the Footer or Footer Secondary location, from Appearance → Menus', 'custom-elementor-widgets' );
		}

		if ( empty( $waiting ) ) {
			return;
		}

		$this->editor_hint(
			sprintf(
				/* translators: %s: a list of what the section has still to be given. */
				__( 'This footer is waiting for %s.', 'custom-elementor-widgets' ),
				implode( __( ', and ', 'custom-elementor-widgets' ), $waiting )
			)
		);
	}

	/**
	 * The heading and the mocked sign-up form.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_signup( $settings ) {
		$heading = isset( $settings['signup_heading'] ) ? trim( (string) $settings['signup_heading'] ) : '';
		$button  = isset( $settings['signup_button'] ) ? trim( (string) $settings['signup_button'] ) : '';
		$tag     = isset( $settings['signup_heading_tag'] ) ? $settings['signup_heading_tag'] : '';
		$allowed = array( 'h2', 'h3', 'h4', 'span' );
		$tag     = in_array( $tag, $allowed, true ) ? $tag : 'span';

		if ( '' !== $heading ) :
			?>
			<<?php echo esc_attr( $tag ); ?> class="custom-footer__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $tag ); ?>>
			<?php
		endif;

		if ( '' === $button ) {
			return;
		}
		?>
		<form class="custom-footer__form" method="post">
			<label class="screen-reader-text" for="custom-footer-email-<?php echo esc_attr( $this->get_id() ); ?>">
				<?php echo esc_html__( 'Email address', 'custom-elementor-widgets' ); ?>
			</label>
			<input
				class="custom-footer__field"
				id="custom-footer-email-<?php echo esc_attr( $this->get_id() ); ?>"
				type="email"
				name="email"
				placeholder="<?php echo esc_attr( isset( $settings['signup_placeholder'] ) ? $settings['signup_placeholder'] : '' ); ?>"
			/>
			<button class="custom-footer__submit" type="submit"><?php echo esc_html( $button ); ?></button>
		</form>
		<?php
	}

	/**
	 * The contact lines.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_contact( $settings ) {
		$label   = isset( $settings['contact_label'] ) ? trim( (string) $settings['contact_label'] ) : '';
		$address = isset( $settings['contact_address'] ) ? trim( (string) $settings['contact_address'] ) : '';
		$details = isset( $settings['contact_details'] ) ? trim( (string) $settings['contact_details'] ) : '';

		if ( '' === $label && '' === $address && '' === $details ) {
			return;
		}
		?>
		<div class="custom-footer__contact">
			<?php if ( '' !== $label ) : ?>
				<p><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $address ) : ?>
				<p><?php echo nl2br( esc_html( $address ) ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $details ) : ?>
				<p><?php echo nl2br( esc_html( $details ) ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * The two lists — whatever menus are assigned to the theme's locations.
	 */
	private function render_menus() {
		$locations = array_filter( self::MENU_LOCATIONS, 'has_nav_menu' );

		if ( empty( $locations ) ) {
			return;
		}
		?>
		<div class="custom-footer__menus">
			<?php foreach ( $locations as $location ) : ?>
				<nav class="custom-footer__menu">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => $location,
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * The copyright line.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_copyright( $settings ) {
		$copyright = isset( $settings['copyright'] ) ? trim( (string) $settings['copyright'] ) : '';

		if ( '' === $copyright ) {
			return;
		}
		?>
		<p class="custom-footer__copyright"><?php echo esc_html( $copyright ); ?></p>
		<?php
	}

	/**
	 * The social icons — however many the client has added, and none until
	 * they have.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_social( $settings ) {
		$links = isset( $settings['social_links'] ) ? (array) $settings['social_links'] : array();

		$links = array_filter(
			$links,
			static function ( $link ) {
				return ! empty( $link['social_icon']['value'] );
			}
		);

		if ( empty( $links ) ) {
			return;
		}
		?>
		<div class="custom-footer__social">
			<?php foreach ( $links as $link ) : ?>
				<a class="custom-footer__social-link"<?php
					echo $this->link_attributes( isset( $link['social_link'] ) ? $link['social_link'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>>
					<?php Icons_Manager::render_icon( $link['social_icon'], array( 'aria-hidden' => 'true' ) ); ?>
					<span class="screen-reader-text"><?php echo esc_html( isset( $link['social_name'] ) ? $link['social_name'] : '' ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
