<?php
include("../includes/db.php");

// Datos del usuario de prueba
$nombre   = "Usuario Test";
$email    = "test@bistro.com";
$password = "test123"; // contraseña en texto plano
$rol      = "admin";

// Generar hash seguro
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Verificar si ya existe
$check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$res = $check->get_result();

if ($res && $res->num_rows > 0) {
    echo "⚠️ Ya existe un usuario con el email $email<br>";
} else {
    $stmt = $conn->prepare("INSERT INTO users (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $hashed, $rol);

    if ($stmt->execute()) {
        echo "✅ Usuario de prueba creado:<br>";
        echo "Email: <b>$email</b><br>";
        echo "Contraseña: <b>$password</b><br>";
        echo "Rol: <b>$rol</b><br>";
    } else {
        echo "❌ Error al crear usuario: " . $stmt->error;
    }
}