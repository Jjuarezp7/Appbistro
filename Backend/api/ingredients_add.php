<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);

$nombre      = trim($data["nombre"] ?? "");
$categoriaId = intval($data["categoria_id"] ?? 0);
$cantidad    = floatval($data["cantidad"] ?? 0);
$unidadId    = intval($data["unidad_id"] ?? 0);

// Validaciones básicas
if (!$nombre || !$categoriaId || !$unidadId) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO ingredients (nombre, categoria_id, cantidad, unidad_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sidi", $nombre, $categoriaId, $cantidad, $unidadId);

if ($stmt->execute()) {
    $newId = $stmt->insert_id;

    // Obtener nombre de la categoría
    $catRes = $conn->query("SELECT nombre FROM categorias_ingredientes WHERE id=$categoriaId");
    $catNombre = $catRes->fetch_assoc()['nombre'] ?? 'Desconocida';

    // Obtener nombre de la unidad
    $uniRes = $conn->query("SELECT nombre, abreviatura FROM unidades_medida WHERE id=$unidadId");
    $uniRow = $uniRes->fetch_assoc();
    $unidadNombre = $uniRow['nombre'] ?? 'Desconocida';
    $unidadAbrev  = $uniRow['abreviatura'] ?? '';

    // 👇 Registrar en bitácora
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null, // el usuario que hizo la acción
        "crear",
        "Ingrediente '$nombre' (ID $newId, categoría: $catNombre, cantidad: $cantidad $unidadNombre) creado"
    );

    echo json_encode([
        "status"       => "ok",
        "id"           => $newId,
        "nombre"       => $nombre,
        "categoria_id" => $categoriaId,
        "categoria"    => $catNombre,
        "cantidad"     => $cantidad,
        "unidad_id"    => $unidadId,
        "unidad"       => $unidadNombre,
        "abreviatura"  => $unidadAbrev
    ]);
} else {
    error_log("Error SQL en ingredients_add: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al crear ingrediente"]);
}