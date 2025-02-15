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

		add_action( 'carbon_fields_register_fields', array( __CLASS__, 'register_settings' ) );
	}


	/**
	 * Register settings.
	 */
	public static function register_settings() {
		Container::make( 'theme_options', __( 'Theme Settings', 'playground' ) )
		->set_page_menu_position( 4 )
		->set_icon( 'dashicons-admin-settings' )
		->add_tab(
			__( 'Header', 'playground' ),
			array( ... ),
		)
		->add_tab(
			__( 'Contact', 'playground' ),
			array( ... )
		)
		->add_tab(
			__( 'Socials', 'playground' ),
			array( ... )
		)
		->add_tab(
			__( 'Footer', 'playground' ),
			array( ... )
		);
	}
}
