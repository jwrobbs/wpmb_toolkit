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
	 * @param mixed ...$args Arguments to pass to the logger.
	 */
	function write_wpmb_log( ...$args ) {
		\WPMB_Toolkit\common\Logger\Logger::log_it( ...$args );
	}
}
