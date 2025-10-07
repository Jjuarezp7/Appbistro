<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

$sql = "SELECT o.id, o.cliente, o.total, o.estado, o.creado_en,
               GROUP_CONCAT(CONCAT(p.nombre, ' x', op.cantidad) SEPARATOR ', ') AS productos
        FROM orders o
        LEFT JOIN order_products op ON o.id = op.order_id
        LEFT JOIN products p ON op.product_id = p.id
        GROUP BY o.id
        ORDER BY o.id DESC";

$result = $conn->query($sql);

$ordenes = [];
while ($row = $result->fetch_assoc()) {
    $ordenes[] = $row;
}
echo json_encode($ordenes);