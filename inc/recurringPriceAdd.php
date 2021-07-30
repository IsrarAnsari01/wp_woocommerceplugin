<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class recurringPriceAdd
{
    /** Load initial functions
     * @param NULL
     * @return NULL
     */
    function __construct()
    {
        add_filter('wcs_renewal_order_created', [$this, 'add_price_in_second_time_of_recurring'], 10, 2);
    }
    /** Add 100 Rs in every recurring after purchase
     * @param object $renewal_order object $subscription
     * @return object updated object
     */
    public function add_price_in_second_time_of_recurring($renewal_order, $subscription)
    {
        $item = new WC_Order_Item_Fee();
        $item->set_name("Monthly Fees");
        $item->set_total(100);
        $item->save();
        $renewal_order->add_item($item);
        $renewal_order->calculate_totals();
        return $renewal_order;
    }
}
new recurringPriceAdd();
