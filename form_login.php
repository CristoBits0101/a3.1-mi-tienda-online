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
        <h1>Formulario de autentificación:</h1>
        <br/>
        <form action="form.php" method="post">
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
            <input type="submit" id="login" name="login" value="Entrar">
            <br/><br/>
            <a href="./form_register.php">Ir a registrarse</a>
        </form>
    </body>
</html>