<?php

function get_product_api(){

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://vendorapi.amrod.co.za/api/v1/Products/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwRTZGNTY1QkUzRTkyNTRFOUREMDcwQzhCQzYwQkYwRjI1RkE5NDhSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik1PYjFaYjQta2xUcDNRY01pOFlMOFBKZnFVZyJ9.eyJuYmYiOjE3MDcyOTk1NDMsImV4cCI6MTcwNzMwMzE0MywiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5hbXJvZC5jby56YSIsImNsaWVudF9pZCI6InZlbmRvckNsaWVudCIsInN1YiI6IjUzNWVkYTQ2LWIyMTctNDM4MC04NzEyLTZjYzNiMmM0OTBjMiIsImF1dGhfdGltZSI6MTcwNzI5OTU0MywiaWRwIjoibG9jYWwiLCJyb2xlIjpbIlZlbmRvciBBUEkiLCJBbXJvZCBXZWJzaXRlIl0sInBlcm1pc3Npb24iOiJDVi5BbGwiLCJ0aWVyIjoiQ2hyb21lIiwiZXhjbHVkZXNXb3Jrd2VhckRpc2NvdW50UGVyY2VudGFnZSI6dHJ1ZSwiY3VzdG9tZXJDb2RlIjoiMDE4OTYzIiwicmVnaW9uQ29kZSI6IlNBIiwidXNlciI6Im1vcm5lQG1pZGVzaWduc3R1ZGlvLmNvLnphIiwid29ya3dlYXJEaXNjb3VudFBlcmNlbnRhZ2UiOjAsImZpcnN0TmFtZSI6Ik1vcm5lIiwibGFzdE5hbWUiOiJQcmluc2xvbyIsImR5bmFtaWNzSWQiOiI4ZDNmMTNmMC1mMzUwLWVhMTEtYTgxMy0wMDBkM2EyYjVlODkiLCJqdGkiOiJEODkzNzE4ODgyMDdERkUzOEIzNTQzNEE0NzcwMEE2QSIsImlhdCI6MTcwNzI5OTU0Mywic2NvcGUiOlsiY2F0YWxvZ3VlQVBJIl0sImFtciI6WyJwd2QiXX0.LMkCH75c9c2NnjWTEOf9-ooiANUV0R3SAeawjc7IqBfmAujyArvTv4NbOGij9FBZ_Uo7X5BgPz8DwcLbqATjtKnfzzF1UljMahd7Cc1NL2iRUH7uCFCHllrSAvCFrI6Qh_m_4UsGZFGtrn2DgQyghnShGkJBQQMhsWOHngA7-Kza-MVxo2dkJ7rxD29ksKharHce214EL7tWpLmMs-Zrej9v9jeg_vz7WtMS3eUVLXtEV5wKq1cMnuXxLxKP_k_z-YNwDPGhftS3zGy_K1nqZOedcDrbamlaw497UG3W_Cvuwz8sm7oyl_HpvpYQ6JzlmA0mweurNtHbatn6wf24jQ',
        'Cookie: SERVERID=iis2; __cflb=02DiuEfjaS5QYNRiHiy4sYLgVKJh4ozFrhwoQUbQvrb9N'
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    return $response;
}

function get_product_to_callback(){

    ob_start();
    $api_response = get_product_api();

    $products = json_decode( $api_response, true );
    // Insert to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_products';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    foreach ( $products as $product ) {
        $product_json = json_encode( $product );
        $wpdb->insert(
            $table_name,
            [
                'operation_type'  => 'category_create',
                'operation_value' => $product_json,
                'status'          => 'pending',
            ]
        );
    }

    echo '<h4>Products inserted successfully</h4>';

    return ob_get_clean();
}

add_shortcode( 'product_insert_wpdb', 'get_product_to_callback' );

