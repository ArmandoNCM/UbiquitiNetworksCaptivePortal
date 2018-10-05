<?php
require_once(dirname(__FILE__).'/../class/Utils.php');
require_once(dirname(__FILE__).'/../class/Log.php');
require_once(dirname(__FILE__).'/../constants.php');

$apiUrl = constant('API_URL') . 'ap/findPerson';
$queryParameters = array(
    'mac' => $client_mac
);
$response = Tool::perform_http_request('GET', $apiUrl, $queryParameters);

if ($response && array_key_exists('response_code', $response)){

    if ($response['response_code'] == 200){
        
        $jsonBodyString = $response['response_body'];

        $body = json_decode($jsonBodyString);

        Log::print("Find Person Body:\n$jsonBodyString", "message", __FILE__, __LINE__);

        if ($body->isFound){

            $isVerified = $body->isVerified;
            if (!$isVerified){
                $isBanned = $body->isBanned;
            }
            $personName = $body->name;
        }
    }
}

/**
 * The Form Process URL
 */
$html_form_process_url = '/guest/s/default/splash-page/form_processing.php'; // TODO get this from API

if (isset($hidden_fields_array)){

    if (isset($bypass_mac_lookup) && $bypass_mac_lookup){
        $html_page = 'full_login';
    } else {

        if (isset($isBanned) && $isBanned){
            $html_page = 'banned_page';
        } else {
            if (isset($personName)){
                $html_page = 'quick_login';
            } else {
                $html_page = 'full_login';
            }
        }
    }
}

switch ($html_page){
    case 'full_login':
        require_once(dirname(__FILE__) . '/' . 'full_login_form.php');
        break;

    case 'quick_login':
        require_once(dirname(__FILE__) . '/' . 'quick_login_form.php');
        break;

    case 'banned_page':
        require_once(dirname(__FILE__) . '/' . 'banned_page.php');
        break;

    default:
        require_once(dirname(__FILE__) . '/' . 'generic_splash.php');
        break;
}
?>