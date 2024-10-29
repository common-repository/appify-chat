<?php

/*
 * Plugin Name:       AppifyCommerce - Social Chat
 * Description:       Let your store visitors contact you on your provided WhatsApp number.
 * Version:           1.0.5
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            AppifyCommerce
 * Author URI:        https://www.appifycommerce.com/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Use a prefix for your functions
include_once plugin_dir_path(__FILE__) . 'includes/hooks.php';
require_once plugin_dir_path(__FILE__) . '/includes/acwc-functions.php';

/**
 * Function to create table and insert/update record upon plugin activation
 */

function acwc_activation() {
    acwc_create_table();
    acwc_insert_record();
    acwc_store_unique_id();
}
register_activation_hook(__FILE__, 'acwc_activation');

// Settings link
function acwc_plugin_settings_link($links) {
    $settings_link = '<a href="admin.php?page=appifychat-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'acwc_plugin_settings_link');

// Provide a deactivation hook for cleanup
function acwc_deactivation() {
    // Perform cleanup actions on deactivation if needed
    acwc_store_unique_id(false);
}
register_deactivation_hook(__FILE__, 'acwc_deactivation');

// plugin uninstall hook
function acwc_uninstall() {
    // global $wpdb;
    // // $table_name = $wpdb->prefix . 'acw';
    // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}acw");

    // // Clear the cache if needed
    // wp_cache_delete('acw_record');

    acwc_store_unique_id(false);
}
register_uninstall_hook(__FILE__, 'acwc_uninstall');