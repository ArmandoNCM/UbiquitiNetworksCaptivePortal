<?php
require_once(dirname(__FILE__).'/class/Log.php');

$client_mac = $_GET['id'];
$access_point_mac = $_GET['ap'];
$ssid = $_GET['ssid'];

Log::print("Received GET from $client_mac", "message", __FILE__, __LINE__);
?>

<html>
<head>
        <style>
                .mac_address {
                        color: red;
                }
        </style>
</head>
<body>
<h1>Welcome !</h1>
<p>
        <h2>You're connected to <?php echo $ssid; ?> !</h2>
        <br>
        Your MAC Address is: 
        <span class="mac_address"><?php echo $client_mac ?></span>
        <br>
        The Access Point's MAC Address is: 
        <span class="mac_address"><?php echo $access_point_mac ?></span>
</p>
</body>