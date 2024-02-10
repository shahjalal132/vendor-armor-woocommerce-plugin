<?php

function get_price_api() {

    $curl = curl_init();

    curl_setopt_array( $curl, array(
        CURLOPT_URL            => 'https://vendorapi.amrod.co.za/api/v1/Prices/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => array(
                'Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwRTZGNTY1QkUzRTkyNTRFOUREMDcwQzhCQzYwQkYwRjI1RkE5NDhSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik1PYjFaYjQta2xUcDNRY01pOFlMOFBKZnFVZyJ9.eyJuYmYiOjE3MDc1NDQ1NTIsImV4cCI6MTcwNzU0ODE1MiwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5hbXJvZC5jby56YSIsImNsaWVudF9pZCI6InZlbmRvckNsaWVudCIsInN1YiI6IjUzNWVkYTQ2LWIyMTctNDM4MC04NzEyLTZjYzNiMmM0OTBjMiIsImF1dGhfdGltZSI6MTcwNzU0NDU1MiwiaWRwIjoibG9jYWwiLCJyb2xlIjpbIlZlbmRvciBBUEkiLCJBbXJvZCBXZWJzaXRlIl0sInBlcm1pc3Npb24iOiJDVi5BbGwiLCJ0aWVyIjoiQ2hyb21lIiwiZXhjbHVkZXNXb3Jrd2VhckRpc2NvdW50UGVyY2VudGFnZSI6dHJ1ZSwiY3VzdG9tZXJDb2RlIjoiMDE4OTYzIiwicmVnaW9uQ29kZSI6IlNBIiwidXNlciI6Im1vcm5lQG1pZGVzaWduc3R1ZGlvLmNvLnphIiwid29ya3dlYXJEaXNjb3VudFBlcmNlbnRhZ2UiOjAsImZpcnN0TmFtZSI6Ik1vcm5lIiwibGFzdE5hbWUiOiJQcmluc2xvbyIsImR5bmFtaWNzSWQiOiI4ZDNmMTNmMC1mMzUwLWVhMTEtYTgxMy0wMDBkM2EyYjVlODkiLCJqdGkiOiJFNjEwNUMzNjdDRjk0OEYwNjFEM0MxRjVEREQ3OUQ5MyIsImlhdCI6MTcwNzU0NDU1Miwic2NvcGUiOlsiY2F0YWxvZ3VlQVBJIl0sImFtciI6WyJwd2QiXX0.iCK8jooufqT7sCi02CGhA-meqN2Rjq-0ANVgCpUXATtithIKarBaWSTzPGN2tOk3y61IvVwanFcLBSp_lY1HIDOc-MaQ8tmnC9jbk8s6g6lK1EfqBk-K8Moqg0t3Klj-m1ozv-B9-TRCDScqbcZxEPYj3q2mPaqe9cOa-SJ6BOE8_IgI-U5iAvIQnVVsUm-vCTmODFQ3rOrb4YmzViFYG4ulLYFLj34ApcK_fkV9BHq1RMZD7u-wtId3cmhXM1zQegCTCLeoGAiZzKi1lqdChFfAEEMoN81hbL3d4GYspnsiEESDg8DxTwN7imND0qF7iasIVC7J1f7T6IaoUe5EVQ',
                'Cookie: SERVERID=iis2; __cflb=02DiuEfjaS5QYNRiHiy4sYLgVKJh4ozFqZXQm5jQsiPec',
            ),
    )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}


function get_price_to_callback() {

    ob_start();

    // get api response
    $api_response = get_price_api();

    // get file path
    $file_path = VENDOR_PLUGIN_PATH . '/uploads/price-data.json';

    // put api response to json file
    // file_put_contents($file_path, $api_response);

    // get file contents
    // $file_contents = file_get_contents($file_path);

    $prices = json_decode( $api_response, true );
    // $prices       = json_decode($file_contents, true);

    // Insert to database
    global $wpdb;

    $table_name = $wpdb->prefix . 'sync_price';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    foreach ( $prices as $price ) {

        // get simplecode full code and price
        $simple_code = $price['simplecode'];
        $fullCode    = $price['fullCode'];
        $price       = $price['price'];

        $wpdb->insert(
            $table_name,
            [
                'simplecode' => $simple_code,
                'fullCode'   => $fullCode,
                'price'      => $price,
            ]
        );
    }

    return '<h4>Price inserted successfully</h4>';

    return ob_get_clean();
}

add_shortcode( 'price_insert_wpdb', 'get_price_to_callback' );
