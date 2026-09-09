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
				'label'       => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Signup to our newsletter', 'custom-elementor-widgets' ),
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
			'signup_field_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'What the box says and what the button reads are set in WP Form → Form editor → Newsletter form.', 'custom-elementor-widgets' ),
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
				'label'       => esc_html__( 'Label', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Contact Info', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'location_label',
			array(
				'label'       => esc_html__( 'Second label', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'placeholder' => esc_html__( 'Location', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'contact_address',
			array(
				'label'       => esc_html__( 'Address', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 5,
				'placeholder' => esc_html__( 'Town Hall Sukhumvit 49, Sukhumvit 49, Khlong Tan Nuea, Watthana, Bangkok 10110', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'contact_details',
			array(
				'label'       => esc_html__( 'Contact info', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::TEXTAREA,
				'dynamic'     => array( 'active' => true ),
				'rows'        => 5,
				'placeholder' => "0xx-xxx-xxxx\nexample@gmail.com",
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
	 * The places the design draws, in the order it draws them.
	 *
	 * @return array
	 */
	private function social_places() {
		return array(
			'facebook'  => esc_html__( 'Facebook', 'custom-elementor-widgets' ),
			'instagram' => esc_html__( 'Instagram', 'custom-elementor-widgets' ),
			'tiktok'    => esc_html__( 'TikTok', 'custom-elementor-widgets' ),
			'line'      => esc_html__( 'LINE', 'custom-elementor-widgets' ),
			'whatsapp'  => esc_html__( 'WhatsApp', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * Each place's mark — the design's own, carried by the widget.
	 *
	 * A mark that says which of several is not a choice the client makes: the
	 * file draws these five and no others. What they give is where each goes.
	 *
	 * @return array
	 */
	private function social_marks() {
		return array(
			'facebook'  => 'M18.125 10.0003C18.1224 11.9861 17.3938 13.9024 16.0764 15.3883C14.7591 16.8742 12.9438 17.8271 10.9726 18.0675C10.9287 18.0724 10.8843 18.0681 10.8422 18.0546C10.8001 18.0411 10.7614 18.0189 10.7286 17.9894C10.6957 17.9598 10.6695 17.9237 10.6516 17.8833C10.6338 17.8429 10.6247 17.7991 10.625 17.755V11.8753H12.5C12.5856 11.8755 12.6704 11.858 12.7491 11.8241C12.8278 11.7901 12.8986 11.7403 12.9572 11.6778C13.0158 11.6153 13.061 11.5415 13.0898 11.4608C13.1187 11.3801 13.1306 11.2944 13.125 11.2089C13.1112 11.0482 13.037 10.8986 12.9174 10.7904C12.7979 10.6821 12.6417 10.6231 12.4804 10.6253H10.625V8.75029C10.625 8.41876 10.7567 8.10082 10.9911 7.8664C11.2255 7.63198 11.5434 7.50029 11.875 7.50029H13.125C13.2106 7.50047 13.2954 7.48304 13.3741 7.44908C13.4528 7.41511 13.5236 7.36533 13.5822 7.30284C13.6408 7.24034 13.686 7.16646 13.7148 7.08579C13.7437 7.00512 13.7556 6.91937 13.75 6.83388C13.7361 6.67289 13.6618 6.52313 13.5419 6.41482C13.422 6.30652 13.2654 6.24772 13.1039 6.25029H11.875C11.2119 6.25029 10.576 6.51368 10.1072 6.98252C9.63836 7.45136 9.37496 8.08724 9.37496 8.75029V10.6253H7.49996C7.41429 10.6251 7.32948 10.6425 7.25082 10.6765C7.17216 10.7105 7.10133 10.7602 7.04271 10.8227C6.9841 10.8852 6.93897 10.9591 6.91011 11.0398C6.88125 11.1205 6.86929 11.2062 6.87497 11.2917C6.88879 11.4527 6.96316 11.6024 7.08306 11.7107C7.20297 11.8191 7.3595 11.8779 7.52106 11.8753H9.37496V17.7565C9.37523 17.8006 9.36616 17.8443 9.34836 17.8846C9.33055 17.925 9.3044 17.9611 9.27164 17.9906C9.23887 18.0201 9.20023 18.0424 9.15826 18.0559C9.11628 18.0694 9.07192 18.0739 9.02809 18.069C7.00415 17.8226 5.1465 16.8252 3.82283 15.2744C2.49917 13.7236 1.80597 11.7324 1.88043 9.69482C2.03668 5.47607 5.45387 2.04638 9.67575 1.88232C10.7688 1.83998 11.8591 2.01853 12.8816 2.40729C13.904 2.79605 14.8376 3.38703 15.6263 4.14489C16.4151 4.90275 17.0429 5.81191 17.4723 6.818C17.9016 7.82409 18.1236 8.90643 18.125 10.0003Z',
			'instagram' => 'M13.7498 1.875H6.24976C5.08981 1.87624 3.97774 2.33758 3.15753 3.15778C2.33733 3.97798 1.876 5.09006 1.87476 6.25V13.75C1.876 14.9099 2.33733 16.022 3.15753 16.8422C3.97774 17.6624 5.08981 18.1238 6.24976 18.125H13.7498C14.9097 18.1238 16.0218 17.6624 16.842 16.8422C17.6622 16.022 18.1235 14.9099 18.1248 13.75V6.25C18.1235 5.09006 17.6622 3.97798 16.842 3.15778C16.0218 2.33758 14.9097 1.87624 13.7498 1.875ZM9.99976 13.75C9.25808 13.75 8.53305 13.5301 7.91637 13.118C7.29968 12.706 6.81904 12.1203 6.53521 11.4351C6.25138 10.7498 6.17712 9.99584 6.32181 9.26841C6.46651 8.54098 6.82366 7.8728 7.34811 7.34835C7.87255 6.8239 8.54074 6.46675 9.26817 6.32206C9.9956 6.17736 10.7496 6.25162 11.4348 6.53545C12.12 6.81928 12.7057 7.29993 13.1178 7.91661C13.5298 8.5333 13.7498 9.25832 13.7498 10C13.7487 10.9942 13.3533 11.9475 12.6503 12.6505C11.9472 13.3535 10.994 13.749 9.99976 13.75ZM14.6873 6.25C14.5018 6.25 14.3206 6.19502 14.1664 6.092C14.0122 5.98899 13.8921 5.84257 13.8211 5.67127C13.7502 5.49996 13.7316 5.31146 13.7678 5.1296C13.8039 4.94775 13.8932 4.7807 14.0243 4.64959C14.1555 4.51848 14.3225 4.42919 14.5044 4.39301C14.6862 4.35684 14.8747 4.37541 15.046 4.44636C15.2173 4.51732 15.3637 4.63748 15.4668 4.79165C15.5698 4.94582 15.6248 5.12708 15.6248 5.3125C15.6248 5.56114 15.526 5.7996 15.3502 5.97541C15.1744 6.15123 14.9359 6.25 14.6873 6.25ZM12.4998 10C12.4998 10.4945 12.3531 10.9778 12.0784 11.3889C11.8037 11.8 11.4133 12.1205 10.9565 12.3097C10.4996 12.4989 9.99698 12.5484 9.51203 12.452C9.02708 12.3555 8.58162 12.1174 8.23199 11.7678C7.88236 11.4181 7.64426 10.9727 7.54779 10.4877C7.45133 10.0028 7.50084 9.50011 7.69006 9.04329C7.87928 8.58648 8.19971 8.19603 8.61083 7.92133C9.02195 7.64662 9.5053 7.5 9.99976 7.5C10.6628 7.5 11.2987 7.76339 11.7675 8.23223C12.2364 8.70107 12.4998 9.33696 12.4998 10Z',
			'tiktok'    => 'M18.1248 6.25V9.375C18.1248 9.54076 18.0589 9.69973 17.9417 9.81694C17.8245 9.93415 17.6655 10 17.4998 10C16.1941 10.003 14.9073 9.6891 13.7498 9.08516V12.1875C13.7498 13.7622 13.1242 15.2724 12.0107 16.3859C10.8972 17.4994 9.38698 18.125 7.81226 18.125C6.23753 18.125 4.72731 17.4994 3.61381 16.3859C2.50031 15.2724 1.87476 13.7622 1.87476 12.1875C1.87476 9.30469 3.9771 6.75625 6.76538 6.25938C6.85533 6.24339 6.94769 6.24731 7.03596 6.27088C7.12423 6.29444 7.20626 6.33707 7.27627 6.39576C7.34629 6.45446 7.40258 6.52778 7.44119 6.61058C7.47981 6.69338 7.4998 6.78364 7.49976 6.875V10.2102C7.49979 10.3284 7.46627 10.4443 7.40306 10.5443C7.33986 10.6443 7.24959 10.7243 7.14272 10.775C6.8901 10.8948 6.67425 11.0802 6.51768 11.3119C6.36112 11.5435 6.26959 11.8129 6.25263 12.092C6.23567 12.3711 6.29391 12.6496 6.42127 12.8985C6.54863 13.1474 6.74045 13.3576 6.97671 13.5071C7.21297 13.6566 7.48503 13.74 7.76449 13.7486C8.04396 13.7571 8.3206 13.6905 8.56556 13.5557C8.81051 13.4209 9.01481 13.2229 9.15715 12.9822C9.29948 12.7415 9.37463 12.4671 9.37476 12.1875V1.875C9.37476 1.70924 9.4406 1.55027 9.55781 1.43306C9.67502 1.31585 9.834 1.25 9.99976 1.25H13.1248C13.2905 1.25 13.4495 1.31585 13.5667 1.43306C13.6839 1.55027 13.7498 1.70924 13.7498 1.875C13.7508 2.86924 14.1462 3.82247 14.8492 4.52551C15.5523 5.22855 16.5055 5.62397 17.4998 5.625C17.6655 5.625 17.8245 5.69085 17.9417 5.80806C18.0589 5.92527 18.1248 6.08424 18.1248 6.25Z',
			'line'      => 'M9.984 0C4.48032 0 0 3.65914 0 8.1561C0 12.1838 3.55264 15.5584 8.34912 16.1999C8.67443 16.2689 9.11706 16.4162 9.22938 16.6941C9.32922 16.9462 9.2951 17.3356 9.26099 17.5985L9.12454 18.4521C9.0871 18.7042 8.92486 19.4447 9.99731 18.9921C11.0714 18.5411 15.7514 15.5775 17.8481 13.1523C19.2824 11.5715 19.968 9.95155 19.968 8.1561C19.968 3.65914 15.4877 0 9.984 0ZM6.0761 10.8301H4.09094C3.8039 10.8301 3.56678 10.5914 3.56678 10.3035V6.30906C3.56678 6.02035 3.8039 5.78157 4.09094 5.78157C4.38048 5.78157 4.6151 6.02035 4.6151 6.30906V9.776H6.0761C6.36563 9.776 6.59942 10.0131 6.59942 10.3035C6.59942 10.5914 6.3648 10.8301 6.0761 10.8301ZM8.12781 10.3035C8.12781 10.5914 7.89318 10.8301 7.60282 10.8301C7.31578 10.8301 7.08115 10.5914 7.08115 10.3035V6.30906C7.08115 6.02035 7.31578 5.78157 7.60531 5.78157C7.89318 5.78157 8.12781 6.02035 8.12781 6.30906V10.3035ZM12.9043 10.3035C12.9043 10.5298 12.7596 10.7303 12.5449 10.8027C12.4916 10.8202 12.4342 10.8285 12.3793 10.8285C12.2038 10.8285 12.054 10.7528 11.955 10.6188L9.92243 7.8416V10.3027C9.92243 10.5905 9.6903 10.8293 9.39744 10.8293C9.10957 10.8293 8.87661 10.5905 8.87661 10.3027V6.30906C8.87661 6.08275 9.02054 5.88224 9.23437 5.81069C9.28429 5.79155 9.34752 5.78323 9.39578 5.78323C9.55802 5.78323 9.70778 5.87059 9.80762 5.99622L11.856 8.78426V6.30906C11.856 6.02035 12.0906 5.78157 12.3802 5.78157C12.6672 5.78157 12.9043 6.02035 12.9043 6.30906V10.3035ZM16.1117 7.77837C16.402 7.77837 16.6358 8.01715 16.6358 8.30669C16.6358 8.59539 16.402 8.83418 16.1117 8.83418H14.6515V9.776H16.1117C16.402 9.776 16.6358 10.0131 16.6358 10.3035C16.6358 10.5914 16.402 10.8301 16.1117 10.8301H14.1265C13.8395 10.8301 13.6049 10.5914 13.6049 10.3035V6.30906C13.6049 6.02035 13.8395 5.78157 14.129 5.78157H16.1142C16.402 5.78157 16.6358 6.02035 16.6358 6.30906C16.6358 6.60109 16.402 6.83654 16.1117 6.83654H14.6515V7.77837H16.1117Z',
			'whatsapp'  => 'M11.9206 11.3464L13.7174 12.2433C13.6323 12.6689 13.4021 13.0518 13.0661 13.3266C12.7302 13.6014 12.3093 13.7512 11.8752 13.7503C10.3839 13.7487 8.95413 13.1555 7.8996 12.101C6.84506 11.0464 6.2519 9.61666 6.25024 8.12533C6.25012 7.69183 6.40022 7.27168 6.67498 6.93638C6.94974 6.60108 7.3322 6.37134 7.75728 6.28627L8.65415 8.08314L7.89087 9.21908C7.83383 9.30464 7.79879 9.40295 7.78884 9.50529C7.77889 9.60764 7.79435 9.71086 7.83384 9.8058C8.28101 10.8686 9.12651 11.7141 10.1893 12.1613C10.2845 12.2025 10.3885 12.2194 10.4919 12.2104C10.5953 12.2014 10.6948 12.1668 10.7815 12.1097L11.9206 11.3464ZM18.1252 10.0003C18.1256 11.4031 17.7627 12.782 17.0719 14.0029C16.3812 15.2239 15.3861 16.2451 14.1836 16.9674C12.9811 17.6896 11.612 18.0882 10.2097 18.1244C8.80743 18.1606 7.41966 17.8331 6.18149 17.1738L3.52134 18.0605C3.30109 18.1339 3.06474 18.1446 2.83878 18.0913C2.61282 18.0379 2.40617 17.9227 2.242 17.7586C2.07783 17.5944 1.96263 17.3878 1.9093 17.1618C1.85598 16.9358 1.86664 16.6995 1.94009 16.4792L2.82681 13.8191C2.24724 12.7294 1.92352 11.522 1.88024 10.2886C1.83696 9.05514 2.07525 7.82804 2.57702 6.70043C3.07879 5.57283 3.83085 4.57435 4.77612 3.78079C5.72139 2.98723 6.83502 2.41945 8.03248 2.12054C9.22995 1.82164 10.4798 1.79946 11.6871 2.0557C12.8944 2.31193 14.0275 2.83985 15.0003 3.59937C15.9731 4.35889 16.7602 5.33006 17.3016 6.43915C17.8431 7.54825 18.1247 8.76612 18.1252 10.0003ZM15.0002 11.8753C15.0003 11.7592 14.9681 11.6454 14.9071 11.5466C14.8461 11.4478 14.7588 11.3679 14.6549 11.316L12.1549 10.066C12.0565 10.0169 11.9469 9.9947 11.8372 10.0016C11.7275 10.0084 11.6215 10.0441 11.5299 10.105L10.3823 10.8706C9.85544 10.581 9.42189 10.1475 9.13228 9.62064L9.8979 8.47299C9.95881 8.38145 9.9945 8.27547 10.0014 8.16573C10.0082 8.056 9.986 7.94639 9.93696 7.84799L8.68696 5.34799C8.63515 5.24335 8.55507 5.15533 8.45579 5.09388C8.35651 5.03243 8.242 5.00003 8.12524 5.00033C7.29644 5.00033 6.50159 5.32957 5.91554 5.91562C5.32948 6.50167 5.00024 7.29653 5.00024 8.12533C5.00231 9.94806 5.7273 11.6955 7.01617 12.9844C8.30503 14.2733 10.0525 14.9983 11.8752 15.0003C12.2856 15.0003 12.692 14.9195 13.0711 14.7625C13.4503 14.6054 13.7948 14.3752 14.085 14.085C14.3751 13.7949 14.6053 13.4504 14.7624 13.0712C14.9194 12.6921 15.0002 12.2857 15.0002 11.8753Z',
		);
	}

	/**
	 * Content → Social.
	 *
	 * One link for each place the file draws. A place with nowhere to go is not
	 * shown at all.
	 */
	private function register_social_controls() {
		$this->start_controls_section(
			'section_social',
			array(
				'label' => esc_html__( 'Social', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		foreach ( $this->social_places() as $which => $label ) {
			$this->add_link_controls( $this, $which . '_link', $label );
		}

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
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'footer_typography',
				'selector'       => '{{WRAPPER}} .custom-footer',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'label_typography',
				'label'          => esc_html__( 'Label', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-footer__contact-label',
				'fields_options' => array(
					'typography'     => array( 'default' => 'yes' ),
					'font_family'    => array( 'default' => 'Roboto' ),
					'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
					'font_weight'    => array( 'default' => '500' ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Label colour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9BCA6',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__contact-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'copyright_typography',
				'label'          => esc_html__( 'Copyright', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-footer__copyright',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 12 ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-footer__heading',
				'fields_options' => array(
					'typography'     => array( 'default' => 'yes' ),
					'font_family'    => array( 'default' => 'Fenul Compressed' ),
					'font_size'      => array(
						'default'        => array( 'unit' => 'px', 'size' => 48 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 32 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_weight'    => array( 'default' => '500' ),
					'text_transform' => array( 'default' => 'uppercase' ),
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading colour', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__heading' => 'color: {{VALUE}};',
				),
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
			'field_border_color',
			array(
				'label'     => esc_html__( 'Field border', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F9F8F6',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__field' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submit_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9D5434',
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
				'default'   => '#FFFFFF',
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
				'name'           => 'menu_typography',
				'selector'       => '{{WRAPPER}} .custom-footer__menu a',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_control(
			'menu_color',
			array(
				'label'     => esc_html__( 'Link', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F9F8F6',
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
				'default'   => '#C9BCA6',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__menu a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_background',
			array(
				'label'     => esc_html__( 'Social background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3A2114',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-footer__social-link' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_color',
			array(
				'label'     => esc_html__( 'Social icon', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9BCA6',
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
	 * What the design settles, for a control the client has left alone.
	 *
	 * The control carries none of this as a stored value — it shows it as a
	 * hint and stands empty — so the value is read from here on the way out.
	 *
	 * @return array
	 */
	private function design_text() {
		return array(
			'signup_heading'     => esc_html__( 'Signup to our newsletter', 'custom-elementor-widgets' ),
			'contact_label'      => esc_html__( 'Contact Info', 'custom-elementor-widgets' ),
			'location_label'     => esc_html__( 'Location', 'custom-elementor-widgets' ),
			'contact_address'    => esc_html__( 'Town Hall Sukhumvit 49, Sukhumvit 49, Khlong Tan Nuea, Watthana, Bangkok 10110', 'custom-elementor-widgets' ),
			'contact_details'    => "0xx-xxx-xxxx\nexample@gmail.com",
		);
	}

	/**
	 * One control's text: what the client typed, or what the design settles.
	 *
	 * @param array  $settings The widget's settings.
	 * @param string $key      The control's name.
	 * @return string
	 */
	private function text( $settings, $key ) {
		$typed = isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';

		if ( '' !== $typed ) {
			return $typed;
		}

		$design = $this->design_text();

		return isset( $design[ $key ] ) ? $design[ $key ] : '';
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
						<?php $this->render_menus( $settings ); ?>
					</div>

					<hr class="custom-footer__rule" />

					<div class="custom-footer__bottom">
						<?php
						$this->render_copyright();
						$this->render_social( $settings );
						?>
					</div>

					<?php $this->render_editor_hint(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Say what the footer is still waiting for, in the editor only.
	 */
	private function render_editor_hint() {
		$settings = $this->get_settings_for_display();

		if ( 0 !== $this->chosen_menu( $settings ) ) {
			return;
		}

		$this->editor_hint( __( 'This footer is waiting for its links: build a menu in Appearance → Menus, then choose it on the Content tab.', 'custom-elementor-widgets' ) );
	}

	/**
	 * The menu the client chose, if it still exists.
	 *
	 * @param array $settings The widget's settings.
	 * @return int
	 */
	private function chosen_menu( $settings ) {
		$menu = isset( $settings['menu'] ) ? (int) $settings['menu'] : 0;

		return ( 0 !== $menu && wp_get_nav_menu_object( $menu ) ) ? $menu : 0;
	}

	/**
	 * The heading and the mocked sign-up form.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_signup( $settings ) {
		$heading = $this->text( $settings, 'signup_heading' );
		$tag     = isset( $settings['signup_heading_tag'] ) ? $settings['signup_heading_tag'] : '';
		$allowed = array( 'h2', 'h3', 'h4', 'span' );
		$tag     = in_array( $tag, $allowed, true ) ? $tag : 'span';

		if ( '' !== $heading ) :
			?>
			<div class="custom-footer__column">
			<<?php echo esc_attr( $tag ); ?> class="custom-footer__heading"><?php echo esc_html( $heading ); ?></<?php echo esc_attr( $tag ); ?>>
			<?php
		endif;

		// The list, what a press does to it, and what the box says all belong to
		// the theme's WP Form. The widget draws the form and prints what it is
		// told; it does not keep the address or settle a word of it.
		if ( ! function_exists( 'kadence_child_subscribe_fields' ) ) {
			return;
		}

		$label       = kadence_child_signup_word( 'newsletter', 'label' );
		$placeholder = kadence_child_signup_word( 'newsletter', 'placeholder' );
		$button      = kadence_child_signup_word( 'newsletter', 'button' );

		$label       = '' !== $label ? $label : esc_html__( 'Email address', 'custom-elementor-widgets' );
		$placeholder = '' !== $placeholder ? $placeholder : esc_html__( 'Enter your email', 'custom-elementor-widgets' );
		$button      = '' !== $button ? $button : esc_html__( 'Submit', 'custom-elementor-widgets' );

		$result = kadence_child_subscribe_result();
		$said   = isset( $result['state'] ) ? (string) $result['state'] : '';
		$answer = '' !== $said ? kadence_child_signup_answer( 'newsletter', $said ) : '';
		?>
		<div class="custom-footer__actions">
		<form class="custom-footer__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php kadence_child_subscribe_fields( 'newsletter' ); ?>
			<label class="screen-reader-text" for="custom-footer-email-<?php echo esc_attr( $this->get_id() ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
			<input
				class="custom-footer__field"
				id="custom-footer-email-<?php echo esc_attr( $this->get_id() ); ?>"
				type="email"
				name="email"
				required
				value="<?php echo esc_attr( isset( $result['email'] ) ? $result['email'] : '' ); ?>"
				placeholder="<?php echo esc_attr( $placeholder ); ?>"
			/>
			<?php
			// A box the reader has to answer stands directly above the button
			// that sends the form. One that asks nothing shows nothing.
			if ( function_exists( 'kadence_child_form_captcha_field' ) ) {
				kadence_child_form_captcha_field( 'newsletter' );
			}
			?>
			<button class="custom-footer__submit" type="submit"><?php echo esc_html( $button ); ?></button>
		</form>

		<?php if ( '' !== $answer ) : ?>
			<p class="custom-footer__result" role="status"><?php echo esc_html( $answer ); ?></p>
		<?php endif; ?>
		</div>
		</div>
		<?php
	}


	/**
	 * The contact lines.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_contact( $settings ) {
		$columns = array(
			array( $this->text( $settings, 'contact_label' ), $this->text( $settings, 'contact_details' ) ),
			array( $this->text( $settings, 'location_label' ), $this->text( $settings, 'contact_address' ) ),
		);

		$columns = array_filter(
			$columns,
			static function ( $column ) {
				return '' !== $column[0] || '' !== $column[1];
			}
		);

		if ( empty( $columns ) ) {
			return;
		}
		?>
		<div class="custom-footer__contact">
			<?php foreach ( $columns as $column ) : ?>
				<div class="custom-footer__contact-column">
					<?php if ( '' !== $column[0] ) : ?>
						<p class="custom-footer__contact-label"><?php echo esc_html( $column[0] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $column[1] ) : ?>
						<div class="custom-footer__contact-lines">
							<?php foreach ( preg_split( '/\r\n|\r|\n/', $column[1] ) as $line ) : ?>
								<p><?php echo esc_html( $line ); ?></p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * The links — one menu, laid out across before it is laid out down.
	 *
	 * The design draws two columns, and they are two columns of one list: the
	 * client keeps one order and the row it breaks on is the design's.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_menus( $settings ) {
		$menu = $this->chosen_menu( $settings );

		if ( 0 === $menu ) {
			return;
		}
		?>
		<div class="custom-footer__menus">
			<nav class="custom-footer__menu"><?php $this->menu( $menu ); ?></nav>
		</div>
		<?php
	}

	/**
	 * The line at the foot of the page, and who made it.
	 *
	 * Not asked for: it says who built the site, which is not the client's to
	 * write. The maker's name is followed from the front page alone — one page
	 * vouching for the work, and the rest of the site saying nothing about it.
	 */
	private function render_copyright() {
		// A tab opened from here must not be handed a way back to this one.
		$rel = is_front_page() ? 'noopener' : 'nofollow noopener';

		$maker = sprintf(
			'<a class="custom-footer__maker" href="%1$s" target="_blank" rel="%2$s">%3$s</a>',
			esc_url( 'https://yeswebdesignstudio.com/' ),
			esc_attr( $rel ),
			esc_html__( 'Yes Web Design.', 'custom-elementor-widgets' )
		);
		?>
		<p class="custom-footer__copyright"><?php
			printf(
				/* translators: %s: the maker's name, as a link. */
				esc_html__( '© 2026 CAVO design by %s', 'custom-elementor-widgets' ),
				$maker // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts.
			);
		?></p>
		<?php
	}

	/**
	 * The links to the places the design draws, and none that have nowhere to
	 * go.
	 *
	 * @param array $settings The widget's settings.
	 */
	private function render_social( $settings ) {
		$marks  = $this->social_marks();
		$places = array();

		foreach ( $this->social_places() as $which => $label ) {
			$where = isset( $settings[ $which . '_link' ] ) ? trim( (string) $settings[ $which . '_link' ] ) : '';

			if ( '' !== $where ) {
				$places[ $which ] = $label;
			}
		}

		if ( empty( $places ) ) {
			return;
		}
		?>
		<div class="custom-footer__social">
			<?php foreach ( $places as $which => $label ) : ?>
				<a class="custom-footer__social-link"<?php
					echo $this->link_from( $settings, $which . '_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
				?>>
					<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="<?php
						echo esc_attr( $marks[ $which ] );
					?>"/></svg>
					<span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
