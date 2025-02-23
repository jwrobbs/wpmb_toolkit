<?php
/**
 * Logger helper functions.
 *
 * No class. No namespace.
 *
 * @package WPMB_Toolkit
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'write_wpmb_log' ) ) {
	/**
	 * Get the logger instance.
	 *
	 * @return \WPMB_Toolkit\Includes\Tools\Logger\Logger
	 */
	function write_wpmb_log() {
		return \WPMB_Toolkit\Includes\Tools\Logger\Logger::get_instance();
	}
}
