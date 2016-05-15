<?php  

require_once(__PLUGIN_ROOT__.'/helper-functions.php');

function woo_pm_admin_menu() {

	add_menu_page('WC Stock Availability', 'WC Stock', 'administrator', 'woo_pm_admin_settings', 'wc_stock_availability_settings_page', 'dashicons-admin-generic');

}
add_action('admin_menu', 'woo_pm_admin_menu');


function woo_pm_admin_settings() {

	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_name' );
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_phone' );
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_email' );

}
add_action( 'admin_init', 'woo_pm_admin_settings' );



function woo_pm_visual_status_style(){
	echo '<style>.woo-preorder-box{background-color: rgb(235, 233, 235);height: 22px;}.preorder-status-bar{    color: #ffffff;line-height: 22px;height: 22px;background-color: #77A464;text-align:center;    border-radius: 0px 10px 10px 0px;max-width:100%;} </style>';
}
add_action( 'wp_head', 'woo_pm_visual_status_style', 11, 0 );


function woo_pm_visual_status_placement(){

	global $product;

	$preorder_limit = get_post_meta( $product->id, '_preorder_limit', true );
	$units_sold     = get_post_meta( $product->id, 'total_sales', true );

	if ( $preorder_limit !== '' ) { 
		$status = ( $units_sold / $preorder_limit ) * 100 ; ?>
		<div class="woo-preorder-box">
			<div class="preorder-status-bar" style="width:<?php echo $status; ?>%">
				<?php echo sprintf( __( '%s of %s', 'woocommerce' ), $units_sold, $preorder_limit );  ?>
			</div>
		</div>
	<?php }

}

add_action( 'woocommerce_after_shop_loop_item', 'woo_pm_visual_status_placement', 6);	
//add_action( 'woocommerce_before_add_to_cart_form', 'woo_pm_visual_status_placement', 10, 0);


function action_woocommerce_payment_complete( $order_id ) { 

    $order = new WC_Order( $order_id );
	$items = $order->get_items();

	foreach ( $items as $item ) {
		$product_name = $item['name'];
		$product_qty  = $item['qty'];
		$product_id   = $item['product_id'];

	    woo_pm_insert_data_into_ordered_tabale( $order_id, $product_id, $product_qty );

	    if ( is_preorder_exceded( $product_id ) ) {

	    	$is_notified = get_post_meta($product_id, 'is_notified', true);

	    	if ( !$is_notified ) {
	    		woo_pm_send_email_to_ordered_customer( $product_id, $product_name );
	    		update_post_meta($product_id, 'is_notified', true);
	    	}

	    } #end if 
	    

	} #end-foreach

}     
add_action( 'woocommerce_payment_complete', 'action_woocommerce_payment_complete', 10, 1 ); 



function woo_pm_action_socket_to_thankyou( $this_id ) {
	do_action( 'woocommerce_payment_complete',  $this_id ); 
}
add_action ('woocommerce_thankyou', 'woo_pm_action_socket_to_thankyou');



function woo_pm_add_preorder_fields() {

  global $woocommerce, $post;
  
  echo '<div class="options_group">';
  woocommerce_wp_text_input( 
		array( 
			'id'          => '_preorder_limit', 
			'label'       => __( 'Pre-order Limit', 'woocommerce' )
		)
	);
  echo '</div>';
	
}
add_action( 'woocommerce_product_options_general_product_data', 'woo_pm_add_preorder_fields' );



function woo_pm_add_preorder_fields_save( $post_id ){
	
	$woocommerce_preorder_field = $_POST['_preorder_limit'];
	if( !empty( $woocommerce_preorder_field ) )
		update_post_meta( $post_id, '_preorder_limit', esc_attr( $woocommerce_preorder_field ) );

}
add_action( 'woocommerce_process_product_meta', 'woo_pm_add_preorder_fields_save' );
                                                   