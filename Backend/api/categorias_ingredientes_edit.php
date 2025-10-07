<?php
session_start();
include("../includes/db.php");
include("../includes/logger.php");
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);
$nombre = trim($data["nombre"] ?? "");

if ($id <= 0 || !$nombre) {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

$stmt = $conn->prepare("UPDATE categorias_ingredientes SET nombre = ? WHERE id = ?");
$stmt->bind_param("si", $nombre, $id);

if ($stmt->execute()) {
    registrarBitacora($conn, $_SESSION['user_id'] ?? null, "editar", "Categoría de ingrediente ID $id actualizada a '$nombre'");
    echo json_encode(["status" => "ok", "id" => $id, "nombre" => $nombre]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar categoría"]);
}