<?php
/**
 * ToolsListTable class file.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\ToolsAdminPage;

use WPMB_Toolkit\Includes\Tools\ToolsManager;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * ToolsListTable class.
 */
class ToolsListTable extends \WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'tool',
				'plural'   => 'tools',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Define table columns
	 */
	public function get_columns() {
		return array(
			'cb'     => '<input type="checkbox" />',
			'name'   => __( 'Tool Name', 'wpmb-toolkit' ),
			'status' => __( 'Status', 'wpmb-toolkit' ),
			'origin' => __( 'Origin', 'wpmb-toolkit' ),
		);
	}

	/**
	 * Define sortable columns
	 */
	protected function get_sortable_columns() {
		return array(
			'name'   => array( 'name', false ),
			'origin' => array( 'is_built_in', false ),
		);
	}


	/**
	 * Fetch data for the table
	 */
	private function get_tools_data() {
		$tools = ToolsManager::get_manifest();

		if ( isset( $_POST['origin_filter'] ) ) { // phpcs:ignore
			$selected_origin = sanitize_text_field( wp_unslash( $_POST['origin_filter'] ) ); // phpcs:ignore
		}

		$data = array();
		foreach ( $tools as $tool_id => $tool ) {
			$is_built_in = ! empty( $tool->is_built_in );

			// Apply filter correctly.
			if ( 'built-in' === $selected_origin && ! $is_built_in ) {
				continue;
			}
			if ( 'user' === $selected_origin && $is_built_in ) {
				continue;
			}

			$data[] = array(
				'ID'          => $tool_id,
				'name'        => $tool->name,
				'status'      => $tool->status ?? false,
				'is_built_in' => $is_built_in,
			);
		}

		return $data;
	}





	/**
	 * Prepare items for display
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$data = $this->get_tools_data(); // Now correctly filtered!

		// Sorting logic.
		// @codingStandardsIgnoreStart
		$orderby = sanitize_text_field( wp_unslash( $_POST['orderby'] ) ) ?? 'name';
		$order   = sanitize_text_field( wp_unslash( $_POST['order'] ) ) ?? 'asc';
		// @codingStandardsIgnoreEnd
		usort(
			$data,
			function ( $a, $b ) use ( $orderby, $order ) {
				$result = strcmp( (string) $a[ $orderby ], (string) $b[ $orderby ] );
				return ( 'asc' === $order ) ? $result : -$result;
			}
		);

		$this->items = $data;
	}



	/**
	 * Checkbox column for bulk actions
	 *
	 * @param array $item The item data.
	 * @return string
	 */
	protected function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="tool_ids[]" value="%s" />', $item['ID'] );
	}

	/**
	 * Default column rendering
	 *
	 * @param array  $item The item data.
	 * @param string $column_name The column name.
	 * @return string
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				$name    = esc_html( $item['name'] );
				$actions = $this->get_row_actions( $item );
				return sprintf( '%s %s', $name, $actions );
			case 'status':
				return esc_html( $item['status'] );
			case 'origin':
				return ! empty( $item['is_built_in'] ) ? __( 'Built-in', 'wpmb-toolkit' ) : __( 'User', 'wpmb-toolkit' );
			default:
				return '';
		}
	}

	/**
	 * Define row actions
	 *
	 * @param array $item The item data.
	 * @return string
	 */
	private function get_row_actions( $item ) {
		$activate_url   = add_query_arg(
			array(
				'action' => 'activate',
				'tool'   => $item['ID'],
			)
		);
		$deactivate_url = add_query_arg(
			array(
				'action' => 'deactivate',
				'tool'   => $item['ID'],
			)
		);
		$delete_url     = add_query_arg(
			array(
				'action' => 'delete',
				'tool'   => $item['ID'],
			)
		);

		$actions = array();
		if ( 'Inactive' === $item['status'] ) {
			$actions['activate'] = sprintf( '<a href="%s">%s</a>', esc_url( $activate_url ), __( 'Activate', 'wpmb-toolkit' ) );
		} else {
			$actions['deactivate'] = sprintf( '<a href="%s">%s</a>', esc_url( $deactivate_url ), __( 'Deactivate', 'wpmb-toolkit' ) );
		}
		$actions['delete'] = sprintf( '<a href="%s" style="color:red;">%s</a>', esc_url( $delete_url ), __( 'Delete', 'wpmb-toolkit' ) );

		return $this->row_actions( $actions );
	}

	/**
	 * Bulk actions
	 */
	protected function get_bulk_actions() {
		return array(
			'activate'   => __( 'Activate', 'wpmb-toolkit' ),
			'deactivate' => __( 'Deactivate', 'wpmb-toolkit' ),
			'delete'     => __( 'Delete', 'wpmb-toolkit' ),
		);
	}

	/**
	 * Process bulk actions
	 *
	 * @param string $which The action to perform.
	 */
	public function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		$selected = $_POST['origin_filter'] ?? ''; // phpcs:ignore

		echo '<form method="POST" action="">';
		echo '<input type="hidden" name="page" value="' . esc_attr( wp_unslash( $_POST['page'] ) ?? '' ) . '">'; // phpcs:ignore
		echo '<input type="hidden" name="origin_filter" value="' . esc_attr( $selected ) . '">';

		echo '<label for="filter-by-origin" class="screen-reader-text">' . esc_html__( 'Filter by Origin', 'wpmb-toolkit' ) . '</label>';
		echo '<select name="origin_filter" id="filter-by-origin">';
		echo '<option value="" ' . selected( $selected, '', false ) . '>' . esc_html__( 'All Origins', 'wpmb-toolkit' ) . '</option>';
		echo '<option value="built-in" ' . selected( $selected, 'built-in', false ) . '>' . esc_html__( 'Built-in', 'wpmb-toolkit' ) . '</option>';
		echo '<option value="user" ' . selected( $selected, 'user', false ) . '>' . esc_html__( 'User', 'wpmb-toolkit' ) . '</option>';
		echo '</select>';

		submit_button( __( 'Filter', 'wpmb-toolkit' ), '', 'filter_action', false );

		echo '</form>';
	}
}
