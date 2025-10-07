<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);

$id          = intval($data["id"] ?? 0);
$nombre      = trim($data["nombre"] ?? "");
$categoriaId = intval($data["categoria_id"] ?? 0);
$cantidad    = floatval($data["cantidad"] ?? 0);
$unidadId    = intval($data["unidad_id"] ?? 0);

// Validaciones básicas
if (!$id || !$nombre || !$categoriaId || !$unidadId) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$stmt = $conn->prepare("UPDATE ingredients SET nombre=?, categoria_id=?, cantidad=?, unidad_id=? WHERE id=?");
$stmt->bind_param("sidii", $nombre, $categoriaId, $cantidad, $unidadId, $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
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
            $_SESSION['user_id'] ?? null,
            "editar",
            "Ingrediente ID $id actualizado (nombre: $nombre, categoría: $catNombre, cantidad: $cantidad $unidadNombre)"
        );

        echo json_encode([
            "status"       => "ok",
            "id"           => $id,
            "nombre"       => $nombre,
            "categoria_id" => $categoriaId,
            "categoria"    => $catNombre,
            "cantidad"     => $cantidad,
            "unidad_id"    => $unidadId,
            "unidad"       => $unidadNombre,
            "abreviatura"  => $unidadAbrev
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Ingrediente no encontrado"]);
    }
} else {
    error_log("Error SQL en ingredients_edit: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al actualizar ingrediente"]);
}