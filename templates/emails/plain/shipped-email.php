<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

$order = $item_data;

echo "= " . $email_heading . " =\n\n";

$opening_paragraph = __('A new order has been made by %s. The details of the item are as follows:', 'shipped_email');

$billing_first_name =  $order->get_billing_first_name();
$billing_last_name = $order->get_billing_last_name();
if ($order && $billing_first_name && $billing_last_name) {
    echo sprintf($opening_paragraph, $billing_first_name . ' ' . $billing_last_name) . "\n\n";
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
foreach ($order->get_items() as $item_id => $item) :
    $product       = $item->get_product();
    if (is_object($product)) {
        $purchase_note = $product->get_purchase_note();
    }
    echo wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false));
    echo ' X ' . apply_filters('woocommerce_email_order_item_quantity', $item->get_quantity(), $item);
    echo ' = ' . $order->get_formatted_line_subtotal($item) . "\n";
    if ($show_purchase_note && $purchase_note) {
        echo "\n" . do_shortcode(wp_kses_post($purchase_note));
    }
    echo "\n\n";
endforeach;

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo __('This is the information email that your product shipped successfully.', 'shipped_email') . "\n\n";

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
