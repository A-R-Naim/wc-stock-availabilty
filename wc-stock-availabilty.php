<?php 
/**
 * Plugin Name: WooCommerce Stock Availability (BETA)
 * Plugin URI: http://arnaim.me/plugins/wc-stock-availabilty
 * Description: This plugin will display WooCommerce product's stock availability status.
 * Version: 1.0.0
 * Author: A R Naim
 * Author URI: http://arnaim.me
 * License: GPL2
 */

function wc_stock_availability_placement(){
	global $product;
	$total_units = number_format($product->stock,0,'','');
	$units_sold = get_post_meta( $product->id, 'total_sales', true );
	echo '<p>' . sprintf( __( 'Sold %s out of %s', 'woocommerce' ), $units_sold, $total_units ) . '</p>';

	global $wpdb;
	$product_id = 37;

	// get_author_meta();
    $author_id = $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE $wpdb->posts.ID = $product_id");

    $author_email = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $author_id AND meta_key = 'billing_email'");

    echo $author_email;
}

add_action( 'woocommerce_after_shop_loop_item', 'wc_stock_availability_placement', 6);	
add_action( 'woocommerce_before_add_to_cart_form', 'wc_stock_availability_placement', 10, 0);


// 
function hide_default_stock_message(){
	echo '<style> .in-stock{display:none;} </style>';
}

add_action( 'wp_head', 'hide_default_stock_message', 11, 0 );


// 
function wc_stock_availability_admin_menu() {
	add_menu_page('WC Stock Availability', 'WC Stock', 'administrator', 'wc_stock_availability_admin_settings', 'wc_stock_availability_settings_page', 'dashicons-admin-generic');
}
add_action('admin_menu', 'wc_stock_availability_admin_menu');


// 
function wc_stock_availability_admin_settings() {
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_name' );
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_phone' );
	register_setting( 'wc_stock_availability_settings_group', 'wc_sa_email' );
}
add_action( 'admin_init', 'wc_stock_availability_admin_settings' );

// 
function wc_stock_availability_settings_page(){ ?>
	<div class="wrap">
	<h2>Settings</h2>

	<form method="post" action="options.php">
	    <?php settings_fields( 'wc_stock_availability_settings_group' ); ?>
	    <?php do_settings_sections( 'wc_stock_availability_settings_group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Accountant Name</th>
	        <td><input type="text" name="wc_sa_name" value="<?php echo esc_attr( get_option('wc_sa_name') ); ?>" /></td>
	        </tr>
	         
	        <tr valign="top">
	        <th scope="row">Accountant Phone Number</th>
	        <td><input type="text" name="wc_sa_phone" value="<?php echo esc_attr( get_option('wc_sa_phone') ); ?>" /></td>
	        </tr>
	        
	        <tr valign="top">
	        <th scope="row">Accountant Email</th>
	        <td><input type="text" name="wc_sa_email" value="<?php echo esc_attr( get_option('wc_sa_email') ); ?>" /></td>
	        </tr>
	    </table>
	    
	    <?php submit_button(); ?>

	</form>
	</div>
<?php }


// 
function shop_inventory() {

  global $product;
  global $shop_order;
  $total_units = number_format($product->stock,0,'','');
  $units_sold = get_post_meta( $product->id, 'total_sales', true );

 //  	$order = new WC_Order(105);
	// $items = $order->get_items();
	// foreach ( $items as $item ) {
	//   echo $product_id = $item['product_id'] . '<br/>';
	//   echo $product_name = $item['name'] . '<br>' ;
	//   echo $product_variation_id = $item['variation_id'] . '<br>';
	// }

  if ( $units_sold >= $total_units ) {
    echo 'send email';
  } else{
  	echo 'don\'t execute mail';
  }
  // 
 //  $args = array(
	//   'post_type' => 'shop_order',
	//   'post_status' => 'publish',
	//   'meta_key' => '_customer_user',
	//   'posts_per_page' => '-1'
	// );
	// $my_query = new WP_Query($args);

	// $customer_orders = $my_query->posts;

	// foreach ($customer_orders as $customer_order) {
	//  $order = new WC_Order();

	//  $order->populate($customer_order);
	//  $orderdata = (array) $order;
	//  echo '<pre>';
	//  print_r( $orderdata );
	 
	//  echo '<pre/>';

	// }
}

function is_preorder_exceded( $product_id ){
	$total_units = get_post_meta( $product_id, '_stock', true );
	// $units_sold  = get_post_meta( $product_id, 'total_sales', true );
	$preorder_unit = 2;

	return $total_units < $preorder_unit;
}

function action_woocommerce_payment_complete( $order_id ) { 

    $order = new WC_Order( $order_id );
	$items = $order->get_items();

	foreach ( $items as $item ) {
	    $product_qty = $item['qty'];
	    // ${ 'sold_qty_of_' . $product_id } = $item['qty'] . '<br/><br/>';
	    // $product_name = $item['name'] . '<br>';
	    $product_id = $item['product_id'] . '<br>';
	    if ( is_preorder_exceded( $product_id ) ) {

	    }
	}

}     

add_action( 'woocommerce_payment_complete', 'action_woocommerce_payment_complete', 10, 1 ); 



function custom_thankyou_message( $this_id ) {
	do_action( 'woocommerce_payment_complete',  $this_id ); 
}
add_action ('woocommerce_thankyou', 'custom_thankyou_message');
