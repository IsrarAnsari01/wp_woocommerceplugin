<?php
add_filter('woocommerce_payment_gateways', 'ia_add_gateway_class');

/**
 * Register are own custom payment gateway in woo commerence
 * @param array $gateways | WooCommerence registered payment gateways
 * @return array $gateways | Default + our custom payment gateways
 */

function ia_add_gateway_class($gateways)
{
    $gateways[] = 'WC_IA_Gateway'; 
    return $gateways;
}

add_action('plugins_loaded', 'ia_initialize_gateway_class');

/**
 * Its a main function it create gateway 
 * @param NULL
 * @return NULL
 */

function ia_initialize_gateway_class()
{
    class WC_IA_Gateway extends WC_Payment_Gateway
    {

        /**
         * Initialize all the basic credintial
         * @param NULL
         * @return NULL
         */

        public function __construct()
        {
            $this->id = 'ia_payment_gateway';
            $this->icon = false;
            $this->has_fields = true;
            $this->method_title = 'IA Payment Gateways';
            $this->method_description = 'This is the esay and simple payment gateway for customer';
            $this->init_form_fields_for_payment_gateway();
            $this->init_settings();
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->envoirment = $this->get_option('environment');
            $this->enabled = $this->get_option('enabled');
            $this->testmode = 'yes' === $this->get_option( 'environment' );
            $this->private_key = $this->testmode ? $this->get_option( 'trans_key' ) : $this->get_option( 'trans_key' );
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * Create Fields For payment gateway
         * @param NULL
         * @return NULL
         */


        public function init_form_fields_for_payment_gateway()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable IA Gateway',
                    'type'        => 'checkbox',
                    'description' => 'After enable it you can customize it',
                    'default'     => "no"
                ),
                'title' => array(
                    'title'       => 'IA Payment Gateway',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with your credit card via our super-cool payment gateway.',
                    'desc_tip'    => true,
                ),
                'api_login' => array(
                    'title'    => "Stripe Publishable key",
                    'type'    => 'text',
                    'desc_tip'  => true,
                    "description" => 'This is the API Login provided by Authorize.net when you signed up for an account.',
                ),
                'trans_key' => array(
                    'title'    => 'Stripe Transaction Key',
                    'type'    => 'password',
                    'desc_tip'  => true,
                    "description" => 'This is the Transaction Key provided by Authorize.net when you signed up for an account.',
                ),
                'environment' => array(
                    'title'    => 'Stripe Test Mode',
                    'label'    => 'Enable Test Mode',
                    'type'    => 'checkbox',
                    'description' => 'This is the test mode of gateway.',
                    'default'  => 'no',
                )
            );
        }


        /**
         * Create fields to get the credit card information 
         * @param NULL
         * @return NULL
         */



        public function payment_fields()
        {
            if ($this->description) {
                if ($this->envoirment) {
                    $this->description .= '<br> TEST MODE ENABLED. In test mode, you can use the card numbers listed in.';
                    $this->description  = trim($this->description);
                }
                echo wpautop(wp_kses_post($this->description));
            }

            echo '<fieldset id="wc-' . esc_attr($this->id) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';

            do_action('woocommerce_credit_card_form_start', $this->id);

            echo '<div class="form-row form-row-wide"><label>Card Number <span class="required">*</span></label>
                <input id="ia-authorizenet-aim-card-number" name="ia-authorizenet-aim-card-number" type="text" autocomplete="off">
                </div>
                <div class="form-row form-row-wide">
                    <label>Expiry Year <span class="required">*</span></label>
                    <input id="ia-authorizenet-aim-card-expiry-year" name="ia-authorizenet-aim-card-expiry-year" type="number" autocomplete="off" placeholder="YY">
                </div>
                <div class="form-row form-row-wide">
                <label>Expiry Month <span class="required">*</span></label>
                <input id="ia-authorizenet-aim-card-expiry-month" name="ia-authorizenet-aim-card-expiry-month" type="number" autocomplete="off" placeholder="MM">
            </div>
                <div class="form-row form-row-wide">
                    <label>Card Code (CVC) <span class="required">*</span></label>
                    <input id="ia-authorizenet-aim-card-cvc" name="ia-authorizenet-aim-card-cvc" type="password" autocomplete="off" placeholder="CVC">
                </div>
                <div class="clear"></div>';

            do_action('woocommerce_credit_card_form_end', $this->id);

            echo '<div class="clear"></div></fieldset>';
        }

        /**
         * Create Logger which is shown in woocommerce status log
         * @param array or string state_array
         * @return NULL 
         */

        function logForDebugging($state_array)
        {
            $log = new WC_Logger();
            $log_entry = print_r($state_array, true);
            $log->add('WooCommerenceDebugging', $log_entry);
        }


        /**
         * Process Payment and get the payment through Stripe In this function I use Stripe
         * @param $order_id
         * @return NULL 
         */

        public function process_payment($order_id)
        {
            global $woocommerce;
            $customer_order = new WC_Order($order_id);
            $cardNumber = $_POST["ia-authorizenet-aim-card-number"];
            $expYear = $_POST["ia-authorizenet-aim-card-expiry-year"];
            $expMonth = $_POST["ia-authorizenet-aim-card-expiry-month"];
            $cardCvc = $_POST["ia-authorizenet-aim-card-cvc"];

            if (empty($cardNumber) && empty($expYear) && empty($expMonth) && empty($cardCvc)) {
                wc_add_notice(__('<b>Invalid Credit card information Please fill all fields</b> '), 'error');
                return;
            }

            $payload = array(
                "card" => [
                    "number" => $cardNumber,
                    "exp_month" => $expMonth,
                    "exp_year" => $expYear,
                    "cvc" => $cardCvc,
                ],
            );

            $endPoint = "https://api.stripe.com/v1/tokens";

            $tokenCreationResponse = wp_remote_post($endPoint,  array(
                'method'    => 'POST',
                'headers'     => array(
                    'Authorization' => 'Bearer '.$this->private_key,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ),
                'body'      => http_build_query($payload),
                'timeout'   => 90,
                'sslverify' => false,
            ));

            if (is_wp_error($tokenCreationResponse)) {
                $error_message = $tokenCreationResponse->get_error_message();
                echo "Something went wrong: $error_message";
                return;
            }
            $decodeInJson = json_decode($tokenCreationResponse["body"]);

            $payloadForPayment = array(
                'amount' => $customer_order->order_total,
                "currency" => "pkr",
                'source' => $decodeInJson->card->id,
                "receipt_email" => $customer_order->billing_email,
                "shipping" => $customer_order->billing_state . " " . $customer_order->shipping_country,
                "customer" =>  $customer_order->user_id,
            );
            $this->logForDebugging($payloadForPayment);

            $endPointForChargers = "https://api.stripe.com/v1/charges";

            $responseAftergetCharges = wp_remote_post($endPointForChargers, array(
                'method'    => 'POST',
                'body'      => http_build_query($payloadForPayment),
                'timeout'   => 90,
                'sslverify' => false,
            ));

            if (is_wp_error($responseAftergetCharges))
                throw new Exception('There is issue for connectin payment gateway. Sorry for the inconvenience.');
            $finalResponseConvertIntoJson = json_decode($responseAftergetCharges);
            if ($finalResponseConvertIntoJson->status = "succeeded") {
                $customer_order->add_order_note("Stripe Payment completed");
                $customer_order->payment_complete();
                $woocommerce->cart->empty_cart();
                $customer_order->reduce_order_stock();
                $customer_order->update_status('on-hold',  'Awaiting IA Payments');
                $this->logForDebugging("Hello inside if condition");
                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url($customer_order),

                );
            } else {
                $customer_order->add_order_note('Error: Some thing went wrong in transection');
            }
        }
    }
}
