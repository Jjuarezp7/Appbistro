<?php
include("../includes/db.php");
header('Content-Type: application/json; charset=utf-8');

$res = $conn->query("SELECT id, nombre, abreviatura FROM unidades_medida ORDER BY nombre ASC");
$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);