<?php 

// Load helper functions
require_once( PLUGIN_ROOT . '/helper-functions.php' );


/**
 * Add some CSS styles
 */
function woo_pm_styles(){

	echo "<style>.woo-preorder-box{background-color: silver;height: 22px;}.preorder-status-bar{color: #fff;line-height: 22px;height: 22px;background-color: #df1f26;text-align:center;border-radius: 0px 10px 10px 0px;max-width:100%;} </style>";
}
add_action( 'wp_head', 'woo_pm_styles', 11, 0 );


/**
 * Display order visual status 
 * @return string
 */
function woo_pm_product_status_bar(){

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
// added on product loop
add_action( 'woocommerce_after_shop_loop_item', 'woo_pm_product_status_bar', 6 );
// added on single product	
add_action( 'woocommerce_before_add_to_cart_form', 'woo_pm_product_status_bar', 8, 0 );



/**
 * Action after WooCommerce payment complete
 * @param  int $order_id
 */
function action_woocommerce_payment_complete( $order_id ) { 

    $order = new WC_Order( $order_id );
	$items = $order->get_items();

	foreach ( $items as $item ) {
		$product_name = $item['name'];
		$product_qty  = $item['qty'];
		$product_id   = $item['product_id'];

		// insert data into 'wc_ordered_products' table
	    woo_pm_insert_data_into_table( $order_id, $product_id, $product_qty );

	    // check if preorder limis is exceded
	    if ( woo_pm_is_preorder_exceded( $product_id ) ) {

	    	//make sure one mail after reaching to the preorder limit
	    	$is_notified = get_post_meta($product_id, 'is_notified', true);

	    	if ( !$is_notified ) {
	    		// send email to customer if product reach to the preorder limit
	    		woo_pm_send_email( $product_id, $product_name );
	    		// so it should update only once for one product 
	    		update_post_meta($product_id, 'is_notified', true);
	    	}
	    } #end if 
	} #end-foreach

}     
add_action( 'woocommerce_payment_complete', 'action_woocommerce_payment_complete', 10, 1 ); 


/**
 * Get data after successfully created order post
 */
function custom_thankyou_message( $this_id ) {
	do_action( 'woocommerce_payment_complete',  $this_id ); 
}
add_action ('woocommerce_thankyou', 'custom_thankyou_message');


/**
 * Add product meta fields
 */
function woo_pm_add_product_fields() {

  global $woocommerce, $post;
  echo '<div class="options_group">';
  woocommerce_wp_text_input( 
		array( 
			'id'          => '_preorder_limit', 
			'label'       => __( 'Pre-order Limit', 'woocommerce' ),
		)
	);
  echo '</div>';
	
}
add_action( 'woocommerce_product_options_general_product_data', 'woo_pm_add_product_fields' );


/**
 * Save product's meta fields data
 */
function woo_pm_add_product_fields_save( $post_id ){
	
	$woocommerce_preorder_field = $_POST['_preorder_limit'];
	if( !empty( $woocommerce_preorder_field ) ){
		if ( is_numeric( $woocommerce_preorder_field) )
		update_post_meta( $post_id, '_preorder_limit', $woocommerce_preorder_field );
	}

}
add_action( 'woocommerce_process_product_meta', 'woo_pm_add_product_fields_save' );