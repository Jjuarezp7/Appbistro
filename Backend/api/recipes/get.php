<?php
header('Content-Type: application/json; charset=utf-8');
include("../../includes/db.php");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["error" => "ID de receta inválido"]);
    exit;
}

$sql = "SELECT r.id, r.nombre, r.descripcion, r.costo, r.tipo,
               ri.ingredient_id, ri.cantidad,
               i.nombre AS ingrediente, i.unidad
        FROM recipes r
        LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        LEFT JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$receta = null;
while ($row = $result->fetch_assoc()) {
    if (!$receta) {
        $receta = [
            "id"          => $row['id'],
            "nombre"      => $row['nombre'],
            "descripcion" => $row['descripcion'],
            "costo"       => $row['costo'],
            "tipo"        => $row['tipo'],   // 👈 ahora se devuelve
            "ingredientes"=> []
        ];
    }
    if ($row['ingredient_id']) {
        $receta["ingredientes"][] = [
            "ingredient_id" => $row['ingredient_id'],
            "nombre"        => $row['ingrediente'],
            "unidad"        => $row['unidad'],
            "cantidad"      => $row['cantidad']
        ];
    }
}

if ($receta) {
    echo json_encode($receta, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["error" => "Receta no encontrada"]);
}