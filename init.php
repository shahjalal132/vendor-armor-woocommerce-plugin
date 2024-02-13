<?php

/*
 * Plugin Name:       vendor-api
 * Plugin URI:        #
 * Description:       vendor API
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Imjol
 * Author URI:        https://imjol.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 */

if ( !defined( 'WPINC' ) ) {
    die;
}

// Define plugin path
if ( !defined( 'VENDOR_PLUGIN_PATH' ) ) {
    define( 'VENDOR_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Define plugin uri
if ( !defined( 'VENDOR_PLUGIN_URI' ) ) {
    define( 'VENDOR_PLUGIN_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

// Create wp_sync_products db table when plugin activate
register_activation_hook( __FILE__, 'vendor_amrod_bd_table_create' );

//deactivation hook
register_deactivation_hook( __FILE__, 'vendor_amrod_bd_table_remove' );

// Create wp_sync_products db table when plugin activate
register_activation_hook( __FILE__, 'vendor_stock_bd_table_create' );

//deactivation hook
register_deactivation_hook( __FILE__, 'vendor_stock_bd_table_remove' );

//category table
register_activation_hook( __FILE__, 'sync_categories_table_creation' );

//category table
register_deactivation_hook( __FILE__, 'sync_categories_table_deletion' );

//brand table
register_activation_hook( __FILE__, 'sync_brand_table_creation' );

//brand table
register_deactivation_hook( __FILE__, 'sync_brand_table_deletion' );

//price table
register_activation_hook( __FILE__, 'sync_price_table_creation' );

//price table
register_deactivation_hook( __FILE__, 'sync_price_table_deletion' );

//sync branding department table
register_activation_hook( __FILE__, 'sync_branding_departments_table_create' );

//sync branding department table
register_deactivation_hook( __FILE__, 'sync_branding_departments_table_remove' );

//sync branding price table
register_activation_hook( __FILE__, 'sync_branding_price_table_create' );

//sync branding price table
register_deactivation_hook( __FILE__, 'sync_branding_price_table_remove' );




// Including requirements files
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_db-table.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_insert_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_stock_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_category_insert_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/sync_branding_departments_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_brandign_price_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_price_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_brands_db.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_custom_functions.php';
require_once VENDOR_PLUGIN_PATH . '/inc/vendor_amrod_insert_product_woocommerce.php';
