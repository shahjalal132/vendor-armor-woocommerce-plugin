<?php
function vendor_amrod_stock_api() {
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://vendorapi.amrod.co.za/api/v1/Stock/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwRTZGNTY1QkUzRTkyNTRFOUREMDcwQzhCQzYwQkYwRjI1RkE5NDhSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik1PYjFaYjQta2xUcDNRY01pOFlMOFBKZnFVZyJ9.eyJuYmYiOjE3MDczMDUzNDEsImV4cCI6MTcwNzMwODk0MSwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5hbXJvZC5jby56YSIsImNsaWVudF9pZCI6InZlbmRvckNsaWVudCIsInN1YiI6IjUzNWVkYTQ2LWIyMTctNDM4MC04NzEyLTZjYzNiMmM0OTBjMiIsImF1dGhfdGltZSI6MTcwNzMwNTM0MSwiaWRwIjoibG9jYWwiLCJyb2xlIjpbIlZlbmRvciBBUEkiLCJBbXJvZCBXZWJzaXRlIl0sInBlcm1pc3Npb24iOiJDVi5BbGwiLCJ0aWVyIjoiQ2hyb21lIiwiZXhjbHVkZXNXb3Jrd2VhckRpc2NvdW50UGVyY2VudGFnZSI6dHJ1ZSwiY3VzdG9tZXJDb2RlIjoiMDE4OTYzIiwicmVnaW9uQ29kZSI6IlNBIiwidXNlciI6Im1vcm5lQG1pZGVzaWduc3R1ZGlvLmNvLnphIiwid29ya3dlYXJEaXNjb3VudFBlcmNlbnRhZ2UiOjAsImZpcnN0TmFtZSI6Ik1vcm5lIiwibGFzdE5hbWUiOiJQcmluc2xvbyIsImR5bmFtaWNzSWQiOiI4ZDNmMTNmMC1mMzUwLWVhMTEtYTgxMy0wMDBkM2EyYjVlODkiLCJqdGkiOiI3RERGNzc5MkZENjg4RTAzREU4MTYxRTJBQkQ4RkM0NCIsImlhdCI6MTcwNzMwNTM0MSwic2NvcGUiOlsiY2F0YWxvZ3VlQVBJIl0sImFtciI6WyJwd2QiXX0.ALQ7jR5KwBEUzoB_PPV689K7GOlU2VRKBfaeL_ClmqsZq2UeXLrNysrMTnxsqXaeq1gtd_8CNR4Ogmr64Hjrr8_6C1N-XDLK2p6R3WJYcLG9hR9KqR-h2kyE3Y-0DfHvujhWET4PtwB6w081QaSxB1nUI8n2MX1CDzKQCpeeECVx30GE41K0YxlZBOrt9fiVXFv3fEbX4i8B8YzXYxLh-69Xps78n2m0zKtQE8uYCAmFPa4zJ_W6J1EYJrHNLPDK7BgR_d1qlTWqm1Gp_cFjKtBNip3zdA6OZ4iTB19VRqiFvp1b599npVJDoXBNDdNzmYsgFhuxLmriMIPKru2t0w',
                'Cookie: SERVERID=iis2; __cflb=02DiuEfjaS5QYNRiHiy4sYLgVKJh4ozFrhwoQUbQvrb9N',
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}


function get_stock_to_callback() {

    ob_start();
    $api_response = vendor_amrod_stock_api();
    $stocks       = json_decode( $api_response, true );
    // Insert to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_stocks';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    foreach ( $stocks as $stock ) {
        $stock_json = json_encode( $stock );
        $wpdb->insert(
            $table_name,
            [
                'operation_type'  => 'stock_create',
                'operation_value' => $stock_json,
            ]
        );
    }

    echo '<h4>Stock inserted successfully</h4>';

    return ob_get_clean();
}

add_shortcode( 'stock_insert_wpdb', 'get_stock_to_callback' );
