<?php

/*

 Transacciones Normales (El flujo "Todo o Nada")
Se usa cuando una acción depende de otra. Por ejemplo: para comprar un juego, primero resto el stock y luego registro la venta. Si el stock no se puede restar, la venta no debe existir.

Funciones Clave:
$pdo->beginTransaction(): "Congela" la base de datos para tus cambios.

$pdo->commit(): Guarda los cambios permanentemente.

$pdo->rollBack(): Deshace todo lo hecho desde el inicio de la transacción.


*/
try {
    // 1. Iniciamos la transacción
    $pdo->beginTransaction();

    // 2. Primera operación: Restar saldo al usuario
    $stmt1 = $pdo->prepare("UPDATE usuarios SET saldo = saldo - :precio WHERE id = :id");
    $stmt1->execute([':precio' => 50, ':id' => 1]);

    // 3. Segunda operación: Añadir juego a la biblioteca
    // Forzamos un error si queremos probar el rollback (ej: tabla inexistente)
    $stmt2 = $pdo->prepare("INSERT INTO biblioteca (id_usuario, id_juego) VALUES (:u, :j)");
    $stmt2->execute([':u' => 1, ':j' => 99]);

    // 4. Si todo ha ido bien, confirmamos
    $pdo->commit();
    echo "Compra realizada con éxito.";

} catch (Exception $e) {
    // 5. Si algo falla en CUALQUIERA de los pasos, volvemos atrás
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error en la compra: " . $e->getMessage();
}