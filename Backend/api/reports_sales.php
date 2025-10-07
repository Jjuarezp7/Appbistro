<?php
include("../includes/db.php");
header("Content-Type: application/json");


$inicio = $_GET['inicio'] ?? null;
$fin    = $_GET['fin'] ?? null;

if ($inicio && $fin) {
    // Ventas por día en rango de fechas
    $stmt = $conn->prepare("
        SELECT DATE(creado_en) AS fecha, SUM(total) AS total
        FROM orders
        WHERE DATE(creado_en) BETWEEN ? AND ?
          AND estado = 'completada'
        GROUP BY DATE(creado_en)
        ORDER BY fecha ASC
    ");
    $stmt->bind_param("ss", $inicio, $fin);
} else {
    // Ventas de los últimos 7 días
    $stmt = $conn->prepare("
        SELECT DATE(creado_en) AS fecha, SUM(total) AS total
        FROM orders
        WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
          AND estado = 'completada'
        GROUP BY DATE(creado_en)
        ORDER BY fecha ASC
    ");
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "fecha" => $row["fecha"],
        "total" => (float)$row["total"]
    ];
}

echo json_encode($data);