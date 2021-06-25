<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
$order = $item_data;
$opening_paragraph = __('A new order has been made by %s. The details of the item are as follows:', 'shipped_email');
$text_align  = is_rtl() ? 'right' : 'left';
$margin_side = is_rtl() ? 'left' : 'right';
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<?php
$billing_first_name = $order->get_billing_first_name();
$billing_last_name = $order->get_billing_last_name();
if ($order && $billing_first_name && $billing_last_name) : ?>
    <p><?php printf($opening_paragraph, $billing_first_name . ' ' . $billing_last_name); ?></p>
<?php endif;
$totalAmmount = 0;
?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
    <th>
        <h2> Your order Information </h2>
    </th>
    <tbody>
        <?php
        foreach ($order->get_items() as $item_id => $item) :
            $product       = $item->get_product();
        ?>
            <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
                <th scope="row" class="td" style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;"><?php _e('Ordered Product Name', 'shipped_email'); ?></th>
                <td style="text-align:left; border: 1px solid #eee;">
                    <?php
                    echo wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false));
                    ?>
                </td>
            </tr>
            <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
                <th scope="row" class="td" style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;"><?php _e('Ordered Quantity Quantity', 'shipped_email'); ?></th>
                <td style="text-align:left; border: 1px solid #eee;">
                    <?php
                    $qty          = $item->get_quantity();
                    $qty_display = esc_html($qty);
                    echo wp_kses_post(apply_filters('woocommerce_email_order_item_quantity', $qty_display, $item));
                    ?>
                </td>
            </tr>
            <?php
            $totalAmmount += (int)$item->get_total();
            ?>
        <?php endforeach; ?>
        <tr>
            <th scope="row" class="td" style="text-align:<?php echo esc_attr($text_align); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;"><?php _e('Ordered Product Total ammount', 'shipped_email'); ?></th>
            <td style="text-align:left; border: 1px solid #eee;">
                <?php echo $totalAmmount; ?>
            </td>
        </tr>
    </tbody>
</table>
<p><?php _e('This is the information email that your product shipped successfully .', 'shipped_email'); ?></p>
<?php do_action('woocommerce_email_footer'); ?>