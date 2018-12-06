<?php
require_once(dirname(__FILE__).'/Log.php');

class SimpleAWS
{
    const AWS4_ERROR_NONE = 0;
    const AWS4_ERROR_NULL_INPUT = 1;
    const AWS4_ERROR_INPUT_BUFFER_TOO_SMALL = 2;
    const AWS4_ERROR_INVALID_PROTOCOL = 3;
    const AWS4_ERROR_INPUT_URL_TOO_BIG = 4;
    const AWS4_ERROR_INPUT_ID_TOO_BIG = 5;
    const AWS4_ERROR_INPUT_KEY_TOO_BIG = 6;
    const AWS4_ERROR_INVALID_REGION = 7;
    const AWS4_ERROR_INVALID_SIGNATURE = 8;
    const AWS4_ERROR_MISSING_QUERY = 9;
    const AWS4_ERROR_MISSING_QUERY_DATE = 10;
    const AWS4_ERROR_MISSING_QUERY_SIGNED_HEADERS = 11;
    const AWS4_ERROR_MISSING_QUERY_EXPIRES = 12;
    const AWS4_ERROR_MISSING_QUERY_SIGNATURE = 13;
    const AWS4_ERROR_MISSING_QUERY_CREDENTIAL = 14;
    const AWS4_ERROR_MISSING_QUERY_ALGORITHM = 15;
    const AWS4_ERROR_MISSING_QUERY_PARAMS = 16;
    const AWS4_ERROR_MISSING_CRED_PARAMS = 17;
    const AWS4_ERROR_STALE_REQUEST = 2001;
    const AWS4_ERROR_UNKNOWN_IDENTITY = 2002;
    const AWS4_EXTREME_REQUEST = "aws4_request";
    const AWS4_MAX_URL_SIZE = 512;
    const AWS4_HTTP_REQ = "http://";
    const AWS4_HTTPS_REQ = "https://";
    const AWS4_MANDATORY_CRED_PARAMS = 4;

    const EWC_HTTP_REQ = "http://";
    const EWC_HTTPS_REQ = "https://";
    const EWC_REDIRECT_TARGET = "/ext_approval.php?";

    /**
     * Method to verify the AWS signature based on given full URL address.
     *
     * @param string $pUrl
     * @param array $awsKeyPairs identity, shared secret key pairs
     * @return AWS error code
     */
    public static function verifyAwsUrlSignature($pUrl, $awsKeyPairs)
    {
        // Perform basic validation
        if ($pUrl == null) {
            return SimpleAWS::AWS4_ERROR_NULL_INPUT;
        }
        if (2 * SimpleAWS::AWS4_MAX_URL_SIZE < strlen($pUrl)) {
            return SimpleAWS::AWS4_ERROR_INPUT_URL_TOO_BIG;
        }
        if (stripos($pUrl, SimpleAWS::AWS4_HTTP_REQ) != 0 || stripos($pUrl, SimpleAWS::AWS4_HTTPS_REQ) != 0) {
            return SimpleAWS::AWS4_ERROR_INVALID_PROTOCOL;
        }
        $urlParams = parse_url($pUrl);
        if (!isset($urlParams['query'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY;
        }
        $queryParams = explode("&", $urlParams['query']);
        foreach ($queryParams as $el) {
            $arr = explode("=", $el);
            $q[$arr[0]] = $arr[1];
        }
        $valResult = SimpleAWS::validateQueryParms($q);
        if (SimpleAWS::AWS4_ERROR_NONE != $valResult) {
            return $valResult;
        }
        // Done with the basic validations.
        $date = $q['X-Amz-Date'];
        $sign = $q['X-Amz-Signature'];
        $credentVal = rawurldecode($q['X-Amz-Credential']);
        ksort($q);
        // Remove the signature from the list of parameters over
        // which the signature will be recomputed.
        unset($q['X-Amz-Signature']);
        $credentAttrs = explode("/", $credentVal);
        $pKey = $credentAttrs[0];
        if (SimpleAWS::AWS4_MAX_URL_SIZE < strlen($pKey)) {
            return SimpleAWS::AWS4_ERROR_INPUT_KEY_TOO_BIG;
        }
        if (SimpleAWS::AWS4_MANDATORY_CRED_PARAMS > count($credentAttrs)) {
            return SimpleAWS::AWS4_ERROR_MISSING_CRED_PARAMS;
        }
        if (!isset($awsKeyPairs[$pKey])) {
            return SimpleAWS::AWS4_ERROR_UNKNOWN_IDENTITY;
        }
        $scope = 
            $credentAttrs[1]
            ."/"
            .$credentAttrs[2]
            ."/"
            . $credentAttrs[3]
            ."/"
            .$credentAttrs[4];
        if (array_key_exists('port', $urlParams)){
            $port = $urlParams['port'];
        }
        $host = strtolower($urlParams['host']);
        if (isset($port) && $port && (($urlParams['scheme'] == 'https' && $port != 443) || ($urlParams['scheme'] == 'http' && $port != 80))) {
            $host .= ':' . $port;
        }
        $canonical_request = SimpleAWS::getCanonicalFFECPContent($q,$host, $urlParams['path']);
        $stringToSign = "AWS4-HMAC-SHA256\n{$date}\n{$scope}\n" . hash('sha256', $canonical_request);
        $signingKey = SimpleAWS::getSigningKey($credentAttrs[1], $credentAttrs[2],$credentAttrs[3], $awsKeyPairs[$pKey]);
        $mySign = hash_hmac('sha256', $stringToSign, $signingKey);
        if (strcmp($mySign, $sign)) {
            // If string comparison results in something other than 0
            return SimpleAWS::AWS4_ERROR_INVALID_SIGNATURE;
        }
    }

    /**
     * Method to verify that the query parameters contain the elements
     * required in the response to the controller and the ones required to
     * sign the request.
     * @param array $qParams: an associative array in which the key of an
     * entry is the name of a query parameter and the corresponding value
     * is the value of that parameter.
     * @return an AWS_ERROR code.
     */
    private static function validateQueryParms($qParams)
    {
        if (is_null($qParams)) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY;
        }
        if ((!isset($qParams['wlan'])) or (!isset($qParams['token'])) or (!isset($qParams['dest']))) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_PARAMS;
        }
        if (!isset($qParams['X-Amz-Signature'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_SIGNATURE;
        }
        if (!isset($qParams['X-Amz-Algorithm'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_ALGORITHM;
        }
        if (!isset($qParams['X-Amz-Credential'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_CREDENTIAL;
        }
        if (!isset($qParams['X-Amz-Date'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_DATE;
        }
        if (!isset($qParams['X-Amz-Expires'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_EXPIRES;
        }
        if (!isset($qParams['X-Amz-SignedHeaders'])) {
            return SimpleAWS::AWS4_ERROR_MISSING_QUERY_SIGNED_HEADERS;
        }
        // The date & expires parameters exist in the request.
        // Verify that the request is not stale or replayed.
        $redirectedAt = DateTime::createFromFormat('Ymd?Gis?', $qParams['X-Amz-Date'], new DateTimeZone("UTC"));
        $expires = $qParams['X-Amz-Expires'];
        $now = date_create();
        $delta = $now->getTimestamp() - $redirectedAt->getTimestamp();
        // The following gives some lattitude for clocks not being synched
        if (($delta < -10) or ($delta > $expires)) {
            $timingMessage =
            date("Y-m-d H:i:sZ", $now->getTimestamp())
            ."\n"
            ."Redirected at: "
            .date("Y-m-d H:i:sZ", $redirectedAt->getTimestamp())
            ."\n"
            .$now->getTimeZone()->getName()
            ."\n"
            .$redirectedAt->getTimeZone()->getName()
            ."\n"
            .$expires
            ."\n"
            .$delta;
            Log::print($timingMessage, "message", __FILE__, __LINE__);
            return SimpleAWS::AWS4_ERROR_STALE_REQUEST;
        }
        return SimpleAWS::AWS4_ERROR_NONE;
    }

    /**
     * Method to generate the AWS signed URL address
     * @param string $pUrl: the URL that need to be appened with AWS parameters
     * @param string $identity: the AWS identity
     * @param string $sharedSecret: the secret shared with the controller
     * @param string $region: the region component of the scope
     * @param string $service: the service component of the scope
     * @param int $expires: number of seconds till presigned URL is untrusted.
     * @return URL string with AWS parameters
     **/
    public static function createPresignedUrl($pUrl, $identity, $sharedSecret, $region,$service, $expires) {
        $urlParams = parse_url($pUrl);
        $httpDate = gmdate('Ymd\THis\Z', time());
        $scopeDate = substr($httpDate, 0, 8);
        $scope = "{$scopeDate}/" . $region . "/" . $service . "/" . SimpleAWS::AWS4_EXTREME_REQUEST;
        $credential = $identity . '/' . $scope;
        $duration = $expires;
        //set the aws parameters
        $awsParams = array(
            'X-Amz-Date' => $httpDate,
            'X-Amz-Algorithm' => 'AWS4-HMAC-SHA256',
            'X-Amz-Credential' => $credential,
            'X-Amz-SignedHeaders' => 'host',
            'X-Amz-Expires' => $duration,
        );
        parse_str($urlParams['query'], $q);
        $q = array_merge($q, $awsParams);
        ksort($q);
        if (array_key_exists('port', $urlParams)){
            $port = $urlParams['port'];
        }
        $host = strtolower($urlParams['host']);
        if (isset($port) && (($urlParams['scheme'] == 'https' && $port != 443) || ($urlParams['scheme'] == 'http' && $port != 80))) {
            $host .= ':' . $port;
        }
        $canonical_request = SimpleAWS::getCanonicalFFECPContent($q, $host, $urlParams['path'], true);
        $stringToSign = "AWS4-HMAC-SHA256\n{$httpDate}\n{$scope}\n" . hash('sha256', $canonical_request);
        $signingKey = SimpleAWS::getSigningKey($scopeDate, $region, $service, $sharedSecret);
        $q['X-Amz-Signature'] = hash_hmac('sha256', $stringToSign, $signingKey);
        $p = substr($pUrl, 0, strpos($pUrl, '?'));
        $queryParams = array();
        foreach ($q as $k => $v) {
            $queryParams[] = "$k=" . rawurlencode($v);
        }
        $p .= '?' . implode('&', $queryParams);
        return $p;
    }

    /**
     * Method to generate the AWS signing key
     * @param string $shortDate: short date format (20140611)
     * @param string $region: Region name (us-east-1)
     * @param string $service: Service name (s3)
     * @param string $secretKey Secret Access Key
     * @return string
     */
    private static function getSigningKey($shortDate, $region, $service, $secretKey)
    {
        $dateKey = hash_hmac('sha256', $shortDate, 'AWS4'.$secretKey, true);
        $regionKey = hash_hmac('sha256', $region, $dateKey, true);
        $serviceKey = hash_hmac('sha256', $service, $regionKey, true);
        return hash_hmac('sha256', SimpleAWS::AWS4_EXTREME_REQUEST, $serviceKey, true);
    }

    /**
     * Create the canonical context for the AWS service
     * @param array $queryHash the query parameter hash
     * @param string $host host name or ip address for the target service
     * @param string $path the service address for the target service
     * @param boolean $encode determine if the query parameter need to be encoded or not.
     * @return string the canonical content for the request
     */
    private static function getCanonicalFFECPContent($queryHash, $host, $path, $encode = false) {
        $queryParams = array();
        foreach ($queryHash as $k => $v) {
            if ($encode) {
                $v = rawurlencode($v);
            }
            $queryParams[] = "$k=$v";
        }
        $canonical_request = 
            "GET\n"
            .$path
            ."\n"
            .implode('&', $queryParams)
            ."\n"
            .'host:'.$host
            ."\n\nhost\nUNSIGNED-PAYLOAD";
        return $canonical_request;
    }

    /**
     * Create user readable error message
     * @param integer $eid error code after verifying the AWS URL
     * @return string the error message
     */
    public static function getAwsError($eid)
    {
        $forAws = " for Amazon Web Service request.";
        switch ($eid) {
            case SimpleAWS::AWS4_ERROR_NULL_INPUT:
                $res = "Empty input" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_INPUT_BUFFER_TOO_SMALL:
                $res = "Input buffer is too small" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_INVALID_PROTOCOL:
                $res = "Invalid protocol" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_INPUT_URL_TOO_BIG:
                $res = "Input url is too big" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_INPUT_ID_TOO_BIG:
                $res = "Input ID is too big" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_INVALID_REGION:
                $res = "Invalid region" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_INVALID_SIGNATURE:
                $res = "Invalid signature" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY:
                $res = "Missing all query parameters" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_DATE:
                $res = "Missing query date" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_SIGNED_HEADERS:
                $res = "Missing query signed headers" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_EXPIRES:
                $res = "Missing query expires" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_SIGNATURE:
                $res = "Missing query signature" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_CREDENTIAL:
                $res = "Missing query credential" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_ALGORITHM:
                $res = "Missing query algorithm" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_QUERY_PARAMS:
                $res = "Missing query parameter" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_MISSING_CRED_PARAMS:
                $res = "Missing credential parameters" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_STALE_REQUEST:
                $res = "Invalid request date" . $forAws;
                break;
            case SimpleAWS::AWS4_ERROR_UNKNOWN_IDENTITY:
                $res = "Unrecognized identity or identity without a shared secret.";
                break;
            default:
                $res = "Successfully validated" . $forAws;
                break;
        }
        return $res;
    }

    /**
     * Return the AWS validation error message
     * @param string $pUrl
     * @param string $awsKeyPairs
     * @return string the error message
     */
    public static function getUrlValidationResult($pUrl, $awsKeyPairs)
    {
        $eid = SimpleAWS::verifyAwsUrlSignature($pUrl, $awsKeyPairs);
        return SimpleAWS::getAwsError($eid);
    }

    public static function is_not_empty($string) {
        return (isset($string) && (0 < strlen($string)));
    }

    /**
     * A method that assembles an unsigned URL out of the
     * the input from the user's succesful login
     * @param string $ewc_ip IP or FQDN of controller
     * @param int $ewc_port Port on controller to receive redirection
     * @param bool $useHttps Whether the redirect uses HTTP or HTTPS
     * @param string $token Identifier for the station's session
     * @param string $username The name the station's user logged in with
     * @param int $wlanid Identifier for the WLAN the station is using
     * @param string $assigned_role Name of the access control role to assign
     * @param string $dest The URL the station was trying to get to
     * @param int $max_duration The maximum length of the station's session.
     */
    public static function makeUnsignedUrl($ewc_ip, $ewc_port, $useHttps, $token, $username, $wlanid, $assigned_role, $dest, $max_duration) {
        $redirectUrl = ($useHttps ? (SimpleAWS::EWC_HTTPS_REQ) : (SimpleAWS::EWC_HTTP_REQ)) . $ewc_ip;
        if ((80 != $ewc_port) && (443 != $ewc_port)) {
            $redirectUrl .= ":" . $ewc_port;
        }
        $redirectUrl .= SimpleAWS::EWC_REDIRECT_TARGET
            .'token=' . rawurlencode($token)
            .'&wlan=' . rawurlencode($wlanid)  
            .'&username=' . rawurlencode($username)
            .(SimpleAWS::is_not_empty($dest) ? '&dest=' . rawurlencode($dest) : '')
            .(SimpleAWS::is_not_empty($assigned_role) ? '&role=' . rawurlencode($assigned_role) : '')
            .(SimpleAWS::is_not_empty($max_duration) ? '&opt27=' . $max_duration : '');
        return $redirectUrl;
    }
}
