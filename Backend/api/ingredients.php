<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

$sql = "
    SELECT 
        i.id, 
        i.nombre, 
        i.cantidad, 
        i.categoria_id, 
        c.nombre AS categoria,
        i.unidad_id,
        u.nombre AS unidad,
        u.abreviatura
    FROM ingredients i
    LEFT JOIN categorias_ingredientes c ON i.categoria_id = c.id
    LEFT JOIN unidades_medida u ON i.unidad_id = u.id
    ORDER BY i.id DESC
";

$result = $conn->query($sql);

$ingredientes = [];
while ($row = $result->fetch_assoc()) {
    $ingredientes[] = $row;
}

echo json_encode($ingredientes);