<?php
/**
 * Plugin Name: WPMB Toolkit
 * Plugin URI: https://joshrobbs.com/wpmb-toolkit
 * Description: A toolkit plugin for WordPress devs and webmasters.
 * Version: 0.1.0
 * Author: Josh Robbs
 * Author URI: https://joshrobbs.com
 * License: BSD-3
 * Text Domain: wpmb-toolkit
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit;

use WPMB_Toolkit\Common\Hooks;

defined( 'ABSPATH' ) || exit;


/**
 * Class WPMBToolkit.
 */
class WPMBToolkit {
	/**
	 * Path.
	 *
	 * @var string
	 */
	protected static $path;

	/**
	 * URL.
	 *
	 * @var string
	 */
	protected static $url;

	/**
	 * Is_init.
	 *
	 * @var bool
	 */
	protected static $is_init = false;

	/**
	 * Instance.
	 *
	 * @var WPMBToolkit
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 */
	public static function init() {
		if ( self::$is_init ) {
			return;
		}
		self::$is_init = true;

		self::$path = plugin_dir_path( __FILE__ );
		self::$url  = plugin_dir_url( __FILE__ );

		require_once __DIR__ . '/vendor/autoload.php';
		Hooks::register();
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public static function get_path() {
		return self::$path;
	}

	/**
	 * Get the plugin URL.
	 *
	 * @return string
	 */
	public static function get_url() {
		return self::$url;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
	}
}

/**
 * Initialize the plugin.
 */
WPMBToolkit::init();
