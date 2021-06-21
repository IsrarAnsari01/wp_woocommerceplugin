<?php

/**
 *
 * @author            IA
 * @copyright         2021 IA or CodeX
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name:       IA-CWF
 * Plugin URI:        https://example.com/plugin-name
 * Description:       This plugin will add seller information and display them in single product page 
 *                    | We give 10% off to seller 
 * Version:           1.0.0
 * Author:            IA
 * Author URI:        https://example.com
 * Text Domain:       iawcf-Like
 * License:           GPL2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 */
// Block user to access it directly
if (!defined('WPINC')) {
    die;
}
// Define Constant
if (!defined("CW_ PLUGIN_VERSION")) {
    define("CW_ PLUGIN_VERSION", "1.0.0");
}
// Define constant for directory
if (!defined("CW_PLUGIN_DIR")) {
    define("CW_PLUGIN_DIR", plugin_dir_url(__FILE__));
}
// Check the avalibilty of woocommerce plugin
$active_plugins = (array) get_option('active_plugins', array());
if (in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins)) {
    require plugin_dir_path(__FILE__) . "inc/customWooCTab.php";
    require plugin_dir_path(__FILE__) . "inc/customUserRole.php";
    require plugin_dir_path(__FILE__) . "inc/userBasedDiscount.php";
    require plugin_dir_path(__FILE__) . "inc/customFieldInCheckout.php";
    require plugin_dir_path(__FILE__) . "inc/countryConfigration.php";
    require plugin_dir_path(__FILE__) . "inc/ia-payment-for-woocommerce.php";
}
