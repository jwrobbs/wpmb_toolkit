<?php
/**
 * Tool actions.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\ToolsAdminPage;

use WPMB_Toolkit\Includes\Tools\ToolsManager;

defined( 'ABSPATH' ) || exit;

/**
 * ToolActions class.
 */
class ToolActions {

	/**
	 * Route.
	 */
	public static function route() {
		if ( empty( $_GET['action'] ) // phpcs:ignore
			|| empty( $_GET['tool'] ) // phpcs:ignore 
			|| ! isset( $_GET['page'] ) // phpcs:ignore
		) {
			return;
		}

		$action  = sanitize_text_field( wp_unslash( $_GET['action'] ) ); // phpcs:ignore
		$tool_id = sanitize_text_field( wp_unslash( $_GET['tool'] ) ); // phpcs:ignore

		// Ensure this is only for your tools page.
		if ( 'wpmb-tools' !== $_GET['page'] ) { // phpcs:ignore
			return;
		}

		switch ( $action ) {
			case 'activate':
				self::activate_tool( $tool_id );
				break;
			case 'deactivate':
				self::deactivate_tool( $tool_id );
				break;
			case 'delete':
				self::delete_tool( $tool_id );
				break;
		}
	}

	/**
	 * Activate a tool.
	 *
	 * @param string $tool_id Tool ID.
	 */
	public static function activate_tool( $tool_id ) {
		$message = ToolsManager::activate_tool( $tool_id );
		self::wrap_it_up( $message );
	}

	/**
	 * Deactivate a tool.
	 *
	 * @param string $tool_id Tool ID.
	 */
	public static function deactivate_tool( $tool_id ) {
		$message = ToolsManager::deactivate_tool( $tool_id );
		self::wrap_it_up( $message );
	}

	/**
	 * Delete a tool.
	 *
	 * @param string $tool_id Tool ID.
	 */
	public static function delete_tool( $tool_id ) {

		$message = ToolsManager::delete_tool( $tool_id );
		self::wrap_it_up( $message );
	}

	/**
	 * Wrap it up.
	 *
	 * @param ?string $message Optional message.
	 */
	public static function wrap_it_up( $message = null ) {

		if ( ! $message ) {
			$message = __( 'Action completed.', 'wpmb-toolkit' );
		}

		$redirect_url = add_query_arg( 'wpmb_message', rawurlencode( $message ), remove_query_arg( array( 'action', 'tool' ) ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
}
