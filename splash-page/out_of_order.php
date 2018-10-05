<?php

if (!isset($html_location_logo_url) || !$html_location_logo_url){
    $html_location_logo_url = '/guest/s/default/splash-page/assets/images/logo.png';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Trinitip - Marketing</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/guest/s/default/splash-page/assets/css/styles.css">
</head>
<body>

<div class="container">
    <div id="login-container" class="succesful-container">
        <div id="header">
            <h1 id="business-name">Oops!</h1>
        </div>

        <img src="<?php echo $html_location_logo_url; ?>" id="logo">

        <p id="main-text">
            Location ouf of order
        </p>
    </div>
</div>

</body>
</html>