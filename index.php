<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú principal</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
        }
        header {
            margin: 2% auto 2% auto;
            padding: 2%;
            width: 80%;
            display: flex;
            align-items: center;
            background-color: olivedrab;
        }
        h1 {
            margin: 0;
            padding: 0;
            width: 20%;
            color: gold;
            display: flex;
            justify-content: center;
            font-size: 1.5rem;
        }
        nav {
            width: 80%;
        }
        ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 5%;
        }
        a {
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Tienda on-line</h1>
        <nav>
            <ul>
                <li>
                    <a href="./crear_producto.php">Crear producto</a>
                </li>
                <li>
                    <a href="./listado_productos.php">Consultar el listado de productos</a>
                </li>
                <li>
                    <a href="./modifica_producto.php">Modificar producto</a>
                </li>
                <li>
                    <a href="./elimina_producto.php">Eliminar producto</a>
                </li>
            </ul>
        </nav>
    </header>
</body>
</html>