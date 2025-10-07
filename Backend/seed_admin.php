<?php
// seed_admin.php
session_start();
header('Content-Type: text/plain; charset=utf-8');

include("./includes/db.php");

// Asegurar charset correcto
$conn->set_charset("utf8mb4");

$email = "admin@admin.com";
$nombre = "admin";
$rol = "admin";
$passwordPlano = "admin123";

// Generar hash seguro
$hash = password_hash($passwordPlano, PASSWORD_DEFAULT);

// Verificar si ya existe
$sql = "SELECT id, email, password FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $user = $res->fetch_assoc();
    echo "⚠️ Ya existe un usuario con email {$email}\n";
    echo "Hash actual en BD: {$user['password']}\n";
    echo "Contraseña de prueba que deberías usar: {$passwordPlano}\n";
} else {
    $sql = "INSERT INTO users (nombre, email, password, rol, creado_en) 
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $hash, $rol);

    if ($stmt->execute()) {
        echo "✅ Usuario admin creado correctamente\n";
        echo "Email: {$email}\n";
        echo "Contraseña: {$passwordPlano}\n";
        echo "Hash guardado: {$hash}\n";
    } else {
        echo "❌ Error al crear usuario: " . $stmt->error;
    }
}