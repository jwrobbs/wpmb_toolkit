<?php
/**
 * ToolsManager class file.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\Tools;

use WPMB_Toolkit\WPMBToolkit;

defined( 'ABSPATH' ) || exit;

/**
 * ToolsManager class.
 *
 * Reponsible for:
 * - Detecting available tools.
 * - Detecting missing / removed tools.
 * - Activating / deactivating tools.
 * - Upgrading tools.
 * - Loading tools.
 * - Analyzing tool dependencies.
 */
class ToolsManager {
	/**
	 * Stores detected tools and their metadata.
	 *
	 * @var array
	 */
	protected static $tools = array();

	/**
	 * Directory where user tools are stored.
	 *
	 * @var string
	 */
	protected static $main_tools_directory;

	/**
	 * WP_Filesystem Instance.
	 *
	 * @var WP_Filesystem_Base
	 */
	protected static $filesystem;

	/**
	 * Initialize tool detection and loading.
	 *
	 * 1. Initialize props.
	 * 2. Scan the builtin tools directory.
	 * 3. Activate the builtin tools.
	 * 4. Scan the user tools directory.
	 * 5. Check the tool activations.
	 * 6. Load the active tools as appropriate.
	 *
	 * CoPilot spitballing.
	 * 7. Check for tool upgrades.
	 * 8. Check for tool removals.
	 * 9. Check for tool dependencies.
	 * 10. Check for tool conflicts.
	 * 11. Check for tool updates.
	 * 12. Check for tool deactivations.
	 * 13. Check for tool activations.
	 */
	public static function init() {

		self::$main_tools_directory = WPMBToolkit::get_path() . 'tools/';
		self::detect_tools();

		self::load_built_in_tools( self::$tools['BuiltIn'] );
		self::load_user_tools( self::$tools['user'] );
	}

	/**
	 * Scan the user tools directory and detect available tools.
	 */
	public static function detect_tools() {
		if ( ! is_dir( self::$main_tools_directory ) ) {
			\write_wpmb_log(
				'Tools directory does not exist',
				array( 'directory' => self::$main_tools_directory )
			);
			return false;
		}

		$tool_dirs      = array();
		$subdirectories = array( 'BuiltIn', 'user' );
		foreach ( $subdirectories as $subdir ) {
			$tool_dirs[ $subdir ] = array();

			$path = self::$main_tools_directory . $subdir . '/';
			if ( is_dir( $path ) ) {
				$dirs = scandir( $path );
				if ( is_array( $dirs ) ) {
					foreach ( $dirs as $dir ) {
						if (
						'.' === $dir
						|| '..' === $dir
						|| ! is_dir( $path . $dir )
						|| ! file_exists( $path . $dir . '/config.json' )
						|| ! file_exists( $path . $dir . '/index.php' )
						) {
							continue;
						}
						$tool_dirs[ $subdir ][] = $path . $dir;
					}
				}
			}
		}
		self::$tools['BuiltIn'] = $tool_dirs['BuiltIn'];
		self::$tools['user']    = $tool_dirs['user'];

		// if ( is_dir( $tool_path ) && file_exists( $config_file ) ) {
		// $config_data = json_decode( file_get_contents( $config_file ), true );

		// if ( self::validate_config( $config_data ) ) {
		// self::$tools[ $config_data['key'] ] = array(
		// 'name'         => $config_data['name'],
		// 'key'          => $config_data['key'],
		// 'version'      => $config_data['version'],
		// 'description'  => $config_data['description'] ?? '',
		// 'dependencies' => $config_data['dependencies'] ?? array(),
		// );
		// }
		// }
		// }
	}

	/**
	 * Load built-in tools.
	 *
	 * @param array $tool_dirs Array of tool directories.
	 */
	protected static function load_built_in_tools( array $tool_dirs ) {
		foreach ( $tool_dirs as $tool_dir ) {

			require_once $tool_dir . '/index.php';

			$config_file = $tool_dir . '/config.json';
			if ( ! file_exists( $config_file ) ) {
				continue;
			}

			$config_data = json_decode( file_get_contents( $config_file ), true ); // phpcs:ignore
			if ( ! self::validate_config( $config_data ) ) {
				\write_wpmb_log(
					'Invalid config.json',
					array(
						'path' => $tool_dir,
						'file' => $config_file,
					)
				);
				continue;
			}

			\write_wpmb_log(
				'Loaded built-in tool',
				array(
					'name'    => $config_data['name'],
					'key'     => $config_data['key'],
					'version' => $config_data['version'],
				)
			);
		}
	}

	/**
	 * Load user tools.
	 *
	 * @param array $tool_dirs Array of tool directories.
	 */
	protected static function load_user_tools( array $tool_dirs ) {
		foreach ( $tool_dirs as $tool_dir ) {
			$config_file = $tool_dir . '/config.json';
			if ( ! file_exists( $config_file ) ) {
				continue;
			}

			$config_data = json_decode( file_get_contents( $config_file ), true ); // phpcs:ignore
			if ( ! self::validate_config( $config_data ) ) {
				\write_wpmb_log(
					'Invalid config.json',
					array(
						'path' => $tool_dir,
						'file' => $config_file,
					),
					'error'
				);
				continue;
			}

			// Check that it's activated.
			// !! Add activation logic.
			$activated = false;
			if ( $activated ) {
				require_once $tool_dir . '/index.php';
			}
		}
	}

	/**
	 * Validate a tool's config.json structure.
	 *
	 * ?? Is this in use?
	 *
	 * @param array $config I have no idea what this is.
	 *
	 * @return bool
	 */
	protected static function validate_config( $config ) {
		$required_keys = array( 'name', 'key', 'version' );
		foreach ( $required_keys as $key ) {
			if ( ! isset( $config[ $key ] ) || empty( $config[ $key ] ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Get the list of detected tools.
	 * ?? is this in use?
	 *
	 * @return array
	 */
	public static function get_tools(): array {
		return self::$tools;
	}
}
