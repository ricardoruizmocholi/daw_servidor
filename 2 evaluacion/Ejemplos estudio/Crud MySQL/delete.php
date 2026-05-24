<?php

$idBorrar = 10;

$stmt = $pdo->prepare("DELETE FROM videojuego WHERE id_videojuego = :id");
$stmt->execute([':id' => $idBorrar]);

echo "Eliminado correctamente.";

/*

Conceptos de PHP que preguntaste (Aplicados a MySQL)Array Asociativo: 
Es lo que te da una sola fila. 
Ejemplo: $juego['titulo']. Es una pareja Clave => Valor.
Array Bidimensional: Es lo que te da fetchAll(). 
Es una "lista de listas". 
La primera dimensión es la fila (0, 1, 2...) y la segunda es la columna ('titulo', 'precio').
Ejemplo de acceso: $videojuegos[0]['titulo'] (El título del primer videojuego de la lista).
Objetos: Si usaras $stmt->fetchAll(PDO::FETCH_OBJ), accederías así: $juego->titulo. 
Pero en clase soléis usar FETCH_ASSOC porque es más directo para trabajar con JSON.💡
 Resumen de Sintaxis para el examenFunción¿Para qué sirve?$pdo->prepare($sql)Envía la consulta al servidor de 
 SQL para que la "estudie" sin ejecutarla.$stmt->execute([...])
 Envía los datos reales y ejecuta la consulta.$stmt->fetchAll()
 Recupera todos los resultados de un SELECT.$stmt->fetch()
 Recupera solo la primera fila (útil para buscar por ID único).$stmt->rowCount()
 Devuelve el número de filas afectadas (útil en UPDATE y DELETE).

*/
?>