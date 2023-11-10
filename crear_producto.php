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
        if (!isset($_POST['categoria']) || empty($_POST['categoria']) || !is_numeric($_POST['categoria'])) $datosErroneos[] = "❌ El campo categoría contiene un error.";

        // 4.6) Si no hay datos erróneos, almacenamos los datos y se lo comunicamos al usuario.
        if (empty($datosErroneos)) 
        {
            save_data();
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
        $target_dir = "ficheros\\";                                        // Directorio donde se van a guardar las imágenes.
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);   // basename devuelve el nombre de la imagen sin el directorio.

        $counter = 0;                                                       // Incrementa el nombre de la imagen.

        while (file_exists($target_file)) 
        {
            $counter++;                                                     // Si la imagen existe, aumentar el valor de counter en 1.

            $pathinfo = pathinfo($target_file);                             // Obtener la información sobre la imagen.

            $name = $pathinfo["filename"];                                  // Obtener el nombre de la imagen.
            $extension = $pathinfo["extension"];                            // Obtener la extesión de la imagen.
    
            $target_file =  $target_dir                                     // Directorio donde se van a guardar las imagenes.
                            . 
                            $name                                           // Nombre de la imagen.
                            . 
                            "_"                                             // Añadimos la barra baja para concatenar el número incremental.
                            . 
                            $counter                                        // Concatenamos el contador para diferenciar la nueva imagen de la vieja.
                            .
                            "."
                            .
                            $extension;                                     // Extensión de la imagen.
        }

        move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);    // Movemos la imagen de la ruta temporal a la ruta de destino.

        return basename($target_file);                                      // Devuelve el nombre de archivo de la imagen almacenada.
    }

    // Función intenta conectarse a la base de datos para almacenar los datos nuevos.
    function save_data()
    {
        try
        {
            // Almacena la imagen y obtiene su nombre de archivo.
            $imagePath = store_imagen();

            // Establece la conexión a la base de datos.
            $conn = connect_to_database();

            // Establecemos una consulta preparada para insertar datos en la tabla 'productos'.
            $stmt = $conn->prepare("INSERT INTO productos (Nombre, Precio, Imagen, Categoría) VALUES (:nombre, :precio, :imagen, :categoria)");

            // Los marcadores de parámetros como :nombre, :precio, :imagen y :categoria se utilizan en lugar de valores directos para evitar la inyección SQL.
            $stmt->bindParam(':nombre', $_POST['nombre']);         
            $stmt->bindParam(':precio', $_POST['precio']);
            $stmt->bindParam(':imagen', $imagePath);
            $stmt->bindParam(':categoria', $_POST['categoria']);

            // Ejecutamos la consulta preparada para insertar los datos en la base de datos. Los valores vinculados a los marcadores de parámetros se utilizarán en la consulta.
            $stmt->execute();
        }
        
        catch(PDOException $e)
        {
            echo "Error al insertar datos: " . $e->getMessage();
        }
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
                include_once "./menu.php";
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
                        <input type="file" id="imagen" name="imagen" accept=".jpg,.png,.gif,.jfif" />
                    </div>

                    <br/>

                    <div class="inputs">
                        <p style="margin: 0 0 0.2rem 0 ;"><b>Categoría:</b></p>
                        <select name="categoria" id="categoria">
                            <?php
                                // Paso 1) Realizamos una conexión a la base de datos.
                                $connection = connect_to_database();

                                // Paso 2) Guardamos la consulta en la variable sql.
                                $sql_query = "SELECT id, nombre FROM Categorías";

                                // Paso 3) Ejecutamos la consulta dentro de la conexión.
                                $stmt = $connection->query($sql_query);

                                // Paso 4) Comprobamos que la consulta se realizó correctamente.
                                if ($stmt)
                                {
                                    /**
                                     *  - Cada vuelta fetch usa la conexión para retornar un registro de la tabla categorías en un array.
                                     *  - Este array se almacena en la variable row.
                                     */
                                    while ($row = $stmt->fetch())
                                    {
                                        // Imprimimos los valores del array.
                                        echo '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
                                    }
                                }
                                
                                else
                                {
                                    // En caso de no poder mostrar las categorías lo indicamos.
                                    echo '<option value="null">Las categorías no están disponibles</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <br/>

                    <button type="submit">Envíar</button>

                </form>
            </main>
        </div>
    </body>
</html>
