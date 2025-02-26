<?php
/**
 * ToolsAdminPage class.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\ToolsAdminPage;

defined( 'ABSPATH' ) || exit;

/**
 * ToolsAdminPage class.
 */
class ToolsAdminPage {

	/**
	 * Initialize the admin page.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_menu_page(
			__( 'WPMB Tools', 'wpmb' ),
			__( 'WPMB Tools', 'wpmb' ),
			'manage_options',
			'wpmb-tools',
			array( __CLASS__, 'render_page' ),
			'dashicons-admin-generic',
			65
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public static function render_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'WPMB Tools', 'wpmb' ) . '</h1>';
		echo '<form method="post">';
		$tools_table = new Tools_List_Table();
		$tools_table->prepare_items();
		$tools_table->display();
		echo '</form>';
		echo '</div>';
	}
}
