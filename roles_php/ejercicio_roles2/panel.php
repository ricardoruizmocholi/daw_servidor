<?php
session_start();

//cierre de sesion

if (isset($_GET['logout'])) {
    // Limpia todas las variables de sesión
    $_SESSION = [];

    // Si quieres destruir la cookie de sesión (muy recomendable)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente destruir la sesión
    session_destroy();

    // Redirigir a login u otra página
    header("Location: index.php");
    exit;
}
// -----------------------------
// Si no autenticado redirigir
// -----------------------------
if (!isset($_SESSION["usuario"], $_SESSION["rol"], $_SESSION["password"])) {
    header("Location: index.php");
    exit;
}


$usuario = $_SESSION["usuario"];
$rol = $_SESSION["rol"];

// -----------------------------
// Inicializar usuarios en sesión
// -----------------------------
if (!isset($_SESSION["usuarios"])) {
    $_SESSION["usuarios"] = [
        ["usuario" => "pikachu", "password" => "trueno", "rol" => "entrenador"],
        ["usuario" => "charizard", "password" => "fuego", "rol" => "lider_gimnasio"],
        ["usuario" => "mewtwo", "password" => "psiquico", "rol" => "legendario"],
        ["usuario" => "bulbasaur", "password" => "hoja", "rol" => "novato"],
        ["usuario" => "snorlax", "password" => "zzz", "rol" => "guardian"]
    ];
}
$usuarios = &$_SESSION["usuarios"]; // referencia para modificar

// -----------------------------
// Permisos y roles
// -----------------------------
$todos_los_permisos = [
    "usuarios" => "Administrar Pokémon",
    "contenido" => "Configurar panel",
    "informes" => "Ver todos los informes",
    "configuracion" => "Acceso total al sistema",
    "torneos" => "Participar en torneos",
    "entrenamiento" => "Entrenar Pokémon",
    "captura" => "Capturar Pokémon",
    "seguridad" => "Zona de seguridad",
    "reposo" => "Zona de descanso"
];

$roles = [
    "legendario" => [
        "usuarios" => true,
        "contenido" => true,
        "informes" => true,
        "configuracion" => true,
        "torneos" => true,
        "entrenamiento" => true,
        "captura" => true,
        "seguridad" => true,
        "reposo" => true
    ],
    "lider_gimnasio" => [
        "usuarios" => true,
        "contenido" => true,
        "informes" => true,
        "configuracion" => false,
        "torneos" => true,
        "entrenamiento" => true,
        "captura" => false,
        "seguridad" => true,
        "reposo" => false
    ],
    "entrenador" => [
        "usuarios" => false,
        "contenido" => true,
        "informes" => false,
        "configuracion" => false,
        "torneos" => true,
        "entrenamiento" => true,
        "captura" => true,
        "seguridad" => false,
        "reposo" => false
    ],
    "guardian" => [
        "usuarios" => false,
        "contenido" => false,
        "informes" => true,
        "configuracion" => false,
        "torneos" => false,
        "entrenamiento" => false,
        "captura" => false,
        "seguridad" => true,
        "reposo" => true
    ],
    "novato" => [
        "usuarios" => false,
        "contenido" => false,
        "informes" => false,
        "configuracion" => false,
        "torneos" => false,
        "entrenamiento" => true,
        "captura" => true,
        "seguridad" => false,
        "reposo" => false
    ]
];

// -----------------------------
// FUNCIONES
// -----------------------------

// 2) Bienvenida
function bienvenido($usuario, $rol) {
    return "<p>👋 Bienvenido <strong>" . htmlspecialchars($usuario) . "</strong> (rol: <span class='" . htmlspecialchars($rol) . "'>" . htmlspecialchars($rol) . "</span>)</p>";
}

// 3) Añadir usuario (solo para roles con permiso usuarios = true)
function anadirUsuario(&$usuarios, $creadorRol, $nuevoUsuario, $nuevaPass, $nuevoRol, $roles) {
    if (empty($nuevoUsuario) || empty($nuevaPass) || empty($nuevoRol)) {
        return "Error: Debes rellenar todos los campos.";
    }
    $rolesValidos = array_keys($roles);
    if (!in_array($nuevoRol, $rolesValidos)) {
        return "Error: El rol asignado no existe.";
    }
    if (!$roles[$creadorRol]["usuarios"]) {
        return "No tienes permisos para añadir usuarios.";
    }
    foreach ($usuarios as $u) {
        if ($u["usuario"] === $nuevoUsuario) {
            return "Error: El usuario ya existe.";
        }
    }
    $usuarios[] = [
        "usuario" => $nuevoUsuario,
        "password" => $nuevaPass,
        "rol" => $nuevoRol
    ];
    return "Usuario '$nuevoUsuario' creado con rol '$nuevoRol'.";
}

// 4) Buscar usuario (roles con permiso usuarios, informes o contenido pueden buscar)
function buscarUsuario($usuarios, $nombreBuscado, $roles, $rolActual) {
    if (!$roles[$rolActual]["usuarios"] && !$roles[$rolActual]["informes"] && !$roles[$rolActual]["contenido"]) {
        return "No tienes permisos para buscar usuarios.";
    }
    foreach ($usuarios as $u) {
        if ($u["usuario"] === $nombreBuscado) {
            return $u;
        }
    }
    return null; // no encontrado
}

// 5) Modificar permisos (solo para legendario y lider_gimnasio)
function modificarPermisos(&$roles, $rolAModificar, $permiso, $valor) {
    $rolesConPermisos = ["legendario", "lider_gimnasio"];
    if (!in_array($_SESSION["rol"], $rolesConPermisos)) {
        return "No tienes permisos para modificar permisos.";
    }
    if (!isset($roles[$rolAModificar])) {
        return "El rol a modificar no existe.";
    }
    if (!array_key_exists($permiso, $roles[$rolAModificar])) {
        return "El permiso no existe.";
    }
    $roles[$rolAModificar][$permiso] = $valor ? true : false;
    return "Permiso '$permiso' modificado para rol '$rolAModificar'.";
}

// -----------------------------
// PROCESAMIENTO DE FORMULARIOS
// -----------------------------

$mensaje = "";
$buscarResultado = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Añadir usuario
    if (isset($_POST["nuevo_usuario"], $_POST["nueva_password"], $_POST["nuevo_rol"]) && isset($_POST["accion"]) && $_POST["accion"] === "anadir_usuario") {
        $mensaje = anadirUsuario($usuarios, $rol, trim($_POST["nuevo_usuario"]), trim($_POST["nueva_password"]), trim($_POST["nuevo_rol"]), $roles);
    }

    // Buscar usuario
    if (isset($_POST["buscar_usuario"]) && isset($_POST["accion"]) && $_POST["accion"] === "buscar_usuario") {
        $nombreBuscado = trim($_POST["buscar_usuario"]);
        $buscarResultado = buscarUsuario($usuarios, $nombreBuscado, $roles, $rol);
        if ($buscarResultado === null) {
            $mensaje = "Usuario '$nombreBuscado' no encontrado.";
        }
    }

    // Modificar permisos
    if (isset($_POST["rol_modificar"], $_POST["permiso_modificar"], $_POST["valor_permiso"]) && isset($_POST["accion"]) && $_POST["accion"] === "modificar_permisos") {
        $rolAModificar = $_POST["rol_modificar"];
        $permiso = $_POST["permiso_modificar"];
        $valor = $_POST["valor_permiso"];
        $mensaje = modificarPermisos($roles, $rolAModificar, $permiso, $valor);
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Panel Pokémon</title>
<style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    .btn { display: inline-block; padding: 5px 10px; background: #0078d7; color: white; border-radius: 4px; text-decoration: none; }
    .btn:hover { background: #005fa3; }
    .legendario { color: purple; }
    .entrenador { color: orange; }
    .lider_gimnasio { color: red; }
    .guardian { color: green; }
    .novato { color: gray; }
    form { margin-bottom: 30px; }
</style>
</head>
<body>

<?= bienvenido($usuario, $rol) ?>
<p><a href="index.php?logout=1">Cerrar sesión</a></p>

<?php if ($mensaje): ?>
    <p><strong><?= htmlspecialchars($mensaje) ?></strong></p>
<?php endif; ?>

<!-- FORMULARIO AÑADIR USUARIO (sólo roles con permiso usuarios) -->
<?php if ($roles[$rol]["usuarios"]): ?>
<h2>Añadir nuevo usuario</h2>
<form method="post" action="">
    <input type="hidden" name="accion" value="anadir_usuario">
    <label>Nombre usuario: <input type="text" name="nuevo_usuario" required></label><br><br>
    <label>Contraseña: <input type="text" name="nueva_password" required></label><br><br>
    <label>Rol: 
        <select name="nuevo_rol" required>
            <?php foreach ($roles as $r => $perms): ?>
                <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>
    <button type="submit">Crear usuario</button>
</form>
<?php endif; ?>

<!-- FORMULARIO BUSCAR USUARIO (roles que tengan permisos usuarios, informes o contenido) -->
<?php if ($roles[$rol]["usuarios"] || $roles[$rol]["informes"] || $roles[$rol]["contenido"]): ?>
<h2>Buscar usuario</h2>
<form method="post" action="">
    <input type="hidden" name="accion" value="buscar_usuario">
    <label>Nombre usuario: <input type="text" name="buscar_usuario" required></label>
    <button type="submit">Buscar</button>
</form>

<?php if ($buscarResultado !== null && is_array($buscarResultado)): ?>
    <h3>Resultado:</h3>
    <ul>
        <li><strong>Usuario:</strong> <?= htmlspecialchars($buscarResultado["usuario"]) ?></li>
        <li><strong>Contraseña:</strong> <?= htmlspecialchars($buscarResultado["password"]) ?></li>
        <li><strong>Rol:</strong> <?= htmlspecialchars($buscarResultado["rol"]) ?></li>
    </ul>
<?php endif; ?>
<?php endif; ?>

<!-- FORMULARIO MODIFICAR PERMISOS (solo legendario y lider_gimnasio) -->
<?php if (in_array($rol, ["legendario", "lider_gimnasio"])): ?>
<h2>Modificar permisos de roles</h2>
<form method="post" action="">
    <input type="hidden" name="accion" value="modificar_permisos">
    <label>Selecciona rol:
        <select name="rol_modificar" required>
            <?php foreach ($roles as $r => $perms): ?>
                <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>
    <label>Permiso:
        <select name="permiso_modificar" required>
            <?php foreach ($todos_los_permisos as $clave => $texto): ?>
                <option value="<?= htmlspecialchars($clave) ?>"><?= htmlspecialchars($texto) ?></option>
            <?php endforeach; ?>
        </select>
    </label><br><br>
    <label>Valor:
        <select name="valor_permiso" required>
            <option value="1">Activado</option>
            <option value="0">Desactivado</option>
        </select>
    </label><br><br>
    <button type="submit">Modificar permiso</button>
</form>
<?php endif; ?>

</body>
</html>
