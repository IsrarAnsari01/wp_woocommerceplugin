<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class donationFieldInCheckout
{
    function __construct()
    {
        add_action('woocommerce_after_order_notes', [$this, 'donate_checkbox'], 10, 1);
        add_action('woocommerce_cart_calculate_fees', [$this, "donateFees"]);
        add_action('wp_enqueue_scripts', [$this, 'runtime_update']);
    }
    function runtime_update()
    {
        wp_enqueue_script("doneupdate", CW_PLUGIN_DIR . "assets/js/main.js", array("jquery"), rand(), false);
    }
    /**
     * Add Checkout for donation box
     * @param $checkout | to get the value from fields
     * @return  NULL 
     */

    function donate_checkbox($checkout)
    {
        $checkBox_slug = 'ia_donate_box';
        echo '<div id="donateCheckWrapper">
        <h4>' . __('Donate 100Rs for poor family') . '</h4>';

        woocommerce_form_field($checkBox_slug, array(
            'type'          => 'checkbox',
            'class'     => array('input-checkbox'),
            'label'         => __('Donate 100Rs'),
        ), $checkout->get_value($checkBox_slug));
        echo '</div>';
?>
    
<?php
    }

    /**
     * If user checked tha box then we will add 100 RS in product order
     * @param $card| Specific card
     * @return  NULL 
     */
    function donateFees($cart)
    {
        if (isset($_POST['post_data'])) {
            parse_str($_POST['post_data'], $post_data);
        } else {
            $post_data = $_POST;
        }
        if (isset($post_data['ia_donate_box'])) {
            $donation_fee = 100.00;
            WC()->cart->add_fee('Donation Fees', $donation_fee);
        }
    }
    /**
     * Custom funtion for debugging
     * @param array | String $state_array accept array and string that will be log
     * @return NULL
     */
    function logForDebugging($state_array)
    {
        $log = new WC_Logger();
        $log_entry = print_r($state_array, true);
        $log->add('WooCommerenceDebugging', $log_entry);
    }
}
new donationFieldInCheckout();
