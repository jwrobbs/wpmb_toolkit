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
		Container::make( 'theme_options', 'Theme Options' )
		->set_page_file( 'theme-options123' )
		->add_fields(
			array(
				Field::make( 'text', 'crb_facebook_url' )->set_width( 50 ),
				Field::make( 'textarea', 'crb_footer_text' )->set_width( 50 ),
			)
		);

		$menu_name = carbon_get_theme_option( 'menu_name' ) ?? 'WPMB Options';
		Container::make( 'theme_options', $menu_name )
		->set_page_file( 'wpmb-options' )
		->add_fields(
			array(
				Field::make( 'text', 'menu_name', 'Menu Name' )
				->set_width( 33 )
				->set_default_value( 'WPMB Options' ),
				Field::make( 'text', 'crb_facebook_url' )->set_width( 33 ),
			)
		);
		Container::make( 'theme_options', 'Theme Options3' )
		->set_page_parent( 'theme-options123' )
		->add_fields(
			array(
				Field::make( 'text', 'crb_facebook_url' ),
				Field::make( 'textarea', 'crb_footer_text' ),
			)
		);
	}
}
