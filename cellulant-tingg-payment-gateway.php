<?php
/**
 * Plugin Name: Cellulant Tingg Checkout
 * Plugin URI:  https://dev-portal.tingg.africa/wordpress-plugin
 * Description: A WordPress-WooCommerce plugin for merchants to integrate Tingg payment gateway on their online shops offering their customers payment options across Africa.
 * Version:     1.1.0
 * Author:      Cellulant Corporation
 * Author URI:  https://www.cellulant.io/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/*
 * Required files
 */
require('includes/TinggPaymentGatewayConstants.php');
require('includes/TinggPaymentGatewayUtils.php');

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter('woocommerce_payment_gateways', 'add_tingg_gateway_class');
function add_tingg_gateway_class($gateways)
{
    $gateways[] = 'WC_Gateway_Tingg';
    return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action('plugins_loaded', 'init_tingg_gateway_class');
function init_tingg_gateway_class()
{

    class WC_Gateway_Tingg extends WC_Payment_Gateway
    {
        //WC_Payment_Gateway
        public $id;
        public $icon;
        public $supports;
        public $has_fields;
        public $method_title;
        public $method_description;
        public $base_country;


        //Payment gateway configurations
        public $title;
        public $iv_key;
        public $enabled;
        public $test_mode;
        public $logging;
        public $access_key;
        public $secret_key;
        public $service_code;
        public $checkout_version;
        public $payment_period;
        public $client_id;
        public $client_secret;
        public $api_key;
        public $checkout_url;
        public $oauth_token_url;
        public $acknowledge_url;

        public function __construct()
        {
            $countries = new WC_Countries;

            $this->icon = TinggWordPressConstants::TINGG_ICON;
            $this->has_fields = true;
            $this->id = TinggWordPressConstants::PAYMENT_GATEWAY;
            $this->method_title = ucfirst(TinggWordPressConstants::BRAND_NAME);
            $this->method_description = ucfirst(TinggWordPressConstants::PAYMENT_GATEWAY_DESCRIPTION);
            $this->base_country = $countries->get_base_country();

            $this->supports = array(
                'products'
            );

            $this->init_form_fields();

            //settings
            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            $this->enabled = $this->get_option('enabled');
            $this->test_mode = 'yes' === $this->get_option('test_mode');
            $this->logging = 'yes' === $this->get_option('logging');
            $this->checkout_version = $this->get_option('checkout_version');
            $this->payment_period = $this->get_option('payment_period');
            $this->api_key = $this->get_option('api_key');

            $test_checkout_url = $this->checkout_version === '2.8' ? TinggWordPressConstants::CHECKOUT_REDIRECT_LINKS["2.8"]["test"]
                : TinggWordPressConstants::CHECKOUT_REDIRECT_LINKS["3.0"]["test"];
            $live_checkout_url = $this->checkout_version === '2.8' ? TinggWordPressConstants::CHECKOUT_REDIRECT_LINKS["2.8"]["live"]
                : TinggWordPressConstants::CHECKOUT_REDIRECT_LINKS["3.0"]["live"];
            $test_oauth_token_url = $this->checkout_version === '2.8' ? TinggWordPressConstants::GENERATE_OAUTH_TOKEN["2.8"]["test"]
                : TinggWordPressConstants::GENERATE_OAUTH_TOKEN["3.0"]["test"];
            $live_oauth_token_url = $this->checkout_version === '2.8' ? TinggWordPressConstants::GENERATE_OAUTH_TOKEN["2.8"]["live"]
                : TinggWordPressConstants::GENERATE_OAUTH_TOKEN["3.0"]["live"];
            $test_acknowledge_url = $this->checkout_version === '2.8' ? TinggWordPressConstants::ACKNOWLEDGE_PAYMENTS["2.8"]["test"]
                : TinggWordPressConstants::ACKNOWLEDGE_PAYMENTS["3.0"]["test"];
            $live_acknowledge_url = $this->checkout_version === '2.8' ? TinggWordPressConstants::ACKNOWLEDGE_PAYMENTS["2.8"]["live"]
                : TinggWordPressConstants::ACKNOWLEDGE_PAYMENTS["3.0"]["live"];

            $this->iv_key = $this->test_mode ? $this->get_option('test_iv_key') : $this->get_option('live_iv_key');
            $this->secret_key = $this->test_mode ? $this->get_option('test_secret_key') : $this->get_option('live_secret_key');
            $this->access_key = $this->test_mode ? $this->get_option('test_access_key') : $this->get_option('live_access_key');
            $this->checkout_url = $this->test_mode ? $test_checkout_url : $live_checkout_url;
            $this->oauth_token_url = $this->test_mode ? $test_oauth_token_url : $live_oauth_token_url;
            $this->acknowledge_url = $this->test_mode ? $test_acknowledge_url : $live_acknowledge_url;
            $this->service_code = $this->test_mode ? $this->get_option('test_service_code') : $this->get_option('live_service_code');
            $this->client_id = $this->test_mode ? $this->get_option('test_client_id') : $this->get_option('live_client_id');
            $this->client_secret = $this->test_mode ? $this->get_option('test_client_secret') : $this->get_option('live_client_secret');

            // action hook to save the settings
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            // custom JavaScript to obtain a token
            add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

            //payment gateway webhook
            add_action('woocommerce_api_tingg_payment_webhook', array($this, 'webhook'));
        }

        /**
         * Plugin form field options
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'label' => 'Enable Payment Gateway',
                    'type' => 'checkbox',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => ucfirst(TinggWordPressConstants::BRAND_NAME),
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'This is the description which the user sees during checkout.',
                    'default' => 'Tingg allows you to make payments with Mobile money, mobile banking and card in 33+ countries in Africa from a single integration'
                ),
                'payment_period' => array(
                    'title' => 'Payment period',
                    'type' => 'text',
                    'description' => 'This sets the amount of time in minutes before a checkout request on an order expires',
                    'default' => '1440',
                    'desc_tip' => true,
                ),
                'checkout_version' => array(
                    'title' => 'Tingg Checkout Version',
                    'type' => 'select',
                    'default' => '3.0',
                    'description' => 'This defines the version of the Tingg payments platform to use. Use sandbox keys for the selected platform version.',
                    'options' => array(
                        '3.0' => 'version 3',
                        '2.8' => 'version 2'
                    ),
                    'desc_tip' => true,
                ),
                'test_mode' => array(
                    'title' => 'Test mode',
                    'label' => 'Enable Test Mode',
                    'type' => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys. Go live by disabling this option and updating the live keys.',
                    'default' => 'yes',
                    'desc_tip' => true,
                ),
                'logging' => array(
                    'title' => 'Logging',
                    'label' => 'Enable Logging',
                    'type' => 'checkbox',
                    'description' => 'Enable logs to be written on your debug.log file.',
                    'default' => 'no',
                    'desc_tip' => true,
                ),
                // test fields
                'test_service_code' => array(
                    'title' => 'Test Service Code',
                    'type' => 'text'
                ),
                'test_iv_key' => array(
                    'title' => 'Test IV Key',
                    'type' => 'text'
                ),
                'test_secret_key' => array(
                    'title' => 'Test Secret Key',
                    'type' => 'text',
                ),
                'test_access_key' => array(
                    'title' => 'Test Access Key',
                    'type' => 'text',
                ),
                'test_client_id' => array(
                    'title' => 'Test Client ID',
                    'type' => 'text',
                ),
                'test_client_secret' => array(
                    'title' => 'Test Client Secret',
                    'type' => 'text',
                ),
                // live fields
                'live_service_code' => array(
                    'title' => 'Live Service Code',
                    'type' => 'text'
                ),
                'live_iv_key' => array(
                    'title' => 'Live IV Key',
                    'type' => 'text'
                ),
                'live_secret_key' => array(
                    'title' => 'Live Secret Key',
                    'type' => 'text'
                ),
                'live_access_key' => array(
                    'title' => 'Live Access Key',
                    'type' => 'text',
                ),
                'live_client_id' => array(
                    'title' => 'Live Client ID',
                    'type' => 'text',
                ),
                'live_client_secret' => array(
                    'title' => 'Live Client Secret',
                    'type' => 'text',
                ),
                'api_key' => array(
                    'title' => 'API Key (For Checkout Version 3.0)',
                    'type' => 'text',
                ),
            );
        }

        /*
         * Custom JavaScript
         */
        public function payment_scripts()
        {
            // we need JavaScript to process a token only on cart/checkout pages
            if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) {
                return;
            }

            // if our payment gateway is disabled, we do not have to enqueue JS too
            if ($this->enabled === 'no') {
                return;
            }
        }

        /*
          * Fields validation
         */
        public function validate_fields(): bool
        {
            $billing_country = sanitize_text_field($_POST['billing_country']);

            $supported_countries = array_map(function ($value) {
                return $value['country_code'];
            }, TinggWordPressConstants::COUNTRIES);

            if (in_array($billing_country, $supported_countries)){
                $this->base_country = $billing_country;
            }else{
                //billing country not supported
                if (!in_array($this->base_country, $supported_countries)) {
                    //base country not supported
                    wc_add_notice('Country is not supported on Tingg platform!', 'error');
                    return false;
                }
            }
            return true;
        }

        private function get_iso_code(string $code)
        {
            foreach (TinggWordPressConstants::COUNTRIES as $country) {
                if ($country['country_code'] == strtoupper($code)) {
                    return $country['iso3_country_code'];
                }
            }
            return;
        }
        private function write_log($log) {
            if ($this->logging) {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }
        /*
         * Processing the payment here
         */
        public function process_payment($order_id)
        {
			global $woocommerce;

            // we need it to get any order details
            $order = wc_get_order($order_id);

            // checkout transaction description
            $order_excerpt = array_reduce($order->get_items(), function ($carry, $item) {
                $format = '%d x %s, ';

                $quantity = $item->get_quantity();

                $product = $item->get_product();
                $product_name = $product->get_name();
                return $carry . sprintf($format, $quantity, $product_name);
            });
            $currency_code = strtoupper(get_woocommerce_currency());
            $request_amount = $order->get_total();

            // array with parameters for API interaction
            $payload = $this->checkout_version === '2.8'
                ?
                array(
                    "accessKey" => $this->access_key,
                    "accountNumber" => $order->get_id(),
                    "serviceCode" => $this->service_code,
                    "requestAmount" => !empty($request_amount) ? $request_amount : 0,
                    "MSISDN" => $order->get_billing_phone(),
                    "merchantTransactionID" => uniqid($order->get_id()),
                    "customerEmail" => $order->get_billing_email(),
                    "customerLastName" => $order->get_billing_last_name(),
                    "customerFirstName" => $order->get_billing_first_name(),
                    "requestDescription" => rtrim(trim($order_excerpt), ','),
                    "currencyCode" => !empty($currency_code) ? $currency_code : 'USD',
                    "dueDate" => date("Y-m-d H:i:s", strtotime("+" . $this->payment_period . " minutes")),

                    // webhooks
                    "failRedirectUrl" => get_permalink(get_page_by_path('shop')),
                    "successRedirectUrl" => $order->get_checkout_order_received_url(),
                    "paymentWebhookUrl" => get_site_url() . '/wc-api/tingg_payment_webhook',
                    "pendingRedirectUrl" => get_permalink(get_page_by_path('shop'))

                )
                :
                array(
                    "access_key" => $this->access_key,
                    "account_number" => $order->get_id(),
                    "service_code" => $this->service_code,
                    "request_amount" => !empty($request_amount) ? $request_amount : 0,
                    "msisdn" => $order->get_billing_phone(),
                    "merchant_transaction_id" => uniqid($order->get_id()),
                    "customer_email" => $order->get_billing_email(),
                    "customer_last_name" => $order->get_billing_last_name(),
                    "customer_first_name" => $order->get_billing_first_name(),
                    "request_description" => rtrim(trim($order_excerpt), ','),
                    "currency_code" => !empty($currency_code) ? $currency_code : 'USD',
                    "due_date" => date("Y-m-d H:i:s", strtotime("+" . $this->payment_period . " minutes")),
                    "country_code" => $this->get_iso_code($this->base_country),

                    // webhooks
                    "fail_redirect_url" => get_permalink(get_page_by_path('shop')),
                    "success_redirect_url" => $order->get_checkout_order_received_url(),
                    "callback_url" => get_site_url() . '/wc-api/tingg_payment_webhook',
                    "pending_redirect_url" => get_permalink(get_page_by_path('shop'))
                );

            $this->write_log($payload);
            $checkout_payment_url = $this->checkout_version === '2.8'
                ? sprintf(
                    $this->checkout_url . "?params=%s&accessKey=%s&countryCode=%s",
                    TinggPaymentGatewayUtils::encryptCheckoutRequest($this->iv_key, $this->secret_key, $payload),
                    $this->access_key,
                    $this->base_country
                )
                : sprintf(
                    $this->checkout_url . "?access_key=%s&encrypted_payload=%s",
                    $this->access_key,
                    TinggPaymentGatewayUtils::encryptCheckoutRequest($this->iv_key, $this->secret_key, $payload)
                );


//            clear the cart
            if (!empty($woocommerce->cart)) {
                $this->write_log("Clearing cart...");
                $woocommerce->cart->empty_cart();
            }

            // redirect to Tingg checkout express
            return array(
                'result' => 'success',
                'redirect' => $checkout_payment_url
            );
        }

        private function generate_oauth(): string
        {
            $body = [
                "grant_type" => "client_credentials",
                "client_id" => sanitize_text_field($this->client_id),
                "client_secret" => sanitize_text_field($this->client_secret)
            ];
            $args = [
                'body' => json_encode($body),
                'headers' => [
                    'apikey' => sanitize_text_field($this->api_key) ?? '',
                    'content-type' => 'application/json; charset=utf-8'
                ]
            ];
            $this->write_log("Oauth Request");
            $this->write_log($args);
            $response = wp_remote_post( $this->oauth_token_url, $args );
            $body = json_decode(wp_remote_retrieve_body($response));

            $this->write_log("Oauth Response");
            $this->write_log($body);
            $stat_code = wp_remote_retrieve_response_code($response);
            $this->write_log("Status Code: ". $stat_code);
            $access_token = $body->access_token;
            if (!isset($access_token)){
                $this->write_log("Could not retrieve access token");
                return false;
            }
            return $access_token;
        }

        /*
         * Payment webhook callback
         */
        public function webhook()
        {
            $callback_json_payload = file_get_contents('php://input');
            $payload = json_decode($callback_json_payload, true);
            $account_number = $this->checkout_version === '2.8' ? $payload["accountNumber"] : $payload["account_number"];
            $request_status_code = $this->checkout_version === '2.8' ? $payload["requestStatusCode"] : $payload["request_status_code"];
            $order = wc_get_order($account_number);

            //successful payments
            if (in_array($request_status_code, [176, 178])) {
                // mark order as fully paid
                if ($request_status_code == 178) {
                    $order->payment_complete();
                }
                wc_reduce_stock_levels($order);
                $note = '';
                // add a note to the order
                if ($request_status_code == 176) {
                    $note .= sprintf("Order #%s has been partially paid", $account_number);
                }
                if ($request_status_code == 178) {
                    $note .= sprintf("Order #%s has been paid in full", $account_number);
                }
                $order->add_order_note($note);

                $response_payload_v2 = $this->checkout_version === '2.8' ? [
                    "statusCode" => 183,
                    "statusDescription" => "Payment accepted",
                    "receiptNumber" => $payload['accountNumber'],
                    "checkoutRequestID" => $payload["checkoutRequestID"],
                    "merchantTransactionID" => $payload["merchantTransactionID"]
                ]: [];

                //Acknowledge payment
                //Generate Oauth token
                if ($this->generate_oauth()){
                    $this->write_log("Acknowledging via custom...");
                    $access_token = $this->generate_oauth();
                    $ack_body = $this->checkout_version === '2.8' ?
                        $response_payload_v2:
                        [
                            "acknowledgement_amount" => $payload['request_amount'],
                            "acknowledgement_type" => "Full",
                            "acknowledgement_narration" => "Payment accepted",
                            "acknowledgment_reference" => $payload['account_number'],
                            "merchant_transaction_id" => $payload["merchant_transaction_id"],
                            "service_code" => $payload["service_code"],
                            "status_code" =>"183",
                            "currency_code" => $payload["currency_code"]
                        ];
                    $args = [
                        'body' => json_encode($ack_body),
                        'headers' => [
                            'apikey' => $this->api_key ?? '',
                            'content-type' => 'application/json; charset=utf-8',
                            'Authorization' => 'Bearer '.$access_token
                        ]
                    ];
                    $response = wp_remote_post( $this->acknowledge_url, $args );
                    $body = json_decode(wp_remote_retrieve_body( $response ));
                    $this->write_log("Response after acknowledgement");
                    $this->write_log($body);
                    echo json_encode($body, true);
                }else {
                    // send back a response to acknowledge the payment
                    $this->write_log("Acknowledging via webhook");
                    $response = ($this->checkout_version === '2.8')
                        ? $response_payload_v2
                        : [
                            "status_code" => 183,
                            "status_description" => "Payment accepted",
                            "receipt_number" => $payload['account_number'],
                            "merchant_transaction_id" => $payload["merchant_transaction_id"]
                        ];

                    echo json_encode($response, true);
                }
            }
            exit();
        }
    }
}
