<?php
/**
 * The list of addresses, as a screen draws it.
 *
 * @package Custom_Elementor_Widgets
 */

namespace Custom_Elementor_Widgets\Admin;

use Custom_Elementor_Widgets\Form\Forms;
use Custom_Elementor_Widgets\Form\Subscribers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Who is on the list.
 */
class Subscribers_Table extends \WP_List_Table {

	/**
	 * How many rows to a page.
	 */
	const PER_PAGE = 50;

	/**
	 * Name the thing being listed.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'subscriber',
				'plural'   => 'subscribers',
				'ajax'     => false,
			)
		);
	}

	/**
	 * The columns.
	 *
	 * The column naming where an address came from appears once a second form
	 * can put one there, and not before: until then every row would say the
	 * same word.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'     => '<input type="checkbox" />',
			'email'  => esc_html__( 'Address', 'custom-elementor-widgets' ),
			'status' => esc_html__( 'Standing', 'custom-elementor-widgets' ),
		);

		if ( self::sources_differ() ) {
			$columns['source'] = esc_html__( 'Form', 'custom-elementor-widgets' );
		}

		$columns['date'] = esc_html__( 'Signed up', 'custom-elementor-widgets' );

		return $columns;
	}

	/**
	 * Whether more than one form has actually put somebody on the list.
	 *
	 * @return bool
	 */
	private static function sources_differ() {
		$subscribing = 0;

		foreach ( array_keys( Forms::all() ) as $slug ) {
			if ( Forms::subscribes( $slug ) ) {
				++$subscribing;
			}
		}

		return $subscribing > 1;
	}

	/**
	 * Which columns can be sorted on.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'email' => array( 'title', false ),
			'date'  => array( 'date', true ),
		);
	}

	/**
	 * What can be done to a selection.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'unsubscribe' => esc_html__( 'Mark unsubscribed', 'custom-elementor-widgets' ),
			'remove'      => esc_html__( 'Remove from the list', 'custom-elementor-widgets' ),
		);
	}

	/**
	 * Which standing is being looked at.
	 *
	 * @return string
	 */
	private function filter() {
		$asked = isset( $_GET['standing'] ) ? sanitize_key( wp_unslash( $_GET['standing'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- narrowing a list.
		$states = Subscribers::states();

		return isset( $states[ $asked ] ) ? $asked : '';
	}

	/**
	 * The counts across the top.
	 *
	 * @return array
	 */
	protected function get_views() {
		$here  = $this->filter();
		$base  = admin_url( Menu::parent() . '&page=cavo-subscribers' );
		$views = array(
			'' => sprintf(
				'<a href="%s"%s>%s <span class="count">(%s)</span></a>',
				esc_url( $base ),
				'' === $here ? ' class="current"' : '',
				esc_html__( 'All', 'custom-elementor-widgets' ),
				esc_html( number_format_i18n( Subscribers::count() ) )
			),
		);

		foreach ( Subscribers::states() as $slug => $label ) {
			$views[ $slug ] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%s)</span></a>',
				esc_url( add_query_arg( 'standing', $slug, $base ) ),
				$slug === $here ? ' class="current"' : '',
				esc_html( $label ),
				esc_html( number_format_i18n( Subscribers::count( $slug ) ) )
			);
		}

		return $views;
	}

	/**
	 * Fetch the page being looked at.
	 */
	public function prepare_items() {
		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

		$paged   = $this->get_pagenum();
		$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ordering a list.
		$order   = isset( $_GET['order'] ) && 'asc' === strtolower( sanitize_key( wp_unslash( $_GET['order'] ) ) ) ? 'ASC' : 'DESC'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ordering a list.
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- searching a list.

		$query = array(
			'post_type'      => Subscribers::POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => self::PER_PAGE,
			'paged'          => $paged,
			'orderby'        => in_array( $orderby, array( 'title', 'date' ), true ) ? $orderby : 'date',
			'order'          => $order,
		);

		if ( '' !== $search ) {
			$query['s'] = $search;
		}

		$standing = $this->filter();

		if ( '' !== $standing ) {
			$query['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- narrowed by standing.
				array(
					'key'   => Subscribers::STATUS,
					'value' => $standing,
				),
			);
		}

		$found = new \WP_Query( $query );

		$this->items = $found->posts;

		$this->set_pagination_args(
			array(
				'total_items' => (int) $found->found_posts,
				'per_page'    => self::PER_PAGE,
				'total_pages' => (int) $found->max_num_pages,
			)
		);
	}

	/**
	 * The checkbox.
	 *
	 * @param \WP_Post $item One row.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="subscriber[]" value="%d" />', (int) $item->ID );
	}

	/**
	 * The address, and what can be done to it.
	 *
	 * @param \WP_Post $item One row.
	 * @return string
	 */
	public function column_email( $item ) {
		$base = admin_url( Menu::parent() . '&page=cavo-subscribers' );

		$actions = array();

		if ( 'unsubscribed' !== Subscribers::status( $item->ID ) ) {
			$actions['unsubscribe'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'subscriber_action' => 'unsubscribe',
								'subscriber'        => (int) $item->ID,
							),
							$base
						),
						'cavo_subscriber_' . $item->ID
					)
				),
				esc_html__( 'Mark unsubscribed', 'custom-elementor-widgets' )
			);
		}

		$actions['remove'] = sprintf(
			'<a href="%s" class="submitdelete">%s</a>',
			esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'subscriber_action' => 'remove',
							'subscriber'        => (int) $item->ID,
						),
						$base
					),
					'cavo_subscriber_' . $item->ID
				)
			),
			esc_html__( 'Remove', 'custom-elementor-widgets' )
		);

		return sprintf(
			'<strong>%s</strong>%s',
			esc_html( $item->post_title ),
			$this->row_actions( $actions )
		);
	}

	/**
	 * Where the address stands.
	 *
	 * @param \WP_Post $item One row.
	 * @return string
	 */
	public function column_status( $item ) {
		$states = Subscribers::states();
		$status = Subscribers::status( $item->ID );

		return esc_html( isset( $states[ $status ] ) ? $states[ $status ] : $status );
	}

	/**
	 * Which form it came through.
	 *
	 * @param \WP_Post $item One row.
	 * @return string
	 */
	public function column_source( $item ) {
		$slug = (string) get_post_meta( $item->ID, Subscribers::SOURCE, true );

		return esc_html( Forms::exists( $slug ) ? Forms::name( $slug ) : '—' );
	}

	/**
	 * When they asked.
	 *
	 * @param \WP_Post $item One row.
	 * @return string
	 */
	public function column_date( $item ) {
		return esc_html( get_the_date( 'j F Y', $item ) );
	}

	/**
	 * What an empty list says.
	 */
	public function no_items() {
		esc_html_e( 'Nobody has signed up yet.', 'custom-elementor-widgets' );
	}
}
