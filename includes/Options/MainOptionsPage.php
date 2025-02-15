<?php
/**
 * MainOptionsPage class file.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\Options;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

defined( 'ABSPATH' ) || exit;

/**
 * MainOptionsPage class.
 */
class MainOptionsPage {

	/**
	 * Register hooks.
	 */
	public static function register() {
		add_action(
			'after_setup_theme',
			function () {
				\Carbon_Fields\Carbon_Fields::boot();
			}
		);

		// add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'carbon_fields_register_fields', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Add page.
	 */
	public static function add_page() {
		$menu_title = get_option( 'wpmb_toolkit_title', 'WPMB Toolkit' );
		add_menu_page(
			$menu_title,                                // Page title.
			$menu_title,                                // Menu title.
			'manage_options',                           // Capability.
			'wpmb-toolkit',                             // Menu slug.
			array( __CLASS__, 'render_settings_page' ), // Callback (empty since Carbon Fields handles UI).
			'dashicons-admin-tools',                    // Icon.
			25                                          // Position in menu.
		);
	}

	/**
	 * Render settings page.
	 */
	public static function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( \get_admin_page_title() ); ?></h1>
			<?php do_action( 'carbon_fields_container_wpmb_toolkit' ); ?>
		</div>
		<?php
	}


	/**
	 * Register settings.
	 */
	public static function register_settings() {
		Container::make()
			->set_page_menu_position( 25 )
			->set_page_menu_title( 'WPMB Toolkit' )
			->set_page_parent_slug( 'wpmb-toolkit' )
			->set_page_file( 'wpmb-toolkit' )
			->add_fields(
				array(
					Field::make( 'text', 'wpmb_toolkit_title', 'Main Options Page Title' )
						->set_default_value( 'WPMB Toolkit' ),
				)
			);
		// Container::make( 'theme_options', 'WPMB Toolkit Settings' )
		// ->add_fields(
		// array(
		// Field::make( 'text', 'wpmb_toolkit_title', 'Main Options Page Title' )
		// ->set_default_value( 'WPMB Toolkit' ),
		// )
		// );
	}
}
