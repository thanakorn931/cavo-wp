<?php
/**
 * Contact, get in touch — one section of the design.
 *
 * The widget draws the form; it does not settle it. What the form asks, and
 * what happens when one is sent, belong to the Form editor and the Settings
 * beside it. This section's own are the picture the band holds and the name
 * standing on it.
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
 * The Contact page's form.
 */
class Contact_Form extends Base_Widget {

	/**
	 * The widget's name, and its asset handle's suffix.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'contact-form';
	}

	/**
	 * The title shown in the panel.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Contact — Form', 'custom-elementor-widgets' );
	}

	/**
	 * The icon shown in the panel.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * What finds this widget in the panel's search.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'contact', 'form', 'touch', 'message' );
	}

	/**
	 * The words the design settles.
	 *
	 * @return array
	 */
	protected function design_text() {
		return array(
			'heading' => esc_html__( 'Get in touch', 'custom-elementor-widgets' ),
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
	 * The forms the site has, by name.
	 *
	 * @return array
	 */
	private function forms() {
		return function_exists( 'kadence_child_enquiry_forms' ) ? kadence_child_enquiry_forms() : array();
	}

	/**
	 * The Content tab and the Style tab.
	 */
	protected function register_controls() {
		$design = $this->design_text();
		$forms  = $this->forms();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Section', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'form',
			array(
				'label'       => esc_html__( 'Form', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'contact',
				'options'     => $forms,
				'description' => esc_html__( 'What it asks is set in WP Form → Form editor.', 'custom-elementor-widgets' ),
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
				'default' => 'h2',
				'options' => array(
					'h2'   => 'H2',
					'h3'   => 'H3',
					'span' => esc_html__( 'None', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'picture',
			array(
				'label' => esc_html__( 'Picture', 'custom-elementor-widgets' ),
				'type'  => Controls_Manager::MEDIA,
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
			'veil_color',
			array(
				'label'     => esc_html__( 'Veil', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(58, 33, 20, 0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-form__veil' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_color',
			array(
				'label'     => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F3F1ED',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-form__heading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'heading_typography',
				'label'          => esc_html__( 'Heading', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-contact-form__heading',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Fenul Compressed' ),
					'font_size'   => array(
						'default'        => array( 'unit' => 'px', 'size' => 48 ),
						'tablet_default' => array( 'unit' => 'px', 'size' => 32 ),
						'mobile_default' => array( 'unit' => 'px', 'size' => 32 ),
					),
					'font_weight' => array( 'default' => '500' ),
				),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label'     => esc_html__( 'Labels and fields', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-form__column' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'placeholder_color',
			array(
				'label'     => esc_html__( 'Placeholder', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.5)',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-form__input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} select.custom-contact-form__input:invalid' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'field_typography',
				'label'          => esc_html__( 'Labels and fields', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-contact-form__label, {{WRAPPER}} .custom-contact-form__input, {{WRAPPER}} .custom-contact-form__result',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
				),
			)
		);

		$this->add_control(
			'send_color',
			array(
				'label'     => esc_html__( 'Button text', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#3D3426',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-form__send' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'send_background',
			array(
				'label'     => esc_html__( 'Button background', 'custom-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .custom-contact-form__send' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'send_typography',
				'label'          => esc_html__( 'Button', 'custom-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .custom-contact-form__send',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_family' => array( 'default' => 'Roboto' ),
					'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
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

		$slug    = isset( $settings['form'] ) ? (string) $settings['form'] : '';
		$picture = isset( $settings['picture']['url'] ) ? $settings['picture']['url'] : '';
		$tag     = isset( $settings['heading_tag'] ) ? $settings['heading_tag'] : 'h2';
		$tag     = in_array( $tag, array( 'h2', 'h3', 'span' ), true ) ? $tag : 'h2';

		$fields = function_exists( 'kadence_child_form_definition' ) ? kadence_child_form_definition( $slug ) : array();
		$typed  = $this->typed();
		?>
		<div class="custom-contact-form">
			<span class="custom-contact-form__picture" aria-hidden="true"><?php $this->media( $picture ); ?></span>
			<span class="custom-contact-form__veil" aria-hidden="true"></span>

			<<?php echo esc_attr( $tag ); ?> class="custom-contact-form__heading"><?php
				echo esc_html( $this->text( $settings, 'heading' ) );
			?></<?php echo esc_attr( $tag ); ?>>

			<div class="custom-contact-form__column">
				<form class="custom-contact-form__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="cavo_form" />
					<input type="hidden" name="cavo_form" value="<?php echo esc_attr( $slug ); ?>" />
					<?php
					wp_nonce_field( 'cavo_form_' . $slug, 'cavo_nonce' );

					// The trap and the clock are the theme's, printed the same
					// way for every form so none can be built without them.
					if ( function_exists( 'kadence_child_form_trap' ) ) {
						kadence_child_form_trap();
					}
					?>

					<div class="custom-contact-form__fields">
						<?php foreach ( $fields as $index => $field ) : ?>
							<?php $this->render_field( $index, $field, $typed ); ?>
						<?php endforeach; ?>
					</div>

					<div class="custom-contact-form__captcha">
						<?php
						if ( function_exists( 'kadence_child_form_captcha_field' ) ) {
							kadence_child_form_captcha_field( $slug );
						}
						?>
					</div>

					<div class="custom-contact-form__actions">
						<button type="submit" class="custom-contact-form__send"><?php
							echo esc_html__( 'Submit', 'custom-elementor-widgets' );
						?></button>
					</div>
				</form>

				<?php $this->render_result( $slug ); ?>

				<?php
				if ( empty( $fields ) ) {
					$this->editor_hint( __( 'This form is waiting for its fields, in WP Form → Form editor.', 'custom-elementor-widgets' ) );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * One field, as the Form editor left it.
	 *
	 * @param int   $index Which field.
	 * @param array $field The field.
	 * @param array $typed What they typed, where it did not go through.
	 */
	private function render_field( $index, $field, $typed = array() ) {
		$label       = isset( $field['label'] ) ? (string) $field['label'] : '';
		$type        = isset( $field['type'] ) ? (string) $field['type'] : 'text';
		$width       = isset( $field['width'] ) && '50' === (string) $field['width'] ? 'half' : 'full';
		$required    = ! empty( $field['required'] );
		$placeholder = isset( $field['placeholder'] ) ? (string) $field['placeholder'] : '';
		$name        = 'field_' . (int) $index;
		$id          = 'cavo-' . $this->get_id() . '-' . (int) $index;
		$was         = isset( $typed[ $name ] ) ? (string) $typed[ $name ] : '';
		?>
		<div class="custom-contact-form__field custom-contact-form__field--<?php echo esc_attr( $width ); ?>">
			<label class="custom-contact-form__label" for="<?php echo esc_attr( $id ); ?>"><?php
				echo esc_html( $label );
				echo $required ? '*' : '';
			?></label>

			<?php if ( 'textarea' === $type ) : ?>
				<textarea
					class="custom-contact-form__input custom-contact-form__input--area"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					<?php echo $required ? 'required' : ''; ?>
				><?php echo esc_textarea( $was ); ?></textarea>
			<?php elseif ( 'select' === $type ) : ?>
				<select
					class="custom-contact-form__input"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					<?php echo $required ? 'required' : ''; ?>
				>
					<option value=""><?php echo esc_html( '' !== $placeholder ? $placeholder : __( 'Select one …', 'custom-elementor-widgets' ) ); ?></option>
					<?php foreach ( $this->choices( $field ) as $choice ) : ?>
						<option value="<?php echo esc_attr( $choice ); ?>"<?php selected( $was, $choice ); ?>><?php echo esc_html( $choice ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else : ?>
				<input
					class="custom-contact-form__input"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $name ); ?>"
					type="<?php echo esc_attr( in_array( $type, array( 'email', 'tel', 'date' ), true ) ? $type : 'text' ); ?>"
					value="<?php echo esc_attr( $was ); ?>"
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					<?php echo $required ? 'required' : ''; ?>
				/>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * What a choice field offers, one to a line.
	 *
	 * @param array $field The field.
	 * @return array
	 */
	private function choices( $field ) {
		$raw = isset( $field['choices'] ) ? (string) $field['choices'] : '';

		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw ) ) ) );
	}

	/**
	 * What they typed, where it did not go through.
	 *
	 * @return array
	 */
	private function typed() {
		if ( ! isset( $_GET['typed'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading back what was already sent.
			return array();
		}

		$was = get_transient( 'cavo_typed_' . sanitize_key( wp_unslash( $_GET['typed'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading back what was already sent.

		return is_array( $was ) ? $was : array();
	}

	/**
	 * What the form says after it has been sent, in the client's own words.
	 *
	 * @param string $slug The form's slug.
	 */
	private function render_result( $slug ) {
		if ( ! isset( $_GET['sent'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a result, not acting on one.
			return;
		}

		$good   = 'yes' === sanitize_key( wp_unslash( $_GET['sent'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- reading a result, not acting on one.
		$result = function_exists( 'kadence_child_form_settings' ) ? kadence_child_form_settings( $slug, 'result' ) : array();
		$key    = $good ? 'success' : 'fail';
		$said   = isset( $result[ $key ] ) ? trim( (string) $result[ $key ] ) : '';

		if ( '' === $said ) {
			return;
		}
		?>
		<p class="custom-contact-form__result" role="status"><?php echo esc_html( $said ); ?></p>
		<?php
	}
}
