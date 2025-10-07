<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");

$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}
echo json_encode($usuarios);