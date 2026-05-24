<?php
//Habrimso sesion
session_start();

//Recogemos lo enviado por POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    //Validamos constraseña y usuario
    if ($usuario === 'juan' && $contraseña ==='1234'){
        //Guardamos los dato en sesion
        $_SESSION['usuario'] = 'juan';
        $_SESSION['rol'] = 'usuario';
        //redirigimos a la pagina perfil.php
        header('Location: perfil.php');
        exit;

    }else{
        echo 'Usuario o contraseña son incorrectas. <br>';
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
    <input type="text" name="usuario" id="usuario" placeholder="Usuario:" required>
    <input type="password" name="contraseña" id="contraseña" placeholder="Contraseña:" required>
    <input type="submit" value="Iniciar sesion">
</form>
    
</body>
</html>