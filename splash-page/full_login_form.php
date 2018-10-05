<?php
/**
 * Created by PhpStorm.
 * User: ArmandoNCM
 * Date: 9/7/18
 * Time: 10:00 AM
 */
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

    <div id="login-container" class="captive-portal">
        <div id="header">
            <h1 id="business-name"><?php echo $html_location_name; ?></h1>
        </div>

        <img src="<?php echo $html_location_logo_url; ?>" id="logo">

        <p id="main-text">
            Conéctate gratis a nuestra red Wi-Fi.
        </p>

        <div id="btn-container-down">
            <a href="#login-form"><img src="/guest/s/default/splash-page/assets/images/down.png"
                                       class="btn-down"></a>
        </div>
    </div>

    <div id="login-form">
        <?php
        if (isset($isVerified) && !$isVerified){
            echo '<h3 class="error-message">Por favor recuerde confirmar su dirección de correo</h3><br>';
        }
        ?>
        <form action="<?php echo $html_form_process_url; ?>" method="post">
            <?php if (isset($html_error_message)): ?>
                <div class="error-message">
                    <strong>Error: </strong> <?php echo $html_error_message; ?>
                </div>
            <?php endif; ?>
            <p>
                <label for="txt-name">Nombre</label>
                <input type="text" class="form-control" name="name" id="txt-name" required>
            </p>
            <p>
                <label for="txt-email">Correo electrónico</label>
                <input type="email" class="form-control" name="email" id="txt-email" required>
            </p>
            <p>
                <label for="txt-birthdate">Fecha de nacimiento</label>
                <input type="date" class="form-control" name="birthdate" id="txt-birthdate" required>
            </p>
            <p>
		Género
                <br>
                <span>
                    <input style="display: inline" type="radio" name="gender" id="gender-male" value="male" required>
                    <label style="display: inline" for="gender-male">Masculino</label>
                </span>
                <br>
                <span>
                    <input style="display: inline" type="radio" name="gender" id="gender-female" value="female">
                    <label style="display: inline" for="gender-female">Femenino</label>
                </span>
                <br>
                <span>
                    <input style="display: inline" type="radio" name="gender" id="gender-unspecified" value="unspecified">
                    <label style="display: inline" for="gender-unspecified">Sin especificar</label>
                </span>
            </p>

            <p>
                Al registrarme estoy de acuerdo con los <strong><a href="#tos">Términos y Condiciones</a></strong> y
                <strong><a href="#tos">Políticas de Privacidad</a></strong> de Trinitip.
            </p>

            <?php
            foreach ($hidden_fields_array as $key => $value) {
                echo "<input type='hidden' name='$key' id='hfv-$key' value='$value' />";
            }
            echo "<input type='hidden' name='login_type' id='hfv-login_type' value='full' />";
            ?>

            <input type="submit" value="Conectar" class="btn-send">
        </form>
    </div>

    <hr>

    <a href="#tos"><img src="/guest/s/default/splash-page/assets/images/down.png"
                        class="btn-down-footer"></a>

    <div id="tos">
        <h2>Términos y condiciones</h2>
        <h3>CONTRATO DE USO DEL SERVICIO DE INTERNET GRATUITO</h3>
        <h4>DEFINICIONES:</h4>

        <p><strong>Trinitip SAS:</strong> Es una compañía encargada de suministrar una plataforma de servicios de
            inteligencia de negocios, marketing, publicidad y comunicación a través del acceso a internet mediante Wifi
            en diversos establecimientos comerciales en diversas ciudades de Colombia.
        </p>
        <p><strong>Usuario(s):</strong>Todo aquella persona que se encuentre dentro del establecimiento comercial y
            aproximadamente a 60 metros a la redonda y con lÍnea de vista a los puntos de wifi de los establecimientos
            aliados que se conecta al wifi que cuentan con el uso de la plataforma de Trinitip.</p>
        <p><strong>Cliente(s):</strong>Toda compañía que con fin comercial que implemente la plataforma de Trinitip en
            su establecimiento o que se promocione en la misma.</p>
        <p><strong>El siguiente servicio se encuentra regido por las siguientes cláusulas:</strong></p>
        <br>
        <p><strong>PRIMERO:</strong>El servicio de Internet Patrocinado es gratuito, ya que Trinitip SAS, no cobra al
            USUARIO que se encuentra dentro del Establecimiento Comercial ninguna tarifa anual o mensual por el acceso a
            la plataforma personalizada para el Establecimiento Comercial.</p>
        <p><strong>SEGUNDO:</strong>Como en una conexión a internet gratuita mediante Wifi, la velocidad de
            transferencia de datos no puede ser garantizada por Trinitip SAS.</p>
        <p><strong>TERCERO:</strong>Igualmente acepta de forma expresa y sin excepciones que el acceso y la utilización
            de la plataforma personalizada del Establecimiento Comercial, es bajo su única y exclusiva responsabilidad.
        </p>
        <p><strong>CUARTO:</strong>Aunque Trinitip SAS, ha implementado todas las medidas para garantizar la seguridad,
            no controla ni garantiza la ausencia de software malicioso, ni de otros elementos contenidos a los que
            acceda el USUARIO durante la navegación en la web al estar conectado a la plataforma personalizada del
            Establecimiento Comercial que puedan producir alteraciones en el sistema informático (equipos o programas)
            del USUARIO. Trinitip SAS se exime de cualquier responsabilidad por los datos y perjuicios de toda
            naturaleza que pueda deberse a la presencia de software malicioso en los contenidos que puedan producir
            alteraciones en el sistema.</p>
        <p><strong>QUINTO:</strong>Trinitip SAS pone a disposición de los usuarios y sus CLIENTES los sistemas de
            privacidad de datos personales (Habeas Data) que impiden el acceso por parte de terceros. En ese sentido,
            Trinitip SAS se exime de toda responsabilidad por los datos y por los perjuicios ocasionados en caso de que
            se produzca dicho acceso.</p>
        <p><strong>SEXTO:</strong>Si el USUARIO que se encuentra dentro del Establecimiento Comercial, no estuviera de
            acuerdo con el contenido de estas condiciones generales de navegación, debe abandonar la plataforma Wifi de
            Trinitip, sin poder acceder ni disponer de los servicios que se ofrecen.</p>
    </div>
</div>
</body>
</html>