<?php
/**
 * Plugin Name: WPMB Toolkit
 * Plugin URI: https://joshrobbs.com/wpmb-toolkit
 * Description: A toolkit plugin for WordPress devs and webmasters.
 * Version: 0.1.0
 * Author: Josh Robbs
 * Author URI: https://joshrobbs.com
 * License: BSD-3
 * Text Domain: wpmb-toolkit
 *
 * @package WPMB_Toolkit
 */

namespace WPMB_Toolkit;

use WPMB_Toolkit\Common\Hooks;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';
Hooks::register();
