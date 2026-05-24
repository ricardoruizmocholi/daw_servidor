<?php
session_start();

/*
  ==========================================================
  EXAMEN PHP – DOCUMENTO 2
  ==========================================================
  Objetivos:
  - Usar $_GET para acceder a la sección seleccionada
  - Aplicar roles y permisos
  - Trabajar con arrays y estructuras de control
  ==========================================================
*/
;
// ======= TODO: Comprobar si hay una sesión activa =======
// Si no existe $_SESSION['usuario'], redirigir a documento1.php.
if(!isset($_SESSION['usuario'])){
    header('Location: doc1.php');
    exit;
}


// ======= TODO: Obtener la sección desde $_GET['seccion'] =======
// Si no existe, asignar 'perfil' por defecto.
$seccion = $_GET['seccion'] ?? 'perfil';
// ======= TODO: Crear un array asociativo con los permisos de cada rol =======
// Ejemplo: $permisos = ['admin'=>['admin','editor','perfil'], 'editor'=>['editor','perfil'], 'usuario'=>['perfil']];

$permisos = [
    'admin' => ['admin', 'editor', 'perfil'],
    'editor' => ['editor', 'perfil'],
    'usuario' => ['perfil']
];
// ======= TODO: Comprobar si el rol actual tiene permiso para la sección =======
// Si no lo tiene, mostrar un mensaje "Acceso denegado" y finalizar la ejecución.
// Obtenemos rol actula
$rol = $_SESSION['rol'] ?? "usuario";

// ======= Comprobar si el rol tiene permiso para la sección =======

if (!in_array($seccion, $permisos[$rol])) {
    echo "<h1 style='color:red;'>Acceso denegado a la sección: " . htmlspecialchars($seccion) . "</h1>";
    exit;
}




?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel de <?php /* TODO: Mostrar nombre de la sección */ ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      padding: 20px;
    }
    .panel {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      border-bottom: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }
    th {
      background: #007bff;
      color: white;
    }
    a {
      text-decoration: none;
      color: #007bff;
    }
  </style>
</head>
<body>
  <div class="panel">
    <h2>Panel de <?php echo htmlspecialchars($seccion); ?></h2>

    <?php
    // ======= TODO: Según la sección actual, mostrar diferente contenido =======
    // SECCIÓN PERFIL:
    // - Mostrar nombre de usuario y rol de la sesión.
    // - Añadir enlace de regreso a documento1.php.
    if ($seccion === 'perfil'){
        echo "<p><strong>Usuario:</strong> " . htmlspecialchars($_SESSION['usuario']) . "</p>";
        echo "<p><strong>Rol:</strong> " . htmlspecialchars($rol) . "</p>";
        echo '<p><a href="doc1.php?logout=true">Cerrar sesión</a></p>';
    }
    // SECCIÓN EDITOR:
    // - Crear un array de artículos ["PHP Básico", "Arrays", "Sesiones"]
    // - Añadir uno nuevo al array (push).
    // - Eliminar uno específico (unset()).
    // - Recorrer el array con foreach y mostrarlo como lista <ul>.
    elseif($seccion === 'editor'){
        // Array de artículos
        $articulos = ["PHP Básico", "Arrays", "Sesiones"];

        // Añadir uno nuevo (push)
        $articulos[] = "Funciones";

        // Eliminar uno específico (por ejemplo "Arrays")
        $indexEliminar = array_search("Arrays", $articulos);
        if ($indexEliminar !== false) {
            unset($articulos[$indexEliminar]);
        }

        //Recorre y mostrar com lista ul
        echo "<ul>";
        foreach($articulos as $articulo){
            echo "<li>".$articulo."</li>";
        }
        echo "</ul>";
        echo '<p><a href="doc2.php?seccion=perfil">Volver a Perfil</a></p>';

    }
    // SECCIÓN ADMIN:
    // - Crear un array de usuarios (nombre y rol).
    // - Mostrar los datos en una tabla con foreach.
    // - Cada fila tendrá un enlace con ?seccion=perfil&user=nombreUsuario para ver su perfil.
    elseif ($seccion === 'admin') {
        // SECCIÓN ADMIN
        // Array usuarios (nombre y rol)
        $usuarios = [
            ["nombre" => "admin", "rol" => "administrador"],
            ["nombre" => "juan", "rol" => "usuario"],
            ["nombre" => "editor", "rol" => "editor"]
        ];

        echo "<table>";
        echo "<tr><th>Nombre</th><th>Rol</th><th>Acciones</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($user['rol']) . "</td>";
            echo "<td><a href='documento2.php?seccion=perfil&user=" . urlencode($user['nombre']) . "'>Ver Perfil</a></td>";
            echo "</tr>";
        }
        echo "</table>";

        echo '<p><a href="documento2.php?seccion=perfil">Volver a Perfil</a></p>';

    } else {
        echo "<p>Sección no reconocida.</p>";
    }
    ?>
    
    ?>
  </div>
</body>
</html>
