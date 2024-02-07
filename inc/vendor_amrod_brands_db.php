<?php

function get_brands_bd_api()
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://vendorapi.amrod.co.za/api/v1/Brands/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6IjMwRTZGNTY1QkUzRTkyNTRFOUREMDcwQzhCQzYwQkYwRjI1RkE5NDhSUzI1NiIsInR5cCI6ImF0K2p3dCIsIng1dCI6Ik1PYjFaYjQta2xUcDNRY01pOFlMOFBKZnFVZyJ9.eyJuYmYiOjE3MDczMDg2MjIsImV4cCI6MTcwNzMxMjIyMiwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5hbXJvZC5jby56YSIsImNsaWVudF9pZCI6InZlbmRvckNsaWVudCIsInN1YiI6IjUzNWVkYTQ2LWIyMTctNDM4MC04NzEyLTZjYzNiMmM0OTBjMiIsImF1dGhfdGltZSI6MTcwNzMwODYyMiwiaWRwIjoibG9jYWwiLCJyb2xlIjpbIlZlbmRvciBBUEkiLCJBbXJvZCBXZWJzaXRlIl0sInBlcm1pc3Npb24iOiJDVi5BbGwiLCJ0aWVyIjoiQ2hyb21lIiwiZXhjbHVkZXNXb3Jrd2VhckRpc2NvdW50UGVyY2VudGFnZSI6dHJ1ZSwiY3VzdG9tZXJDb2RlIjoiMDE4OTYzIiwicmVnaW9uQ29kZSI6IlNBIiwidXNlciI6Im1vcm5lQG1pZGVzaWduc3R1ZGlvLmNvLnphIiwid29ya3dlYXJEaXNjb3VudFBlcmNlbnRhZ2UiOjAsImZpcnN0TmFtZSI6Ik1vcm5lIiwibGFzdE5hbWUiOiJQcmluc2xvbyIsImR5bmFtaWNzSWQiOiI4ZDNmMTNmMC1mMzUwLWVhMTEtYTgxMy0wMDBkM2EyYjVlODkiLCJqdGkiOiJDRjhEQjE2QkU4RTVDNDIwNDhCNEE4RjQyMEE4REEzMSIsImlhdCI6MTcwNzMwODYyMiwic2NvcGUiOlsiY2F0YWxvZ3VlQVBJIl0sImFtciI6WyJwd2QiXX0.RVUdH_g1lZJlmltLIoKHBaKU76QiYCYO_A4pMs6eGQs7ZcHoUjavhtwUvJxA063YXoauYEtnkktXZn6UL66YEQ74omc0y1NOMrnoVmss1o4Uf4nkqTHK9UJDiZza9Xme1EVM1FMx6RWhmGHHJ3X3relwp0qf2WdYbgm3TEkqYRVLl6TFDRCFaO3Tc2mTM-jSmD2hGqreIk8PY5ookUyT0zRoEij9IHNiYhmC2pFCcIK8qzFsi2DScf2CpnaScIqdzccv49bJa4L5aFDwoQTPPzEShFIMN4KXILW4DZcXlEk78bKpfyygFR6sz3Nl_hqKqH-XlYJetmUhObc9RKJTAw',
            'Cookie: SERVERID=iis2; __cflb=02DiuEfjaS5QYNRiHiy4sYLgVKJh4ozFrhwoQUbQvrb9N'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

function get_brands_to_callback()
{

    ob_start();
    $api_response = get_brands_bd_api();
    $brands = json_decode($api_response, true);
    // Insert to database
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_brands';
    $wpdb->query("TRUNCATE TABLE $table_name");

    foreach ($brands as $brand) {
        $brand_json = json_encode($brand);
        $wpdb->insert(
            $table_name,
            [
                'operation_type'  => 'brand_create',
                'operation_value' => $brand_json,
            ]
        );
    }

    echo '<h4>Price inserted successfully</h4>';

    return ob_get_clean();
}

add_shortcode('brands_insert_wpdb', 'get_brands_to_callback');

