<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

// Solo admin puede eliminar usuarios
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    http_response_code(403);
    echo json_encode(["error" => "Permisos insuficientes"]);
    exit;
}

// Recibir datos
$data = json_decode(file_get_contents("php://input"), true);
$id   = intval($data["id"] ?? 0);

// Validación básica
if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID de usuario inválido"]);
    exit;
}

// Evitar que un admin se elimine a sí mismo (opcional)
if ($id == ($_SESSION["user_id"] ?? 0)) {
    http_response_code(400);
    echo json_encode(["error" => "No puedes eliminar tu propio usuario"]);
    exit;
}

// Eliminar usuario
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // 👇 Registrar en bitácora
        registrarBitacora(
            $conn,
            $_SESSION['user_id'] ?? null, // el admin que hizo la acción
            "eliminar",
            "Usuario ID $id eliminado"
        );

        echo json_encode(["status" => "ok", "id" => $id]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
} else {
    error_log("Error SQL en users_delete: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar usuario"]);
}