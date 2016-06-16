<?php 

/**
 * Insert data into database table
 * @param  int $order_id    
 * @param  int $product_id  
 * @param  int $product_qty 
 */
function woo_pm_insert_data_into_table( $order_id, $product_id, $product_qty ){

	global $wpdb;
    $author_id = $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE $wpdb->posts.ID = 
    	$order_id");
    $author_email = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $order_id AND meta_key = '_billing_email'");
    $wpdb->insert( $wpdb->prefix.'wc_ordered_products', array(
		'product_id'   => $product_id,
		'order_id'     => $order_id,
		'order_qty'    => $product_qty,
		'author_email' => $author_email
		)
	);

}

/**
 * Check preorder limit status
 * @param  int $product_id 
 * @return boolean             
 */
function woo_pm_is_preorder_exceded( $product_id ){

	$units_sold    = get_post_meta( $product_id, 'total_sales', true );
	$preorder_unit = get_post_meta( $product->id, '_preorder_limit', true );

	return $units_sold >= $preorder_unit;
}

/**
 * Send preorder limit reaching notification mail to customer
 * @param  int $product_id   
 * @param  string $product_name 
 */
function woo_pm_send_email( $product_id, $product_name ){

	global $wpdb;
	$table_name = $wpdb->prefix . 'wc_ordered_products';
	$customer_emails = $wpdb->get_results("SELECT author_email FROM $table_name WHERE product_id = $product_id" );

	foreach ($customer_emails as $email) {
		$to      = $email->author_email;
		$subject = "Product $product_name just reached to limit";
		$message = esc_html( 'Your orderd product just reached to the preorder limit, so i\'ll trigger soon', 'text-domain' );
		wp_mail( $to, $subject, $message );
	}

}