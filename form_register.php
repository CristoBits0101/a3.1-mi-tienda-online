<?php

    // Iniciamos sesión para ver si el formulario fue enviado previamente y contenía errores.
    session_start();

    // Comprueba si se declararon campos con errores y los serializa.
    function imprimirError($campo) 
    {
        if (isset($_SESSION['error_messages'][$campo]))
            echo '<div class="error-message">' . $_SESSION['error_messages'][$campo] . '</div>';
    }
    
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulario de autentificación</title>

        <style>
            body                {   display: flex           ; flex-direction: column; justify-content: center; align-items: center;}
            h1                  { font-size: 2rem           ;                                                                      }
            form                {    border: 1px solid black;        padding: 3rem  ;                                              }
            input               {margin-top: 0.5rem         ;                                                                      }
            input[type="submit"]{margin-top: 0              ;         margin: 0 auto;                                              }
        </style>

    </head>

    <body>

        <h1>Formulario de registro:</h1>

        <br/>

        <form action="form.php" method="post">

            <div>
                <label for="name">Nombre:</label>
                <br/>
                <input type="text" id="name" name="name" required minlength="3" maxlength="20" size="30" />
            </div>

            <br/><br/>

            <div>
                <label for="email">Correo electrónico:</label>
                <br/>
                <input type="email" id="email" name="email" pattern=".+@example\.com" size="30" required />
            </div>

            <br/><br/>

            <div>
                <label for="password">Contraseña:</label>
                <br/>
                <input type="password" id="password" name="password" minlength="8" maxlength="30" size="30" required />
            </div>

            <br/><br/>

            <input type="submit" id="register" name="register"  value="Registrarse">

            <br/><br/>

            <a href="./form_login.php">Iniciar sesión</a>

        </form>
    </body>
</html>
