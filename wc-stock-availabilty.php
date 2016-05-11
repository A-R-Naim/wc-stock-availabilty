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


function shop_inventory() {

  global $product;
  global $shop_order;
  $total_units = number_format($product->stock,0,'','');
  $units_sold = get_post_meta( $product->id, 'total_sales', true );

  	$order = new WC_Order(105);
	$items = $order->get_items();
	foreach ( $items as $item ) {
	  echo $product_id = $item['product_id'] . '<br/>';
	  echo $product_name = $item['name'] . '<br>' ;
	  echo $product_variation_id = $item['variation_id'] . '<br>';
	}

  // if ( $units_sold >= $total_units ) {
  //   echo 'send email';
  // } else{
  // 	echo 'don\'t execute mail';
  // }

}

add_action( 'woocommerce_after_shop_loop_item', 'shop_inventory', 8);