<?php
class dateFieldInCheckout
{
    function __construct()
    {
        add_action('woocommerce_after_order_notes', [$this, 'customer_date_field'], 10, 1);
        add_action('woocommerce_checkout_process', [$this, 'customer_date_field_process']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'customer_dob_field_update_order_meta']);
        add_action('wp_enqueue_scripts', [$this, 'enabling_date_picker']);
    }

    /**
     * This function will enable date picker
     * @param NULL
     * @return  NULL 
     */

    function enabling_date_picker()
    {
        // Only on front-end and checkout page
        if (is_admin() || !is_checkout()) return;

        // Load the datepicker jQuery-ui plugin script
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script("datePickerJs", CW_PLUGIN_DIR . "assets/js/main.js", array("jquery", "jquery-ui-datepicker"), rand(), false);
    }

    /**
     * Add the date field in checkout form
     * @param $checkout | to get the value from fields
     * @return  NULL 
     */

    function customer_date_field($checkout)
    {
        $datepicker_slug = 'ia_datepicker_for_dob';
        echo '<div id="datepicker-wrapper">
        <h2>' . __('Enter your DOB Here') . '</h2>';

        woocommerce_form_field($datepicker_slug, array(
            'type'          => 'text',
            'required'      => true,
            'class'         => array('my-field-class form-row-wide'),
            'label'         => __('Enter your DOB'),
            'placeholder'       => __('Selectn your DOB'),
        ), $checkout->get_value($datepicker_slug));
        echo '</div>';
?>
    
<?php
    }


    /**
     * Process data and check wether user age is enough to bypass our condition or not 
     * @param NULL
     * @return  NULL 
     */

    function customer_date_field_process()
    {
        if (!$_POST['ia_datepicker_for_dob']) {
            wc_add_notice(__('<b>Billing Enter your age</b> is a required field'), 'error');
            return;
        }
        $currentCountry = wc_get_base_location();
        $currentState = $currentCountry['state'];
        $getAllAgeLimitToSetDefault = get_option('ia_configure_restriction');
        $defaultAgeLimit = $getAllAgeLimitToSetDefault["age_restriction"]['Default'];
        $currentStateRistriction = get_option("ia_configure_restriction", $defaultAgeLimit);
        $currentStateRistriction = $currentStateRistriction[$currentState];
        $currentStateRistriction = (int)$currentStateRistriction;
        $secondsInLimitedAge = $currentStateRistriction * 31556952;
        $date1 = $_POST['ia_datepicker_for_dob'];
        $date2 = date('Y-m-d');
        $diff = abs(strtotime($date2) - strtotime($date1));
        if ($diff < $secondsInLimitedAge) {
            wc_add_notice(__('<b>You are not eligibile for check out</b> '), 'error');
            return;
        }
    }
    /**
     * Process data and check wether user is 18 or not
     * @param $order_id | Product ID
     * @return  NULL 
     */

    function customer_dob_field_update_order_meta($order_id)
    {
        $time = strtotime($_POST['customer_date_field']);
        $year = date("Y", $time);
        $currentYear = date('Y');
        $diff = $currentYear - $year;
        if (!empty($_POST['customer_date_field'])) {
            update_post_meta($order_id, 'customer_date_field', sanitize_text_field($diff));
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
new dateFieldInCheckout();
