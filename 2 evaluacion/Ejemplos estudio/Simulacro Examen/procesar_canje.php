<?php
header("Content-Type: application/json");
require 'vendor/autoload.php';

// TODO 2: Leer el JSON entrante (php://input) y decodificarlo.
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// mi puerto de xamp es el 3306
$host = "127.0.0.1"; $port = "3306"; $db = "gaming_db"; $user = "root"; $pass = "";

$idS = $data["skin"];
$idU = $data["usuario"];
$accion = $data["accion"];

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $repuesta = ["ok"=>true, "mensaje"=>[]];

    if ($accion === "canjear"){
        // TODO 4: Iniciar una TRANSACCIÓN MySQL.
        $pdo->beginTransaction();
        
        // TODO 5: Ejecutar UPDATE 1 -> Restar 100 puntos al usuario (usar parámetros preparados).
        // SQL: UPDATE usuarios SET puntos = puntos - 100 WHERE id = :id
        $stmt = $pdo->prepare("UPDATE usuarios SET puntos = puntos - 100 WHERE id = :id");
        $stmt->execute([':id'=>$idU]);
        
    
        // TODO 6: Ejecutar UPDATE 2 -> Restar 1 al stock de la skin.
        // SQL: UPDATE skins SET stock = stock - 1 WHERE id = :id
        $stmt = $pdo->prepare("UPDATE skins SET stock = stock - 1 WHERE id = :id");
        $stmt->execute([':id'=>$idS]);
    
        $repuesta['mensaje'] = "Canje realizado en MySQL";
    
        // Si llegamos aquí, todo está bien.
        // TODO 7: Confirmar la transacción (COMMIT).
        $pdo->commit();
    
        echo json_encode($repuesta);

    }else{
        $repuesta['mensaje'] = "Accion no reconocida";
        echo json_encode($repuesta);
    }


} catch (Exception $e) {
    // TODO 8: Si algo falla, deshacer cambios (ROLLBACK).

      if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["ok" => false, "mensaje" => $e->getMessage()]);
}