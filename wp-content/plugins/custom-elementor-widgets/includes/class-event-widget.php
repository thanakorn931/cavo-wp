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
	 * The three facts the card states.
	 *
	 * Typed, a fact is the section's and every card states the same thing.
	 * Pointed at a field, it is the item's and each card states its own. Which
	 * of the two is the client's to decide, so nothing here names a field.
	 */
	protected function register_more_source_controls() {
		$this->add_control(
			'date',
			array(
				'label'          => esc_html__( 'Date', 'custom-elementor-widgets' ),
				'type'           => Controls_Manager::DATE_TIME,
				'dynamic'        => array( 'active' => true ),
				'picker_options' => array( 'enableTime' => false ),
			)
		);

		foreach ( array(
			'time'  => esc_html__( 'Time', 'custom-elementor-widgets' ),
			'genre' => esc_html__( 'Genre', 'custom-elementor-widgets' ),
		) as $key => $label ) {
			$this->add_control(
				$key,
				array(
					'label'   => $label,
					'type'    => Controls_Manager::TEXT,
					'dynamic' => array( 'active' => true ),
				)
			);
		}
	}

	/**
	 * The items the section shows.
	 *
	 * Everything the source holds is fetched; how much of it is on screen at
	 * once is the section's own.
	 *
	 * @param array $settings The widget's settings.
	 * @return array
	 */
	protected function items( $settings ) {
		$posts = get_posts(
			$this->source_query(
				$settings,
				array(
					'posts_per_page'      => 100,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			)
		);

		$standing = array();

		foreach ( $posts as $place => $post ) {
			$standing[] = array(
				'when'  => $this->item_starts( $post ),
				'place' => $place,
				'post'  => $post,
			);
		}

		$way = isset( $settings['order'] ) && 'ASC' === $settings['order'] ? 1 : -1;

		usort(
			$standing,
			function ( $a, $b ) use ( $way ) {
				// Two on the same day keep the order the source gave them,
				// whichever way round the list is being read.
				if ( $a['when'] === $b['when'] ) {
					return $a['place'] - $b['place'];
				}

				return $a['when'] < $b['when'] ? -$way : $way;
			}
		);

		return wp_list_pluck( $standing, 'post' );
	}

	/**
	 * When an item is, as a moment.
	 *
	 * The day the client set for it. A day they have not set is the day the post
	 * appeared, so an item is never left without a place in the order.
	 *
	 * @param \WP_Post $post The item.
	 * @return int
	 */
	private function item_starts( $post ) {
		$item = $this->item_settings( $post );
		$date = isset( $item['date'] ) ? trim( (string) $item['date'] ) : '';
		$when = '' !== $date ? strtotime( $date ) : false;

		return (int) ( $when ? $when : strtotime( $post->post_date ) );
	}

	/**
	 * One item's date, hour and kind, as the design draws them.
	 *
	 * The day and the hour are the event's own fields, not when the post was
	 * published; the design draws the hour it begins. Each fact is a mark and a
	 * word; the rule between them is the section's, not part of what the client
	 * typed.
	 *
	 * @param \WP_Post $post The item.
	 */
	protected function render_meta( $post ) {
		$item = $this->item_settings( $post );

		$facts = array(
			'calendar' => $this->as_day( isset( $item['date'] ) ? $item['date'] : '' ),
			'timer'    => isset( $item['time'] ) ? $item['time'] : '',
			'note'     => isset( $item['genre'] ) ? $item['genre'] : '',
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
