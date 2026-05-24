<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: perfil.php');
    exit;
}

echo "<h1>Panel de administración</h1>";
echo "Solo los administradores pueden ver esta página.<br>";
echo "<a href='perfil.php'>Volver al perfil</a>";
?>
