<?php

require_once(dirname(__FILE__).'/constants.php');
require_once(dirname(__FILE__).'/class/Log.php');
require_once(dirname(__FILE__).'/class/Utils.php');

$browserData = get_browser(NULL, TRUE);
$browser = $browserData['browser'];
$platform = $browserData['platform'];
$open_external_browser = ($platform === 'Android');
Log::print("Browser and Platform: $browser & $platform", "info", __FILE__, __LINE__);

// Get authorization data
$client_mac = strtoupper($_GET['id']);
$access_point_mac = strtoupper($_GET['ap']);

Log::print("Redirecting unauthenticated traffic by MAC: $client_mac", "message", __FILE__, __LINE__);

$hidden_fields_array = array(
    'access_point_mac' => $access_point_mac,
    'client_mac' => $client_mac,
    'open_external_browser' => $open_external_browser
);

$apiUrl = constant('API_URL') . '/exhibition-forms/expo-cund/exists/' . $client_mac;
$apiResponse = Tool::perform_http_request('GET', $apiUrl);

$alreadyRegistered = (isset($apiResponse) && array_key_exists('response_code', $apiResponse) && $apiResponse['response_code'] == 204);

if (isset($alreadyRegistered) && $alreadyRegistered) {
    require_once(dirname(__FILE__) . '/splash-page/grant_access.php');
} else {
    require_once(dirname(__FILE__).'/splash-page/login_form.php');
}

?>