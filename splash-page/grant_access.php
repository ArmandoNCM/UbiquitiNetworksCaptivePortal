<?php
require_once(dirname(__FILE__).'/../class/Unifi.php');

$destination = "https://www.google.com/";
$minutes = $seconds_allowed / 60;

$unifi = new Unifi();

$unifi->login();

$unifi->authorize_guest($client_mac, $minutes, NULL, NULL, NULL, $access_point_mac);

header('Location: '.$destinationl);

exit();

?>