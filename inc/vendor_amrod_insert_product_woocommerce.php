<?php

// Include necessary files
require_once VENDOR_PLUGIN_PATH . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

// Function to insert products into WooCommerce
function product_insert_woocommerce()
{

    // Get global $wpdb object
    global $wpdb;

    // Define table names
    $product_table_name        = $wpdb->prefix . 'sync_products';
    $stock_table_name          = $wpdb->prefix . 'sync_stocks';
    $category_table_name       = $wpdb->prefix . 'sync_categories';
    $brand_table_name          = $wpdb->prefix . 'sync_brands';
    $price_table_name          = $wpdb->prefix . 'sync_price';
    $branding_dp_table_name    = $wpdb->prefix . 'sync_branding_departments';
    $branding_price_table_name = $wpdb->prefix . 'sync_branding_price';

    // Retrieve pending products from the database
    $products       = $wpdb->get_results("SELECT * FROM $product_table_name WHERE status = 'pending' LIMIT 1");
    $stocks         = $wpdb->get_results("SELECT * FROM $stock_table_name  LIMIT 1");
    $category       = $wpdb->get_results("SELECT * FROM $category_table_name  LIMIT 1");
    $brand          = $wpdb->get_results("SELECT * FROM $brand_table_name  LIMIT 1");
    $prices         = $wpdb->get_results("SELECT * FROM $price_table_name  LIMIT 1");
    $branding_db    = $wpdb->get_results("SELECT * FROM $branding_dp_table_name  LIMIT 1");
    $branding_price = $wpdb->get_results("SELECT * FROM $branding_price_table_name  LIMIT 1");

    // WooCommerce store information
    $website_url     = home_url();
    $consumer_key    = 'ck_43fc16f5ebb0dfdde9bc2d9d5abd7615170d5b3e';
    $consumer_secret = 'cs_2f85757eec5b1c7b482855005351e0c47bca9dcb';

    foreach ($products as $product) {

        // get product data
        $product_data = $product->operation_value;

        // convert json to array
        $product_data = json_decode($product_data);

        // echo "<pre>";
        // print_r($product_data);
        // echo "</pre>";
        // die();

        // Retrieve products information
        $product_name       = $product_data->productName;
        $product_code       = $product_data->simpleCode;
        $sku                = $product_code;
        $description        = $product_data->description;
        $inventory          = $product_data->inventoryType;
        $promotion          = $product_data->promotion;
        $full_Brands        = $product_data->fullBrandingGuide;
        $variants           = $product_data->variants;
        $branding_templates = $product_data->brandingTemplates;
        $minimum            = $product_data->minimum;
        $maximum            = $product_data->maximum;
        $categories = $product_data->categories;

        $images = $product_data->images;

        // get category name and image from $categories array
        foreach ($categories as $category) {
            $category_name = $category->name;
            $category_image = $category->image;
        }

        // echo '<pre>';
        // print_r($categories);
        // die();

        // get image url
        foreach ($images as $image) {
            $image_data  = $image->urls;
            $image_url_j = $image_data[0]->url;

            // echo '<pre>';
            // print_r($image_data);
            // die();

            // get image url
            // foreach ($image_data as $image_url) {
            //     $image_url = $image_url->url . ',';
            // }
        }

        //   var_dump($image_url);
        // die();

        // convert image strong to array
        // $images_urls = explode(',', $image_url);

        // concat , with image url
        $image_url_j = $image_url_j . ',';

        // convert image url to array
        $images_urls = explode(',', $image_url_j);

        // echo '<pre>';
        // print_r($images_urls);
        // die();

        // var_dump($images_urls);
        // die();


        foreach ($stocks as $stock) {

            // get stock data
            $stock_data = $stock->operation_value;

            // convert json to array
            $stock_data = json_decode($stock_data);

            // retrieve stock information
            $simpleCode_stock = $stock_data->simpleCode;
            $fullCode_stock   = $stock_data->fullCode;
            $stock_stock      = $stock_data->stock;
        }

        foreach ($brand as $brand) {

            // get brand data
            $brand_data = $brand->operation_value;

            // convert json to array
            $brand_data = json_decode($brand_data);

            // echo '<pre>';
            // print_r( $brand_data );
            // die();
        }

        foreach ($prices as $price) {

            // get price data
            $price_data = $price->operation_value;

            // convert json to array
            $price_data = json_decode($price_data);

            // retrieve price information
            // $simpl_code = $price_data->simpleCode;
            $full_code     = $price_data->fullCode;
            $product_price = $price_data->price;
        }

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
        $existing_products = new WP_Query($args);

        if ($existing_products->have_posts()) {
            $existing_products->the_post();

            // get product id
            $product_id = get_the_ID();

            // Update the status of the processed product database
            $wpdb->update(
                $product_table_name,
                ['status' => 'completed'],
                ['id' => $product->id]
            );

            // Update the product  if already exists
            $product_data = [
                'name'        => $product_name,
                'sku'         => $sku,
                'type'        => 'simple',
                'description' => $description,
                'attributes'  => [
                    [
                        'name'      => 'Dimensions',
                        'visible'   => true,
                        'variation' => true,
                    ],
                ],
            ];

            // update product
            $client->put('products/' . $product_id, $product_data);
        } else {

            // Create a new product if not exists
            $product_data = [
                'name'        => $product_name,
                'sku'         => $sku,
                'type'        => 'simple',
                'description' => $description,
                'attributes'  => [
                    [
                        'name'      => 'Dimensions',
                        'visible'   => true,
                        'variation' => true,
                    ],
                ],
            ];

            // Create the product
            $product    = $client->post('products', $product_data);
            $product_id = $product->id;

            // Set product categories
            wp_set_object_terms($product_id, $parent_category, 'product_cat');

            // Set product information
            wp_set_object_terms($product_id, 'simple', 'product_type');
            update_post_meta($product_id, '_visibility', 'visible');
            update_post_meta($product_id, '_stock_status', 'instock');
            // update_post_meta($product_id, '_regular_price', $regular_price);
            update_post_meta($product_id, '_sale_price', $product_price);
            update_post_meta($product_id, '_price', $product_price);

            // Update product meta data in WordPress
            update_post_meta($product_id, '_stock', $stock_stock);

            // display out of stock message if stock is 0
            if ($stock_stock <= 0) {
                update_post_meta($product_id, '_stock_status', 'outofstock');
            } else {
                update_post_meta($product_id, '_stock_status', 'instock');
            }
            update_post_meta($product_id, '_manage_stock', 'yes');


            // set product gallery images
            foreach ($images_urls as $image_url) {

                // echo '<pre>';
                // print_r($image_url);
                // die();

                // Extract image name
                $image_name = basename($image_url);
                // Get WordPress upload directory
                $upload_dir = wp_upload_dir();

                // Download the image from URL and save it to the upload directory
                $image_data = file_get_contents($image_url);

                if ($image_data !== false) {
                    $image_file = $upload_dir['path'] . '/' . $image_name;
                    file_put_contents($image_file, $image_data);

                    // Prepare image data to be attached to the product
                    $file_path = $upload_dir['path'] . '/' . $image_name;
                    $file_name = basename($file_path);

                    // Insert the image as an attachment
                    $attachment = [
                        'post_mime_type' => mime_content_type($file_path),
                        'post_title'     => preg_replace('/\.[^.]+$/', '', $file_name),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    ];

                    $attach_id = wp_insert_attachment($attachment, $file_path, $product_id);

                    // Add the image to the product gallery
                    $gallery_ids   = get_post_meta($product_id, '_product_image_gallery', true);
                    $gallery_ids   = explode(',', $gallery_ids);
                    $gallery_ids[] = $attach_id;
                    update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));

                    set_post_thumbnail($product_id, $attach_id);
                }

                // Update the status of the processed product in your database
                $wpdb->update(
                    $product_table_name,
                    ['status' => 'completed'],
                    ['id' => $product->id]
                );
            }

            return "Product Inserted Successfully";
        }
    }
}
add_shortcode('insert_product_api', 'product_insert_woocommerce');
