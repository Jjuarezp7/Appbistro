<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["id"])) {
        http_response_code(400);
        echo json_encode(["error" => "Falta el parámetro id"]);
        exit;
    }

    $id = intval($data["id"]);

    // 🔹 Obtener datos del producto antes de eliminar
    $sql = "
        SELECT p.nombre, c.nombre AS categoria
        FROM products p
        LEFT JOIN categorias_productos c ON p.categoria_id = c.id
        WHERE p.id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $producto = $res->fetch_assoc();

    if (!$producto) {
        http_response_code(404);
        echo json_encode(["error" => "Producto no encontrado"]);
        exit;
    }

    // 🔹 Eliminar producto
    $stmtDel = $conn->prepare("DELETE FROM products WHERE id=?");
    $stmtDel->bind_param("i", $id);

    if ($stmtDel->execute()) {
        if ($stmtDel->affected_rows > 0) {
            // 👇 Registrar en bitácora con nombre y categoría
            registrarBitacora(
                $conn,
                $_SESSION['user_id'] ?? null,
                "eliminar",
                "Producto eliminado: '{$producto['nombre']}' (ID $id, categoría: {$producto['categoria']})"
            );

            echo json_encode([
                "status"    => "ok",
                "id"        => $id,
                "nombre"    => $producto['nombre'],
                "categoria" => $producto['categoria']
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Producto no encontrado"]);
        }
    } else {
        error_log("Error SQL en products_delete: " . $stmtDel->error);
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar producto"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error"   => "Error en el servidor",
        "detalle" => $e->getMessage()
    ]);
}