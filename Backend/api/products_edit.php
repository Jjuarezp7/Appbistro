<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

// Solo admin puede editar productos (ajusta si quieres permitir a otros roles)
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    http_response_code(403);
    echo json_encode(["error" => "Permisos insuficientes"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id          = intval($data["id"] ?? 0);
$nombre      = trim($data["nombre"] ?? "");
$precio      = floatval($data["precio"] ?? 0);
$categoriaId = intval($data["categoria_id"] ?? 0);
$recetaId    = !empty($data["receta_id"]) ? intval($data["receta_id"]) : null;

// Validaciones básicas
if (!$id || !$nombre || !$precio || !$categoriaId) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

// Actualizar producto con receta_id
$stmt = $conn->prepare("UPDATE products SET nombre=?, precio=?, categoria_id=?, receta_id=? WHERE id=?");
$stmt->bind_param("sdiii", $nombre, $precio, $categoriaId, $recetaId, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        // Obtener nombres para bitácora
        $catRes = $conn->query("SELECT nombre FROM categorias_productos WHERE id=$categoriaId");
        $catNombre = $catRes->fetch_assoc()['nombre'] ?? 'Desconocida';

        $recetaNombre = "Sin receta";
        if ($recetaId) {
            $recRes = $conn->query("SELECT nombre FROM recipes WHERE id=$recetaId");
            $recetaNombre = $recRes->fetch_assoc()['nombre'] ?? 'Desconocida';
        }

        // Registrar en bitácora
        registrarBitacora(
            $conn,
            $_SESSION['user_id'] ?? null,
            "editar",
            "Producto ID $id actualizado (nombre: $nombre, precio: $precio, categoría: $catNombre, receta: $recetaNombre)"
        );

        echo json_encode([
            "status"       => "ok",
            "id"           => $id,
            "nombre"       => $nombre,
            "precio"       => $precio,
            "categoria_id" => $categoriaId,
            "categoria"    => $catNombre,
            "receta_id"    => $recetaId,
            "receta"       => $recetaNombre
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Producto no encontrado"]);
    }
} else {
    error_log("Error SQL en products_edit: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar producto"]);
}