<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

/*
   Ahora ingredients tiene:
   - categoria_id → FK a categorias_ingredientes
   - unidad_id    → FK a unidades_medida
*/

$sql = "
    SELECT 
        i.id,
        i.nombre,
        ci.nombre AS categoria,
        i.cantidad,
        CONCAT(um.nombre, IF(um.abreviatura IS NOT NULL AND um.abreviatura <> '', CONCAT(' (', um.abreviatura, ')'), '')) AS unidad,
        i.actualizado_en
    FROM ingredients i
    LEFT JOIN categorias_ingredientes ci ON i.categoria_id = ci.id
    LEFT JOIN unidades_medida um ON i.unidad_id = um.id
    ORDER BY i.nombre ASC
";

$result = $conn->query($sql);

$inventario = [];
while ($row = $result->fetch_assoc()) {
    $inventario[] = $row;
}

echo json_encode($inventario);