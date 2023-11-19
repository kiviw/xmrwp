<?php
/*
 * Plugin Name: Monero Payment Gateway
 * Description: WooCommerce payment gateway for Monero.
 * Version: 1.0
 * Author: Your Name
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include necessary files
include_once(plugin_dir_path(__FILE__) . 'monero-payment-config.php');
include_once(plugin_dir_path(__FILE__) . 'monero-transaction-processor.php');
include_once(plugin_dir_path(__FILE__) . 'woocommerce-integration.php');

// Initialize the Monero Payment Gateway
function initialize_monero_payment_gateway() {
    // Add any initialization code here
}
add_action('init', 'initialize_monero_payment_gateway');

// Register the Monero Payment Gateway with WooCommerce
function add_monero_payment_gateway($gateways) {
    $gateways[] = 'WC_Monero_Payment_Gateway';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'add_monero_payment_gateway');

// Define the Monero Payment Gateway class
class WC_Monero_Payment_Gateway extends WC_Payment_Gateway {
    // Add your gateway properties and methods here
}

// Add custom settings link in plugins page
function monero_payment_gateway_settings_link($links) {
    $settings_link = '<a href="admin.php?page=wc-settings&tab=checkout&section=monero_payment_gateway">' . __('Settings') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin_basename = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin_basename", 'monero_payment_gateway_settings_link');
