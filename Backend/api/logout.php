<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../includes/db.php");
include("../includes/logger.php");

// 🔹 Registrar en bitácora ANTES de destruir la sesión
if (isset($_SESSION["id"])) {
registrarBitacora(
    $conn,
    $_SESSION["id"] ?? null,
    "logout",
    "Usuario {$_SESSION['nombre']} cerró sesión"
);
}

// 🔹 Destruir todas las variables de sesión
$_SESSION = [];

// 🔹 Destruir la cookie de sesión en el navegador (opcional pero recomendado)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 🔹 Finalmente destruir la sesión
session_destroy();

// 🔹 Redirigir al login
header("Location: ../../Frontend/login.php");
exit;