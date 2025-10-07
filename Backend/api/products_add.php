<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

// Solo admin puede agregar productos
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    http_response_code(403);
    echo json_encode(["error" => "Permisos insuficientes"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$nombre      = trim($data["nombre"] ?? "");
$precio      = floatval($data["precio"] ?? 0);
$categoriaId = intval($data["categoria_id"] ?? 0);
$recetaId    = !empty($data["receta_id"]) ? intval($data["receta_id"]) : null;

// Validaciones básicas
if (!$nombre || !$precio || !$categoriaId) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

// Insertar producto con receta_id (puede ser NULL)
$stmt = $conn->prepare("INSERT INTO products (nombre, precio, categoria_id, receta_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdii", $nombre, $precio, $categoriaId, $recetaId);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;

    // Obtener nombre de la categoría para la bitácora
    $catRes = $conn->query("SELECT nombre FROM categorias_productos WHERE id=$categoriaId");
    $catNombre = $catRes->fetch_assoc()['nombre'] ?? 'Desconocida';

    // Obtener nombre de la receta (si aplica)
    $recetaNombre = "Sin receta";
    if ($recetaId) {
        $recRes = $conn->query("SELECT nombre FROM recipes WHERE id=$recetaId");
        $recetaNombre = $recRes->fetch_assoc()['nombre'] ?? 'Desconocida';
    }

    // Registrar en bitácora
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null,
        "crear",
        "Producto '$nombre' (ID $newId, precio: $precio, categoría: $catNombre, receta: $recetaNombre) creado"
    );

    echo json_encode([
        "status"       => "ok",
        "id"           => $newId,
        "nombre"       => $nombre,
        "precio"       => $precio,
        "categoria_id" => $categoriaId,
        "categoria"    => $catNombre,
        "receta_id"    => $recetaId,
        "receta"       => $recetaNombre
    ]);
} else {
    error_log("Error SQL en products_add: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al crear producto"]);
}