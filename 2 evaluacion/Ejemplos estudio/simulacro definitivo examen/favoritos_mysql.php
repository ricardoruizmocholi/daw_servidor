<?php
header("Content-Type: application/json; charset=utf-8");
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
$accion = $data["accion"] ?? "";

try {
    // Conexión
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=videojuegos_asir", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($accion === "guardar_favorito") {
        $juego = $data["juego"] ?? null;

        // TODO 7: Preparar un INSERT seguro en la tabla 'favorito'
        // Campos: titulo, fecha_lanzamiento, precio_base (guardado_en es automático)
        // Usa parámetros con nombre (:titulo, etc.)
        $sql = "INSERT INTO favorito (titulo, fecha_lanzamiento, precio_base) 
                VALUES (:t, :f, :p)";
        $stmt = $pdo->prepare($sql);
        // TODO 8: Ejecutar el INSERT con los datos del array $juego.
        $stmt->execute([
            ':t' => $j['titulo'],
            ':f' => $j['fecha_lanzamiento'],
            ':p' => $j['precio_base']
        ]);



        // TODO 9: Devolver un JSON diciendo "Guardado con éxito" Y 
        // además devolver el ID que MySQL le acaba de asignar (pista: lastInsertId()).
        
        echo json_encode([
            "ok"=>true,
            "mensaje"=>"Guardado con exito",
            "id_generado" => $pdo->lastInsertId()
        ]);

        exit;
    }

    if ($accion === "eliminar_favorito") {
        // TODO 10: Preparar una consulta DELETE para borrar por 'id_favorito'.
        $stmt = $pdo->prepare("DELETE FROM favorito WHERE id_favorito = :id");
        // TODO 11: Ejecutar el delete.
        $stmt->execute([
            ':id'=>$data['id_favorito']
        ]);
        
        
        // TODO 12: Devolver un JSON indicando CUÁNTOS registros se han borrado realmente.
        // Pista: Usa el método rowCount() del statement.
        $filas_borradas = $stmt->rowCount();

        echo json_encode([
            "ok" => true,
            "mensaje" => ($filasBorradas > 0) ? "Borrado OK" : "No existía ese ID",
            "cantidad" => $filasBorradas
        ]);
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}