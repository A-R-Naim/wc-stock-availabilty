<?php 
/**
 * Plugin Name: WooCommerce Pre-order mailer
 * Plugin URI: http://arnaim.me/plugins/woo-preorder-mailer
 * Description: This plugin will display WooCommerce pre-order limit status.
 * Version: 1.0.0
 * Author: A R Naim
 * Author URI: http://arnaim.me
 * License: GPL2
 */

// Define 'PLUGIN_ROOT'
define( 'PLUGIN_ROOT', dirname( dirname(__FILE__) ) . '/woo-preorder-mailer');

// Load customized hooks
require_once( PLUGIN_ROOT . '/hooks.php');

// Plugin admin options
require_once( PLUGIN_ROOT . '/admin-options.php');


/**
 * Create database table after plugin activation
 */
function woo_pm_create_table() {

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
register_activation_hook(__FILE__,'woo_pm_create_table');


/**
 * Drop database table after plugin deactivation
 */
function woo_pm_drop_table() {

    global $wpdb;
    $table = $wpdb->prefix."wc_ordered_products";
	$wpdb->query("DROP TABLE IF EXISTS $table");

}
register_deactivation_hook( __FILE__, 'woo_pm_drop_table' );


/**
 * Admin enqueue
 */
function woo_pm_enqueue_admin_scripts() {
	wp_enqueue_script (  'preorder-modal', PLUGIN_ROOT . 'assets/plugin.js', array('jquery-ui-dialog')); 
    wp_enqueue_style (  'wp-jquery-ui-dialog');
}
add_action( 'admin_enqueue_scripts', 'woo_pm_enqueue_admin_scripts');
