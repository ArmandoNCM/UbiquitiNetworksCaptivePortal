<?php
require_once(dirname(__FILE__).'/../class/Log.php');
require_once(dirname(__FILE__).'/../class/Utils.php');
require_once(dirname(__FILE__).'/../class/SimpleAWS.php');
require_once(dirname(__FILE__).'/../constants.php');

$token = $_POST['token'];
$username = $_POST['email'];
$client_mac = $_POST['client_mac'];
$access_point_mac = $_POST['access_point_mac'];
$controller_ip = $_POST['controller_ip'];
$controller_port = $_POST['controller_port'];
$wlan_identifier = $_POST['wlan_identifier'];
$login_type = $_POST['login_type'];
$seconds_allowed = $_POST['seconds_allowed'];

$valid_fields = TRUE;

if ($login_type == 'quick'){

    if (array_key_exists('negative_button', $_POST) && $_POST['negative_button']){
        $bypass_mac_lookup = TRUE;
        $html_form_hidden_fields_array = array(
            // TODO rebuild
        );
        // require_once(dirname(__FILE__) . '/../../../splash-page/index.php');
        return;
    }
} else {
    $full_login = TRUE;
    // TODO Check name and email and in case of error, show form with error and retry
    $person_name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    //Email Validation
    $person_email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

    $birthdate = filter_input(INPUT_POST, "birthdate", FILTER_SANITIZE_STRING);

    $gender = filter_input(INPUT_POST, "gender", FILTER_SANITIZE_STRING);

    if ($person_email){
        strtolower(trim($person_email));
    } else {
        Log::print("Error in the email validation.", "error", __FILE__, __LINE__);
        $valid_fields = FALSE;
    }
}

if ($valid_fields) {

    $eventMessage = array(
        'nodeMac' => $access_point_mac,
        'mac' => $client_mac,
        'eventType' => 'in'
    );

    if ($full_login){
        $eventMessage['name'] = $person_name;
        $eventMessage['email'] = $person_email;
        $eventMessage['gender'] = $gender;
        $eventMessage['birthdate'] = $birthdate; // TODO change to 'birthdate'
    }

    $apiUrl = constant('API_URL') . 'ap/indoorEvents';
    $response = Tool::perform_http_request('POST', $apiUrl, json_encode($eventMessage));

    if ($response && array_key_exists('response_code', $response)){
        $response_code = $response['response_code'];
        if ($response_code == 200){

            $body = json_decode($response['response_body']);

            if ($body->isVerified){
                // TODO do something
            } else {
                // TODO do something
            }
            
        } else {
            Log::print("Creation of IN event failed with HTTP Code: $response_code", "error", __FILE__, __LINE__);
            Log::print("Response Body: " . $response['response_body'], "error", __FILE__, __LINE__);
        }
    } else {
        Log::print("Creation of IN event failed, couldn't successfully consume 'indoorEvents' WS", "error", __FILE__, __LINE__);
    }
 
    require_once(dirname(__FILE__).'/grant_access.php');   
}

?>