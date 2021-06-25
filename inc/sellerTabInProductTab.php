<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * This function will create table head
 * @param array $tabs 
 * @return updated array $tabs
 */
add_filter('woocommerce_product_data_tabs', 'seller_product_settings_tabs');
function seller_product_settings_tabs($tabs)
{
    $tabs['seller'] = array(
        'label'    => 'Seller Information',
        'target'   => 'seller_information_product_data',
        'class'    => array('show_if_simple'),
        'priority' => 21,
    );
    return $tabs;
}
/**
 * This is table content which is open on click
 * @param NULL
 * @return NULL
 */
add_action('woocommerce_product_data_panels', 'seller_information_product_panel');
function seller_information_product_panel()
{
    echo '<div id="seller_information_product_data" class="panel woocommerce_options_panel hidden">';
    woocommerce_wp_text_input(array(
        'id'                => 'seller_name',
        'label'             => 'Seller Name',
        'placeholder' => 'Enter your name here',
        'description'       => 'Enter Seller Name here',
        'desc_tip'    => true
    ));
    woocommerce_wp_text_input(array(
        'id'                => 'seller_email',
        'label'             => 'Seller Email',
        'placeholder' => 'Enter your email here',
        'description'       => 'Enter Seller Email here',
        'desc_tip'    => true
    ));
    woocommerce_wp_text_input(array(
        'id'                => 'seller_number',
        'label'             => 'Seller number',
        'placeholder' => 'Enter your number here',
        'description'       => 'Enter Seller Number',
        'desc_tip'    => true
    ));
    echo '</div>';
}
/**
 * Saving data of custom field
 * @param NULL
 * @return NULL
 */
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields_save()
{
    $post_id = get_the_ID();
    // Custom Product Text Field
    $woocommerce_custom_product_name_field = $_POST['seller_name'];
    if (!empty($woocommerce_custom_product_name_field))
        update_post_meta($post_id, 'seller_name', esc_attr($woocommerce_custom_product_name_field));
    // Custom Product Number Field
    $woocommerce_custom_product_email_field = $_POST['seller_email'];
    if (!empty($woocommerce_custom_product_email_field))
        update_post_meta($post_id, 'seller_email', esc_attr($woocommerce_custom_product_email_field));
    // Custom Product Textarea Field
    $woocommerce_custom_product_number_field = $_POST['seller_number'];
    if (!empty($woocommerce_custom_product_number_field))
        update_post_meta($post_id, 'seller_number', esc_html($woocommerce_custom_product_number_field));
}

/**
 * Change table head ICON
 * @param NULL
 * @return NULL
 */
add_action('admin_head', 'seller_css_icon');
function seller_css_icon()
{
    echo '<style>
	#woocommerce-product-data ul.wc-tabs li.seller_options.seller_tab a:before{
		content: "\f110";
	}
	</style>';
}


/**
 * Retrive Meta information
 * @param NULL
 * @return NULL
 */

function sellerInformation()
{
    $Seller_Name = get_post_meta(get_the_ID(), 'seller_name', true);
    $Seller_Mail_Id = get_post_meta(get_the_ID(), 'seller_email', true);
    $Seller_Contact_Number  = get_post_meta(get_the_ID(), 'seller_number', true);
?>
    <h2>Seller Details: </h2>
    <p>Seller Name: <b><?php echo $Seller_Name; ?></b></p>
    <p>Seller Email: <b><?php echo $Seller_Mail_Id; ?></b></p>
    <p>Seller Contact Number: <b><?php echo $Seller_Contact_Number; ?></b></p>


<?php
}
add_action('woocommerce_product_meta_end', 'sellerInformation');
