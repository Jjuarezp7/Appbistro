<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["ingredient_id"], $data["cantidad"], $data["tipo"])) {
    echo json_encode(["error" => "Faltan parámetros"]);
    exit;
}

$ingredient_id = intval($data["ingredient_id"]);
$cantidad = floatval($data["cantidad"]);
$tipo = trim($data["tipo"]);

if ($ingredient_id <= 0 || $cantidad <= 0 || !in_array($tipo, ["entrada", "salida"])) {
    echo json_encode(["error" => "Datos inválidos"]);
    exit;
}

// Determinar signo del ajuste
$ajuste = ($tipo === "entrada") ? $cantidad : -$cantidad;

try {
    // Verificar que el ingrediente exista
    $check = $conn->prepare("SELECT nombre, cantidad FROM ingredients WHERE id = ?");
    $check->bind_param("i", $ingredient_id);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows === 0) {
        echo json_encode(["error" => "Ingrediente no encontrado"]);
        exit;
    }
    $ing = $res->fetch_assoc();

    // Actualizar inventario
    $stmt = $conn->prepare("UPDATE ingredients SET cantidad = cantidad + ?, actualizado_en = NOW() WHERE id = ?");
    $stmt->bind_param("di", $ajuste, $ingredient_id);
    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    // Registrar en bitácora
    $descripcion = "Ajuste de inventario: {$tipo} de {$cantidad} al ingrediente '{$ing['nombre']}' (ID {$ingredient_id})";
    registrarBitacora($conn, $_SESSION['user_id'] ?? null, "ajuste_inventario", $descripcion);

    echo json_encode([
        "status" => "ok",
        "ingrediente" => $ing['nombre'],
        "ajuste" => $ajuste,
        "nuevo_total" => $ing['cantidad'] + $ajuste
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => "Error al ajustar inventario", "detalle" => $e->getMessage()]);
}