<?php
/*
 * Plugin Name: Monero Payment Gateway
 * Description: WooCommerce payment gateway for Monero.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('WPINC')) {
    die;
}

include_once(plugin_dir_path(__FILE__) . 'monero-payment-config.php');
include_once(plugin_dir_path(__FILE__) . 'monero-transaction-processor.php');

class WC_Monero_Payment_Gateway extends WC_Payment_Gateway {
    public function __construct() {
        $this->id = 'monero_payment_gateway';
        $this->has_fields = false;
        $this->method_title = 'Monero Payment Gateway';
        $this->method_description = 'Accept Monero payments for your WooCommerce store.';
        $this->title = 'Monero';
        $this->icon = ''; // Add a link to your Monero logo

        $this->init_form_fields();
        $this->init_settings();

        $this->generateAndWriteSubaddress(); // Generate and write subaddress on plugin activation

        add_action('woocommerce_review_order_before_payment', array($this, 'displayMoneroSubaddress'));
    }

    public function init_form_fields() {
        // Fields for the settings page if needed
        $this->form_fields = array(
            // Add any necessary settings fields here
        );
    }

    private function generateAndWriteSubaddress() {
        $cliPath = escapeshellarg(MONERO_CLI_PATH);
        $walletFile = escapeshellarg(MONERO_WALLET_FILE);
        $passwordFile = escapeshellarg(PASSWORD_FILE_PATH);
        $subaddressFile = plugin_dir_path(__FILE__) . 'subaddress.txt';

        // Generate subaddress using Monero CLI command
        $command = "{$cliPath} --wallet-file {$walletFile} --password-file {$passwordFile} --command \"address new\"";

        // Execute the command and capture the output
        exec($command, $output);

        // Get the last line of the output (assuming the subaddress is the last line)
        $subaddress = end($output);

        // Write subaddress to the file
        file_put_contents($subaddressFile, $subaddress . PHP_EOL, FILE_APPEND);
    }

    private function getLastSubaddress() {
        $subaddressFile = plugin_dir_path(__FILE__) . 'subaddress.txt';

        // Read the subaddress file
        $subaddresses = file($subaddressFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Get the last subaddress
        $lastSubaddress = end($subaddresses);

        return $lastSubaddress;
    }

    public function displayMoneroSubaddress() {
        $lastSubaddress = $this->getLastSubaddress();

        echo "<p>Copy the following Monero address to make your payment: {$lastSubaddress}</p>";
    }
}

// Register the gateway
function add_monero_payment_gateway($gateways) {
    $gateways[] = 'WC_Monero_Payment_Gateway';
    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'add_monero_payment_gateway');
