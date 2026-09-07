<?php
/**
 * What the blog sections share.
 *
 * The card the design draws for a post is the same card wherever posts are
 * listed, so it is written once here rather than in each section that lists
 * them. It sits outside the widgets folder, which is the register: only a
 * section of the design belongs in there.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The base the blog sections extend.
 */
abstract class Blog_Widget extends Base_Widget {

	/**
	 * The date, as the design writes it.
	 */
	const DATE_FORMAT = 'j F Y';

	/**
	 * Whether this section lists posts as cards.
	 *
	 * @return bool
	 */
	protected function draws_cards() {
		return true;
	}

	/**
	 * The card's stylesheet, declared beside the section's own — and only where
	 * a card is drawn.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		$handle = Widgets_Loader::HANDLE_PREFIX . 'blog-widget';

		return array_merge(
			parent::get_style_depends(),
			$this->draws_cards() && wp_style_is( $handle, 'registered' ) ? array( $handle ) : array()
		);
	}

	/**
	 * Where a card leads. Called from inside the loop.
	 *
	 * A section that pages its list adds the page to it, so the post can send
	 * the reader back to where they were rather than to the start.
	 *
	 * @return string
	 */
	protected function card_url() {
		return (string) get_permalink();
	}

	/**
	 * One post, as a card. Called from inside the loop.
	 */
	protected function render_card() {
		?>
		<a class="custom-blog-card" href="<?php echo esc_url( $this->card_url() ); ?>">
			<span class="custom-blog-card__picture">
				<?php $this->media( get_the_post_thumbnail_url( null, 'large' ), '', true ); ?>
			</span>

			<span class="custom-blog-card__body">
				<span class="custom-blog-card__date">
					<?php $this->render_mark( 'calendar' ); ?>
					<span><?php echo esc_html( get_the_date( self::DATE_FORMAT ) ); ?></span>
				</span>

				<span class="custom-blog-card__text">
					<span class="custom-blog-card__title"><?php echo esc_html( get_the_title() ); ?></span>
					<span class="custom-blog-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></span>
				</span>
			</span>
		</a>
		<?php
	}

	/**
	 * One of the marks the design draws, which the build carries itself.
	 *
	 * @param string $which Which mark.
	 */
	protected function render_mark( $which ) {
		$marks = array(
			'calendar'  => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M10 2.66667V1.33333M10 2.66667V4M10 2.66667H7M2 6.66667V12.6667C2 13.0203 2.14048 13.3594 2.39052 13.6095C2.64057 13.8595 2.97971 14 3.33333 14H12.6667C13.0203 14 13.3594 13.8595 13.6095 13.6095C13.8595 13.3594 14 13.0203 14 12.6667V6.66667M2 6.66667H14M2 6.66667V4C2 3.64638 2.14048 3.30724 2.39052 3.05719C2.64057 2.80714 2.97971 2.66667 3.33333 2.66667H4.66667M14 6.66667V4C14 3.64638 13.8595 3.30724 13.6095 3.05719C13.3594 2.80714 13.0203 2.66667 12.6667 2.66667H12.3333M4.66667 1.33333V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'back'      => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M15 6L9 12L15 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
			'instagram' => '<svg width="24" height="24" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false" aria-hidden="true"><path d="M11 1.5H5C4.07205 1.50099 3.18238 1.87006 2.52622 2.52622C1.87006 3.18238 1.50099 4.07205 1.5 5V11C1.50099 11.928 1.87006 12.8176 2.52622 13.4738C3.18238 14.1299 4.07205 14.499 5 14.5H11C11.928 14.499 12.8176 14.1299 13.4738 13.4738C14.1299 12.8176 14.499 11.928 14.5 11V5C14.499 4.07205 14.1299 3.18238 13.4738 2.52622C12.8176 1.87006 11.928 1.50099 11 1.5ZM8 11C7.40666 11 6.82664 10.8241 6.33329 10.4944C5.83994 10.1648 5.45542 9.69623 5.22836 9.14805C5.0013 8.59987 4.94189 7.99667 5.05764 7.41473C5.1734 6.83279 5.45912 6.29824 5.87868 5.87868C6.29824 5.45912 6.83279 5.1734 7.41473 5.05764C7.99667 4.94189 8.59987 5.0013 9.14805 5.22836C9.69623 5.45542 10.1648 5.83994 10.4944 6.33329C10.8241 6.82664 11 7.40666 11 8C10.9992 8.7954 10.6828 9.55798 10.1204 10.1204C9.55798 10.6828 8.7954 10.9992 8 11ZM11.75 5C11.6017 5 11.4567 4.95601 11.3333 4.8736C11.21 4.79119 11.1139 4.67406 11.0571 4.53701C11.0003 4.39997 10.9855 4.24917 11.0144 4.10368C11.0433 3.9582 11.1148 3.82456 11.2197 3.71967C11.3246 3.61478 11.4582 3.54335 11.6037 3.51441C11.7492 3.48547 11.9 3.50032 12.037 3.55709C12.1741 3.61386 12.2912 3.70999 12.3736 3.83332C12.456 3.95666 12.5 4.10166 12.5 4.25C12.5 4.44891 12.421 4.63968 12.2803 4.78033C12.1397 4.92098 11.9489 5 11.75 5ZM10 8C10 8.39556 9.8827 8.78224 9.66294 9.11114C9.44318 9.44004 9.13082 9.69638 8.76537 9.84776C8.39991 9.99913 7.99778 10.0387 7.60982 9.96157C7.22186 9.8844 6.86549 9.69392 6.58579 9.41421C6.30608 9.13451 6.1156 8.77814 6.03843 8.39018C5.96126 8.00222 6.00087 7.60009 6.15224 7.23463C6.30362 6.86918 6.55996 6.55682 6.88886 6.33706C7.21776 6.1173 7.60444 6 8 6C8.53043 6 9.03914 6.21071 9.41421 6.58579C9.78929 6.96086 10 7.46957 10 8Z"/></svg>',
		);

		echo isset( $marks[ $which ] ) ? $marks[ $which ] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- a fixed mark the widget carries.
	}
}
