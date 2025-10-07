<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);

$id        = intval($data["id"]);
$cliente   = trim($data["cliente"] ?? "");
$estado    = trim($data["estado"] ?? "");
$productos = $data["productos"] ?? [];

$conn->begin_transaction();

try {
    // Calcular total
    $total = 0;
    foreach ($productos as $prod) {
        $pid  = intval($prod["producto_id"]);
        $cant = intval($prod["cantidad"]);
        $res = $conn->query("SELECT precio FROM products WHERE id = $pid");
        if ($row = $res->fetch_assoc()) {
            $total += $row["precio"] * $cant;
        }
    }

    // Actualizar cabecera
    $stmt = $conn->prepare("UPDATE orders SET cliente=?, total=?, estado=? WHERE id=?");
    $stmt->bind_param("sdsi", $cliente, $total, $estado, $id);
    $stmt->execute();

    // Borrar detalle previo
    $conn->query("DELETE FROM order_products WHERE order_id = $id");

    // Insertar nuevo detalle
    $stmt = $conn->prepare("INSERT INTO order_products (order_id, product_id, cantidad) VALUES (?, ?, ?)");
    foreach ($productos as $prod) {
        $pid  = intval($prod["producto_id"]);
        $cant = intval($prod["cantidad"]);
        $stmt->bind_param("iii", $id, $pid, $cant);
        $stmt->execute();
    }

    // 🔹 Descontar inventario si la orden está completada
    if ($estado === "completada") {
        foreach ($productos as $prod) {
            $pid  = intval($prod["producto_id"]);
            $cant = intval($prod["cantidad"]);

            // Buscar receta asociada al producto
            $sql = "SELECT receta_id FROM products WHERE id = $pid";
            $res = $conn->query($sql);
            if ($row = $res->fetch_assoc()) {
                $receta_id = $row["receta_id"];

                if ($receta_id) {
                    // Buscar ingredientes de la receta
                    $sqlIng = "SELECT ingredient_id, cantidad 
                               FROM recipe_ingredients 
                               WHERE recipe_id = $receta_id";
                    $resIng = $conn->query($sqlIng);

                    while ($ing = $resIng->fetch_assoc()) {
                        $ingredient_id = intval($ing["ingredient_id"]);
                        $cantReceta    = floatval($ing["cantidad"]);

                        // Cantidad total a descontar = cantidad receta * cantidad pedida
                        $descuento = $cantReceta * $cant;

                        $stmtIng = $conn->prepare("UPDATE ingredients 
                                                   SET cantidad = cantidad - ?, actualizado_en = NOW() 
                                                   WHERE id = ?");
                        $stmtIng->bind_param("di", $descuento, $ingredient_id);
                        $stmtIng->execute();
                    }
                }
            }
        }
    }

    $conn->commit();

    // 👇 Registrar en bitácora después de commit exitoso
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null, // el usuario que hizo la acción
        "editar",
        "Orden ID $id actualizada (cliente: $cliente, estado: $estado, total: $total)"
    );

    echo json_encode(["status" => "ok", "total" => $total]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en orders_edit: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Error al editar orden", "detalle" => $e->getMessage()]);
}
