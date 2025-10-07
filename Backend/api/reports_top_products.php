<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");


$fecha_inicio = $_GET["inicio"] ?? null;
$fecha_fin    = $_GET["fin"] ?? null;

$where = "o.estado = 'completada'";
if ($fecha_inicio && $fecha_fin) {
    $where .= " AND DATE(o.creado_en) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

$sql = "SELECT p.nombre, SUM(op.cantidad) AS cantidad
        FROM order_products op
        JOIN orders o ON op.order_id = o.id
        JOIN products p ON op.product_id = p.id
        WHERE $where
        GROUP BY p.id
        ORDER BY cantidad DESC
        LIMIT 10";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = ["nombre" => $row["nombre"], "cantidad" => (int)$row["cantidad"]];
}

echo json_encode($data);