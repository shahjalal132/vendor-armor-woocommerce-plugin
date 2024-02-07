<?php

function get_category_api()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://vendorapi.amrod.co.za/api/v1/Categories/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwRTZGNTY1QkUzRTkyNTRFOUREMDcwQzhCQzYwQkYwRjI1RkE5NDhSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik1PYjFaYjQta2xUcDNRY01pOFlMOFBKZnFVZyJ9.eyJuYmYiOjE3MDczMDQxNjgsImV4cCI6MTcwNzMwNzc2OCwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5hbXJvZC5jby56YSIsImNsaWVudF9pZCI6InZlbmRvckNsaWVudCIsInN1YiI6IjUzNWVkYTQ2LWIyMTctNDM4MC04NzEyLTZjYzNiMmM0OTBjMiIsImF1dGhfdGltZSI6MTcwNzMwNDE2OCwiaWRwIjoibG9jYWwiLCJyb2xlIjpbIlZlbmRvciBBUEkiLCJBbXJvZCBXZWJzaXRlIl0sInBlcm1pc3Npb24iOiJDVi5BbGwiLCJ0aWVyIjoiQ2hyb21lIiwiZXhjbHVkZXNXb3Jrd2VhckRpc2NvdW50UGVyY2VudGFnZSI6dHJ1ZSwiY3VzdG9tZXJDb2RlIjoiMDE4OTYzIiwicmVnaW9uQ29kZSI6IlNBIiwidXNlciI6Im1vcm5lQG1pZGVzaWduc3R1ZGlvLmNvLnphIiwid29ya3dlYXJEaXNjb3VudFBlcmNlbnRhZ2UiOjAsImZpcnN0TmFtZSI6Ik1vcm5lIiwibGFzdE5hbWUiOiJQcmluc2xvbyIsImR5bmFtaWNzSWQiOiI4ZDNmMTNmMC1mMzUwLWVhMTEtYTgxMy0wMDBkM2EyYjVlODkiLCJqdGkiOiJCQzNCN0EzREMyMDRERDU5QTZFQjQzRTY2Njc1OUE5QSIsImlhdCI6MTcwNzMwNDE2OCwic2NvcGUiOlsiY2F0YWxvZ3VlQVBJIl0sImFtciI6WyJwd2QiXX0.OgFoIwpCM3xmF786vL9PyPlBBWgL1yyx3dAjLeGxOT5ysmj2zQX2OtiUWbQd0vXcmnZYh6ymEKSmmKEm3F40i8DO5S4fJ66J0QvcL_21l6oH7PE2foxLgdV2flWeR2sCxEYBxoXWfhkrik3URqICE7-Hz2fk1aO77lvrtMij_mO7MlSoLHHQjtGrtd7cQshyScPUWoneFNvAC8dBlvm3MH5EaN-GGuIwtmDMQMQZR7yKJnqie_w_G2gtjhvVB7G5tyVEd33CkND0vZ95F7DBB24sJtgfVtfyZLB0FACNdBiTA59bIJBgOIBJwVkz_LCvWyvzg3o3nxWgOmuLGqZ5Aw',
            'Cookie: SERVERID=iis2; __cflb=02DiuEfjaS5QYNRiHiy4sYLgVKJh4ozFrhwoQUbQvrb9N'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}


function get_category_to_callback(){

    ob_start();
    $api_response = get_category_api();
    $categorys = json_decode( $api_response, true );
    // Insert to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_categories';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    foreach ( $categorys as $category ) {
        $category_json = json_encode( $category );
        $wpdb->insert(
            $table_name,
            [
                'operation_type'  => 'stock_create',
                'operation_value' => $category_json,
            ]
        );
    }

    echo '<h4>Categories inserted successfully</h4>';

    return ob_get_clean();
}

add_shortcode( 'category_insert_wpdb', 'get_category_to_callback' );

