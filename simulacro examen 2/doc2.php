<?php
session_start();

// Comprobamos si hay sesión activa con usuario, si no, redirigimos a doc1.php (login)
if(!isset($_SESSION['usuario'])){
    header('Location: doc1.php');
    exit;
}

// Obtenemos la sección a mostrar desde $_GET['seccion'], si no existe, usamos 'perfil' por defecto
$seccion = $_GET['seccion'] ?? 'perfil';

// Inicializamos los arrays de usuarios y permisos en sesión, si no existen (simulación persistencia)
if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [
        "admin" => ["password" => "1234", "rol" => "admin"],
        "juan" => ["password" => "abcd", "rol" => "usuario"],
        "editor" => ["password" => "qwer", "rol" => "editor"]
    ];
}

if (!isset($_SESSION['permisos'])) {
    $_SESSION['permisos'] = [
        // Definimos qué secciones puede acceder cada rol
        'admin' => ['admin', 'editor', 'perfil'],
        'editor' => ['editor', 'perfil'],
        'usuario' => ['perfil']
    ];
}

// Creamos referencias para poder modificar directamente los arrays en sesión
$usuarios = &$_SESSION['usuarios'];
$permisos = &$_SESSION['permisos'];

// Obtenemos el rol del usuario activo, por defecto 'usuario' si no está definido
$rol = $_SESSION['rol'] ?? 'usuario';

// Verificamos si el rol tiene permiso para acceder a la sección solicitada
// Si no, mostramos mensaje y detenemos ejecución
if (!isset($permisos[$rol]) || !in_array($seccion, $permisos[$rol])) {
    echo "<h1 style='color:red;'>Acceso denegado a la sección: " . htmlspecialchars($seccion) . "</h1>";
    exit;
}

// ---------------------- FUNCIONES -------------------------

// Función para mostrar mensaje de bienvenida con usuario actual
function bienvenido() {
    $usuario = $_SESSION['usuario'] ?? 'Invitado';  // Si no hay usuario, "Invitado"
    return "<h1>Bienvenido, " . htmlspecialchars($usuario) . "!</h1>";
}

// Función que muestra una tabla con todos los usuarios y rol, con enlace para ver perfil
function mostrarUsuarios($usuarios) {
    echo "<table border='1' style='border-collapse:collapse; width:100%;'>";
    echo "<tr><th>Usuario</th><th>Rol</th><th>Acciones</th></tr>";
    foreach ($usuarios as $nombre => $datos) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($nombre) . "</td>";
        echo "<td>" . htmlspecialchars($datos['rol']) . "</td>";
        // Enlace para ver perfil de usuario (redirige a sección perfil con parámetro user)
        echo "<td><a href='?seccion=perfil&user=" . urlencode($nombre) . "'>Ver Perfil</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Función para añadir un nuevo usuario (solo admin)
// Comprueba si se envió formulario, valida datos y agrega nuevo usuario al array
function añadirUsuario(&$usuarios) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_usuario'])) {
        $nuevo = trim($_POST['nuevo_usuario']);      // Nombre nuevo usuario
        $pass = trim($_POST['nueva_password']);      // Contraseña nueva
        $rol = $_POST['nuevo_rol'] ?? 'usuario';     // Rol asignado, por defecto usuario

        // Validar campos no vacíos
        if ($nuevo === '' || $pass === '') {
            echo "<p style='color:red;'>Faltan campos para crear usuario.</p>";
            return;
        }

        // Verificar que usuario no exista
        if (isset($usuarios[$nuevo])) {
            echo "<p style='color:red;'>El usuario ya existe.</p>";
            return;
        }

        // Añadir nuevo usuario al array global
        $usuarios[$nuevo] = ['password' => $pass, 'rol' => $rol];
        echo "<p style='color:green;'>Usuario $nuevo creado correctamente.</p>";
    }

    // Mostrar formulario para crear usuario
    echo '<form method="POST">';
    echo '<h3>Añadir nuevo usuario</h3>';
    echo '<input type="text" name="nuevo_usuario" placeholder="Nombre usuario" required><br>';
    echo '<input type="password" name="nueva_password" placeholder="Contraseña" required><br>';
    echo '<select name="nuevo_rol">';
    echo '<option value="usuario">Usuario</option>';
    echo '<option value="editor">Editor</option>';
    echo '<option value="admin">Administrador</option>';
    echo '</select><br>';
    echo '<button type="submit">Crear Usuario</button>';
    echo '</form>';
}

// Función para buscar usuario por nombre mediante formulario
// Si lo encuentra, muestra nombre, contraseña y rol
function buscarUsuario($usuarios) {
    $resultado = null;

    // Procesamos el formulario de búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_usuario'])) {
        $buscar = trim($_POST['buscar_usuario']);
        if (isset($usuarios[$buscar])) {
            // Usuario encontrado, guardamos sus datos
            $resultado = $usuarios[$buscar];
            $resultado['nombre'] = $buscar;
        } else {
            // Usuario no encontrado
            echo "<p style='color:red;'>Usuario no encontrado.</p>";
        }
    }

    // Formulario para introducir nombre de usuario a buscar
    echo '<form method="POST">';
    echo '<h3>Buscar usuario</h3>';
    echo '<input type="text" name="buscar_usuario" placeholder="Nombre usuario" required>';
    echo '<button type="submit">Buscar</button>';
    echo '</form>';

    // Si hay resultado, mostramos los datos del usuario encontrado
    if ($resultado) {
        echo "<h4>Datos usuario:</h4>";
        echo "<p><strong>Nombre:</strong> " . htmlspecialchars($resultado['nombre']) . "</p>";
        echo "<p><strong>Contraseña:</strong> " . htmlspecialchars($resultado['password']) . "</p>";
        echo "<p><strong>Rol:</strong> " . htmlspecialchars($resultado['rol']) . "</p>";
    }
}

// Función para modificar permisos de los roles desde interfaz gráfica
function modificarPermisos(&$permisos) {
    // Procesar formulario enviado para actualizar permisos
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mod_rol'])) {
        $rol = $_POST['mod_rol'];
        // Permisos posibles para asignar
        $permisosPosibles = ['admin', 'editor', 'perfil'];

        $permisosNuevo = [];
        // Recogemos qué permisos fueron marcados en el formulario
        foreach ($permisosPosibles as $permiso) {
            if (isset($_POST['perm_' . $permiso]) && $_POST['perm_' . $permiso] === 'on') {
                $permisosNuevo[] = $permiso;
            }
        }

        // Actualizamos el array global de permisos para el rol seleccionado
        $permisos[$rol] = $permisosNuevo;
        echo "<p style='color:green;'>Permisos actualizados para el rol <strong>" . htmlspecialchars($rol) . "</strong>.</p>";
    }

    // Mostrar formulario para seleccionar rol y modificar sus permisos
    echo '<form method="POST">';
    echo '<h3>Modificar permisos</h3>';

    // Dropdown para seleccionar el rol
    echo '<label for="mod_rol">Selecciona rol:</label>';
    echo '<select name="mod_rol" onchange="this.form.submit()">';

    // Opciones con todos los roles disponibles, el seleccionado se marca con selected
    foreach ($permisos as $rol => $perms) {
        $selected = (isset($_POST['mod_rol']) && $_POST['mod_rol'] === $rol) ? 'selected' : '';
        echo "<option value=\"$rol\" $selected>$rol</option>";
    }
    echo '</select>';

    // Si un rol está seleccionado, mostramos los permisos con checkbox marcados según el estado actual
    if (isset($_POST['mod_rol'])) {
        $rol = $_POST['mod_rol'];
        $permisosPosibles = ['admin', 'editor', 'perfil'];

        foreach ($permisosPosibles as $permiso) {
            $checked = in_array($permiso, $permisos[$rol]) ? 'checked' : '';
            echo "<div><label><input type='checkbox' name='perm_$permiso' $checked> $permiso</label></div>";
        }

        // Botón para enviar y guardar cambios
        echo '<button type="submit">Guardar permisos</button>';
    }

    echo '</form>';
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de <?php echo htmlspecialchars($seccion); ?></title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
    .panel { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);}
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px;}
    th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
    th { background: #007bff; color: white; }
    a { text-decoration: none; color: #007bff; }
</style>
</head>
<body>
<div class="panel">
    <!-- Título principal indicando la sección -->
    <h2>Panel de <?php echo htmlspecialchars($seccion); ?></h2>

    <?php
    // Mostramos mensaje de bienvenida con el usuario actual
    echo bienvenido();

    // Según la sección elegida, mostramos distintos contenidos y funcionalidades

    if ($seccion === 'perfil') {
        // Si la URL tiene el parámetro user, mostramos perfil de ese usuario
        $usuarioMostrar = $_GET['user'] ?? $_SESSION['usuario'];

        // Comprobamos si el usuario existe en el array
        if (isset($usuarios[$usuarioMostrar])) {
            echo "<p><strong>Usuario:</strong> " . htmlspecialchars($usuarioMostrar) . "</p>";
            echo "<p><strong>Rol:</strong> " . htmlspecialchars($usuarios[$usuarioMostrar]['rol']) . "</p>";
        } else {
            echo "<p>Usuario no encontrado.</p>";
        }

        echo '<p><a href="?seccion=admin">Volver a Admin</a></p>';
        echo '<p><a href="doc1.php?logout=true">Cerrar sesión</a></p>';

    } elseif ($seccion === 'editor') {
        // Contenido simple para la sección editor
        echo "<p>Sección editor: aquí podrías poner contenido específico.</p>";

    } elseif ($seccion === 'admin') {
        // Mostrar tabla de usuarios con opción a ver perfil
        mostrarUsuarios($usuarios);

        // Solo los admins pueden ver y usar estas funciones avanzadas
        if ($rol === 'admin') {
            // Formulario para añadir nuevo usuario
            añadirUsuario($usuarios);

            // Formulario para modificar permisos de roles
            modificarPermisos($permisos);

            // Formulario para buscar usuario por nombre
            buscarUsuario($usuarios);
        }
    } else {
        // Sección no reconocida
        echo "<p>Sección no reconocida.</p>";
    }
    ?>
</div>
</body>
</html>
