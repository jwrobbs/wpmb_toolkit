<?php
/**
 * Builtin logger.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\Tools\Logger;

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

	/**
	 * The log's file path.
	 * Should this be public?
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * Initialize the logger.
	 */
	public static function init() {
		require_once __DIR__ . '/helper.php';
	}

	/**
	 * Get the logger instance.
	 *
	 * @param mixed ...$args The arguments to pass to the logger.
	 * @return \WPMB_Toolkit\Includes\Tools\Logger\Logger
	 */
	public static function get_instance( ...$args ) {
		return new self( ...$args );
	}

	/**
	 * Constructor.
	 *
	 * @param string $message The message to log.
	 * @param array  $context The context to log. Array of key => value pairs.
	 * @param string $level The level to log.
	 *
	 * @return void
	 */
	public function __construct(
		protected string $message,
		protected array $context = array(),
		protected string $level = 'info',
	) {
		$this->verify_level();
		if ( function_exists( 'SimpleHistory' ) ) {
			\SimpleHistory()->$this->level( $this->message, $this->context );
		}

		$this->file_path = WP_CONTENT_DIR . '/wpmb-toolkit.log';
		$this->write_to_log();
	}

	/**
	 * Verify the level.
	 *
	 * Ensures the level is allowed by SimpleHistory.
	 *
	 * @return void
	 */
	protected function verify_level() {
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
		if ( ! in_array( $this->level, $allowed_levels, true ) ) {
			$this->level = 'info';
		}
	}

	/**
	 * Write to the log.
	 *
	 * @return void
	 */
	protected function write_to_log() {

		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$datetime = gmdate( 'Y-m-d H:i:s' );

		$context = \wp_json_encode( $this->context ) ?? '';
		if ( ! empty( $context ) ) {
			$context = '\n' . $context;
		}

		$log_message = <<<LOG
			{$datetime} [{$this->level}] {$this->message}{$context}
		LOG;

		// Check if the file exists, if not create it.
		if ( ! $wp_filesystem->exists( $this->file_path ) ) {
			$wp_filesystem->put_contents( $this->file_path, '', FS_CHMOD_FILE );
		}

		// Append the log message to the file.
		$existing_content = $wp_filesystem->get_contents( $this->file_path );
		$wp_filesystem->put_contents( $this->file_path, $existing_content . $log_message . PHP_EOL, FS_CHMOD_FILE );
	}
}
