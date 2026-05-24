<?php


$id = 5;
$nuevoPrecio = 19.99;

$sql = "UPDATE videojuego SET precio_base = :precio WHERE id_videojuego = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':precio' => $nuevoPrecio,
    ':id'     => $id
]);

// rowCount() te dice cuántas filas se han modificado realmente
echo "Filas actualizadas: " . $stmt->rowCount();


?>