<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Formulario de autentificación</title>
    </head>
    <body>
        <form action="form.php" method="post">
            <div>
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" pattern=".+@example\.com" size="30" required />
            </div>
            <br/><br/>
            <div>
                <label for="password"></label>
                <input type="password" id="password" name="password" minlength="8" maxlength="30" required />
            </div>
            <br/><br/>
            <input type="submit" value="Enviar">
        </form>
    </body>
</html>