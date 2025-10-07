<?php
header('Content-Type: application/json; charset=utf-8');
include("../../includes/db.php");

$sql = "SELECT r.id, r.nombre, r.descripcion, r.costo, r.tipo,
               ri.ingredient_id, ri.cantidad,
               i.nombre AS ingrediente, i.unidad
        FROM recipes r
        LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        LEFT JOIN ingredients i ON ri.ingredient_id = i.id
        ORDER BY r.id DESC";

$result = $conn->query($sql);

$recetas = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    if (!isset($recetas[$id])) {
        $recetas[$id] = [
            "id"          => $id,
            "nombre"      => $row['nombre'],
            "descripcion" => $row['descripcion'],
            "costo"       => $row['costo'],
            "tipo"        => $row['tipo'],   // 👈 ahora se devuelve
            "ingredientes"=> []
        ];
    }
    if ($row['ingredient_id']) {
        $recetas[$id]["ingredientes"][] = [
            "ingredient_id" => $row['ingredient_id'],
            "nombre"        => $row['ingrediente'],
            "unidad"        => $row['unidad'],
            "cantidad"      => $row['cantidad']
        ];
    }
}

echo json_encode(array_values($recetas), JSON_UNESCAPED_UNICODE);