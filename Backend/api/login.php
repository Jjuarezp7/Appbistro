<?php
session_start();
include("../includes/db.php");
include("../includes/logger.php");

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

$email    = trim($data["email"] ?? "");
$password = trim($data["password"] ?? "");

// 🔹 Validaciones básicas
if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$stmt = $conn->prepare("SELECT id, nombre, email, password, rol FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // 🔹 Verificar contraseña
    if (password_verify($password, $row["password"])) {
        // Guardar sesión
        $_SESSION["id"]     = $row["id"];
        $_SESSION["email"] = $row["email"];
        $_SESSION["rol"]    = strtolower($row["rol"]); // normalizar a minúsculas

        // 🔹 Registrar login en bitácora
registrarBitacora(
    $conn,
    $row["id"], // usuario que inició sesión
    "login",
    "Usuario {$row['nombre']} inició sesión"
);

        echo json_encode([
            "status" => "ok",
            "rol"    => $row["rol"],
            "nombre" => $row["nombre"]
        ]);
        exit;
    } else {
        // Contraseña incorrecta
        error_log("Intento de login fallido (contraseña incorrecta) para $email");
    }
} else {
    // Usuario no encontrado
    error_log("Intento de login fallido (usuario no existe): $email");
}

http_response_code(401);
echo json_encode(["error" => "Credenciales inválidas"]);