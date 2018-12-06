<?php
Log::print("Authenticating Client with MAC Address: $client_mac", "info", __FILE__, __LINE__);

require_once(dirname(__FILE__).'/../class/Unifi.php');
if ($open_external_browser) {
    $destination = "googlechrome://navigate?url=https://redirect.sundevs.cloud/index.html";
} else {
    $destination = "https://redirect.sundevs.cloud/index.html";
}

$minutes = 60 * 24;

$unifi = new Unifi('mpascuas', 'IPwork2018', NULL, NULL, NULL);

$unifi->login();

$unifi->authorize_guest($client_mac, $minutes, NULL, NULL, NULL, $access_point_mac);

header('Location: '.$destination);

exit();

?>