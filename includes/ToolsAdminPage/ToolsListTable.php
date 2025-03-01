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
			'name' => array( 'name', false ),
		);
	}

	/**
	 * Fetch data for the table
	 */
	private function get_tools_data() {
		$tools = ToolsManager::get_manifest();

		$data = array();
		foreach ( $tools as $tool_id => $tool ) {
			$data[] = array(
				'ID'          => $tool_id,
				'name'        => $tool->name,
				'status'      => $tool->status ?? false,
				'is_built_in' => $tool->is_built_in ?? false, // Ensure it exists
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

		$data = $this->get_tools_data();

		// Sorting logic.
		usort(
			$data,
			function ( $a, $b ) {
				return strcmp( $a['name'], $b['name'] );
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
}
