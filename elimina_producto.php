<?php

    session_start();

    // Verifica si no hay una sesión activa.
    if (!isset($_SESSION['user_id']))
    {
        // Se le comunica al usuario que para acceder al resto de páginas debe loguearse primero.
        $_SESSION['session_inactiva'] = '¡Debe inicar sesión primero para poder usar nuestros servicios!';

        // Redirige al usuario a la página de inicio de sesión.
        header("Location: form_login.php");

        // Asegura que el script se detenga después de la redirección.
        exit; 
    }

    // Importamos el archivo de configuración para poder conectarnos a la base de datos.
    require_once "./configuration.php";

    // Verificamos si se ha enviado un ID a través del método GET.
    if (isset($_GET['id']) && !isset($_GET['confirm']))
    {
        $idProducto = $_GET['id'];

        // Se muestra la confirmación de eliminación utilizando JavaScript.
        echo "
            <script>
                if (confirm('¿Está seguro de que desea eliminar este producto?')) 
                {
                    window.location.href = 'elimina_producto.php?id=$idProducto&confirm=yes';
                } 
                else 
                {
                    window.history.back();
                }
            </script>";
        exit; 
    } 

    elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) 
    {
        $idProducto = $_GET['id'];
        $conn = connect_to_database();

        // Preparamos la consulta para eliminar el producto.
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bindParam(1, $idProducto, PDO::PARAM_INT);

        if ($stmt->execute())   
        {
            echo "
                <script>
                    alert('Producto eliminado correctamente.'); window.location.href = 'listado_productos.php';
                </script>";
        }

        else
        {
            echo "
                <script>
                    alert('Hubo un error al eliminar el producto.'); window.history.back();
                </script>";
        }

        $stmt = null; 
        $conn = null; 

        exit;
    } 
    
    else 
    {
        ?>
            <!DOCTYPE html>
            <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet" href="style.css">
                    <title>Eliminar Producto</title>
                </head>
                <body>
                    <div class="container">

                        <?php 
                            include_once "./menu.php"; 
                        ?>

                        <main>
                            <div class="container"> 
                                <form action="elimina_producto.php" method="post">

                                    <label for="producto">Seleccione el producto que desea eliminar:</label>
                                    
                                    <br/><br/>

                                    <select name="producto" id="producto">
                                        <?php
                                            $conn = connect_to_database();

                                            // Obtenemos todos los productos para llenar la lista.
                                            $stmt = $conn->query("SELECT id, Nombre FROM productos");

                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
                                            {
                                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['Nombre']) . "</option>";
                                            }
                                            $stmt = null;
                                        ?>
                                    </select>
                                    
                                    <br/><br/>

                                    <input type="submit" value="Eliminar Producto">

                                </form>
                            </div>
                        </main>
                        
                        <?php 
                            $conn = null;
                        ?>

                    </div>
                </body>
            </html>
        <?php
    } 

    // Verificamos si se ha enviado un formulario para eliminar un producto.
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['producto'])) 
    {
        $idProducto = $_POST['producto'];

        // Mostramos la confirmación de eliminación con JavaScript.
        echo "
            <script> 
                if (confirm('¿Está seguro de que desea eliminar este producto?')) 
                {
                    window.location.href = 'elimina_producto.php?id=$idProducto&confirm=yes';
                } 
            </script>";
            
        exit;
    }
?>
