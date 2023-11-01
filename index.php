<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #saludo
        {
            width: 100%;
            color: black;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Incluye el menú dentro de la aplicación -->
        <?php
            include_once "./menu.php";
        ?>
        <main>
            <h1 id="saludo">¡Hola! 👋 <br/><br/> Bienvenido a la Actividad 3.1 de DSW/DAW</h1>
        </main>
    </div>
</body>
</html>