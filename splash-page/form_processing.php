<?php
require_once(dirname(__FILE__) . '/../class/Log.php');
require_once(dirname(__FILE__) . '/../class/Utils.php');
require_once(dirname(__FILE__) . '/../constants.php');

$client_mac = $_POST['client_mac'];
$access_point_mac = $_POST['access_point_mac'];

$valid_fields = TRUE;

// TODO Check name and email and in case of error, show form with error and retry
$person_name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
//Email Validation
$person_email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);

$open_external_browser = $_POST['open_external_browser'];
$age = filter_input(INPUT_POST, "age", FILTER_SANITIZE_STRING);
$state = filter_input(INPUT_POST, "state", FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_STRING);
$identification_document = filter_input(INPUT_POST, "id_number", FILTER_SANITIZE_STRING);
$identification_document = Tool::remove_non_numeric_characters($identification_document);

if ($person_email) {
    strtolower(trim($person_email));
} else {
    Log::print("Error in the email validation.", "error", __FILE__, __LINE__);
    $valid_fields = FALSE;
}

if ($valid_fields) {

    $bodyArray = array(
        'mac' => $client_mac,
        'state' => $state,
        'city' => $city,
        'name' => $person_name,
        'email' => $person_email,
        'age' => $age
    );

    $bodyJson = json_encode($bodyArray);

    $apiUrl = constant('API_URL') . '/exhibition-forms/expo-cund';
    $apiResponse = Tool::perform_http_request('POST', $apiUrl, $bodyJson);

    if (isset($apiResponse) && array_key_exists('response_code', $apiResponse)) {

        if ($apiResponse['response_code'] == 201) {
            // Registry successful
            Log::print("Successfully registered person on device with mac: $client_mac", 'message', __FILE__, __LINE__);
        } else {
            // Something went wrong
            Log::print("Something went wrong trying to register person on device with mac: $client_mac\nThe service responded with HTTP Code: " . $apiResponse['response_code'], 'error', __FILE__, __LINE__);

            Log::print("Response Body:\n\n" . $apiResponse['response_body'], 'debug', __FILE__, __LINE__);
        }
    } else {
        Log::print("The person registry API could not be consumed", 'error', __FILE__, __LINE__);
    }

    require_once(dirname(__FILE__) . '/grant_access.php');
}

?>