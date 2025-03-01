<?php
/**
 * Helper functions.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Common;

defined( 'ABSPATH' ) || exit;

/**
 * Helper functions.
 */
class Helpers {
	/**
	 * Init filesystem.
	 *
	 * @return WP_Filesystem_Base
	 */
	public static function init_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	/**
	 * Dump it.
	 *
	 * @param mixed $data Data to dump.
	 * @param bool  $suicide Whether to die or not.
	 */
	public static function dumpit( $data, $suicide = false ) {
		echo '<pre>';
		var_dump( $data ); // phpcs:ignore
		echo '</pre>';

		if ( $suicide ) {
			die();
		}
	}
}
