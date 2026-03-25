<?php

// Supongamos que recibimos esto por Fetch JSON
$campoAEditar = $data["campo"]; // Viene del cliente (ej: "precio_base")
$nuevoValor = $data["valor"];   // Viene del cliente (ej: 45.00)
$idJuego = $data["id"];

// 1. LISTA BLANCA: Solo estos campos están permitidos
$camposPermitidos = ["titulo", "precio_base", "pegi", "genero"];

try {
    $pdo->beginTransaction();

    // 2. VALIDACIÓN CRÍTICA
    if (!in_array($campoAEditar, $camposPermitidos)) {
        throw new Exception("Campo no permitido o no existe.");
    }

    // 3. CONSTRUCCIÓN SEGURA
    // El nombre del campo va "a pelo" porque ya hemos comprobado que es seguro.
    // El valor SIEMPRE con marcador (:valor).
    $sql = "UPDATE videojuego SET $campoAEditar = :valor WHERE id_videojuego = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":valor" => $nuevoValor,
        ":id"    => $idJuego
    ]);

    $pdo->commit();
    echo json_encode(["ok" => true, "mensaje" => "Campo $campoAEditar actualizado"]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}