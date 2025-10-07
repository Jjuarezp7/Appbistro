<?php
session_start();
include("../includes/db.php");
include("../includes/logger.php");
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "ID inválido"]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM categorias_productos WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    registrarBitacora($conn, $_SESSION['user_id'] ?? null, "eliminar", "Categoría de producto ID $id eliminada");
    echo json_encode(["status" => "ok", "id" => $id]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar categoría"]);
}