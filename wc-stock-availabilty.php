<?php 
/**
 * Plugin Name: WooCommerce Stock Availability (BETA)
 * Plugin URI: http://arnaim.me/plugins/woo-preorder-mailer
 * Description: This plugin will display WooCommerce pre-order limit status.
 * Version: 1.0.0
 * Author: A R Naim
 * Author URI: http://arnaim.me
 * License: GPL2
 */

define('__PLUGIN_ROOT__', dirname(dirname(__FILE__)) . '/wc-stock-availabilty'); 
require_once(__PLUGIN_ROOT__.'/hooks.php'); 
require_once(__PLUGIN_ROOT__.'/admin-options.php');


function woo_pm_create_ordered_products_table() {

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
register_activation_hook(__FILE__,'woo_pm_create_ordered_products_table');


function woo_pm_drop_ordered_products_table() {

    global $wpdb;
    $table = $wpdb->prefix."wc_ordered_products";

	$wpdb->query("DROP TABLE IF EXISTS $table");
}
register_deactivation_hook( __FILE__, 'woo_pm_drop_ordered_products_table' );