<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

$id = intval($_GET["id"] ?? 0);
$response = [];

if ($id <= 0) {
    echo json_encode(["error" => "ID inválido"]);
    exit;
}

// Cabecera
$stmt = $conn->prepare("SELECT id, cliente, total, estado, creado_en FROM orders WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $response = $row;
} else {
    echo json_encode(["error" => "Orden no encontrada"]);
    exit;
}

// Detalle de productos
$sql = "SELECT op.product_id AS producto_id, op.cantidad, p.nombre, p.receta_id
        FROM order_products op
        JOIN products p ON op.product_id = p.id
        WHERE op.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

$productos = [];
while ($r = $res->fetch_assoc()) {
    $producto = [
        "producto_id" => $r["producto_id"],
        "nombre"      => $r["nombre"],
        "cantidad"    => $r["cantidad"],
        "ingredientes" => []
    ];

    // Si el producto tiene receta asociada, obtener ingredientes
    if (!empty($r["receta_id"])) {
        $sqlIng = "SELECT ri.ingredient_id, i.nombre, ri.cantidad, i.unidad
                   FROM recipe_ingredients ri
                   JOIN ingredients i ON ri.ingredient_id = i.id
                   WHERE ri.recipe_id = ?";
        $stmtIng = $conn->prepare($sqlIng);
        $stmtIng->bind_param("i", $r["receta_id"]);
        $stmtIng->execute();
        $resIng = $stmtIng->get_result();

        while ($ing = $resIng->fetch_assoc()) {
            $producto["ingredientes"][] = [
                "ingredient_id" => $ing["ingredient_id"],
                "nombre"        => $ing["nombre"],
                "cantidad_usada"=> $ing["cantidad"] * $r["cantidad"], // multiplicar por cantidad vendida
                "unidad"        => $ing["unidad"]
            ];
        }
    }

    $productos[] = $producto;
}

$response["productos"] = $productos;

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);