<?php
session_start();
include("../includes/db.php");
include("../includes/logger.php");
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data["id"] ?? 0);
$nombre = trim($data["nombre"] ?? "");
$abreviatura = trim($data["abreviatura"] ?? "");

if ($id <= 0 || !$nombre || !$abreviatura) {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

$stmt = $conn->prepare("UPDATE unidades_medida SET nombre = ?, abreviatura = ? WHERE id = ?");
$stmt->bind_param("ssi", $nombre, $abreviatura, $id);

if ($stmt->execute()) {
    registrarBitacora($conn, $_SESSION['user_id'] ?? null, "editar", "Unidad ID $id actualizada a '$nombre' ($abreviatura)");
    echo json_encode(["status" => "ok", "id" => $id, "nombre" => $nombre, "abreviatura" => $abreviatura]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar unidad"]);
}