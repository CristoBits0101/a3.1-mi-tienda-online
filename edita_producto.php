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
            <main id="listado-productos">
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

                        $stmt = $conn->prepare($select_product);                        // Preparamos la consulta que vamos a ejecutar.
                        $stmt->bindParam(':id', $producto_id);                          // Vinculamos al :id de la consulta el id del producto recibido.
                        $stmt->execute();                                               // Ejecutamos la consulta.

                        if ($stmt)
                        {
                            $row = $stmt->fetch();
                            
                            if ($row)
                            {
                                echo '<tr>';
                                    echo '<td>'                         . $row['Nombre']          .                                           '</td>';
                                    echo '<td>'                         . $row['Precio']          .                                           '</td>';
                                    echo '<td><img src=".\\ficheros\\'  . $row['Imagen']          . '" alt="Imagen del producto" width="100" /></td>';
                                    echo '<td>'                         . $row['CategoriaNombre'] .                                           '</td>';
                                echo '</tr>';
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
                        header("Location: listado_productos.php");                      // Si no se proporcionó un ID, redirige al usuario a 'listado_productos.php'.
                        exit;                                                           // Salimos para evitar que el código siga ejecutándose.
                    }
                ?>
            </main>
        </div>
    </body>
</html>