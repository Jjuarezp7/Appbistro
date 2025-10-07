<?php
session_start();
include("../includes/db.php");

header('Content-Type: application/json; charset=utf-8');

// Detectar modo: JSON (por defecto) o CSV
$mode = $_GET['mode'] ?? 'json';

// Filtros
$filtroUsuario = $_GET['usuario'] ?? '';
$filtroAccion  = $_GET['accion'] ?? '';
$filtroFecha   = $_GET['fecha'] ?? '';

// Paginación (solo para JSON)
$page   = max(1, intval($_GET['page'] ?? 1));
$limit  = max(1, intval($_GET['limit'] ?? 20));
$offset = ($page - 1) * $limit;

// Query base con JOIN a users
$queryBase = "
    FROM bitacora b
    LEFT JOIN users u ON b.usuario_id = u.id
    WHERE 1=1
";

// Aplicar filtros
if ($filtroUsuario !== '') {
    $safeUsuario = $conn->real_escape_string($filtroUsuario);
    $queryBase .= " AND (u.nombre LIKE '%$safeUsuario%' OR u.email LIKE '%$safeUsuario%')";
}
if ($filtroAccion !== '') {
    $safeAccion = $conn->real_escape_string($filtroAccion);
    $queryBase .= " AND b.accion LIKE '%$safeAccion%'";
}
if ($filtroFecha !== '') {
    $safeFecha = $conn->real_escape_string($filtroFecha);
    $queryBase .= " AND DATE(b.fecha) = '$safeFecha'";
}

// --- MODO CSV ---
if ($mode === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=bitacora.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Usuario', 'Acción', 'Descripción', 'Fecha']);

    $csvQuery = "
        SELECT b.id, COALESCE(u.nombre, u.email, '—') AS usuario, 
               b.accion, b.descripcion, b.fecha
        $queryBase
        ORDER BY b.fecha DESC
    ";
    $result = $conn->query($csvQuery);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['usuario'],
            $row['accion'],
            $row['descripcion'],
            $row['fecha']
        ]);
    }
    fclose($output);
    exit;
}

// --- MODO JSON ---
$totalQuery = "SELECT COUNT(*) as total $queryBase";
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'] ?? 0;

$dataQuery = "
    SELECT b.id, COALESCE(u.nombre, u.email, '—') AS usuario, 
           b.accion, b.descripcion, b.fecha
    $queryBase
    ORDER BY b.fecha DESC
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($dataQuery);

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode([
    "page"  => $page,
    "limit" => $limit,
    "total" => $totalRows,
    "pages" => ceil($totalRows / $limit),
    "data"  => $rows
]);