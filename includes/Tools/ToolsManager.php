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
	 * Is init.
	 *
	 * @var bool
	 */
	protected static $is_init = false;

	/**
	 * Manifest.
	 *
	 * Contains array of all Tool objects.
	 *
	 * @var array
	 */
	protected static $manifest = array();

	/**
	 * Activated Tools.
	 *
	 * If the Tool's key is in this array, it is activated.
	 *
	 * @var array
	 */
	protected static $activated_tools = array();

	/**
	 * Main tools directory.
	 *
	 * @var string
	 */
	protected static $main_tools_directory = '';

	/**
	 * Tool dirs.
	 *
	 * Array of arrays of tool directory strings.
	 *
	 * @var array
	 */
	protected static $tool_dirs = array();

	/**
	 * Initialize tool detection and loading.
	 *
	 * 1. Initialize props.
	 * 2. Load activations.
	 * 3. Scan the tools directory.
	 * 4. Load and activate the built-in tools.
	 * 5. Load and activate the user tools.
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
		if ( self::$is_init ) {
			return;
		}
		self::$is_init              = true; // 1.
		self::$main_tools_directory = WPMBToolkit::get_path() . 'tools/';

		self::load_activations(); // 2.
		self::detect_tools(); // 3.

		self::load_tools( self::$tool_dirs['BuiltIn'], true ); // 4.
		self::load_tools( self::$tool_dirs['user'] ); // 5.
	}

	/**
	 * Load activations.
	 */
	protected static function load_activations() {
		self::$activated_tools = \get_option( 'wpmb_toolkit_activations', array() );
	}

	/**
	 * Scan the user tools directory and detect available tools.
	 * Creates list of paths.
	 */
	public static function detect_tools() {
		if ( ! is_dir( self::$main_tools_directory ) ) {
			\write_wpmb_log(
				'Tools directory does not exist',
				array( 'directory' => self::$main_tools_directory ),
				'error'
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
		self::$tool_dirs['BuiltIn'] = $tool_dirs['BuiltIn'];
		self::$tool_dirs['user']    = $tool_dirs['user'];
	}

	/**
	 * Load tools.
	 *
	 * @param array $tools_dir Array of tools.
	 * @param bool  $is_built_in Whether the tools are built-in or user tools.
	 */
	public static function load_tools( array $tools_dir, bool $is_built_in = false ) {
		foreach ( $tools_dir as $tool_dir ) {

			// Get and verify config.json.
			$config_file = $tool_dir . '/config.json';
			if ( ! file_exists( $config_file ) ) {
				// add log/error.
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

			// Fine-tune data and create Tool object.

			if (
				in_array( $config_data['key'], self::$activated_tools, true )
				|| $is_built_in
			) {
				$status = 'Active';
				$active = true;
			} else {
				$status = 'Inactive';
				$active = false;
			}

			$tool = new Tool(
				$config_data['name'],
				$config_data['key'],
				$tool_dir,
				$config_data['version'],
				$status,
				$config_data['description'] ?? '',
				$config_data['link'] ?? 'no data',
				$active,
				$is_built_in
			);

			// Add Tool object to manifest.
			self::$manifest[ $config_data['key'] ] = $tool;

			// Load the tool.
			if ( $tool->active
				|| 'Active' === $status
			) {
				require_once $tool->path . '/index.php';
			}
		}
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
					),
					'error'
				);
				continue;
			}
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
	 * Get the manifest.
	 *
	 * @return array
	 */
	public static function get_manifest() {
		return self::$manifest;
	}
}
