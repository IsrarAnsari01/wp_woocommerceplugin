<?php
add_action('woocommerce_order_status_changed', 'shipped_email_notifications', 20, 4);
function shipped_email_notifications($order_id, $old_status, $new_status, $order)
{
    if ($new_status == 'ia-shipped') {
        $wc_emails = WC()->mailer()->get_emails();
        $wc_emails['WC_Shipped_Email']->trigger($order_id);
    }
    return;
}

