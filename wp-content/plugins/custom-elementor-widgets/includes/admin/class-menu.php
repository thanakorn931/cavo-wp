<?php
/**
 * One menu, called WP Form.
 *
 * Tabs across the top of whichever screen is open, never a list down the
 * sidebar: somebody who has just changed what a form asks is one click from
 * changing who hears about it. The sidebar carries the menu's name and nothing
 * under it.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Admin;

use Custom_Elementor_Widgets\Form\Messages;
use Custom_Elementor_Widgets\Form\Subscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The menu, its tabs, and where each one goes.
 */
class Menu {

	/**
	 * The capability every screen here asks for.
	 */
	const CAP = 'edit_pages';

	/**
	 * The screens hanging under the inbox, in the order their tabs stand.
	 *
	 * The inbox itself is the post type's own list and so is not one of these;
	 * it is drawn into the row by `tabs()`.
	 *
	 * @return array
	 */
	public static function pages() {
		return array(
			'cavo-subscribers'  => esc_html__( 'Subscribers', 'custom-elementor-widgets' ),
			'cavo-form-editor'  => esc_html__( 'Form editor', 'custom-elementor-widgets' ),
			'cavo-form-setting' => esc_html__( 'Settings', 'custom-elementor-widgets' ),
			'cavo-recaptcha'    => esc_html__( 'reCAPTCHA', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * The parent every screen is registered under and asked for under.
	 *
	 * @return string
	 */
	public static function parent() {
		return 'edit.php?post_type=' . Messages::POST_TYPE;
	}

	/**
	 * Hook the menu up.
	 */
	public static function listen() {
		add_action( 'admin_init', array( Subscribers_Screen::class, 'act' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register' ) );
		add_action( 'admin_head', array( __CLASS__, 'trim_sidebar' ) );
		add_action( 'all_admin_notices', array( __CLASS__, 'tabs_above_inbox' ) );
	}

	/**
	 * Register each screen under the inbox.
	 */
	public static function register() {
		foreach ( self::pages() as $slug => $label ) {
			add_submenu_page(
				self::parent(),
				$label,
				$label,
				self::CAP,
				$slug,
				array( __CLASS__, 'render' )
			);
		}
	}

	/**
	 * Take them back out of the sidebar, once it has been built.
	 *
	 * The sidebar list is what WordPress reads to work out a screen's parent,
	 * so this happens as the sidebar is drawn and never while the menu is being
	 * built.
	 */
	public static function trim_sidebar() {
		global $submenu;

		$parent = self::parent();

		if ( ! isset( $submenu[ $parent ] ) ) {
			return;
		}

		foreach ( $submenu[ $parent ] as $index => $item ) {
			if ( isset( $item[2] ) && isset( self::pages()[ $item[2] ] ) ) {
				unset( $submenu[ $parent ][ $index ] );
			}
		}
	}

	/**
	 * Which tab is open, by its whole name.
	 *
	 * A tab is matched by its whole name, never by what it starts with: one
	 * form's name is sooner or later the beginning of another's.
	 *
	 * @return string
	 */
	public static function current() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- which screen to draw.

		return isset( self::pages()[ $page ] ) ? $page : '';
	}

	/**
	 * Print the tab row.
	 */
	public static function tabs() {
		$here  = self::current();
		$count = Subscribers::count( 'confirmed' );
		?>
		<h2 class="nav-tab-wrapper wp-clearfix">
			<a class="nav-tab<?php echo '' === $here ? ' nav-tab-active' : ''; ?>"
				href="<?php echo esc_url( admin_url( self::parent() ) ); ?>">
				<?php echo esc_html__( 'Inbox', 'custom-elementor-widgets' ); ?>
			</a>
			<?php foreach ( self::pages() as $slug => $label ) : ?>
				<a class="nav-tab<?php echo $slug === $here ? ' nav-tab-active' : ''; ?>"
					href="<?php echo esc_url( admin_url( self::parent() . '&page=' . $slug ) ); ?>">
					<?php echo esc_html( $label ); ?>
					<?php if ( 'cavo-subscribers' === $slug && $count > 0 ) : ?>
						<span class="awaiting-mod"><span class="pending-count"><?php echo esc_html( number_format_i18n( $count ) ); ?></span></span>
					<?php endif; ?>
				</a>
			<?php endforeach; ?>
		</h2>
		<?php
	}

	/**
	 * The inbox is WordPress's own list screen, so its tabs are printed into it.
	 */
	public static function tabs_above_inbox() {
		$screen = get_current_screen();

		if ( ! $screen || 'edit-' . Messages::POST_TYPE !== $screen->id ) {
			return;
		}

		self::tabs();
	}

	/**
	 * Draw whichever screen was asked for.
	 */
	public static function render() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You cannot see this screen.', 'custom-elementor-widgets' ) );
		}

		$here  = self::current();
		$pages = self::pages();
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'WP Form', 'custom-elementor-widgets' ); ?></h1>
			<?php self::tabs(); ?>
			<?php
			if ( 'cavo-subscribers' === $here ) {
				Subscribers_Screen::render();
			} else {
				printf(
					'<p style="margin-top:1.5em;">%s</p>',
					esc_html(
						sprintf(
							/* translators: %s: the tab's name. */
							__( '%s is not built yet.', 'custom-elementor-widgets' ),
							isset( $pages[ $here ] ) ? $pages[ $here ] : ''
						)
					)
				);
			}
			?>
		</div>
		<?php
	}
}
