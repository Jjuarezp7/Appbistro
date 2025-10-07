<?php
include("../includes/db.php");

$email = "admin@bistro.com";
$password = "123456";

echo "<h2>🔍 Depuración de Login</h2>";

$stmt = $conn->prepare("SELECT id, nombre, email, password, rol FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<p>✅ Usuario encontrado: <strong>{$row['email']}</strong></p>";
    echo "<p>Rol: <strong>{$row['rol']}</strong></p>";
    echo "<p>Hash en BD: <code>{$row['password']}</code></p>";

    if (password_verify($password, $row["password"])) {
        echo "<p style='color:green;'>✅ Contraseña válida</p>";
    } else {
        echo "<p style='color:red;'>❌ Contraseña inválida</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Usuario no encontrado</p>";
}

