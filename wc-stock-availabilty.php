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

define('__PLUGIN_ROOT__', dirname(dirname(__FILE__)) . '/wc-stock-availabilty'); 
require_once(__PLUGIN_ROOT__.'/activation-hook.php'); 
require_once(__PLUGIN_ROOT__.'/hooks.php'); 
require_once(__PLUGIN_ROOT__.'/admin-options.php');
require_once(__PLUGIN_ROOT__.'/deactivation-hook.php');
