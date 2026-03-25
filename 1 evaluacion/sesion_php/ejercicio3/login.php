<?php
session_start();

//Si ya hay una cookie, inicia session automaticamente

if (isset($_COOKIE['usuario'])){
    $_SESSION['usuario'] = $_COOKIE['usuario'];
    $_SESSION['rol'] = 'usuario';
    header('Location: perfil.php');
    exit;
}

// Recogemos los datos enviados por post
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    $recordar = isset($_POST['recordar']);

    if ($usuario === 'juan' && $clave === '1234'){
        $_SESSION['usuario'] == 'juan';
        $_SESSION['rol'] == 'admin';

        if($recordar){
            //si lo quiere recordar creamos una cookie en este caso de una hora
            setcookie('usuario', $usuario, time()+3600);
        }
        header('Location: perfil.php');
        exit;

    } else {
        echo "Usuario incorrecto";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="usuario" placeholder="Usuario: "required>
        <input type="password" name="clave" placeholder="Contraseña: "required>
        <label > <input type="checkbox" name="recordar"> Recordarme</label>
        <button type="submit">Iniciar sesión</button>

    </form>
    
</body>
</html>