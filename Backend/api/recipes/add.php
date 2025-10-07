<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../../includes/db.php");
include("../../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);

$nombre       = trim($data["nombre"] ?? "");
$descripcion  = trim($data["descripcion"] ?? "");
$ingredientes = $data["ingredientes"] ?? [];
$tipo         = trim($data["tipo"] ?? "ingrediente"); // 👈 por defecto "ingrediente"

if (!$nombre || !is_array($ingredientes) || !count($ingredientes)) {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

$conn->begin_transaction();
try {
    // Insertar receta con tipo
    $stmt = $conn->prepare("INSERT INTO recipes (nombre, descripcion, costo, tipo, creado_en) VALUES (?, ?, 0, ?, NOW())");
    if (!$stmt) {
        throw new Exception("Error preparando INSERT: " . $conn->error);
    }
    $stmt->bind_param("sss", $nombre, $descripcion, $tipo);
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando INSERT: " . $stmt->error);
    }
    $recipe_id = $stmt->insert_id;

    // Insertar ingredientes
    $stmtIng = $conn->prepare("INSERT INTO recipe_ingredients (recipe_id, ingredient_id, cantidad) VALUES (?, ?, ?)");
    if (!$stmtIng) {
        throw new Exception("Error preparando INSERT ingredientes: " . $conn->error);
    }

    foreach ($ingredientes as $ing) {
        $iid  = intval($ing["ingredient_id"]);
        $cant = floatval($ing["cantidad"]);
        if ($iid > 0 && $cant > 0) {
            $stmtIng->bind_param("iid", $recipe_id, $iid, $cant);
            if (!$stmtIng->execute()) {
                throw new Exception("Error insertando ingrediente: " . $stmtIng->error);
            }
        }
    }

    $conn->commit();

    // 👇 Registrar en bitácora
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null,
        "crear",
        "Receta '$nombre' (ID $recipe_id, tipo: $tipo) creada con " . count($ingredientes) . " ingredientes"
    );

    echo json_encode([
        "status" => "ok",
        "id"     => $recipe_id,
        "nombre" => $nombre,
        "tipo"   => $tipo
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en recipes_add: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error"   => "Error al crear receta",
        "detalle" => $e->getMessage()
    ]);
}