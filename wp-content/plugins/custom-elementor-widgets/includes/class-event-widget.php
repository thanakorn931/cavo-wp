<?php
/**
 * What the two event sections share.
 *
 * Both draw the same card, and both take their items from the same place: a
 * post type the client chooses, narrowed by a taxonomy and its terms. Which
 * post type is theirs to pick, so nothing here names one.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The base the event sections extend.
 */
abstract class Event_Widget extends Base_Widget {

	/**
	 * The card and its button are the two sections' between them, so their
	 * stylesheet is declared beside the section's own.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		return array_merge( parent::get_style_depends(), $this->shared_handle( 'style' ) );
	}

	/**
	 * The script that brings more of the list on screen, likewise.
	 *
	 * @return array
	 */
	public function get_script_depends(): array {
		return array_merge( parent::get_script_depends(), $this->shared_handle( 'script' ) );
	}

	/**
	 * The handle the two sections share, where it is registered.
	 *
	 * @param string $kind Either style or script.
	 * @return array
	 */
	private function shared_handle( $kind ) {
		$handle = Widgets_Loader::HANDLE_PREFIX . 'event-widget';

		if ( 'style' === $kind ) {
			return wp_style_is( $handle, 'registered' ) ? array( $handle ) : array();
		}

		return wp_script_is( $handle, 'registered' ) ? array( $handle ) : array();
	}

	/**
	 * Content → Source.
	 *
	 * Three controls, each one narrowing the last: the post type, one of its
	 * taxonomies, and terms of that taxonomy. A taxonomy left alone means the
	 * whole post type; terms left alone mean the whole taxonomy.
	 */
	protected function register_source_controls() {
		$this->start_controls_section(
			'section_source',
			array(
				'label' => esc_html__( 'Source', 'custom-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => self::post_type_options(),
			)
		);

		$this->add_control(
			'taxonomy',
			array(
				'label'       => esc_html__( 'Taxonomy', 'custom-elementor-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array_merge(
					array( '' => esc_html__( 'All', 'custom-elementor-widgets' ) ),
					self::taxonomy_options()
				),
				'description' => esc_html__( 'Left on All, the whole source is shown. Choose one and an item with no term in it is not shown at all.', 'custom-elementor-widgets' ),
			)
		);

		// One term control per taxonomy, shown only for the taxonomy chosen.
		foreach ( self::taxonomy_options() as $name => $label ) {
			$this->add_control(
				'terms_' . $name,
				array(
					'label'       => esc_html__( 'Terms', 'custom-elementor-widgets' ),
					'type'        => Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => self::term_options( $name ),
					'condition'   => array( 'taxonomy' => $name ),
					'description' => esc_html__( 'Left empty, every term is shown.', 'custom-elementor-widgets' ),
				)
			);
		}

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'custom-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => esc_html__( 'Newest first', 'custom-elementor-widgets' ),
					'ASC'  => esc_html__( 'Oldest first', 'custom-elementor-widgets' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * The post types a client may draw from.
	 *
	 * @return array
	 */
	private static function post_type_options() {
		$options = array();

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type ) {
			if ( 'attachment' === $type->name ) {
				continue;
			}

			$options[ $type->name ] = $type->labels->singular_name;
		}

		return $options;
	}

	/**
	 * The taxonomies a client may narrow by.
	 *
	 * @return array
	 */
	private static function taxonomy_options() {
		$options = array();

		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $taxonomy ) {
			if ( ! $taxonomy->show_ui ) {
				continue;
			}

			$options[ $taxonomy->name ] = $taxonomy->labels->singular_name;
		}

		return $options;
	}

	/**
	 * The terms of one taxonomy.
	 *
	 * @param string $taxonomy The taxonomy's name.
	 * @return array
	 */
	private static function term_options( $taxonomy ) {
		$options = array();
		$terms   = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			return $options;
		}

		foreach ( $terms as $term ) {
			$options[ $term->slug ] = $term->name;
		}

		return $options;
	}

	/**
	 * The items the section shows.
	 *
	 * Everything the source holds is fetched; how much of it is on screen at
	 * once is the button's business, not the query's.
	 *
	 * @param array $settings The widget's settings.
	 * @return \WP_Post[]
	 */
	protected function items( $settings ) {
		$source   = isset( $settings['source'] ) ? $settings['source'] : 'post';
		$taxonomy = isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : '';
		$order    = isset( $settings['order'] ) && 'ASC' === $settings['order'] ? 'ASC' : 'DESC';

		$query = array(
			'post_type'           => $source,
			'post_status'         => 'publish',
			'posts_per_page'      => 100,
			'order'               => $order,
			'orderby'             => 'date',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		if ( '' !== $taxonomy ) {
			$terms = isset( $settings[ 'terms_' . $taxonomy ] ) ? (array) $settings[ 'terms_' . $taxonomy ] : array();
			$terms = array_filter( $terms );

			// Terms left alone are the whole taxonomy: whatever has a term in
			// it, and nothing that has none.
			$query['tax_query'] = array(
				empty( $terms )
					? array(
						'taxonomy' => $taxonomy,
						'operator' => 'EXISTS',
					)
					: array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms,
					),
			);
		}

		return get_posts( $query );
	}

	/**
	 * One item's date, hour and kind, as the design draws them.
	 *
	 * Each fact is a mark and a word; the rule between them is the section's,
	 * not part of what the client typed.
	 *
	 * @param \WP_Post $post The item.
	 */
	protected function render_meta( $post ) {
		$facts = array(
			'calendar' => $this->field( $post, 'event_date' ),
			'timer'    => $this->field( $post, 'event_time' ),
			'note'     => $this->field( $post, 'event_genre' ),
		);

		$facts = array_filter( $facts );

		if ( empty( $facts ) ) {
			return;
		}
		?>
		<p class="custom-event-card__meta">
			<?php foreach ( $facts as $mark => $fact ) : ?>
				<span class="custom-event-card__fact">
					<span class="custom-event-card__mark" aria-hidden="true"><?php $this->render_mark( $mark ); ?></span>
					<?php echo esc_html( $fact ); ?>
				</span>
			<?php endforeach; ?>
		</p>
		<?php
	}

	/**
	 * One field of an item, where the plugin that holds it is active.
	 *
	 * @param \WP_Post $post The item.
	 * @param string   $name The field's name.
	 * @return string
	 */
	protected function field( $post, $name ) {
		if ( function_exists( 'get_field' ) ) {
			return trim( (string) get_field( $name, $post->ID ) );
		}

		return trim( (string) get_post_meta( $post->ID, $name, true ) );
	}

	/**
	 * One card.
	 *
	 * @param \WP_Post $post   The item.
	 * @param bool     $hidden Whether it waits for the button before it shows.
	 */
	protected function render_card( $post, $hidden = false ) {
		$picture = get_the_post_thumbnail_url( $post, 'large' );
		$title   = get_the_title( $post );
		?>
		<article class="custom-event-card<?php echo $hidden ? ' is-waiting' : ''; ?>"<?php echo $hidden ? ' hidden' : ''; ?>>
			<span class="custom-event-card__picture">
				<?php $this->media( $picture, $title, true ); ?>
				<span class="custom-event-card__badge" aria-hidden="true"></span>
			</span>

			<div class="custom-event-card__words">
				<?php $this->render_meta( $post ); ?>

				<h3 class="custom-event-card__title"><?php echo esc_html( $title ); ?></h3>

				<p class="custom-event-card__body"><?php echo esc_html( get_the_excerpt( $post ) ); ?></p>
			</div>
		</article>
		<?php
	}

	/**
	 * The button that brings more of the list on screen.
	 *
	 * While it is working it keeps its shape and loses its words, so the page
	 * below it does not move under the reader's hand.
	 *
	 * @param string $text What it says.
	 * @param int    $step How many more it brings, or 0 for all that are left.
	 */
	protected function render_more_button( $text, $step ) {
		?>
		<button
			type="button"
			class="custom-event-more"
			data-step="<?php echo esc_attr( $step ); ?>"
		>
			<span class="custom-event-more__text"><?php echo esc_html( $text ); ?></span>
			<span class="custom-event-more__spinner" aria-hidden="true"></span>
		</button>
		<?php
	}

	/**
	 * One of the three marks the meta line uses — the design's own, not the
	 * client's, so the widget carries them.
	 *
	 * @param string $which Which mark.
	 */
	private function render_mark( $which ) {
		$marks = array(
			'calendar' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M10 2.66667V1.33333M10 2.66667V4M10 2.66667H7M2 6.66667V12.6667C2 13.0203 2.14048 13.3594 2.39052 13.6095C2.64057 13.8595 2.97971 14 3.33333 14H12.6667C13.0203 14 13.3594 13.8595 13.6095 13.6095C13.8595 13.3594 14 13.0203 14 12.6667V6.66667M2 6.66667H14M2 6.66667V4C2 3.64638 2.14048 3.30724 2.39052 3.05719C2.64057 2.80714 2.97971 2.66667 3.33333 2.66667H4.66667M14 6.66667V4C14 3.64638 13.8595 3.30724 13.6095 3.05719C13.3594 2.80714 13.0203 2.66667 12.6667 2.66667H12.3333M4.66667 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'timer'    => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M6 1.33333H10M8 6.66667V9.33333M8 14.6667C9.41449 14.6667 10.771 14.1048 11.7712 13.1046C12.7714 12.1044 13.3333 10.7478 13.3333 9.33333C13.3333 7.91884 12.7714 6.56229 11.7712 5.5621C10.771 4.5619 9.41449 4 8 4C6.58551 4 5.22896 4.5619 4.22876 5.5621C3.22857 6.56229 2.66667 7.91884 2.66667 9.33333C2.66667 10.7478 3.22857 12.1044 4.22876 13.1046C5.22896 14.1048 6.58551 14.6667 8 14.6667Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'note'     => '<svg width="16" height="16" viewBox="-1.92 -1.25 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false"><path d="M11.4167 8.08334V0.750006L4.08333 2.08334V9.41667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.41667 11.4167H10.0833C10.437 11.4167 10.7761 11.2762 11.0261 11.0261C11.2762 10.7761 11.4167 10.437 11.4167 10.0833V8.08334H9.41667C9.06304 8.08334 8.72391 8.22382 8.47386 8.47386C8.22381 8.72391 8.08333 9.06305 8.08333 9.41667V10.0833C8.08333 10.437 8.22381 10.7761 8.47386 11.0261C8.72391 11.2762 9.06304 11.4167 9.41667 11.4167ZM2.08333 12.75H2.75C3.10362 12.75 3.44276 12.6095 3.69281 12.3595C3.94286 12.1094 4.08333 11.7703 4.08333 11.4167V9.41667H2.08333C1.72971 9.41667 1.39057 9.55715 1.14052 9.8072C0.890476 10.0572 0.75 10.3964 0.75 10.75V11.4167C0.75 11.7703 0.890476 12.1094 1.14052 12.3595C1.39057 12.6095 1.72971 12.75 2.08333 12.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		);

		echo isset( $marks[ $which ] ) ? $marks[ $which ] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a fixed mark the widget carries.
	}
}
