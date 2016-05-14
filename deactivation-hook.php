<?php 

function drop_ordered_products_table() {

    global $wpdb;
    $table = $wpdb->prefix."wc_ordered_products";

	$wpdb->query("DROP TABLE IF EXISTS $table");
}

register_deactivation_hook( __FILE__, 'drop_ordered_products_table' );