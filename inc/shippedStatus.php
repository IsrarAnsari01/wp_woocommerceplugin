<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class shippedStatus
{

    function __construct()
    {
        add_action('init', [$this, 'register_ia_shipped_order_status']);
        add_filter('wc_order_statuses', [$this, 'add_shipped_to_order_statuses']);
    }

    /**
     * Register new Order status in woocommerce old statuses
     * @param NULL
     * @return NULL
     */

    function register_ia_shipped_order_status()
    {
        register_post_status('wc-ia-shipped', array(
            'label'                     => 'Shipped',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Shipped (%s)', 'Shipped (%s)')
        ));
    }

    /**
     * Register and rendering our new shipped status in woocommerce old statuses
     * @param array $order_statuses contain all saved statuses
     * @return updated statuses
     */

    public function add_shipped_to_order_statuses($order_statuses)
    {
        $new_order_statuses = array();
        foreach ($order_statuses as $key => $status) {
            $new_order_statuses[$key] = $status;
            if ('wc-processing' === $key) {
                $new_order_statuses['wc-ia-shipped'] = 'Shipped';
            }
        }
        return $new_order_statuses;
    }
}
new shippedStatus();
