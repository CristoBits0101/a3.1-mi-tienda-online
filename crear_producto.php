<?php

    // Importamos el archivo de configuración para poder conectarnos a la base de datos.
    require_once "./configuration.php";

    // Válida los datos y archivos enviados por el formulario.
    function validations()
    {

        // Validación del nombre.
        (
            !isset($_REQUEST['nombre'])
            ||
            empty($_REQUEST['nombre'])
        )
            ?
                $datosErroneos[] = "El campo nombre contiene un error ❌"
            :
                null;

        // Validación del precio.
        (
            !isset($_REQUEST['precio'])
            ||
            empty($_REQUEST['precio'])
            ||
            !is_numeric($_REQUEST['precio'])
            ||
            $_REQUEST['precio'] <= 0
        )
            ?
                $datosErroneos[] = "El campo precio contiene un error ❌"
            :
                null;

        // Validación de la imagen.
        $_photoName      = $_FILES['imagen']['name'];     
        $_photoError     = $_FILES['imagen']['error'];    
        $_photoSize      = $_FILES['imagen']['size'];    
        $_photoMaxSize   = 1024 * 1024 * 1;
        $_photoExtension = pathinfo($_photoName, PATHINFO_EXTENSION);
        $_photoFormats   = array('jpg','png','gif','jfif');

        (
            !isset($_FILES['imagen'])
            ||
            empty($_FILES['imagen'])
            ||
            $_photoError === true
            ||
            $_photoSize   >  $_photoMaxSize
            ||
            $_photoSize   <  1
            ||
            !in_array($_photoExtension, $_photoFormats)
        )
            ?
                $datosErroneos[] = "El campo imagen contiene un error ❌"
            :
                store_imagen();

        // Validación de la categoría.
        // (

        // )
        //     ?
        //         $datosErroneos[] = "El campo imagen contiene un error ❌"
        //     :
        //         null;
    }

    // Almacena la imagen.
    function store_imagen()
    {

    }

    // Almacena los datos en la base de datos.
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
        <div class="content">

            <!-- Incluye el menú dentro de la aplicación -->
            <?php
                include_once "./index.php";
            ?>

            <!-- Aquí va el cuerpo del contenido -->
            <main>

                <!-- Se envía el formulario a sí mismo mediante el método POST y tras el envío se ejecuta la validación -->
                <form 
                    action="<?php echo $_SERVER['PHP_SELF']; ?>"
                    method="post"
                    onsubmit="<?php validations(); ?>
                ">

                    <div>
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre">
                    </div>

                    <div>
                        <label for="precio">Precio</label>
                        <input type="number">
                    </div>

                    <div>
                        <label for="imagen">Imagen</label>
                        <input type="file">
                    </div>

                    <div>
                        <label for="categoria">Categoría</label>
                        <input type="text">
                    </div>

                    <button type="submit">Envíar</button>

                </form>

            </main>

        </div>

    </body>

</html>