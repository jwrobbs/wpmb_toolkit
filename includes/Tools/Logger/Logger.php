<?php
/**
 * Builtin logger.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\Tools\Logger;

use WPMB_Toolkit\Common\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Logger class.
 *
 * Modelled after SimpleLogger (Simple History).
 * Will act as a SimpleLogger wrapper if it's available.
 *
 * Usage:
 *  use WPMB_Toolkit\Includes\Tools\Logger\Logger;
 *  log()
 */
class Logger {
	use LoggerTrait;

	/**
	 * Instance.
	 *
	 * @var Logger
	 */
	protected static $instance = null;

	/**
	 * Initialize the logger.
	 *
	 * Just loads the helper functions. The rest is handled by the constructor.
	 */
	public static function init() {
		require_once __DIR__ . '/helper.php';
	}

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	protected function __construct() {
	}

	/**
	 * Log it.
	 *
	 * This is the primary public method that drives this system.
	 *
	 * @param string $message The message to log.
	 * @param array  $context The context to log. Array of key => value pairs.
	 * @param string $level The level to log.
	 * @return void
	 */
	public static function log_it(
		$message,
		$context = array(),
		$level = 'info'
	) {

		$level   = self::verify_level( $level );
		$context = self::verify_context( $context );

		// If SimpleHistory is available, use it and return.
		if ( function_exists( 'SimpleHistory' ) ) {
			\SimpleHistory()->$level( $message, $context );
			return;
		}
		// ?? what other systems are available? QM?

		$logger = self::get_instance();
		$logger->write_to_log( $message, $context, $level );
	}

	/**
	 * Get the logger instance.
	 *
	 * @return \WPMB_Toolkit\Includes\Tools\Logger\Logger
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Write to the log.
	 *
	 * At this point, $level has been verified. $context is an array.
	 *
	 * @param string $message The message to log.
	 * @param array  $context The context to log. Array of key => value pairs.
	 * @param string $level The level to log.
	 * @return void
	 */
	protected function write_to_log( $message, $context, $level ) {

		// Convert context to json.
		if ( empty( $context ) ) {
			$context_json = '';
		} else {
			$context_json = "\n" . \wp_json_encode( $context );
		}

		$datetime = gmdate( 'Y-m-d H:i:s' );

		$message_to_log = <<<LOG
		{$datetime} [{$level}] {$message}{$context_json}
		LOG;

		self::prepare_log_file();

		$wp_filesystem = Helpers::init_filesystem();

		// Append the log message to the file.

		$file_path = self::get_log_file_path();

		$existing_content = $wp_filesystem->get_contents( $file_path );
		$wp_filesystem->put_contents(
			$file_path,
			$existing_content . $message_to_log . PHP_EOL,
			FS_CHMOD_FILE
		);
	}
}
