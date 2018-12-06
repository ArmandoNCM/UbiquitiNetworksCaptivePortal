<?php
require_once(dirname(__FILE__).'/Log.php');

class Tool {

    public static function perform_http_request($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    "Cache-Control: no-cache",
                    "Content-Type: application/json",
                ));

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password");

        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response_body = curl_exec($curl);
        $response_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        
        $err = curl_error($curl);

        $response = FALSE;
        if ($err) {
            Log::print("cURL Error #: " . json_encode($err), "message", __FILE__, __LINE__);
        } else {
            $response = array(
                'response_body' => $response_body,
                'response_code' => $response_code
            );
        }
        curl_close($curl);

        return $response;
    }

    public static function remove_non_numeric_characters($subject){
        $pattern = "/[^0-9]/";
        $replacement = '';
        return preg_replace (  $pattern , $replacement ,  $subject );
    }
}
?>