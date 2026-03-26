<?php
header("Content-Type: application/json");
require 'vendor/autoload.php';
use MongoDB\Client;
use MongoDB\BSON\ObjectId;

$data = json_decode(file_get_contents("php://input"), true);
$accion = $data['accion'] ?? '';

$host = "127.0.0.1"; $port = "3306"; $dbname="tienda_hardware"; $user = "root"; $pass = "";

try {
    // TODO 4: Conexión PDO (MySQL) y Client (MongoDB).
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $client = new Client("mongodb://localhost:27017");
    $coleccion = $client->Tienda->Especificaciones;

    switch ($accion) {
        case 'compra':
            // TODO 5: TRANSACCIÓN SIMPLE.
            // 1. Iniciar transacción.
            $pdo->beginTransaction();
            try {

                // 2. Update stock = stock - 1 en MySQL.

                $st1 = $pdo->prepare("UPDATE productos SET stock = stock - 1 WHERE id_producto = :id ");
                $st1->execute([
                    ":id"=>$data['id']
                ]);

                // 3. Si el stock resultante es < 0, lanzar Exception para ir al catch.
                $res = $pdo->query("SELECT stock FROM productos WHERE id_producto = ". $data["id"])->fetch();
                if($res['stock']<0) throw new Exception("Sin existencias");
                //Dudo del comportamiento de esto

                // 4. Commit.
                $pdo->commit();
                echo json_encode(["mensaje"=>"venta OK"]);
            } catch (Exception $e) {
                $pdo->rollBack();
                echo json_encode(["error"=> $e->getMessage()]);
                //throw $th;
            }
            break;

        case 'dinamico':
            $campo = $data['campo'];
            $valor = ($campo == 'precio')? $data['precio'] : $data['stock'];

            

            // TODO 6: UPDATE DINÁMICO (LISTA BLANCA).
            // 1. Crear array $listaBlanca con ['precio', 'stock', 'categoria'].
            $listaBlanca = ['precio', 'stock', 'categoria'];

            // 2. Validar si $data['campo'] está en la lista.

            if (!in_array($campo, $listaBlanca)) {
                die(json_encode(["error" => "Campo no permitido"]));
            }

            // 3. Si es válido, ejecutar UPDATE usando el nombre del campo directamente en el SQL.

            $pdo->beginTransaction();
            // Concatenamos el campo (seguro por whitelist) y bindeamos el valor
            $sql = "UPDATE productos SET $campo = :val WHERE id_producto = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':val' => $valor, ':id' => $data['id']]);
            $pdo->commit();
            echo json_encode(["msg" => "Campo $campo actualizado dinámicamente"]);

            break;

        case 'mongo':
            // TODO 7: MONGODB UPSERT (ANIDADO).
            // 1. Usar updateOne con el filtro ["id_mysql" => $data['id']].
            $filtro = ["id_mysql" => $data['id']];
            $cambios = [
                '$set' => [
                    "nombre"=> $data['nombre'],
                    "detalles"=>[
                        "marca" => $data['specs']['marca'],
                        "consumo" => $data['specs']['consumo'],
                        "ultima_revision" => date("Y-m-d")
                    ]
                ]
                
            ];

            $resultado = $coleccion->updateOne($filtro, $cambios,["upsert" => true]);

            // 2. Usar '$set' para guardar el nombre y el objeto "detalles" {marca, consumo}.
            // 3. Añadir la opción ["upsert" => true].
            break;

        case 'eliminar':
            // TODO 8: ELIMINACIÓN HÍBRIDA (EL RETO).
            // 1. Iniciar transacción MySQL.
            // 2. Borrar el producto de MySQL (DELETE FROM ... WHERE id = ...).
            // 3. Borrar el documento de MongoDB (deleteOne(["id_mysql" => ...])).
            // 4. Si ambos borran, hacer Commit.
            $pdo->beginTransaction();
            
            // 1. Borrar en MySQL
            $stM = $pdo->prepare("DELETE FROM productos WHERE id_producto = :id");
            $stM->execute([':id'=>[$data['id']]]);

            // 2. Borrar en MongoDB
            $coleccion->deleteOne(["id_mysql" => $data['id']]);

            $pdo->commit();
            echo json_encode(["ok" => true, "mensaje" => "Producto eliminado de ambos sistemas"]);
            break;

        default:
            throw new Exception("Acción no válida");
    }

    echo json_encode(["ok" => true, "mensaje" => "Operación $accion completada"]);

} catch (Exception $e) {
    // TODO 9: Gestión de errores y Rollback de MySQL si hay transacción abierta.
    if ($pdo->inTransaction()) {
        
        $pdo->rollBack();
    }
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}