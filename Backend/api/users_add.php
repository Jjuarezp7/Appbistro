<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php");


// Solo admin puede crear usuarios
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    http_response_code(403);
    echo json_encode(["error" => "Permisos insuficientes"]);
    exit;
}

// Recibir datos
$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data["nombre"] ?? "");
$email  = trim($data["email"] ?? "");
$rol    = trim($data["rol"] ?? "");
$pass   = trim($data["password"] ?? "");

// Validaciones básicas
if (!$nombre || !$email || !$rol || !$pass) {
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

// Verificar que el email no esté duplicado
$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result && $result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "El email ya está en uso"]);
    exit;
}

// Insertar usuario
$hashed = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (nombre, email, rol, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $email, $rol, $hashed);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;

    // Registrar en bitácora
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null, // el admin que hizo la acción
        "crear",
        "Usuario '$nombre' (ID $newId, rol $rol) creado"
    );


    echo json_encode([
        "status" => "ok",
        "id"     => $stmt->insert_id,
        "nombre" => $nombre,
        "email"  => $email,
        "rol"    => $rol
    ]);
} else {
    error_log("Error SQL en users_add: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al crear usuario"]);
}