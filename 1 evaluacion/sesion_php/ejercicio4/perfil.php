<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

echo "Bienvenido, " . $_SESSION['usuario'] . "<br>";
echo "Rol: " . $_SESSION['rol'] . "<br>";

if ($_SESSION['rol'] === 'administrador') {
    echo "<a href='admin.php'>Panel de administración</a><br>";
}

echo "<a href='cerrar.php'>Cerrar sesión</a>";
?>
