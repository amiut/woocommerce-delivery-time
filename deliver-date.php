<?php
/**
 * Plugin Name: Delivery time
 * Description: Delivery time plugin for woocommerce by Dornaweb.com
 * Plugin URI:  https://wwww.dornaweb.com
 * Version:     1.0
 * Author:      Dornaweb
 * Author URI:  https://wwww.dornaweb.com
 * License:     GPL
 * Text Domain: deliverytime
 * Domain Path: /languages
 *
 * @package Delivery time Plugin
 */

namespace Dornaweb;

defined("ABSPATH") || exit;

@ini_set('error_reporing', E_ALL);
@ini_set('display_errors', 1);

if (!defined("DWEB_DELIVERY_TIME_PLUGIN_FILE")) {
    define("DWEB_DELIVERY_TIME_PLUGIN_FILE", __FILE__);
}

/**
 * Load core packages and the autoloader.
 * The SPL Autoloader needs PHP 5.6.0+ and this plugin won't work on older versions
 */
if (version_compare(PHP_VERSION, "5.6.0", ">=")) {
    require __DIR__ . "/includes/class-autoloader.php";
}

/**
 * Returns the main instance of WooDeliveryTime.
 *
 * @since  1.0
 * @return WooDeliveryTime\App
 */
function dweb_delivery_time()
{
    // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return WooDeliveryTime\App::instance();
}

// Global for backwards compatibility.
$GLOBALS["dweb_delivery_time"] = dweb_delivery_time();
