<?php

/**
 *
 * @author            IA
 * @copyright         2021 IA or CodeX
 * @license           GPL-2.0
 *
 * @wordpress-plugin
 * Plugin Name:       IA-CWF
 * Plugin URI:        https://github.com/IsrarAnsari01
 * Description:       Seller Product data tab that is use to get the basic information of seller and display them in single product page
 *                    |Age based restriction in checkout you can manunally add minimun age to checkout accourding to your store location | Pre-defined user based discount | Custom Payment gateway and integration with stripe
 * Version:           1.2.0
 * Author:            Israr Ansari
 * Author URI:        https://github.com/IsrarAnsari01
 * Text Domain:       iawcf-Like
 * License:           GPL2
 * License URI:       https://github.com/IsrarAnsari01
 */
// Block user to access it directly
if (!defined('WPINC')) {
    die;
}
// Define Constant
if (!defined("CW_ PLUGIN_VERSION")) {
    define("CW_ PLUGIN_VERSION", "1.2.0");
}
// Define constant for directory
if (!defined("CW_PLUGIN_DIR")) {
    define("CW_PLUGIN_DIR", plugin_dir_url(__FILE__));
}
// Check the avalibilty of woocommerce plugin
$active_plugins = (array) get_option('active_plugins', array());
if (in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins)) {
    require plugin_dir_path(__FILE__) . "inc/sellerTabInProductTab.php";
    require plugin_dir_path(__FILE__) . "inc/preDefinedUserRole.php";
    require plugin_dir_path(__FILE__) . "inc/sellerDiscount.php";
    require plugin_dir_path(__FILE__) . "inc/dateFieldInCheckout.php";
    require plugin_dir_path(__FILE__) . "inc/countryConfigration.php";
    require plugin_dir_path(__FILE__) . "inc/iaPaymentForWoocommerce.php";
}