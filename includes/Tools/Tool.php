<?php
/**
 * Tools class file.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\Tools;

defined( 'ABSPATH' ) || exit;

/**
 * Tools class.
 */
class Tool {

		/**
		 * Tools constructor.
		 *
		 * @param string $name The name of the tool.
		 * @param string $key The key of the tool.
		 * @param string $path The path to the tool.
		 * @param string $version The version of the tool.
		 * @param string $status The status of the tool.
		 * @param string $description The description of the tool.
		 * @param bool   $active Whether the tool is active or not.
		 * @param bool   $is_built_in Whether the tool is built-in or not.
		 *
		 * @return void
		 */
	public function __construct(
		public string $name,
		public string $key,
		public string $path,
		public string $version,
		public string $status,
		public ?string $description,
		public bool $active = false,
		public bool $is_built_in = false,
	) {
		$this->name        = $name;
		$this->key         = $key;
		$this->path        = $path;
		$this->version     = $version;
		$this->status      = $status;
		$this->description = $description ?? '';
		$this->is_built_in = $is_built_in;

		if ( ! in_array( $status, array( 'Active', 'Inactive' ), true ) ) {
			$this->status = 'Inactive';
		} else {
			$this->status = $status;
		}
	}
}
