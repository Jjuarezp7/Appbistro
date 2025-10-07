<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../../includes/db.php");
include("../../includes/logger.php"); // 👈 importante

// Leer JSON del body
$data = json_decode(file_get_contents("php://input"), true);

$id           = intval($data["id"] ?? 0);
$nombre       = trim($data["nombre"] ?? "");
$descripcion  = trim($data["descripcion"] ?? "");
$ingredientes = $data["ingredientes"] ?? [];
$tipo         = trim($data["tipo"] ?? "ingrediente"); // 👈 nuevo

// Validaciones básicas
if ($id <= 0 || !$nombre || !is_array($ingredientes) || !count($ingredientes)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "error"  => "Datos inválidos",
        "debug"  => [
            "id"           => $id,
            "nombre"       => $nombre,
            "ingredientes" => $ingredientes,
            "tipo"         => $tipo
        ]
    ]);
    exit;
}

$conn->begin_transaction();
try {
    // Actualizar receta con tipo
    $stmt = $conn->prepare("UPDATE recipes SET nombre = ?, descripcion = ?, tipo = ? WHERE id = ?");
    if (!$stmt) throw new Exception("Error en prepare UPDATE: " . $conn->error);
    $stmt->bind_param("sssi", $nombre, $descripcion, $tipo, $id);
    if (!$stmt->execute()) throw new Exception("Error en execute UPDATE: " . $stmt->error);

    // Eliminar ingredientes previos
    $del = $conn->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
    if (!$del) throw new Exception("Error en prepare DELETE: " . $conn->error);
    $del->bind_param("i", $id);
    if (!$del->execute()) throw new Exception("Error en execute DELETE: " . $del->error);

    // Insertar nuevos ingredientes
    $stmtIng = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, cantidad) VALUES (?, ?, ?)");
    if (!$stmtIng) throw new Exception("Error en prepare INSERT: " . $conn->error);

    foreach ($ingredientes as $ing) {
        $iid  = intval($ing["ingredient_id"]);
        $cant = floatval($ing["cantidad"]);
        if ($iid > 0 && $cant > 0) {
            $stmtIng->bind_param("iid", $id, $iid, $cant);
            if (!$stmtIng->execute()) {
                throw new Exception("Error en execute INSERT: " . $stmtIng->error . " | Datos: recipe_id=$id, ingredient_id=$iid, cantidad=$cant");
            }
        }
    }

    $conn->commit();

    // 👇 Registrar en bitácora
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null,
        "editar",
        "Receta ID $id actualizada (nombre: $nombre, tipo: $tipo, ingredientes: " . count($ingredientes) . ")"
    );

    echo json_encode(["status" => "ok"]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en recipes_edit: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "error"  => "Error al editar receta",
        "detalle"=> $e->getMessage(),
        "debug"  => [
            "id"           => $id,
            "nombre"       => $nombre,
            "ingredientes" => $ingredientes,
            "tipo"         => $tipo
        ]
    ]);
}