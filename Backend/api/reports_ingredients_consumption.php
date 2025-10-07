<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");


// Filtros opcionales por fecha
$fecha_inicio = $_GET["inicio"] ?? null;
$fecha_fin    = $_GET["fin"] ?? null;

$where = "o.estado = 'completada'";
if ($fecha_inicio && $fecha_fin) {
    $where .= " AND DATE(o.creado_en) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

// Consulta: consumo de ingredientes
$sql = "SELECT i.nombre, i.unidad, SUM(ri.cantidad * op.cantidad) AS total_usado
        FROM orders o
        JOIN order_products op ON o.id = op.order_id
        JOIN products p ON op.product_id = p.id
        JOIN recipes r ON p.receta_id = r.id
        JOIN recipe_ingredients ri ON r.id = ri.recipe_id
        JOIN ingredients i ON ri.ingredient_id = i.id
        WHERE $where
        GROUP BY i.id
        ORDER BY total_usado DESC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "ingrediente" => $row["nombre"],
        "unidad" => $row["unidad"],
        "total_usado" => (float)$row["total_usado"]
    ];
}

echo json_encode($data);