<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/*
   Add Custom User name as seller which is exact similar to subscriber -->
 */
function add_new_user_role() {
    add_role(
        'seller', //  System name of the role.
        __( 'Seller'  ), // Display name of the role.
        array(
            'read'  => true,
        )
        );
}
add_action('init', "add_new_user_role");