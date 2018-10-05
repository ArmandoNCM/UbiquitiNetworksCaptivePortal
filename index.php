<?php
require_once(dirname(__FILE__).'/constants.php');
require_once(dirname(__FILE__).'/class/Log.php');
require_once(dirname(__FILE__).'/class/Utils.php');

$client_mac = strtoupper($_GET['id']);
$access_point_mac = strtoupper($_GET['ap']);
$ssid = $_GET['ssid'];

Log::print("Received GET from $client_mac", "message", __FILE__, __LINE__);

$hidden_fields_array = array(
    'access_point_mac' => $access_point_mac,
    'client_mac' => $client_mac
);


$apiUrl = constant('API_URL') . 'ap/locationInfo';
$queryParameters = array(
    'mac' => $access_point_mac
);
$response = Tool::perform_http_request('GET', $apiUrl, $queryParameters);

if ($response && array_key_exists('response_code', $response)){
    $response_code = $response['response_code'];
    if ($response_code == 200){

        $body = json_decode($response['response_body']);

        $html_location_name = $body->location->name;
        $html_location_logo_url = $body->location->cover->url;
        $user_download_limit = $body->business->apSettings->download;
        $user_upload_limit = $body->business->apSettings->upload;
        $seconds_allowed = $body->business->apSettings->sessionSeconds;

        $location_data_retrieved = TRUE;
        
    } else {
        Log::print("Location Info query failed with HTTP Code: $response_code", "error", __FILE__, __LINE__);
        Log::print("Response Body: " . $response['response_body'], "error", __FILE__, __LINE__);
    }
} else {
    Log::print("Query of Location Info failed, couldn't successfully consume 'locationInfo' WS", "error", __FILE__, __LINE__);
}

if (isset($location_data_retrieved) && $location_data_retrieved){

    if (!isset($html_location_name) || !$html_location_name){
        $html_location_name = 'Trinitip Store';
    }
    
    if (!isset($html_location_logo_url) || !$html_location_logo_url){
        $html_location_logo_url = '/guest/s/default/splash-page/assets/images/logo.png';
    }
    
    if (!isset($seconds_allowed) || !$seconds_allowed){
        $seconds_allowed = 5 * 60;
    }

    $hidden_fields_array['seconds_allowed'] = $seconds_allowed;

    $apiUrl = constant('API_URL') . 'ap/personCanAccessToInternet';

    $queryParameters = array(
        'mac' => $client_mac,
        'nodeMac' => $access_point_mac
    );
    $response = Tool::perform_http_request('GET', $apiUrl, $queryParameters);

    if (isset($response) && array_key_exists('response_code', $response)){
        $response_code = $response['response_code'];
        if ($response_code == 200){
            $jsonBody = $response['response_body'];
            $body = json_decode($jsonBody);
            $internetAvailable = $body->hasInternet;
        } else {
            Log::print("Consumption of personCanAccessToInternet failed with HTTP Code: $response_code", "error", __FILE__, __LINE__);
            Log::print("Response Body: " . $response['response_body'], "error", __FILE__, __LINE__);
        }
    } else {
        Log::print("Couldn't successfully consume 'ap/personCanAccessToInternet' WS", "error", __FILE__, __LINE__);
    }

    if (isset($internetAvailable) && $internetAvailable) {
        Log::print("The person device with mac: $client_mac CAN access to internet", "message", __FILE__, __LINE__);
        Log::print("Client Device with Mac: $client_mac is given access for " . $seconds_allowed . " seconds", "message", __FILE__, __LINE__);
        require_once(dirname(__FILE__).'/splash-page/grant_access.php');
    } else {
        Log::print("The person device with mac: $client_mac CAN NOT access to internet", "message", __FILE__, __LINE__);
        Log::print("Client Device with Mac: $client_mac is not yet authenticated", "message", __FILE__, __LINE__);
        require_once(dirname(__FILE__).'/splash-page/prepare_login.php');
    }

} else {
    require_once(dirname(__FILE__).'/splash-page/out_of_order.php');    
}

?>