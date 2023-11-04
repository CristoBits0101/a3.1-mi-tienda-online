<?php
    // Paso 1) Importamos el archivo de configuración para poder conectarnos a la base de datos.
    require_once "./configuration.php";
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

                <!-- Creamos la tabla que va a almacenar la consulta -->
                <table>

                    <!-- Asignamos un título a la tabla -->
                    <caption><b>EDITA EL SIGUIENTE PRODUCTO</b></caption>

                    <!-- Añadimos el encabezado estático de la tabla -->
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Imagen</th>
                            <th>Categoría</th>
                        </tr>
                    </thead>

                    <!-- Añadimos el cuerpo de la tabla de forma dinámica -->
                    <tbody>
                        <?php

                            // Comprobamos la variable id.
                            if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) 
                            {
                                // Almacenamos el id del producto.
                                $producto_id = $_GET['id'];

                                // Iniciamos una conexión a la base de datos.
                                $conn = connect_to_database();

                                // Guardamos la consulta que queremos realizar en la variable select_product.
                                $select_product = " SELECT 
                                                        productos.Nombre, 
                                                        productos.Precio, 
                                                        productos.Imagen, 
                                                        Categorías.nombre 
                                                    AS 
                                                        CategoriaNombre, 
                                                        productos.id 
                                                    FROM 
                                                        productos 
                                                    INNER JOIN 
                                                        Categorías 
                                                    ON 
                                                        productos.Categoría = Categorías.id
                                                    WHERE 
                                                        productos.id = :id";

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
                                        echo '<tr>';
                                            echo '<td>'                         . $row['Nombre']            .                                           '</td>';
                                            echo '<td>'                         . $row['Precio']            .                                           '</td>';
                                            echo '<td><img src=".\\ficheros\\'  . $row['Imagen']            . '" alt="Imagen del producto" width="100" /></td>';
                                            echo '<td>'                         . $row['CategoriaNombre']   .                                           '</td>';
                                        echo '</tr>';

                                        echo '  <form action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';
                                        echo '      <div class="inputs">
                                                        <label for="nombre">Nombre:</label>

                                                        <br/>

                                                        <input type="text" id="nombre" name="nombre">
                                                    </div>';

                                        echo '      <br/>';

                                        echo '      <div class="inputs">
                                                        <label for="precio">Precio:</label>
                                                        <br/>
                                                        <input type="number" id="precio" name="precio">
                                                    </div>';

                                        echo '      <br/>';

                                        echo '      <div class="inputs">
                                                        <label for="imagen">Imagen:</label>
                                                        <br/>
                                                        <input type="file" id="imagen" name="imagen" accept=".jpg,.png,.gif,.jfif" />
                                                    </div>';

                                        echo '      <br/>';

                                        echo '      <div class="inputs">
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

                                        echo '          </select>
                                                    </div>

                                                    <br/>

                                                    <button type="submit">Envíar</button>

                                                </form>';
                                    } 
                                    
                                    else 
                                    {
                                        echo '<tr><td colspan="4">No se encontró el producto con el ID proporcionado.</td></tr>';
                                    }
                                } 
                                
                                else 
                                {
                                    echo '<tr><td colspan="4">No se pudieron recuperar los datos del producto.</td></tr>';
                                }
                            } 
                            
                            else 
                            {
                                header("Location: listado_productos.php");
                                exit;
                            }
                        ?>
                    </tbody>
                </table>
            </main>
        </div>
    </body>

</html>