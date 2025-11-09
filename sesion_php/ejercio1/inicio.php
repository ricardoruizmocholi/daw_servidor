<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    // Inicia la sesión
    session_start();

    // Guarda datos en la sesión
    $_SESSION['usuario'] = 'Juan Pérez';
    $_SESSION['rol'] = 'administrador';

    // Muestra un mensaje
    echo "Sesión iniciada correctamente.<br>";
    echo "<a href='perfil.php'>Ir al perfil</a>";
    ?>
</body>
</html>