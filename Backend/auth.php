<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario esté autenticado.
 * Si no lo está, devuelve error JSON (para APIs) o redirige al login (para frontend).
 */
function checkAuth($modo = "api") {
    if (!isset($_SESSION["id"]) || !isset($_SESSION["rol"])) {
        if ($modo === "api") {
            http_response_code(401);
            echo json_encode(["error" => "No autenticado"]);
            exit;
        } else {
            header("Location: ../Frontend/login.php");
            exit;
        }
    }
}

/**
 * Verifica que el usuario tenga uno de los roles permitidos.
 * Usa checkAuth() primero para asegurar que hay sesión.
 */
function checkRole(array $rolesPermitidos, $modo = "api") {
    checkAuth($modo);

    $rolUsuario = $_SESSION["rol"] ?? null;

    if (!$rolUsuario || !in_array($rolUsuario, $rolesPermitidos)) {
        if ($modo === "api") {
            http_response_code(403);
            echo json_encode(["error" => "Permisos insuficientes"]);
            exit;
        } else {
            header("Location: ../Frontend/acceso_denegado.php");
            exit;
        }
    }
}