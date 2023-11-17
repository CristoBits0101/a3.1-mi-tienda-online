<?php

    // 1. Iniciamos sesión para ver si el formulario fue enviado previamente y contenía errores.
    session_start();

    // 2. Comprueba si se declararon campos con errores y los serializa.
    function serializeError($field)
    {
        if (isset($_SESSION['error_messages'][$field]))
            echo '<br/><div class="error-message">' . $_SESSION['error_messages'][$field] . '</div>';
    }

    // 3. Comprueba si hubo algún error al intentar loguear al usuario.
    if (isset($_SESSION['wrong_user']['login']))
    {
        // Recogemos el error que trae la variable de sesión.
        $errorMessage = addslashes($_SESSION['wrong_user']['login']);

        // Alertamos sobre el error producido al intentar loguearse.
        echo "<script>alert('" . $errorMessage . "');</script>";
    }

    // 4. Si el usuario ya está registrado, le decimos que inicie sesión.
    elseif (isset($_SESSION['wrong_user']['register']))
    {
        // Recogemos el error que trae la variable de sesión.
        $errorMessage = addslashes($_SESSION['wrong_user']['register']);

        // Alertamos sobre el error producido al intentar registrarse.
        echo "<script>alert('" . $errorMessage . "');</script>";
    }

    // 5. Verifica si hay una sesión activa.
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))
    {
        $_SESSION['session_activa'] = 'Usted ya ha iniciado sesión.';

        // Redirige al usuario a la página de inicio.
        header("Location: index.php");

        // Asegura que el script se detenga después de la redirección.
        exit; 
    }

    // 6. Sin intento ir a otra página sin loguearse se lo comunicamos.
    if (isset($_SESSION['session_inactiva']))
        echo "<script>alert('¡Debe inicar sesión primero para poder usar nuestros servicios!')</script>";

    // 7. Si el usuario se registro correctamente se lo comunicamos para si quiere iniciar sesión.
    if (isset($_SESSION['register_valid']))
        echo "<script>alert('¡Usted se ha registrado correctamente, ya puede iniciar sesión si lo desea!')</script>";

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulario de autentificación</title>

        <style>
            body 
            {
                font-family: Roboto, sans-serif;
                font-size: 16px;
                display: flex; 
                flex-direction: column; 
                justify-content: center; 
                align-items: center;
            }

            form 
            {
                border: 1px solid black;
                padding: 3rem;
                background-color: #ffffff;
                color: #000000;
            }

            input 
            {
                margin-top: 0.5rem;
                border: 1px solid #ccc;
                padding: 10px;
            }

            input[type="submit"] 
            {
                margin-top: 0;
                margin: 0 auto;
                background-color: #000000;
                color: #ffffff;
                font-size: 18px;
            }

            .error-message 
            {
                color: red;
            }

            .icon 
            {
                font-size: 16px;
            }
        </style>

    </head>

    <body>

        <h1>Formulario de autentificación:</h1>

        <br/>

        <form action="form.php" method="post">

            <div>
                <label for="email">Correo electrónico:</label>
                <br/>
                <input type="email" id="email" name="email" pattern=".+@.+" size="30" required />
                <?php serializeError('email'); ?>
            </div>

            <br/><br/>

            <div>
                <label for="password">Contraseña:</label>
                <br/>
                <input type="password" id="password" name="password" minlength="8" maxlength="30" size="30" required />
                <?php serializeError('password'); ?>
            </div>

            <br/><br/>

            <?php
                // Limpia la variable de sesión después de imprimir los errores y antes de volver a enviar el formulario.
                session_unset();

                // Destruye la sesión actual después de imprimir los errores y antes de volver a enviar el formulario.
                session_destroy();
            ?>

            <input type="submit" id="login" name="login" value="Entrar">

            <br/><br/>

            <a href="./form_register.php">Ir a registrarse</a>

        </form>
    </body>
</html>
