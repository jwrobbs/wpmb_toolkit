<?php
/**
 * Activation functions trait.
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit\Includes\Tools;

defined( 'ABSPATH' ) || exit;

/**
 * Activation functions trait.
 */
trait ActivationFns {

	/**
	 * Activation list.
	 *
	 * @var array
	 */
	protected static $activation_list = array();

	/**
	 * Activate tool.
	 */
	public static function activate() {
		if ( empty( self::$activation_list ) ) {
			self::load_activations();
		}
	}

	/**
	 * Deactivate tool.
	 */
	public static function deactivate() {
		if ( empty( self::$activation_list ) ) {
			self::load_activations();
		}
	}

	/**
	 * Load activations.
	 */
	public static function load_activations() {
		self::$activation_list = \get_option( 'wpmb_toolkit_activations', array() );
	}

	/**
	 * Check if tool is activated.
	 *
	 * @return bool
	 */
	public static function is_activated() {
		return false;
	}
}
