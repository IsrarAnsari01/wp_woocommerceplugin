<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class Shipped_Email_manager
{
    function __construct()
    {
        add_filter('woocommerce_email_classes', [$this, 'add_shipped_woocommerce_email']);
        define('CWRFQ_TEMPLATE_DIR', CWRFQ_PLUGIN_DIR . '/templates/');
        add_filter('woocommerce_locate_core_template', array($this, 'locate_core_template'), 99, 3);
    }

    /**
     * Add our Shipped Email in woo commerce existing emails
     * @param array $email_classess contain existing emails
     * @return Updated array $email_classes
     */

    public function add_shipped_woocommerce_email($email_classes)
    {
        require 'shippedEmailConfigration.php';
        $email_classes['WC_Shipped_Email'] = new WC_Shipped_Email();
        return $email_classes;
    }
    /**
     * Add our custom directory in woocommerce
     * @param array $directory | array $template
     * @return Updated file system
     */

    public function locate_core_template($core_file, $template, $template_base)
    {

        $shipped_email_template = array(
            'emails/shipped-email.php',
            'emails/plain/shipped-email.php'
        );

        if (in_array($template, $shipped_email_template)) {
            $core_file = trailingslashit(CWRFQ_TEMPLATE_DIR) . $template;
        }
        var_dump($core_file);
        return $core_file;
    }
}

new Shipped_Email_manager();
