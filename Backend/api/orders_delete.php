<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);
$id   = intval($data["id"] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID de orden inválido"]);
    exit;
}

$conn->begin_transaction();

try {
    // Borrar detalle primero
    $stmt = $conn->prepare("DELETE FROM order_products WHERE order_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Borrar cabecera
    $stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->commit();

        // 👇 Registrar en bitácora
        registrarBitacora(
            $conn,
            $_SESSION['user_id'] ?? null, // el usuario que hizo la acción
            "eliminar",
            "Orden ID $id eliminada"
        );

        echo json_encode(["status" => "ok", "id" => $id]);
    } else {
        $conn->rollback();
        http_response_code(404);
        echo json_encode(["error" => "Orden no encontrada"]);
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en orders_delete: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar orden", "detalle" => $e->getMessage()]);
}