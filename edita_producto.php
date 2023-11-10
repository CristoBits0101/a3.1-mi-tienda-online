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

            foreach ($datosErroneos as $value) 
            {
                echo "<p>$value</p> <br/>";
            }

            echo '<a href="./crear_producto.php">Volver a rellenar formulario</a>';

            echo '</div>';
        }
    }

    // Función almacena la imagen.
    function store_imagen()
    {
        $target_dir = "ficheros\\";                                         // Directorio donde se van a guardar las imágenes.
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);   // basename devuelve el nombre de la imagen sin el directorio.

        $counter = 0;                                                       // Incrementa el nombre de la imagen.

        while (file_exists($target_file)) 
        {
            $counter++;                                                     // Si la imagen existe, aumentar el valor de counter en 1.

            $pathinfo = pathinfo($target_file);                             // Obtener la información sobre la imagen.

            $name = $pathinfo["filename"];                                  // Obtener el nombre de la imagen.
            $extension = $pathinfo["extension"];                            // Obtener la extesión de la imagen.

            $target_file =  
                $target_dir                                                 // Directorio donde se van a guardar las imagenes.
                .
                $name                                                       // Nombre de la imagen.
                .
                "_"                                                         // Añadimos la barra baja para concatenar el número incremental.
                .
                $counter                                                    // Concatenamos el contador para diferenciar la nueva imagen de la vieja.
                .
                "."
                .
                $extension;                                                 // Extensión de la imagen.
        }

        move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);    // Movemos la imagen de la ruta temporal a la ruta de destino.

        return basename($target_file);                                      // Devuelve el nombre de archivo de la imagen almacenada.
    }

    // Función intenta conectarse a la base de datos para actualizar los datos existentes.
    function save_data()
    {
        try 
        {
            // Almacena la imagen y obtiene su nombre de archivo.
            $imagePath = store_imagen();

            // Establece la conexión a la base de datos.
            $conn = connect_to_database();

            // Modificamos la consulta para realizar una actualización (UPDATE) en lugar de una inserción (INSERT).
            $stmt = $conn->prepare("UPDATE productos SET Nombre = :nombre, Precio = :precio, Imagen = :imagen, Categoría = :categoria WHERE id = :producto_id");

            // Los marcadores de parámetros como :nombre, :precio, :imagen y :categoria se utilizan en lugar de valores directos para evitar la inyección SQL.
            // Utilizamos el id recibido por URL para identificar el producto.
            $stmt->bindParam(':nombre', $_POST['nombre']);
            $stmt->bindParam(':precio', $_POST['precio']);
            $stmt->bindParam(':imagen', $imagePath);
            $stmt->bindParam(':categoria', $_POST['categoria']);
            $stmt->bindParam(':producto_id', $_POST['producto_id']);

            // Ejecutamos la consulta preparada para realizar la actualización.
            $stmt->execute();
        } 
        
        catch (PDOException $e) 
        {
            echo "Error al actualizar datos: " . $e->getMessage();
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Consultar el listado de productos</title>
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
                <?php

                    // Comprobamos la variable id.
                    if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) 
                    {
                        $select_product = "SELECT productos.Nombre, productos.Precio, productos.Imagen, Categorías.nombre AS CategoriaNombre, productos.id FROM productos INNER JOIN Categorías ON productos.Categoría = Categorías.id WHERE productos.id = :id";

                        $producto_id = $_GET['id'];                 // Almacenamos el id del producto.
                        $conn = connect_to_database();              // Iniciamos una conexión a la base de datos.
                        $stmt = $conn->prepare($select_product);    // Preparamos la consulta que vamos a ejecutar.
                        $stmt->bindParam(':id', $producto_id);      // Vinculamos al :id de la consulta el id del producto recibido.
                        $stmt->execute();                           // Ejecutamos la consulta.

                        // Comprobamos si la consulta fue exitosa.
                        if ($stmt) 
                        {
                            // Obtenemos los datos del registro.
                            $row = $stmt->fetch();

                            // Si se obtuvieron los datos del registro correctamente los mostramos.
                            if ($row) 
                            {
                                // Mostrar el formulario con los datos del producto
                                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
                                echo '
                                        <div class="inputs">
                                            <label for="nombre">Nombre:</label>
                                            <br/>
                                            <input type="text" id="nombre" name="nombre" value="' . $row['Nombre'] . '">
                                        </div>';

                                echo '  <br/>';

                                echo '      
                                        <div class="inputs">
                                            <label for="precio">Precio:</label>
                                            <br/>
                                            <input type="number" id="precio" name="precio" value="' . $row['Precio'] . '">
                                        </div>';

                                echo '  <br/>';

                                echo '      
                                        <div class="inputs">
                                            <label for="imagen">Imagen:</label>
                                            <br/>
                                            <input type="file" id="imagen" name="imagen" accept=".jpg,.png,.gif,.jfif" />
                                        </div>';

                                echo '  <br/>';

                                echo '
                                        <div class="inputs">
                                            <p style="margin: 0 0 0.2rem 0 ;"><b>Categoría:</b></p>
                                            <select name="categoria" id="categoria">';

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
                                                    while ($categoria = $stmt->fetch()) 
                                                    {
                                                        // Imprimimos los valores del array.
                                                        $selected = ($categoria['id'] == $row['Categoria']) ? ' selected' : '';
                                                        echo '<option value="' . $categoria['id'] . '"' . $selected . '>' . $categoria['nombre'] . '</option>';
                                                    }
                                                }

                                // En caso de no poder mostrar las categorías lo indicamos.
                                else 
                                {
                                    echo '<option value="null">Las categorías no están disponibles</option>';
                                }

                                echo '
                                    </select>
                                    </div>
                                    <br/>
                                    <input type="hidden" name="producto_id" value="' . $producto_id . '">
                                    <button type="submit">Enviar</button>
                                </form>';
                            } 
                            
                            else 
                            {
                                echo '<p>No se encontró el producto con el ID proporcionado.</p>';
                            }
                        } 
                        
                        else 
                        {
                            echo '<p>No se pudieron recuperar los datos del producto.</p>';
                        }

                        $conn = null; // Cierra la conexión a la base de datos.
                    } 
                    
                    else 
                    {
                        // Paso 1) Mostrar el select option dinámico con todos los productos de la base de datos.
                        echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="get"><div class="inputs"><p style="margin: 0 0 0.2rem 0 ;"><b>Selecciona un producto:</b></p><br/><select name="id" id="id">';

                        // Paso 2) Realizamos una conexión a la base de datos.
                        $connection = connect_to_database();

                        // Paso 3) Guardamos la consulta en la variable sql.
                        $sql_query = "SELECT id, nombre FROM productos";

                        // Paso 4) Ejecutamos la consulta dentro de la conexión.
                        $stmt = $connection->query($sql_query);

                        // Paso 5) Comprobamos que la consulta se realizó correctamente y imprimimos los valores del array.
                        if ($stmt) 
                        {
                            while ($producto = $stmt->fetch()) 
                            {
                                echo '<option value="' . $producto['id'] . '">' . $producto['nombre'] . '</option>';
                            }
                        } 
                        
                        else 
                        {
                            // En caso de no poder mostrar los productos lo indicamos.
                            echo '<option value="null">Los productos no están disponibles</option>';
                        }

                        echo '</select></div><br/><button type="submit">Seleccionar</button></form>';
                    }
                ?>
            </main>
        </div>
    </body>

</html>
