<?php

function get_branding_department_to_callback() {
    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://vendorapi.amrod.co.za/api/v1/BrandingDepartments/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwRTZGNTY1QkUzRTkyNTRFOUREMDcwQzhCQzYwQkYwRjI1RkE5NDhSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik1PYjFaYjQta2xUcDNRY01pOFlMOFBKZnFVZyJ9.eyJuYmYiOjE3MDczMDYxMzQsImV4cCI6MTcwNzMwOTczNCwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5hbXJvZC5jby56YSIsImNsaWVudF9pZCI6InZlbmRvckNsaWVudCIsInN1YiI6IjUzNWVkYTQ2LWIyMTctNDM4MC04NzEyLTZjYzNiMmM0OTBjMiIsImF1dGhfdGltZSI6MTcwNzMwNjEzNCwiaWRwIjoibG9jYWwiLCJyb2xlIjpbIlZlbmRvciBBUEkiLCJBbXJvZCBXZWJzaXRlIl0sInBlcm1pc3Npb24iOiJDVi5BbGwiLCJ0aWVyIjoiQ2hyb21lIiwiZXhjbHVkZXNXb3Jrd2VhckRpc2NvdW50UGVyY2VudGFnZSI6dHJ1ZSwiY3VzdG9tZXJDb2RlIjoiMDE4OTYzIiwicmVnaW9uQ29kZSI6IlNBIiwidXNlciI6Im1vcm5lQG1pZGVzaWduc3R1ZGlvLmNvLnphIiwid29ya3dlYXJEaXNjb3VudFBlcmNlbnRhZ2UiOjAsImZpcnN0TmFtZSI6Ik1vcm5lIiwibGFzdE5hbWUiOiJQcmluc2xvbyIsImR5bmFtaWNzSWQiOiI4ZDNmMTNmMC1mMzUwLWVhMTEtYTgxMy0wMDBkM2EyYjVlODkiLCJqdGkiOiJEQUE1RTg5QTI3NjQ4RUJBRERDOUNFQTUzQUEwRkJEMyIsImlhdCI6MTcwNzMwNjEzNCwic2NvcGUiOlsiY2F0YWxvZ3VlQVBJIl0sImFtciI6WyJwd2QiXX0.YW0_BEaFi3q9_LFMdh3y3ge5S8o8zZrKBcWaNqA4g_M8fE5w5blCMMGA4bd1ELa7pzOStkrwPDDCdI3aa8eusjBovQ2iOGdFH40EDXJSWXpcAHIC0AklNcqwxHDXAIHWE8Q0RRbB6RfM3ZZTWJhzm_P0Ot0XJrcVv81bV5UOWVizBmfQjYBzY8nxs6y4J4tkbpeme2Cqm74GuP-whFZbl9AH6PGp1OoJ_tHp_3req3vjWmBQ9QMKHaefq3B7ccrinzpwkc-A6iUAIBgdoPXd2YY7fdPkkoTwfbAqY8hRPTNuSdGHbUgA2pocfvS7xCkDSYktam2z2O1ikg-yBS4zuQ',
                'Cookie: SERVERID=iis2; __cflb=02DiuEfjaS5QYNRiHiy4sYLgVKJh4ozFrhwoQUbQvrb9N',
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    return $response;
}

function get_branding_departments_bd_callback() {

    ob_start();
    $api_response = get_branding_department_to_callback();
    $branding_dp  = json_decode( $api_response, true );
    // Insert to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_branding_departments';
    $wpdb->query( "TRUNCATE TABLE $table_name" );

    foreach ( $branding_dp as $brand ) {
        $brands_json = json_encode( $brand );
        $wpdb->insert(
            $table_name,
            [
                'operation_type'  => 'branding_create',
                'operation_value' => $brands_json,
            ]
        );
    }

    echo '<h4>branding departments inserted successfully</h4>';

    return ob_get_clean();
}

add_shortcode( 'get_branding_departments', 'get_branding_departments_bd_callback' );


