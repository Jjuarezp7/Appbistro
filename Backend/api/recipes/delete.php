<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../../includes/db.php");
include("../../includes/logger.php"); // 👈 importante

// Leer el ID desde GET o JSON
$id = intval($_GET["id"] ?? 0);
if ($id <= 0) {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data["id"] ?? 0);
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => "error", "error" => "ID inválido"]);
    exit;
}

$conn->begin_transaction();
try {
    // Eliminar ingredientes asociados
    $delIng = $conn->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
    $delIng->bind_param("i", $id);
    $delIng->execute();

    // Eliminar receta
    $delRec = $conn->prepare("DELETE FROM recipes WHERE id = ?");
    $delRec->bind_param("i", $id);
    $delRec->execute();

    if ($delRec->affected_rows > 0) {
        $conn->commit();

        // 👇 Registrar en bitácora
        registrarBitacora(
            $conn,
            $_SESSION['user_id'] ?? null, // el usuario que hizo la acción
            "eliminar",
            "Receta ID $id eliminada"
        );

        echo json_encode(["status" => "ok", "id" => $id]);
    } else {
        $conn->rollback();
        http_response_code(404);
        echo json_encode(["status" => "error", "error" => "Receta no encontrada"]);
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en recipes_delete: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "error"  => "Error al eliminar receta",
        "detalle"=> $e->getMessage()
    ]);
}