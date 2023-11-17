<?php

    // 1.1. Como no se puede imprimir los errores antes de redireccionar los envío mediante la variable de sesión.
    session_start();

    // 1.2. Registra que campos del formulario enviado contiene errores.
    $_SESSION['error_messages'] = array();

    // 1.3. Registra que formulario enviado contiene errores en los campos.
    $_error = '';

    // 2.1. Válida el formulario de inicio de sesión.
    if (isset($_POST['login']) && $_POST['login'] == 'Entrar')
        foreach ($_REQUEST as $field => $value)
            if (!isset($value) || empty($value))
            {
                $_SESSION['error_messages'][$field] = "Error en el campo $field no almacenado";
                $_error = 'login';
            }

    // 2.2. Válida el formulario registro.
    elseif (isset($_POST['register']) && $_POST['register'] == 'Registrarse')
        foreach ($_REQUEST as $field => $value)
            if (!isset($value) || empty($value))
            {
                $_SESSION['error_messages'][$field] = "Error en el campo $field no almacenado";
                $_error = 'register';
            }

    // 3.1. Se redirecciona al formulario con errores para que vuelva a rellenarlo.
    if (!empty($_SESSION['error_messages']))
    {
        if ($_error === 'login')
        {
            header("Location: ./form_login.php");
            exit;
        }

        elseif ($_error === 'register')
        {
            header("Location: ./form_register.php");
            exit;
        }
    }

    // 3.2. Si no hay errores y el formulario enviado pertenece a login, intentamos autenticar al usuario.
    elseif (empty($_SESSION['error_messages']) && isset($_POST['login']) && $_POST['login'] == 'Entrar')
    {
        // Registramos el email enviado.
        $email = $_POST['email'];

        // Registramos la password enviada.
        $password = $_POST['password'];

        // Preparamos la consulta.
        $stmt = $pdo->prepare(
            "SELECT
                id, contrasena_hash
            FROM
                usuarios2
            WHERE
                correo_electronico = :email"
        );

        // Sincronizamos el email de la consulta con el enviado.
        $stmt->bindParam(':email', $email);

        // Ejecutamos la consulta.
        $stmt->execute();

        // Se convierte el registro en array asociativo y se comprueba si la consulta devolvió algún valor.
        if (($user = $stmt->fetch(PDO::FETCH_ASSOC)) !== false)
        {
            // Comprobamos la contraseña enviada.
            if (password_verify($password, $user['contrasena_hash'])) 
            {
                // Si la contraseña es válida, le dejamos iniciar sesión o acceder a los privilegios que solicita.
                $_SESSION['user_id'] = $user['id'];

                // Redirigimos a la página de inicio después del inicio de sesión exitoso.
                header("Location: ./index.php");

                // Después de redirigir hay que decir al navegador que pare de redirigir si hay algún problema.
                exit;
            }

            // En caso de que la contraseña sea incorrecta.
            else 
            {
                // Contraseña incorrecta.
                $_SESSION['error_messages']['login'] = "Error al intentar iniciar sesión: contraseña incorrecta";
                header("Location: ./form_login.php");
                exit;
            }
        } 
        else 
        {
            // Usuario no encontrado.
            $_SESSION['error_messages']['login'] = "Error en el inicio de sesión: usuario no encontrado";
            header("Location: ./form_login.php");
            exit;
        }
    }

    // 3.3. Si no hay errores y el formulario enviado pertenece a register, registramos al nuevo usuario.
    elseif (empty($_SESSION['error_messages']) && isset($_POST['register']) && $_POST['register'] == 'Registrarse') 
    {
        // Obtener los datos del formulario.
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Verificar si el correo electrónico ya está registrado.
        $stmt = $pdo->prepare("SELECT id FROM usuarios2 WHERE correo_electronico = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->fetch(PDO::FETCH_ASSOC)) 
        {
            // Correo electrónico ya registrado.
            $_SESSION['error_messages']['register'] = "Error en el registro: el correo electrónico ya está registrado";
            header("Location: ./form_register.php");
            exit;
        } 
        else 
        {
            // Hash de la contraseña antes de almacenarla.
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario en la base de datos.
            $stmt = $pdo->prepare("INSERT INTO usuarios2 (nombre, correo_electronico, contrasena_hash) VALUES (:name, :email, :hashedPassword)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':hashedPassword', $hashedPassword);
            $stmt->execute();

            // Puedes realizar otras acciones después del registro exitoso.
            header("Location: ./form_login.php");
            exit;
        }
    }

    // 3.4. Si no hay errores y el formulario enviado no pertenece a la aplicación, finalizamos la ejecución del script.
    else
    {
        die("Acceso no permitido");
    }
