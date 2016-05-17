<?php 

function woo_pm_insert_data_into_ordered_tabale( $order_id, $product_id, $product_qty ){

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

	$units_sold    = get_post_meta( $product_id, 'total_sales', true );
	$preorder_unit = get_post_meta( $product->id, '_preorder_limit', true );

	return $units_sold >= $preorder_unit;
}


function woo_pm_send_email_to_ordered_customer( $product_id, $product_name ){

	global $wpdb;

	$table_name = $wpdb->prefix . 'wc_ordered_products';
	$customer_emails = $wpdb->get_results("SELECT author_email FROM $table_name WHERE product_id = $product_id" );

	foreach ($customer_emails as $email) {
		$to      = $email->author_email;
		$subject = get_option('woo_pm_mail_subject');
		$message = get_option('woo_pm_mail_content');
		wp_mail( $to, $subject, $message );
	}

}