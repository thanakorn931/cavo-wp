<?php
/**
 * Nightlife, hall of beats — one section of the design.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Widgets;

use Custom_Elementor_Widgets\Base_Widget;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Nightlife page's hall of beats.
 */
class Nightlife_Beat extends Base_Widget {

	/**
	 * How many cards carry their picture from the first paint. Every step
	 * forward gives one more its own.
	 */
	const LOADED = 10;

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'nightlife-beat';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Nightlife — Beat', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-carousel';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'nightlife', 'beat', 'dj', 'artists', 'carousel' );
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
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
				'placeholder' => esc_html__( 'Hall of Beats', 'custom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'heading_tag',
			array(
				'label'   => esc_html__( 'Heading level', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'strip',
			array(
				'label'     => esc_html__( 'Closing picture', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cards',
			array(
				'label' => esc_html__( 'Artists', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'   => esc_html__( 'Name', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'note',
			array(
				'label'   => esc_html__( 'Note', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'genre',
			array(
				'label'   => esc_html__( 'Genre', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'social_text',
			array(
				'label'     => esc_html__( 'Social text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'separator' => 'before',
			)
		);

		$this->add_link_controls( $repeater, 'social_link', esc_html__( 'Social link', 'custom-elementor-widgets' ) );

		$this->add_control(
			'cards',
			array(
				'label'       => esc_html__( 'Artists', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name }}}',
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
			'text_color',
			array(
				'label'     => esc_html__( 'Text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-beat__band' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-beat__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 64 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 24 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 24 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'name_typography',
				'label'          => esc_html__( 'Name', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-beat__name',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 20 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 7 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 7 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'note_typography',
				'label'          => esc_html__( 'Note and chips', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-nightlife-beat__note, {{WRAPPER}} .custom-nightlife-beat__chip',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 16 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 6 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 6 ),
					),
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Arrows', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAF6EA',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-beat__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_off_background',
			array(
				'label'     => esc_html__( 'Arrows, with nowhere to go', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#C9BCA6',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-beat__arrow[disabled]' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow mark', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#121212',
				'selectors' => array(
					'{{WRAPPER}} .custom-nightlife-beat__arrow svg' => 'fill: {{VALUE}};',
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

		$cards   = isset( $settings['cards'] ) ? (array) $settings['cards'] : array();
		$heading = isset( $settings['heading'] ) ? trim( (string) $settings['heading'] ) : '';
		$heading = '' !== $heading ? $heading : esc_html__( 'Hall of Beats', 'custom-elementor-widgets' );

		$tag = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';
		?>
		<div class="custom-nightlife-beat">
			<div class="custom-nightlife-beat__band">
				<<?php echo esc_attr( $tag ); ?> class="custom-nightlife-beat__heading"><?php
					echo esc_html( $heading );
				?></<?php echo esc_attr( $tag ); ?>>

				<div class="custom-nightlife-beat__stage">
					<div class="custom-nightlife-beat__track">
						<?php foreach ( $cards as $index => $card ) : ?>
							<?php $this->render_card( $card, $index < self::LOADED ); ?>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="custom-nightlife-beat__actions">
					<?php
					$this->render_arrow( 'prev', __( 'Previous', 'custom-elementor-widgets' ) );
					$this->render_arrow( 'next', __( 'Next', 'custom-elementor-widgets' ) );
					?>
				</div>

				<?php
				if ( empty( $cards ) ) {
					$this->editor_hint( __( 'This section is waiting for its artists, on the Content tab.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>

			<?php $strip = isset( $settings['strip']['url'] ) ? trim( (string) $settings['strip']['url'] ) : ''; ?>
			<?php if ( '' !== $strip ) : ?>
				<span class="custom-nightlife-beat__strip"><?php $this->media( $strip ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * One card.
	 *
	 * @param array $card The row, or nothing where the list is empty.
	 * @param bool  $now  Whether its picture is fetched with the page.
	 */
	private function render_card( $card, $now ) {
		$picture = isset( $card['picture']['url'] ) ? $card['picture']['url'] : '';
		$name    = isset( $card['name'] ) ? trim( (string) $card['name'] ) : '';
		$note    = isset( $card['note'] ) ? trim( (string) $card['note'] ) : '';
		$genre   = isset( $card['genre'] ) ? trim( (string) $card['genre'] ) : '';

		$social = isset( $card['social_text'] ) ? trim( (string) $card['social_text'] ) : '';
		$social = '' !== $social ? $social : esc_html__( 'Instagram', 'custom-elementor-widgets' );
		?>
		<article class="custom-nightlife-beat__card">
			<div class="custom-nightlife-beat__inner">
				<span class="custom-nightlife-beat__picture" aria-hidden="true">
					<?php if ( '' === $picture ) : ?>
						<?php $this->media( '' ); ?>
					<?php elseif ( $now ) : ?>
						<img src="<?php echo esc_url( $picture ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
					<?php else : ?>
						<img data-src="<?php echo esc_url( $picture ); ?>" alt="<?php echo esc_attr( $name ); ?>" />
					<?php endif; ?>
				</span>

				<span class="custom-nightlife-beat__veil" aria-hidden="true"></span>

				<div class="custom-nightlife-beat__words">
					<p class="custom-nightlife-beat__name"><?php echo esc_html( $name ); ?></p>
					<p class="custom-nightlife-beat__note"><?php echo esc_html( $note ); ?></p>

					<div class="custom-nightlife-beat__chips">
						<?php if ( '' !== $genre ) : ?>
							<span class="custom-nightlife-beat__chip"><?php echo esc_html( $genre ); ?></span>
						<?php endif; ?>

						<a class="custom-nightlife-beat__chip custom-nightlife-beat__chip--social"<?php
							echo $this->link_from( $card, 'social_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in link_attributes().
						?>><?php $this->render_social_mark(); ?><?php echo esc_html( $social ); ?></a>
					</div>
				</div>
			</div>

			<span class="custom-nightlife-beat__badge" aria-hidden="true"></span>
		</article>
		<?php
	}

	/**
	 * The social mark — the design's own, carried by the widget.
	 */
	private function render_social_mark() {
		?>
		<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M10 6.25C9.25832 6.25 8.5333 6.46993 7.91661 6.88199C7.29993 7.29404 6.81928 7.87971 6.53545 8.56494C6.25162 9.25016 6.17736 10.0042 6.32206 10.7316C6.46675 11.459 6.8239 12.1272 7.34835 12.6517C7.8728 13.1761 8.54098 13.5333 9.26841 13.6779C9.99584 13.8226 10.7498 13.7484 11.4351 13.4645C12.1203 13.1807 12.706 12.7001 13.118 12.0834C13.5301 11.4667 13.75 10.7417 13.75 10C13.749 9.00576 13.3535 8.05253 12.6505 7.34949C11.9475 6.64645 10.9942 6.25103 10 6.25ZM10 12.5C9.50555 12.5 9.0222 12.3534 8.61107 12.0787C8.19995 11.804 7.87952 11.4135 7.6903 10.9567C7.50108 10.4999 7.45157 9.99723 7.54804 9.51227C7.6445 9.02732 7.8826 8.58186 8.23223 8.23223C8.58186 7.8826 9.02732 7.6445 9.51227 7.54804C9.99723 7.45157 10.4999 7.50108 10.9567 7.6903C11.4135 7.87952 11.804 8.19995 12.0787 8.61107C12.3534 9.0222 12.5 9.50555 12.5 10C12.5 10.663 12.2366 11.2989 11.7678 11.7678C11.2989 12.2366 10.663 12.5 10 12.5ZM13.75 1.875H6.25C5.09006 1.87624 3.97798 2.33758 3.15778 3.15778C2.33758 3.97798 1.87624 5.09006 1.875 6.25V13.75C1.87624 14.9099 2.33758 16.022 3.15778 16.8422C3.97798 17.6624 5.09006 18.1238 6.25 18.125H13.75C14.9099 18.1238 16.022 17.6624 16.8422 16.8422C17.6624 16.022 18.1238 14.9099 18.125 13.75V6.25C18.1238 5.09006 17.6624 3.97798 16.8422 3.15778C16.022 2.33758 14.9099 1.87624 13.75 1.875ZM16.875 13.75C16.875 14.5788 16.5458 15.3737 15.9597 15.9597C15.3737 16.5458 14.5788 16.875 13.75 16.875H6.25C5.4212 16.875 4.62634 16.5458 4.04029 15.9597C3.45424 15.3737 3.125 14.5788 3.125 13.75V6.25C3.125 5.4212 3.45424 4.62634 4.04029 4.04029C4.62634 3.45424 5.4212 3.125 6.25 3.125H13.75C14.5788 3.125 15.3737 3.45424 15.9597 4.04029C16.5458 4.62634 16.875 5.4212 16.875 6.25V13.75ZM15 5.9375C15 6.12292 14.945 6.30418 14.842 6.45835C14.739 6.61252 14.5926 6.73268 14.4213 6.80364C14.25 6.87459 14.0615 6.89316 13.8796 6.85699C13.6977 6.82081 13.5307 6.73152 13.3996 6.60041C13.2685 6.4693 13.1792 6.30225 13.143 6.1204C13.1068 5.93854 13.1254 5.75004 13.1964 5.57873C13.2673 5.40743 13.3875 5.26101 13.5417 5.158C13.6958 5.05498 13.8771 5 14.0625 5C14.3111 5 14.5496 5.09877 14.7254 5.27459C14.9012 5.4504 15 5.68886 15 5.9375Z"/></svg>
		<?php
	}

	/**
	 * One of the two arrows — the design's own mark, carried by the widget.
	 *
	 * @param string $side  Which arrow this is.
	 * @param string $label What a reader who cannot see it is told.
	 */
	private function render_arrow( $side, $label ) {
		?>
		<button
			type="button"
			class="custom-nightlife-beat__arrow custom-nightlife-beat__arrow--<?php echo esc_attr( $side ); ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
		><svg viewBox="10 6 56 56" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M50.0008 34.0006C50.0008 34.2658 49.8954 34.5201 49.7079 34.7077C49.5204 34.8952 49.266 35.0006 49.0008 35.0006H29.4145L36.7083 42.2931C36.8012 42.386 36.8749 42.4963 36.9252 42.6177C36.9755 42.7391 37.0013 42.8692 37.0013 43.0006C37.0013 43.132 36.9755 43.2621 36.9252 43.3835C36.8749 43.5048 36.8012 43.6151 36.7083 43.7081C36.6154 43.801 36.5051 43.8747 36.3837 43.9249C36.2623 43.9752 36.1322 44.0011 36.0008 44.0011C35.8694 44.0011 35.7393 43.9752 35.6179 43.9249C35.4965 43.8747 35.3862 43.801 35.2933 43.7081L26.2933 34.7081C26.2003 34.6152 26.1266 34.5049 26.0762 34.3835C26.0259 34.2621 26 34.132 26 34.0006C26 33.8691 26.0259 33.739 26.0762 33.6176C26.1266 33.4962 26.2003 33.3859 26.2933 33.2931L35.2933 24.2931C35.4809 24.1054 35.7354 24 36.0008 24C36.2662 24 36.5206 24.1054 36.7083 24.2931C36.8959 24.4807 37.0013 24.7352 37.0013 25.0006C37.0013 25.2659 36.8959 25.5204 36.7083 25.7081L29.4145 33.0006H49.0008C49.266 33.0006 49.5204 33.1059 49.7079 33.2934C49.8954 33.481 50.0008 33.7353 50.0008 34.0006Z" fill="#121212"/></svg></button>
		<?php
	}
}
