<?php  
require_once(__PLUGIN_ROOT__.'/helper-functions.php');

function wc_stock_availability_placement(){
	global $product;
	$total_units = number_format($product->stock,0,'','');
	$units_sold = get_post_meta( $product->id, 'total_sales', true );
	echo '<p>' . sprintf( __( 'Sold %s out of %s', 'woocommerce' ), $units_sold, $total_units ) . '</p>';

	// global $wpdb;
	// $product_list_id = $wpdb->get_results("SELECT product_id FROM wp_wc_ordered_products WHERE order_id =111");
	// echo '<pre>';
	// print_r( $product_list_id );
	// echo '</pre>';
}

add_action( 'woocommerce_after_shop_loop_item', 'wc_stock_availability_placement', 6);	
add_action( 'woocommerce_before_add_to_cart_form', 'wc_stock_availability_placement', 10, 0);


function hide_default_stock_message(){
	echo '<style> .in-stock{display:none;} </style>';
}

add_action( 'wp_head', 'hide_default_stock_message', 11, 0 );


function wc_stock_availability_admin_menu() {
	add_menu_page('WC Stock Availability', 'WC Stock', 'administrator', 'wc_stock_availability_admin_settings', 'wc_stock_availability_settings_page', 'dashicons-admin-generic');
}
add_action('admin_menu', 'wc_stock_availability_admin_menu');


function wc_stock_availability_admin_settings() {
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_name' );
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_phone' );
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_email' );
}
add_action( 'admin_init', 'wc_stock_availability_admin_settings' );


function action_woocommerce_payment_complete( $order_id ) { 

    $order = new WC_Order( $order_id );
	$items = $order->get_items();

	foreach ( $items as $item ) {
	    $product_qty = $item['qty'];
	    // $product_name = $item['name'] . '<br>';
	    $product_id = $item['product_id'];

	    insert_data_into_ordered_tabale( $order_id, $product_id, $product_qty );

	    if ( is_preorder_exceded( $product_id ) ) {
	    	echo 'do something cause preorder limit exceded';
	    	send_email_to_previous_customer( $order_id );
	    } else{
	    	echo 'pre-order limit isn\'t excedet yet';
	    }

	}

}     
add_action( 'woocommerce_payment_complete', 'action_woocommerce_payment_complete', 10, 1 ); 



function custom_thankyou_message( $this_id ) {
	do_action( 'woocommerce_payment_complete',  $this_id ); 
}
add_action ('woocommerce_thankyou', 'custom_thankyou_message');