<?php
session_start();

if (isset($_GET['logout'])){
    session_unset();
    session_destroy();
    //eliminamos cookies
    setcookie("ultimo_usuario","",time()-(7*24*3600),"/");

    header("Location: doc1.php");
    exit;
}
/*
  ==========================================================
  EXAMEN PHP – DOCUMENTO 1
  ==========================================================
  Objetivos:
  - Practicar el uso de $_POST, $_GET, $_SESSION y $_COOKIE
  - Gestionar inicio y cierre de sesión
  - Aplicar roles y permisos
  - Enviar datos a documento2.php mediante GET
  ==========================================================
*/

$usuarios = [
    "admin" => ["password" => "1234", "rol" => "admin"],
    "juan"  => ["password" => "abcd", "rol" => "usuario"],
    "editor" => ["password" => "qwer", "rol" => "editor"]
];

//Comprobacion si existe una sesion activa, redirige al doc2
if(isset($_SESSION['usuario'])){
    header('Location: doc2.php');
    exit;
}
// ======= TODO: Si se ha enviado el formulario (POST) =======

// 1. Comprobar si existen los campos 'usuario' y 'password' en $_POST.
// 2. Si están completos, guardar el nombre y el rol en la sesión.
// 3. Crear una cookie que almacene el último usuario durante 7 días.
// 4. Redirigir de nuevo a documento1.php para recargar la vista.
// 5. Si falta algún campo, guardar un mensaje de error en una variable $error.

//Comprobamos del formulario enviado a post
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["usuario"] ?? "");
    $contraseña = trim($_POST["password"] ?? "");

    // Validar que los campos existan
    if ($nombre === "" || $contraseña === "") {
        $error = "Por favor, completa ambos campos.";
    } else {
        // Validar usuario y contraseña recorriendo array con foreach
        $usuarioValido = false;
        foreach ($usuarios as $clave => $usuario) {
            if ($clave === $nombre && $usuario["password"] === $contraseña) {
                $_SESSION['usuario'] = $clave;
                $_SESSION['rol'] = $usuario['rol'];
                // Guardar cookie con nombre del usuario (7 días)
                setcookie("ultimo_usuario", $nombre, time() + (7 * 24 * 3600), "/");
                // Redirigir a documento2.php
                header("Location: documento2.php");
                exit;
            }
        }
        if (!$usuarioValido) {
            $error= "Usuario o contraseña incorrectos.";
        }
    }
}

// ======= TODO: Si se recibe la variable 'logout' por GET =======
// 1. Cerrar la sesión actual (session_unset() y session_destroy()).
// 2. Eliminar la cookie del último usuario.
// 3. Redirigir de nuevo a documento1.php.


?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Inicio de sesión + Panel de usuario</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      display: flex;
      min-height: 100vh;
      background-color: #f5f5f5;
    }
    aside {
      width: 300px;
      background: white;
      padding: 20px;
      box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 30px;
    }
    form {
      display: flex;
      flex-direction: column;
    }
    input, select, button {
      margin-bottom: 10px;
      padding: 8px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
    }
    button:hover {
      background: #0056b3;
    }
    .panel {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      width: 80%;
      max-width: 700px;
    }
  </style>
</head>
<body>

  <!-- Panel lateral (inicio de sesión o info de usuario) -->
  <aside>
    <h2>Sesión</h2>

    <?php if (isset($_SESSION['usuario'])): ?>
      <!-- TODO: Mostrar nombre de usuario y rol guardados en la sesión -->
      <p><strong>Usuario:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
      <p><strong>Rol:</strong> <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
      <!-- TODO: Añadir un botón o enlace para cerrar sesión (enlace con ?logout=true) -->
    
      <a href="doc1.php?logout=true">Cerrar sesión</a>
      <?php else: ?>
      <!-- TODO: Si existe un mensaje de error, mostrarlo en rojo -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
      <form method="POST" action="?">
        <label>Usuario:</label>
        <!-- TODO: El value debe rellenarse con la cookie 'ultimo_usuario' si existe -->
        <input type="text" name="usuario" value="<?php echo $_COOKIE['ultimo_usuario'] ?? ""; ?>" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        

        <button type="submit">Iniciar sesión</button>
      </form>
    <?php endif; ?>
  </aside>

  <!-- Contenido principal -->
  <main>
    <div class="panel">
      <?php if (isset($_SESSION['usuario'])): ?>
        <!-- TODO: Mostrar mensaje de bienvenida con el nombre de usuario -->
         <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?> </h1>
        <!-- TODO: Según el rol, mostrar diferentes botones con enlaces a documento2.php:
             - Si rol = admin → enlace con ?seccion=admin
             - Si rol = editor → enlace con ?seccion=editor
             - Si rol = usuario → enlace con ?seccion=perfil

             Mostrar botones segun rol
        -->
        <?php 
        $rol = $_SESSION['rol'];
        if($rol === 'admin'){
            echo '<a href="doc2.php?seccion=admin"><button>Panel Admin</button></a>';
        }elseif($rol = 'editor'){
            echo '<a href="doc2.php?seccion=editor"><button>Panel Admin</button></a>';

        }else{
            echo '<a href="doc2.php?seccion=perfil"><button>Panel Admin</button></a>';

        }
        ?>
      <?php else: ?>
        <p>Por favor, inicia sesión para acceder al sistema.</p>
      <?php endif; ?>
    </div>
  </main>

</body>
</html>
