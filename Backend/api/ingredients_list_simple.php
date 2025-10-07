<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

$sql = "
    SELECT 
        i.id, 
        i.nombre, 
        i.categoria_id, 
        c.nombre AS categoria,
        i.unidad_id,
        u.nombre AS unidad,
        u.abreviatura
    FROM ingredients i
    LEFT JOIN categorias_ingredientes c ON i.categoria_id = c.id
    LEFT JOIN unidades_medida u ON i.unidad_id = u.id
    ORDER BY i.nombre ASC
";

$res = $conn->query($sql);

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = [
        "id"           => (int)$row["id"],
        "nombre"       => $row["nombre"],
        "categoria_id" => (int)$row["categoria_id"],
        "categoria"    => $row["categoria"],
        "unidad_id"    => (int)$row["unidad_id"],
        "unidad"       => $row["unidad"],
        "abreviatura"  => $row["abreviatura"]
    ];
}

echo json_encode($data);