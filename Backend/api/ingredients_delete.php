<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);
$id   = intval($data["id"] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "ID de ingrediente inválido"]);
    exit;
}

// 🔹 Obtener datos del ingrediente antes de eliminar
$sql = "
    SELECT i.nombre, i.cantidad, 
           c.nombre AS categoria,
           u.nombre AS unidad, u.abreviatura
    FROM ingredients i
    LEFT JOIN categorias_ingredientes c ON i.categoria_id = c.id
    LEFT JOIN unidades_medida u ON i.unidad_id = u.id
    WHERE i.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$ingrediente = $res->fetch_assoc();

if (!$ingrediente) {
    http_response_code(404);
    echo json_encode(["error" => "Ingrediente no encontrado"]);
    exit;
}

// 🔹 Eliminar ingrediente
$stmtDel = $conn->prepare("DELETE FROM ingredients WHERE id=?");
$stmtDel->bind_param("i", $id);

if ($stmtDel->execute()) {
    if ($stmtDel->affected_rows > 0) {
        // 👇 Registrar en bitácora con nombre, categoría y unidad
        registrarBitacora(
            $conn,
            $_SESSION['user_id'] ?? null,
            "eliminar",
            "Ingrediente eliminado: '{$ingrediente['nombre']}' (ID $id, categoría: {$ingrediente['categoria']}, cantidad: {$ingrediente['cantidad']} {$ingrediente['unidad']} {$ingrediente['abreviatura']})"
        );

        echo json_encode([
            "status"     => "ok",
            "id"         => $id,
            "nombre"     => $ingrediente['nombre'],
            "categoria"  => $ingrediente['categoria'],
            "cantidad"   => $ingrediente['cantidad'],
            "unidad"     => $ingrediente['unidad'],
            "abreviatura"=> $ingrediente['abreviatura']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Ingrediente no encontrado"]);
    }
} else {
    error_log("Error SQL en ingredients_delete: " . $stmtDel->error);
    http_response_code(500);
    echo json_encode(["error" => "Error al eliminar ingrediente"]);
}