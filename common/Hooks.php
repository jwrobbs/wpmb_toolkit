<?php
/**
 * Hooks class file.
 *
 * Initializes all hooks for the plugin.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Common;

use WPMB_Toolkit\Includes\Options\MainOptionsPage;
use WPMB_Toolkit\Includes\Tools\Logger\Logger;

defined( 'ABSPATH' ) || exit;

/**
 * Hooks class.
 */
class Hooks {

	/**
	 * Register hooks.
	 */
	public static function register() {
		Logger::init();
		MainOptionsPage::register();
	}
}
