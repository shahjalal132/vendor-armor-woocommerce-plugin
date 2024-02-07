<?php

// Create wp_sync_category Table When Plugin Activated
function vendor_amrod_bd_table_create()
{

    global $wpdb;

    $table_name      = $wpdb->prefix . 'sync_products';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        status VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Remove wp_sync_category Table when plugin deactivated
function vendor_amrod_bd_table_remove()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'sync_products';
    $sql        = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}

//stock tabel create
function vendor_stock_bd_table_create()
{
    global $wpdb;

    $table_name      = $wpdb->prefix . 'sync_stocks';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Remove wp_sync_category Table when plugin deactivated
function vendor_stock_bd_table_remove()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'sync_stocks';
    $sql        = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}


// create category table
function sync_categories_table_creation() {
    global $wpdb;

    // Table name
    $table_name      = $wpdb->prefix . 'sync_categories';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query for table creation
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Include WordPress upgrade file for 'dbDelta' function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute 'dbDelta' to create or update the table
    dbDelta( $sql );
}

function sync_categories_table_deletion() {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'sync_categories';

    // SQL query to drop the table if it exists
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

// create brand table
function sync_brand_table_creation() {
    global $wpdb;

    // Table name
    $table_name      = $wpdb->prefix . 'sync_brands';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query for table creation
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Include WordPress upgrade file for 'dbDelta' function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute 'dbDelta' to create or update the table
    dbDelta( $sql );
}

// remove category table

// remove brand table
function sync_brand_table_deletion() {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'sync_brands';

    // SQL query to drop the table if it exists
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

// create price table
function sync_price_table_creation() {
    global $wpdb;

    // Table name
    $table_name      = $wpdb->prefix . 'sync_price';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query for table creation
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Include WordPress upgrade file for 'dbDelta' function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute 'dbDelta' to create or update the table
    dbDelta( $sql );
}


// remove price table
function sync_price_table_deletion() {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'sync_price';

    // SQL query to drop the table if it exists
    $wpdb->query( "DROP TABLE IF EXISTS $table_name");
}


// create price table
function sync_branding_departments_table_create() {
    global $wpdb;

    // Table name
    $table_name      = $wpdb->prefix . 'sync_branding_departments';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query for table creation
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Include WordPress upgrade file for 'dbDelta' function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute 'dbDelta' to create or update the table
    dbDelta( $sql );
}


// remove price table
function sync_branding_departments_table_remove() {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'sync_branding_departments';

    // SQL query to drop the table if it exists
    $wpdb->query( "DROP TABLE IF EXISTS $table_name");
}

function sync_branding_price_table_create() {
    global $wpdb;

    // Table name
    $table_name      = $wpdb->prefix . 'sync_branding_price';
    $charset_collate = $wpdb->get_charset_collate();

    // SQL query for table creation
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        operation_type VARCHAR(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Include WordPress upgrade file for 'dbDelta' function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // Execute 'dbDelta' to create or update the table
    dbDelta( $sql );
}


// remove price table
function sync_branding_price_table_remove() {
    global $wpdb;

    // Table name
    $table_name = $wpdb->prefix . 'sync_branding_price';

    // SQL query to drop the table if it exists
    $wpdb->query( "DROP TABLE IF EXISTS $table_name");
}
