<?php
    session_start();
    $usuarios = [
        'juan' => ['clave' => '1234', 'rol' => 'usuario'],
        'ana'  => ['clave' => 'admin', 'rol' => 'administrador']
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
// primero veifica si el usuaraio enviado por post coincido con la lista de usuarios
//lugo verifica si coincide la clave de usuario
    if (isset($usuarios[$usuario]) && $usuarios[$usuario]['clave'] === $clave) {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $usuarios[$usuario]['rol'];
        header('Location: perfil.php');
        exit;
    } else {
        echo "Usuario o contraseña incorrectos.<br>";
    }
}

?>

<form method="post">
    <label>Usuario:</label>
    <input type="text" name="usuario" required><br>
    <label>Contraseña:</label>
    <input type="password" name="clave" required><br>
    <button type="submit">Iniciar sesión</button>
</form>