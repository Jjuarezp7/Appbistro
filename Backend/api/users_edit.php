<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

// Solo admin puede editar usuarios
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    http_response_code(403);
    echo json_encode(["error" => "Permisos insuficientes"]);
    exit;
}

// Recibir datos
$data = json_decode(file_get_contents("php://input"), true);

$id     = intval($data["id"] ?? 0);
$nombre = trim($data["nombre"] ?? "");
$email  = trim($data["email"] ?? "");
$rol    = trim($data["rol"] ?? "");
$pass   = trim($data["password"] ?? ""); 

// Validaciones básicas
if (!$id || !$nombre || !$email) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Formato de email inválido"]);
    exit;
}

// Validar rol contra ENUM
$rolesValidos = ["admin", "cajero", "cocinero"];
if (!in_array($rol, $rolesValidos)) {
    $rol = "cajero"; // valor por defecto
}

// Verificar que el email no esté duplicado en otro usuario
$check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
$check->bind_param("si", $email, $id);
$check->execute();
$result = $check->get_result();

if ($result && $result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "El email ya está en uso por otro usuario"]);
    exit;
}

// ============================
// 🔹 Actualizar usuario
// ============================
if (!empty($pass)) {
    // Si viene contraseña, actualizarla también
    $hashed = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET nombre=?, email=?, rol=?, password=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre, $email, $rol, $hashed, $id);
} else {
    // Si no viene contraseña, no la tocamos
    $stmt = $conn->prepare("UPDATE users SET nombre=?, email=?, rol=? WHERE id=?");
    $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
}

if ($stmt->execute()) {
    // 👇 Aquí registramos en bitácora
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null, // el admin que hizo la acción
        "editar",
        "Usuario ID $id actualizado (nombre: $nombre, rol: $rol)"
    );

    echo json_encode([
        "status" => "ok",
        "id"     => $id,
        "nombre" => $nombre,
        "email"  => $email,
        "rol"    => $rol
    ]);
} else {
    error_log("Error SQL en users_edit: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar usuario"]);
}