<?php

require_once(dirname(__FILE__).'/constants.php');
require_once(dirname(__FILE__).'/class/Log.php');
require_once(dirname(__FILE__).'/class/Utils.php');

// Get authorization data
$client_mac = strtoupper($_GET['id']);
$access_point_mac = strtoupper($_GET['ap']);

Log::print("Redirecting unauthenticated traffic by MAC: $client_mac", "message", __FILE__, __LINE__);

$hidden_fields_array = array(
    'access_point_mac' => $access_point_mac,
    'client_mac' => $client_mac
);

// $apiUrl = constant('API_URL') . '/exhibition-forms/expo-students/exists/' . $client_mac;
// $apiResponse = Tool::perform_http_request('GET', $apiUrl);

$alreadyRegistered = (isset($apiResponse) && array_key_exists('response_code', $apiResponse) && $apiResponse['response_code'] == 204);

// if (isset($alreadyRegistered) && $alreadyRegistered) {
//     require_once(dirname(__FILE__) . '/splash-page/grant_access.php');
//     header('Location: http://www.cundinamarca.gov.co');
//     exit();
// } else {
    require_once(dirname(__FILE__).'/splash-page/login_form.php');
// }

?>