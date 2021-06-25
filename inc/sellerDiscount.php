<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class sellerDiscount
{
    function __construct()
    {
        add_filter('woocommerce_get_price_html', [$this, 'change_price_for_seller'], 10, 2);
        add_action('woocommerce_before_calculate_totals', [$this, 'change_product_price_dynamicaly']);
    }
    /*
    * Create Discount for seller
    * @param $price | price of product & $_product | product details
    * @return string discounted price
    */
    function change_price_for_seller($price, $_product)
    {

        if (wc_current_user_has_role('seller')) {
            $orig_price = wc_get_price_to_display($_product);
            $price = wc_price($orig_price * 0.90);
        }

        return $price;
    }
    /*
    Reflect change in product card
    * @param $cart | get product card to update discounted price
    * @return null
    */
    function change_product_price_dynamicaly($cart)
    {
        if (!wc_current_user_has_role('seller')) return;
        foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $price = $product->get_price();
            $cart_item['data']->set_price($price * 0.90);
        }
    }
}
new sellerDiscount();
