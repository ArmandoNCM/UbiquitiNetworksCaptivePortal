<?php
if (!isset($html_location_name) || !$html_location_name){
    $html_location_name = 'Trinitip Store';
}

if (!isset($html_location_logo_url) || !$html_location_logo_url){
    $html_location_logo_url = '/ExtremeNetworksCaptivePortal/splash-page/assets/images/logo.png';
}

if (!isset($html_message_content) || !$html_message_content){
    $html_message_content = "Gracias por usar nuestros servicios";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Trinitip - Marketing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ExtremeNetworksCaptivePortal/splash-page/assets/css/styles.css">
</head>
<body>

<div class="container">
    <div id="login-container" class="succesful-container">
        <div id="header">
            <h1 id="business-name"><?php echo $html_location_name; ?></h1>
        </div>

        <img src="<?php echo $html_location_logo_url; ?>" id="logo">

        <?php
        if (isset($isVerified) && !$isVerified){
            echo '<h3 class="error-message">Por favor recuerde confirmar su dirección de correo</h3><br>';
        }
        ?>
        
        <p id="main-text">
            <?php echo $html_message_content; ?>
        </p>

        <!-- <a href="#" class="btn-send">Cerrar</a> -->

    </div>
</div>

</body>
</html>