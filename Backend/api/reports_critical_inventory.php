<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");


// Ingredientes con stock bajo
$sql = "SELECT id, nombre, cantidad, unidad
        FROM ingredients
        WHERE cantidad < 5
        ORDER BY cantidad ASC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);