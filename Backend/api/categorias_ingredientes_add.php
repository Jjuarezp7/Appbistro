<?php
session_start();
include("../includes/db.php");
include("../includes/logger.php");
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$nombre = trim($data["nombre"] ?? "");

if (!$nombre) {
    http_response_code(400);
    echo json_encode(["error" => "El nombre es obligatorio"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO categorias_ingredientes (nombre) VALUES (?)");
$stmt->bind_param("s", $nombre);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    registrarBitacora($conn, $_SESSION['user_id'] ?? null, "crear", "Categoría de ingrediente '$nombre' creada");
    echo json_encode(["status" => "ok", "id" => $id, "nombre" => $nombre]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al crear categoría"]);
}