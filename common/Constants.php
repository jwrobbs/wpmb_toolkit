<?php
/**
 * Constants class file.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Constants class.
 */
class Constants {
	/**
	 * Plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		return plugin_dir_path( __DIR__ );
	}

	/**
	 * Plugin URL.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return plugin_dir_url( __DIR__ );
	}
}
