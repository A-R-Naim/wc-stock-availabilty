<?php 

// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'create_orderd_product_table');

function create_orderd_product_table() {

	global $wpdb;
	$table_name = $wpdb->prefix . 'wc_ordered_products';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		product_id int NOT NULL,
		order_id bigint(20) NOT NULL,
		order_qty int(11) NOT NULL,
		author_email varchar(100) NOT NULL,
		UNIQUE KEY id (id)
	);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}