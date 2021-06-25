<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WC_Shipped_Email extends WC_Email
{
    /**
     * Initialize basic setting and get basic values 
     * @param NULL
     * @return NULL
     */

    public function __construct()
    {

        $this->id = 'wc_shipped_order';
        $this->title = 'Shipped';
        $this->description = 'Shipped Order Notification emails are sent when a customer product successfully shipped | This Email will be send to customer';
        $this->heading = 'Shipped Order';
        $this->subject = 'Shipped Order';
        $this->template_html  = 'emails/shipped-email.php';
        $this->template_plain = 'emails/plain/shipped-email.php';
        $this->template_base = CWRFQ_PLUGIN_DIR . '/templates/';
        add_action('woocommerce_order_status_pending_to_processing_notification', array($this, 'trigger'));
        add_action('woocommerce_order_status_failed_to_processing_notification',  array($this, 'trigger'));
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();
    }

    /**
     * Get email subject.
     *
     * @return string
     */
    public function get_subject()
    {
        $subject = $this->get_option('subject', $this->get_default_subject());
        return apply_filters('woocommerce_email_subject_customer_shipped_order', $this->format_string($subject), $this->object, $this);
    }

    /**
     * Get email heading.
     *
     * @return string
     */
    public function get_heading()
    {
        $heading = $this->get_option('heading', $this->get_default_heading());
        return apply_filters('woocommerce_email_heading_customer_shipped_order', $this->format_string($heading), $this->object, $this);
    }



    /**
     * Initialize basic setting and get basic values 
     * @param int $order_id
     * @return NULL
     */


    public function trigger($order_id)
    {
        if (!$order_id) {
            $this->logForDebugging("Can't find order id");
            return;
        }
        $this->object = new WC_Order($order_id);
        $this->find[] = '{order_date}';
        $this->replace[] = date_i18n(wc_date_format(), strtotime($this->object->order_date));
        $this->find[] = '{order_number}';
        $this->replace[] = $this->object->get_order_number();
        $order_email = $this->object->get_billing_email();
        $this->send($order_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
    }

    /**
     * Getting html content  
     * @param NULL
     * @return HTML template
     */

    public function get_content_html()
    {
        ob_start();
        wc_get_template(
            $this->template_html,
            array(
                'item_data'         => $this->object,
                'email_heading' => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'plain_text'         => false,
                'email'              => $this,

            ),
            "shipped-email",
            $this->template_base
        );
        return ob_get_clean();
    }

    /**
     * Getting content plain  
     * @param NULL
     * @return plain
     */

    public function get_content_plain()
    {
        ob_start();
        wc_get_template(
            $this->template_plain,
            array(
                'item_data'              => $this->object,
                'email_heading'      => $this->get_heading(),
                'additional_content' => $this->get_additional_content(),
                'plain_text'         => true,
                'email'              => $this,
            ),
            "shipped-email",
            $this->template_base
        );
        return ob_get_clean();
    }

    /**
     * Initialize form Fields that will render in configuration page  
     * @param NULL
     * @return NULL
     */

    public function init_form_fields()
    {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable this email notification',
                'default' => 'yes'
            ),
            'recipient'  => array(
                'title'       => 'Recipient(s)',
                'type'        => 'text',
                'description' => sprintf('Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr(get_option('admin_email'))),
                'placeholder' => 'Enter Recipient here',
                'default'     => ''
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => sprintf('This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject),
                'placeholder' => 'Enter Email Subject here',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => 'Email Heading',
                'type'        => 'text',
                'description' => sprintf(__('This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.'), $this->heading),
                'placeholder' => 'Enter Email Heading here',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => 'Email type',
                'type'        => 'select',
                'description' => 'Choose which format of email to send.',
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => 'Plain text',
                    'html'      => 'HTML', 'woocommerce',
                    'multipart' => 'Multipart', 'woocommerce',
                )
            )
        );
    }
    /**
     * Custom funtion for debugging
     * @param array | String $state_array accept array and string that will be log
     * @return NULL
     */
    public function logForDebugging($state_array)
    {
        $log = new WC_Logger();
        $log_entry = print_r($state_array, true);
        $log->add('WooCommerenceDebugging', $log_entry);
    }
}
