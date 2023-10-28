<?php

    // Paso 1) Importamos el archivo de configuración para poder conectarnos a la base de datos.
    require_once "./configuration.php";

    // Paso 2) Inicializa la variable $datosErroneos para evitar errores.
    $datosErroneos = array();

    // Paso 3) Detecta el envío de datos y llama a las validaciones.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') validations();

    // Función de validación.
    function validations()
    {
        // 4.1) Validación del nombre.
        if (!isset($_POST['nombre']) || empty($_POST['nombre'])) $datosErroneos[] = "❌ El campo nombre contiene un error.";

        // 4.2) Validación del precio.
        if (!isset($_POST['precio']) || empty($_POST['precio']) || !is_numeric($_POST['precio']) || $_POST['precio'] <= 0) $datosErroneos[] = "❌ El campo precio contiene un error.";

        // 4.3) Validación de la imagen.
        if (!isset($_FILES['imagen']) || empty($_FILES['imagen']['name'])) $datosErroneos[] = "❌ El campo imagen contiene un error.";

        // 4.4) Validación del formato.
        else
        {
            $_photoName = $_FILES['imagen']['name'];
            $_photoError = $_FILES['imagen']['error'];
            $_photoSize = $_FILES['imagen']['size'];
            $_photoMaxSize = 1024 * 1024 * 1;
            $_photoExtension = pathinfo($_photoName, PATHINFO_EXTENSION);
            $_photoFormats = array('jpg', 'png', 'gif', 'jfif');

            if ($_photoError === true || $_photoSize > $_photoMaxSize || $_photoSize < 1 || !in_array($_photoExtension, $_photoFormats)) $datosErroneos[] = "❌ El formato de la imagen contiene un error.";
        }

        // 4.5) Validación de la categoría.
        if (!isset($_POST['categoria']) || empty($_POST['categoria'])) $datosErroneos[] = "❌ El campo categoría contiene un error.";

        // 4.6) Si no hay datos erróneos, almacenamos los datos y se lo comunicamos al usuario.
        if (empty($datosErroneos)) 
        {
            store_imagen(); 
            save_to_database(); 
            echo "<script> alert('¡Datos almacenados correctamente!') </script>";
        }

        // 4.7) Si hubo errores, los mostramos y facilitamos un enlace para volver a rellenar el formulario.
        elseif (!empty($datosErroneos))
        {
            echo '<div id="mensajes">';

                foreach ($datosErroneos as $value) {echo "<p>$value</p> <br/>";}

                echo '<a href="./crear_producto.php">Volver a rellenar formulario</a>';

            echo '</div>';
        }
    }

    // Función almacena la imagen.
    function store_imagen()
    {
        
    }

    // Función intenta conectarse a la base de datos para almacenar los datos nuevos.
    function save_to_database()
    {

    }

?>


<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Crear producto</title>

        <link rel="stylesheet" href="style.css">
    </head>

    <body>

        <!-- Almacena la aplicación -->
        <div class="container">

            <!-- Incluye el menú dentro de la aplicación -->
            <?php
                include_once "./index.php";
            ?>

            <!-- Aquí va el cuerpo del contenido -->
            <main>

                <!-- Se envía el formulario a sí mismo mediante el método POST y tras el envío se ejecuta la validación -->
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                    <div class="inputs">
                        <label for="nombre">Nombre:</label>
                        <br/>
                        <input type="text" id="nombre" name="nombre">
                    </div>

                    <br/>

                    <div class="inputs">
                        <label for="precio">Precio:</label>
                        <br/>
                        <input type="number" id="precio" name="precio">
                    </div>

                    <br/>

                    <div class="inputs">
                        <label for="imagen">Imagen:</label>
                        <br/>
                        <input type="file" id="imagen" name="imagen" accept=".jpg,.png,.gif,.jfif">
                    </div>

                    <br/>

                    <div class="inputs">
                        <label for="categoria">Categoría:</label>
                        <br/>
                        <input type="text" id="categoria" name="categoria">
                    </div>

                    <br/>

                    <button type="submit">Envíar</button>

                </form>
            </main>
        </div>
    </body>
</html>
