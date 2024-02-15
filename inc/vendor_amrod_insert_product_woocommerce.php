<?php

// Include necessary files
require_once VENDOR_PLUGIN_PATH . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

// Function to insert products into WooCommerce
function product_insert_woocommerce() {

    // Get global $wpdb object
    global $wpdb;

    // Define table names
    $product_table_name = $wpdb->prefix . 'sync_products';
    $stock_table_name   = $wpdb->prefix . 'sync_stocks';
    $price_table_name   = $wpdb->prefix . 'sync_price';

    // Retrieve pending products from the database
    $products = $wpdb->get_results( "SELECT * FROM $product_table_name WHERE status = 'pending' LIMIT 1" );

    // WooCommerce store information
    $website_url     = home_url();
    $consumer_key    = 'ck_bc1427945d7b3443d53acff88efdab3ec33fcb11';
    $consumer_secret = 'cs_1b0d293033f864f8e56c354f52292048f014a57a';

    foreach ( $products as $product ) {

        // get product data
        $product_data = $product->operation_value;

        // convert json to array
        $product_data = json_decode( $product_data );

        // Retrieve products information
        $product_name = $product_data->productName;
        $product_code = $product_data->simpleCode;
        $sku          = $product_code;
        $description  = $product_data->description;
        $categories   = $product_data->categories;
        $images       = $product_data->images;

        // get branding values
        $brandings_array = $product_data->brandings;
        $variants        = $product_data->variants;

        $colors = null;
        $sizes  = null;
        foreach ( $variants as $variant ) {
            $colors .= $variant->codeColourName . "|";
            $sizes .= $variant->codeSize . "|";
        }

        // Explode the string into an array using the delimiter '|'
        $color_array = explode( '|', $colors );

        // Remove duplicates from the array
        $unique_color = array_unique( $color_array );

        // Convert the unique values array back to a string
        $colors = implode( '|', $unique_color );

        $category_name = null;
        // get category name and image from $categories array
        foreach ( $categories as $category ) {
            $category_name = $category->name;
        }

        // Initialize an empty string to hold the URLs
        $urls = '';

        // Loop through the array and concatenate the URLs with comma separator
        foreach ( $images as $object ) {
            foreach ( $object->urls as $urlObj ) {
                $urls .= $urlObj->url . ', ';
            }
        }

        // Remove the trailing comma and space
        $urls = rtrim( $urls, ', ' );

        // convert $urls to array
        $urls = explode( ", ", $urls );

        // get price data
        $prices = $wpdb->get_results( "SELECT * FROM $price_table_name WHERE simpleCode = '$sku' LIMIT 1" );

        // get price
        $price = $prices[0]->price ?? null;

        // increase 25% of the price
        $price = $price * 1.25;

        // get stock data
        $stocks = $wpdb->get_results( "SELECT stock FROM $stock_table_name  WHERE simpleCode = '$sku' LIMIT 1" );

        // get stock
        $stock = $stocks[0]->stock ?? null;

        // $color = "Red|Green|Blue";
        $color = $colors ?? '';

        // $updated_sizes = "30|32|34|36";
        $updated_sizes = "";

        // Set up the API client with WooCommerce store URL and credentials
        $client = new Client(
            $website_url,
            $consumer_key,
            $consumer_secret,
            [
                'verify_ssl' => false,
            ]
        );

        // Check if the product already exists in WooCommerce
        $args = array(
            'post_type'  => 'product',
            'meta_query' => array(
                array(
                    'key'     => '_sku',
                    'value'   => $sku,
                    'compare' => '=',
                ),
            ),
        );

        // Check if the product already exists
        $existing_products = new WP_Query( $args );

        if ( $existing_products->have_posts() ) {
            $existing_products->the_post();

            // get product id
            $product_id = get_the_ID();

            // Update the status of the processed product database
            $wpdb->update(
                $product_table_name,
                [ 'status' => 'completed' ],
                [ 'id' => $product->id ]
            );

            // Update the product  if already exists
            $product_data = [
                'name'        => $product_name,
                'sku'         => $sku,
                'type'        => 'variable',
                'description' => $description,
                'attributes'  => [
                    [
                        'name'        => 'Color',
                        'options'     => explode( separator: '|', string: $color ),
                        'position'    => 0,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                    [
                        'name'        => 'Size',
                        'options'     => explode( separator: '|', string: $updated_sizes ),
                        'position'    => 1,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                ],
            ];

            // update product
            $client->put( 'products/' . $product_id, $product_data );

            // Add variations
            foreach ( explode( '|', $color ) as $color_option ) {
                foreach ( explode( '|', $updated_sizes ) as $size_option ) {

                    // Add variation data
                    $variation_data = [

                        'attributes'     => [
                            [
                                'name'  => 'Color',
                                'value' => $color_option,
                            ],
                            [
                                'name'  => 'Size',
                                'value' => $size_option,
                            ],
                        ],

                        'regular_price'  => "{$price}",
                        'stock_quantity' => $stock,
                    ];

                    // Add variation
                    $client->post( 'products/' . $product_id . '/variations', $variation_data );

                }
            }

            return 'product already exists';

        } else {

            // Update the status of the processed product database
            $wpdb->update(
                $product_table_name,
                [ 'status' => 'completed' ],
                [ 'id' => $product->id ]
            );

            // Create a new product if not exists
            $product_data = [
                'name'        => $product_name,
                'sku'         => $sku,
                'type'        => 'variable',
                'description' => $description,
                'attributes'  => [
                    [
                        'name'        => 'Color',
                        'options'     => explode( separator: '|', string: $color ),
                        'position'    => 0,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                    [
                        'name'        => 'Size',
                        'options'     => explode( separator: '|', string: $updated_sizes ),
                        'position'    => 1,
                        'visible'     => true,
                        'variation'   => true,
                        'is_taxonomy' => false,
                    ],
                ],
            ];

            // Create the product
            $product    = $client->post( 'products', $product_data );
            $product_id = $product->id;

            // Add variations
            foreach ( explode( '|', $color ) as $color_option ) {
                foreach ( explode( '|', $updated_sizes ) as $size_option ) {

                    // Add variation data
                    $variation_data = [

                        'attributes'     => [
                            [
                                'name'  => 'Color',
                                'value' => $color_option,
                            ],
                            [
                                'name'  => 'Size',
                                'value' => $size_option,
                            ],
                        ],

                        'regular_price'  => "{$price}",
                        'stock_quantity' => $stock,
                    ];

                    // Add variation
                    $client->post( 'products/' . $product_id . '/variations', $variation_data );

                }
            }

            // Set product categories
            wp_set_object_terms( $product_id, $category_name, 'product_cat' );

            // Set category image
            $term = get_term_by( 'name', $category_name, 'product_cat' );
            if ( $term && !is_wp_error( $term ) ) {

                if ( !empty( $category_image ) ) {
                    update_term_meta( $term->term_id, 'thumbnail_id', attachment_url_to_postid( $category_image ) );
                }
            }


            // Set product information
            wp_set_object_terms( $product_id, 'variable', 'product_type' );
            update_post_meta( $product_id, '_visibility', 'visible' );
            update_post_meta( $product_id, '_stock_status', 'instock' );
            // update_post_meta($product_id, '_regular_price', $regular_price);
            update_post_meta( $product_id, '_sale_price', $price );
            update_post_meta( $product_id, '_price', $price );

            // Update product meta data in WordPress
            update_post_meta( $product_id, '_stock', $stock );

            // display out of stock message if stock is 0
            if ( $stock <= 0 ) {
                update_post_meta( $product_id, '_stock_status', 'outofstock' );
            } else {
                update_post_meta( $product_id, '_stock_status', 'instock' );
            }
            update_post_meta( $product_id, '_manage_stock', 'yes' );

            // Extract brandingName and brandingCode values
            $brandingValues = array();
            $brand_top_name = null;
            foreach ( $brandings_array as $branding ) {

                $brand_top_name = $branding->positionName;

                // Check if method array is set and not empty
                if ( isset( $branding->method ) && is_array( $branding->method ) && !empty( $branding->method ) ) {
                    // Iterate over method array
                    foreach ( $branding->method as $method ) {
                        // Check if brandingName and brandingCode properties are set
                        if ( isset( $method->brandingName ) && isset( $method->brandingCode ) ) {
                            // Store brandingName and brandingCode values
                            $brandingValues[] = array(
                                'brandingName' => $method->brandingName,
                                'brandingCode' => $method->brandingCode,
                            );
                        }
                    }
                }
            }


            // Initialize a variable to store concatenated brand names and codes
            $all_brand_names_codes = '';

            foreach ( $brandingValues as $brandingValue ) {
                // Concatenate brand name and code
                $brand_name_code = $brand_top_name . " " . $brandingValue['brandingName'] . " ({$brandingValue['brandingCode']})";
                // Concatenate with existing brand names and codes
                $all_brand_names_codes .= $brand_name_code . ', ';
            }

            // Remove trailing comma and space
            $all_brand_names_codes = rtrim( $all_brand_names_codes, ', ' );
            $all_brand_names_codes = $all_brand_names_codes ?? '';

            // Save concatenated brand names and codes as post meta
            update_post_meta( $product_id, '_brandingNamesCodes', $all_brand_names_codes );

            // get the brand data
            $brand_data = get_post_meta( $product_id, "_brandingNamesCodes", true );

            // Set the short description
            $short_description = "Brand Data: {$brand_data}";

            // Update the product
            $args = array(
                'ID'           => $product_id,
                'post_excerpt' => $short_description,
            );

            wp_update_post( $args );


            // set product gallery images
            foreach ( $urls as $image_url ) {

                // Extract image name
                $image_name = basename( $image_url );
                // Get WordPress upload directory
                $upload_dir = wp_upload_dir();

                // Download the image from URL and save it to the upload directory
                $image_data = file_get_contents( $image_url );

                if ( $image_data !== false ) {
                    $image_file = $upload_dir['path'] . '/' . $image_name;
                    file_put_contents( $image_file, $image_data );

                    // Prepare image data to be attached to the product
                    $file_path = $upload_dir['path'] . '/' . $image_name;
                    $file_name = basename( $file_path );

                    // Insert the image as an attachment
                    $attachment = [
                        'post_mime_type' => mime_content_type( $file_path ),
                        'post_title'     => preg_replace( '/\.[^.]+$/', '', $file_name ),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    ];

                    $attach_id = wp_insert_attachment( $attachment, $file_path, $product_id );

                    // Add the image to the product gallery
                    $gallery_ids   = get_post_meta( $product_id, '_product_image_gallery', true );
                    $gallery_ids   = explode( ',', $gallery_ids );
                    $gallery_ids[] = $attach_id;
                    update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery_ids ) );

                    set_post_thumbnail( $product_id, $attach_id );
                }
            }

            return "<h3>Product Inserted Successfully</h3>";
        }
    }
}
add_shortcode( 'insert_product_api', 'product_insert_woocommerce' );
