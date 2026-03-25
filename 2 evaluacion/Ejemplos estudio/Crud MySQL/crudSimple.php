<?php
header("Content-Type: application/json; charset=utf-8");

$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Captura de datos del JSON
$id = $data["id"] ?? null;
$titulo = $data["titulo"] ?? null;
$precio = $data["precio"] ?? null;
$fecha = $data["fecha_lanzamiento"] ?? null; // Corregido el nombre
$pegi = $data["pegi"] ?? null;
$motor = $data["motor"] ?? null;
$multijugador = (isset($data["multijugador"]) && $data["multijugador"] == "on") ? 1 : 0;
$desc = $data["desc"] ?? null;
$accion = $data["accion"] ?? null;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $respuesta = ["ok" => true, "juegos" => []];

    switch ($accion) {
        case 'add':
            $stmt = $pdo->prepare("INSERT INTO videojuego (titulo, precio_base, es_multijugador, fecha_lanzamiento, pegi, motor, descripcion) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $precio, $multijugador, $fecha, $pegi, $motor, $desc]);
            $respuesta["mensaje"] = "Añadido con éxito";
            break;

        case 'update':
            $stmt = $pdo->prepare("UPDATE videojuego SET titulo=?, precio_base=?, es_multijugador=?, fecha_lanzamiento=?, pegi=?, motor=?, descripcion=? WHERE id_videojuego=?");
            $stmt->execute([$titulo, $precio, $multijugador, $fecha, $pegi, $motor, $desc, $id]);
            $respuesta["mensaje"] = "Actualizado con éxito";
            break;

        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM videojuego WHERE id_videojuego = ?");
            $stmt->execute([$id]);
            $respuesta["mensaje"] = "Eliminado con éxito";
            break;

        case 'show':
            
            // Si hay ID busca uno, si no, busca por título, si no, trae todos
            if (!empty($id)) {
                $stmt = $pdo->prepare("SELECT * FROM videojuego WHERE id_videojuego = ?");
                $stmt->execute([$id]);
            } elseif (!empty($titulo)) {
                $stmt = $pdo->prepare("SELECT * FROM videojuego WHERE titulo LIKE ?");
                $stmt->execute(["%$titulo%"]);
            } else {
                $stmt = $pdo->query("SELECT * FROM videojuego");
            }
            $respuesta["juegos"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'show2':
            $stmt = $pdo->prepare("SELECT v.titulo, e.nombre AS estudio, GROUP_CONCAT(g.nombre SEPARATOR ', ') AS generos
                FROM videojuego v
                LEFT JOIN estudio e ON v.id_estudio = e.id_estudio
                LEFT JOIN videojuego_genero vg ON v.id_videojuego = vg.id_videojuego
                LEFT JOIN genero g ON vg.id_genero = g.id_genero
                WHERE v.id_videojuego = :id 
                GROUP BY v.id_videojuego");
            
            $stmt->execute([":id" => $id]); // Ejecutamos sin sobrescribir $stmt
            $respuesta["juegos"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break; // IMPORTANTE: Pon el break

        case 'show3':
            $stmt = $pdo->prepare("SELECT v.titulo, e.nombre AS estudio, GROUP_CONCAT(g.nombre SEPARATOR ', ') AS generos
                FROM videojuego v
                LEFT JOIN estudio e ON v.id_estudio = e.id_estudio
                LEFT JOIN videojuego_genero vg ON v.id_videojuego = vg.id_videojuego
                LEFT JOIN genero g ON vg.id_genero = g.id_genero
                WHERE v.titulo LIKE :titulo
                GROUP BY v.id_videojuego");
            
            $stmt->execute([":titulo" => "%$titulo%"]); // Corregido: añadimos los % para el LIKE
            $respuesta["juegos"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
    echo json_encode($respuesta);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}