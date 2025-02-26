<?php
/**
 * A place for helper METHODS.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\common\Logger;

use WPMB_Toolkit\Common\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * LoggerTrait trait.
 */
trait LoggerTrait {

	/**
	 * Verify the level.
	 *
	 * Ensures the level is allowed by SimpleHistory.
	 *
	 * @param string $level The level to verify.
	 * @return string
	 */
	protected static function verify_level( string $level = 'info' ) {
		$allowed_levels = array(
			'info',
			'notice',
			'warning',
			'error',
			'critical',
			'alert',
			'emergency',
			'debug',
		);
		if ( ! in_array( $level, $allowed_levels, true ) ) {
			$level = 'info';
		}
		return $level;
	}

	/**
	 * Verify the context.
	 *
	 * Ensures the context is an array of key => value pairs.
	 * If not, it returns an empty array.
	 *
	 * @param mixed $context The context to verify.
	 * @return array
	 */
	protected static function verify_context( $context ) {
		if ( is_object( $context ) ) {
			$context = (array) $context;
		} elseif ( \is_string( $context ) ) {
			$context = array( $context );

		} elseif ( ! is_array( $context ) ) {
			$context = array();
		}
		return $context;
	}

	/**
	 * Prepare the log file.
	 *
	 * Create file if it doesn't exist.
	 * Ensure it is writable.
	 */
	protected function prepare_log_file() {
		$wp_filesystem = Helpers::init_filesystem();

		$log_file = self::get_log_file_path();

		if ( ! $wp_filesystem->exists( $log_file ) ) {
			error_log( 'Log file does not exist. Creating it.' ); //phpcs:ignore
			$wp_filesystem->put_contents( $log_file, '', FS_CHMOD_FILE );
		}

		if ( ! $wp_filesystem->is_writable( $log_file ) ) {
			error_log( 'Log file is not writable. Changing permissions.' ); // phpcs:ignore
			$wp_filesystem->chmod( $log_file, FS_CHMOD_FILE );
		}
	}

	/**
	 * Get the log file path.
	 *
	 * @return string
	 */
	protected static function get_log_file_path() {
		return WP_CONTENT_DIR . '/wpmb-toolkit.log';
	}
}
