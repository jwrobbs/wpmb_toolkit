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
			'dashicons-hammer',
			65
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public static function render_page() {
		$tools_table = new ToolsListTable();
		$tools_table->prepare_items();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Manage Tools', 'wpmb-toolkit' ); ?></h1>
			<form method="post">
				<?php
				$tools_table->display();
				?>
			</form>
		</div>
		<?php
	}
}
