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
			'status' => array( 'status', false ),
		);
	}

	/**
	 * Fetch data for the table
	 */
	protected function get_tools_data() {
		$tools = ToolsManager::get_manifest();

		$selected_origin = isset( $_GET['origin_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['origin_filter'] ) ) : ''; // phpcs:ignore
		$selected_status = isset( $_GET['status_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['status_filter'] ) ) : ''; // phpcs:ignore

		$data = array();
		foreach ( $tools as $tool_id => $tool ) {
			$is_built_in = ! empty( $tool->is_built_in );
			$status      = $tool->status ?? 'Inactive'; // Default status to "Inactive" if not set.

			// Apply Origin filter.
			if ( 'built-in' === $selected_origin && ! $is_built_in ) {
				continue;
			}
			if ( 'user' === $selected_origin && $is_built_in ) {
				continue;
			}

			// Apply Status filter.
			if ( 'Active' === $selected_status && 'Active' !== $status ) {
				continue;
			}
			if ( 'Inactive' === $selected_status && 'Inactive' !== $status ) {
				continue;
			}

			$data[] = array(
				'ID'          => $tool_id,
				'name'        => $tool->name,
				'status'      => $status,
				'is_built_in' => $is_built_in,
				'link'        => $tool->link,
				'version'     => $tool->version,
				'description' => $tool->description,
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

		$data = $this->get_tools_data(); // Fetch filtered data.

		// Sorting logic.
		// @codingStandardsIgnoreStart
		$orderby = $_GET['orderby'] ?? 'name';
		$order   = $_GET['order'] ?? 'asc';
		// @codingStandardsIgnoreEnd

		$orderby = sanitize_text_field( wp_unslash( $orderby ) );
		$order   = sanitize_text_field( wp_unslash( $order ) );

		usort(
			$data,
			function ( $a, $b ) use ( $orderby, $order ) {
				if ( 'status' === $orderby ) {
					return ( 'asc' === $order )
						? strcmp( (string) $a['status'], (string) $b['status'] )
						: strcmp( (string) $b['status'], (string) $a['status'] );
				}

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
				$name = esc_html( $item['name'] );

				if ( ! empty( $item['description'] ) ) {
					$name .= '     <small>' . esc_html( $item['description'] ) . '</small>';
				}

				// Add version if it exists.
				$version = ! empty( $item['version'] ) ? '<br><small>' . sprintf( __( 'Version: %s', 'wpmb-toolkit' ), esc_html( $item['version'] ) ) . '</small>' : ''; // phpcs:ignore

				$actions = $this->get_row_actions( $item );

				return sprintf( '%s %s %s', $name, $version, $actions );
			case 'status':
				return esc_html( $item['status'] );
			case 'origin':
				return ! empty( $item['is_built_in'] ) ? __( 'Built-in', 'wpmb-toolkit' ) : __( 'User', 'wpmb-toolkit' );
			default:
				return '';
		}
	}

	/**
	 * Define row actions.
	 *
	 * @param array $item The item data.
	 * @return string
	 */
	protected function get_row_actions( $item ) {
		$actions = array();

		// More Info link if `link` property exists.
		if ( ! empty( $item['link'] ) ) {
			$actions['more_info'] = sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $item['link'] ),
				__( 'More Info', 'wpmb-toolkit' )
			);
		}

		// Skip other actions if the tool is built-in.
		if ( ! empty( $item['is_built_in'] ) ) {
			return $this->row_actions( $actions );
		}

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

		if ( 'Inactive' === $item['status'] ) {
			$actions['activate'] = sprintf( '<a href="%s">%s</a>', esc_url( $activate_url ), __( 'Activate', 'wpmb-toolkit' ) );
		} else {
			$actions['deactivate'] = sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $deactivate_url ), __( 'Deactivate', 'wpmb-toolkit' ) );
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

		$selected_origin = esc_attr( wp_unslash( $_GET['origin_filter'] ?? '' ) ); // phpcs:ignore
		$selected_status = esc_attr( wp_unslash( $_GET['status_filter'] ?? '' ) ); // phpcs:ignore

		echo '<form method="GET" action="">';
		echo '<input type="hidden" name="page" value="' . esc_attr( wp_unslash( $_GET['page'] ?? '' ) ) . '">'; // phpcs:ignore

		// Origin filter dropdown.
		echo '<label for="filter-by-origin" class="screen-reader-text">' . esc_html__( 'Filter by Origin', 'wpmb-toolkit' ) . '</label>';
		echo '<select name="origin_filter" id="filter-by-origin">';
		echo '<option value="" ' . selected( $selected_origin, '', false ) . '>' . esc_html__( 'All Origins', 'wpmb-toolkit' ) . '</option>';
		echo '<option value="built-in" ' . selected( $selected_origin, 'built-in', false ) . '>' . esc_html__( 'Built-in', 'wpmb-toolkit' ) . '</option>';
		echo '<option value="user" ' . selected( $selected_origin, 'user', false ) . '>' . esc_html__( 'User', 'wpmb-toolkit' ) . '</option>';
		echo '</select>';

		// Status filter dropdown.
		echo '<label for="filter-by-status" class="screen-reader-text">' . esc_html__( 'Filter by Status', 'wpmb-toolkit' ) . '</label>';
		echo '<select name="status_filter" id="filter-by-status">';
		echo '<option value="" ' . selected( $selected_status, '', false ) . '>' . esc_html__( 'All Statuses', 'wpmb-toolkit' ) . '</option>';
		echo '<option value="Active" ' . selected( $selected_status, 'Active', false ) . '>' . esc_html__( 'Active', 'wpmb-toolkit' ) . '</option>';
		echo '<option value="Inactive" ' . selected( $selected_status, 'Inactive', false ) . '>' . esc_html__( 'Inactive', 'wpmb-toolkit' ) . '</option>';
		echo '</select>';

		submit_button( __( 'Filter', 'wpmb-toolkit' ), '', 'filter_action', false );

		// Reset button.
		$reset_url = admin_url( 'admin.php?page=' . esc_attr( wp_unslash( $_GET['page'] ?? 'wpmb-tools' ) ) ); // phpcs:ignore
		echo '<a href="' . esc_url( $reset_url ) . '" class="button">' . esc_html__( 'Reset', 'wpmb-toolkit' ) . '</a>';

		echo '</form>';

		// JavaScript to update URL parameters on dropdown change.
		echo '<script>
    document.getElementById("filter-by-origin").addEventListener("change", function() {
        let url = new URL(window.location.href);
        url.searchParams.set("origin_filter", this.value);
        window.location.href = url.toString();
    });

    document.getElementById("filter-by-status").addEventListener("change", function() {
        let url = new URL(window.location.href);
        url.searchParams.set("status_filter", this.value);
        window.location.href = url.toString();
    });
    </script>';
	}
}
