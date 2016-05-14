<?php 

function insert_data_into_ordered_tabale( $order_id, $product_id, $product_qty ){
	global $wpdb;

    $author_id = $wpdb->get_var("SELECT post_author FROM $wpdb->posts WHERE $wpdb->posts.ID = 
    	$product_id");
    $author_email = $wpdb->get_var("SELECT meta_value FROM $wpdb->usermeta WHERE user_id = $author_id AND meta_key = 'billing_email'");

    $wpdb->insert( $wpdb->prefix.'wc_ordered_products', array(
		'product_id'   => $product_id,
		'order_id'     => $order_id,
		'order_qty'    => $product_qty,
		'author_email' => $author_email
		)
	);
}

function is_preorder_exceded( $product_id ){
	// $total_units = get_post_meta( $product_id, '_stock', true );
	$units_sold  = get_post_meta( $product_id, 'total_sales', true );
	$preorder_unit = 5;

	return $units_sold > $preorder_unit;
}

function send_email_to_previous_customer( $order_id ){

}