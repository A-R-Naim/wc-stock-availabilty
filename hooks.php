<?php  

require_once(__PLUGIN_ROOT__.'/helper-functions.php');
require_once(__PLUGIN_ROOT__.'/admin-options.php');

function woo_pm_admin_menu() {

	add_menu_page('Woo pre-order mailer', 'WOO PMailer', 'administrator', 'woo_pm_admin_settings', 'woo_pm_admin_settings_page', 'dashicons-admin-generic');

}
add_action('admin_menu', 'woo_pm_admin_menu');


function woo_pm_admin_settings() {

	register_setting( 'woo_pm_settings_group', 'woo_pm_status_bar' );
	register_setting( 'woo_pm_settings_group', 'woo_pm_status_bg' );
	register_setting( 'woo_pm_settings_group', 'woo_pm_status_text' );
	register_setting( 'woo_pm_settings_group', 'woo_pm_disable_on_single' );

}
add_action( 'admin_init', 'woo_pm_admin_settings' );



function woo_pm_visual_status_style(){

	$status_bar_color  = get_option('woo_pm_status_bar');
	$status_bg_color   = get_option('woo_pm_status_bg');
	$status_text_color = get_option('woo_pm_status_text');

	echo "<style>.woo-preorder-box{background-color: $status_bg_color;height: 22px;}.preorder-status-bar{    color: $status_text_color;line-height: 22px;height: 22px;background-color: $status_bar_color;text-align:center;    border-radius: 0px 10px 10px 0px;max-width:100%;} </style>";
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

$disable_status_on_single = get_option( 'woo_pm_disable_on_single' );

if ( $disable_status_on_single == 'no') {
	add_action( 'woocommerce_before_add_to_cart_form', 'woo_pm_visual_status_placement', 12, 0);
}	


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
	
	if( !empty( $woocommerce_preorder_field ) ){
		if ( is_numeric( $woocommerce_preorder_field) )
		update_post_meta( $post_id, '_preorder_limit', $woocommerce_preorder_field );
	}

}
add_action( 'woocommerce_process_product_meta', 'woo_pm_add_preorder_fields_save' );
                                                   