<?php

    // Como no se puede imprimir los errores antes de redireccionar los envío mediante la variable de sesión.
    session_start();

    // Registra que campos del formulario enviado contiene errores.
    $_SESSION['error_messages'] = array();

    // Registra que formulario enviado contiene errores en los campos.
    $_error = '';

    // Válida el formulario de inicio de sesión.
    if (isset($_POST['login']) && $_POST['login'] == 'Entrar')
        foreach ($_REQUEST as $field => $value)
            if (!isset($value) || empty($value)) 
            {
                $_SESSION['error_messages'][$field] = 'Error en el campo $field no almacenado';
                $_error = 'login';
            }

    // Válida el formulario registro.
    if (isset($_POST['register']) && $_POST['register'] == 'Registrarse')
        foreach ($_REQUEST as $field => $value)
            if (!isset($value) || empty($value)) 
            {
                $_SESSION['error_messages'][$field] = "Error en el campo $field no almacenado";
                $_error = 'register';
            }

    // Se redirecciona al formulario con errores para que vuelva a rellenarlo. 
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
