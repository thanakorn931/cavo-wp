<?php
/**
 * The Subscribers tab: who is on the list, what has gone out, and what the
 * list does when something is published.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Admin;

use Custom_Elementor_Widgets\Form\Settings;
use Custom_Elementor_Widgets\Form\Subscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The tab and its three sub-tabs.
 */
class Subscribers_Screen {

	/**
	 * The sub-tabs, in the order they stand.
	 *
	 * @return array
	 */
	public static function tabs() {
		return array(
			'list'       => esc_html__( 'List', 'custom-elementor-widgets' ),
			'broadcasts' => esc_html__( 'Broadcasts', 'custom-elementor-widgets' ),
			'settings'   => esc_html__( 'Settings', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * Which sub-tab is open.
	 *
	 * @return string
	 */
	public static function current() {
		$asked = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( $_GET['view'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- which sub-tab to draw.

		return isset( self::tabs()[ $asked ] ) ? $asked : 'list';
	}

	/**
	 * Where this screen lives.
	 *
	 * @param string $view Which sub-tab, or '' for the one standing first.
	 * @return string
	 */
	public static function url( $view = '' ) {
		$url = admin_url( Menu::parent() . '&page=cavo-subscribers' );

		return '' === $view ? $url : add_query_arg( 'view', $view, $url );
	}

	/**
	 * Draw the tab.
	 */
	public static function render() {
		$notice = isset( $_GET['done'] ) ? sanitize_key( wp_unslash( $_GET['done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- printing what just happened.

		if ( '' !== $notice ) {
			self::notice( $notice );
		}

		$here = self::current();
		?>
		<ul class="subsubsub" style="margin-bottom:1em;">
			<?php
			$last = array_key_last( self::tabs() );

			foreach ( self::tabs() as $slug => $label ) :
				?>
				<li>
					<a href="<?php echo esc_url( self::url( $slug ) ); ?>"<?php echo $slug === $here ? ' class="current"' : ''; ?>>
						<?php echo esc_html( $label ); ?>
					</a>
					<?php echo $slug === $last ? '' : ' |'; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<div style="clear:both;"></div>
		<?php
		if ( 'settings' === $here ) {
			self::render_settings();
			return;
		}

		if ( 'broadcasts' === $here ) {
			self::render_broadcasts();
			return;
		}

		self::render_list();
	}

	/**
	 * A row action changes something on a GET, so it redirects after itself.
	 *
	 * This runs on `admin_init` rather than while the screen is drawn: a
	 * redirect decided halfway down a page has nowhere to go.
	 */
	public static function act() {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- which screen was asked for.

		if ( 'cavo-subscribers' !== $page || ! current_user_can( Menu::CAP ) ) {
			return;
		}

		self::act_on_one();
		self::act_on_many();
		self::act_on_settings();
		self::act_on_added();
	}

	/**
	 * One row's action.
	 */
	private static function act_on_one() {
		$what = isset( $_GET['subscriber_action'] ) ? sanitize_key( wp_unslash( $_GET['subscriber_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the nonce is checked below.
		$id   = isset( $_GET['subscriber'] ) ? absint( wp_unslash( $_GET['subscriber'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the nonce is checked below.

		if ( '' === $what || 0 === $id ) {
			return;
		}

		check_admin_referer( 'cavo_subscriber_' . $id );

		if ( 'remove' === $what ) {
			wp_delete_post( $id, true );
		} elseif ( 'unsubscribe' === $what ) {
			Subscribers::set_status( $id, 'unsubscribed' );
		}

		wp_safe_redirect( add_query_arg( 'done', $what, self::url( 'list' ) ) );
		exit;
	}

	/**
	 * A selection's action.
	 */
	private static function act_on_many() {
		$table = new Subscribers_Table();
		$what  = $table->current_action();

		if ( ! $what || ! in_array( $what, array( 'remove', 'unsubscribe' ), true ) ) {
			return;
		}

		check_admin_referer( 'bulk-subscribers' );

		$chosen = isset( $_REQUEST['subscriber'] ) ? array_map( 'absint', (array) wp_unslash( $_REQUEST['subscriber'] ) ) : array();

		foreach ( $chosen as $id ) {
			if ( 0 === $id ) {
				continue;
			}

			if ( 'remove' === $what ) {
				wp_delete_post( $id, true );
			} else {
				Subscribers::set_status( $id, 'unsubscribed' );
			}
		}

		wp_safe_redirect( add_query_arg( 'done', $what, self::url( 'list' ) ) );
		exit;
	}

	/**
	 * An address added by hand.
	 */
	private static function act_on_added() {
		if ( ! isset( $_POST['cavo_add_subscriber'] ) ) {
			return;
		}

		check_admin_referer( 'cavo_add_subscriber' );

		$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
		$added = Subscribers::add( $email, '', 'confirmed' );

		wp_safe_redirect( add_query_arg( 'done', is_wp_error( $added ) ? 'invalid' : 'added', self::url( 'list' ) ) );
		exit;
	}

	/**
	 * The settings form.
	 */
	private static function act_on_settings() {
		if ( ! isset( $_POST['cavo_subscription_settings'] ) ) {
			return;
		}

		check_admin_referer( 'cavo_subscription_settings' );

		Settings::save(
			array(
				'sending'    => isset( $_POST['sending'] ),
				'confirms'   => isset( $_POST['confirms'] ),
				'post_types' => isset( $_POST['post_types'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['post_types'] ) ) : array(),
				'from_name'  => isset( $_POST['from_name'] ) ? sanitize_text_field( wp_unslash( $_POST['from_name'] ) ) : '',
				'subject'    => isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '',
			)
		);

		wp_safe_redirect( add_query_arg( 'done', 'saved', self::url( 'settings' ) ) );
		exit;
	}

	/**
	 * Say what just happened.
	 *
	 * @param string $what Which thing.
	 */
	private static function notice( $what ) {
		$words = array(
			'remove'      => esc_html__( 'Removed from the list.', 'custom-elementor-widgets' ),
			'unsubscribe' => esc_html__( 'Marked unsubscribed.', 'custom-elementor-widgets' ),
			'added'       => esc_html__( 'Added to the list.', 'custom-elementor-widgets' ),
			'invalid'     => esc_html__( 'That address does not look right.', 'custom-elementor-widgets' ),
			'saved'       => esc_html__( 'Saved.', 'custom-elementor-widgets' ),
		);

		if ( ! isset( $words[ $what ] ) ) {
			return;
		}

		printf(
			'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
			'invalid' === $what ? 'error' : 'success',
			esc_html( $words[ $what ] )
		);
	}

	/**
	 * Who is on the list.
	 */
	private static function render_list() {
		$table = new Subscribers_Table();
		$table->prepare_items();
		?>
		<form method="post" style="margin:1em 0;padding:1em;background:#fff;border:1px solid #c3c4c7;">
			<?php wp_nonce_field( 'cavo_add_subscriber' ); ?>
			<input type="hidden" name="cavo_add_subscriber" value="1" />
			<label for="cavo-add-email"><?php esc_html_e( 'Add an address', 'custom-elementor-widgets' ); ?></label>
			<input type="email" id="cavo-add-email" name="email" class="regular-text" required />
			<?php submit_button( esc_html__( 'Add', 'custom-elementor-widgets' ), 'secondary', 'submit', false ); ?>
		</form>

		<form method="get">
			<input type="hidden" name="post_type" value="<?php echo esc_attr( \Custom_Elementor_Widgets\Form\Messages::POST_TYPE ); ?>" />
			<input type="hidden" name="page" value="cavo-subscribers" />
			<?php
			$table->views();
			$table->search_box( esc_html__( 'Search addresses', 'custom-elementor-widgets' ), 'subscribers' );
			?>
		</form>

		<form method="post">
			<?php
			wp_nonce_field( 'bulk-subscribers' );
			$table->display();
			?>
		</form>
		<?php
	}

	/**
	 * What has gone out.
	 */
	private static function render_broadcasts() {
		?>
		<p><?php esc_html_e( 'Nothing has gone out yet. Sending on publish is not wired up.', 'custom-elementor-widgets' ); ?></p>
		<?php
	}

	/**
	 * What the list does.
	 */
	private static function render_settings() {
		$settings = Settings::all();
		$types    = get_post_types( array( 'public' => true ), 'objects' );
		?>
		<form method="post">
			<?php wp_nonce_field( 'cavo_subscription_settings' ); ?>
			<input type="hidden" name="cavo_subscription_settings" value="1" />

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Sending', 'custom-elementor-widgets' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="sending" value="1" <?php checked( ! empty( $settings['sending'] ) ); ?> />
							<?php esc_html_e( 'Email the list when something is published', 'custom-elementor-widgets' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Signing up', 'custom-elementor-widgets' ); ?></th>
					<td>
						<label>
							<input type="checkbox" name="confirms" value="1" <?php checked( ! empty( $settings['confirms'] ) ); ?> />
							<?php esc_html_e( 'Ask the address to confirm itself before it counts', 'custom-elementor-widgets' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Off, anybody can put anybody else on the list.', 'custom-elementor-widgets' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'What counts as news', 'custom-elementor-widgets' ); ?></th>
					<td>
						<?php foreach ( $types as $type ) : ?>
							<label style="display:block;">
								<input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $type->name ); ?>"
									<?php checked( in_array( $type->name, (array) $settings['post_types'], true ) ); ?> />
								<?php echo esc_html( $type->labels->name ); ?>
							</label>
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cavo-from-name"><?php esc_html_e( 'From', 'custom-elementor-widgets' ); ?></label></th>
					<td>
						<input type="text" id="cavo-from-name" name="from_name" class="regular-text"
							value="<?php echo esc_attr( $settings['from_name'] ); ?>"
							placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
						<p class="description"><?php esc_html_e( 'The name only. The address is the domain’s.', 'custom-elementor-widgets' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cavo-subject"><?php esc_html_e( 'Subject', 'custom-elementor-widgets' ); ?></label></th>
					<td>
						<input type="text" id="cavo-subject" name="subject" class="regular-text"
							value="<?php echo esc_attr( $settings['subject'] ); ?>" />
						<p class="description"><?php esc_html_e( '{title} stands for what was published.', 'custom-elementor-widgets' ); ?></p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
		<?php
	}
}
