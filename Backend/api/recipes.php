<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

try {
    // Validar parámetro
    if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
        echo json_encode(["error" => "Falta o es inválido el parámetro product_id"]);
        exit;
    }

    $product_id = intval($_GET['product_id']);

    // 1. Obtener la receta asociada al producto
    $sql = "SELECT receta_id FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($receta_id);
    $stmt->fetch();
    $stmt->close();

    if (!$receta_id) {
        echo json_encode(["error" => "El producto no tiene receta asociada"]);
        exit;
    }

    // 2. Obtener ingredientes de esa receta
    $sql = "SELECT 
                ri.ingredient_id,
                i.nombre AS ingrediente,
                ri.cantidad,
                i.unidad
            FROM recipe_ingredients ri
            JOIN ingredients i ON ri.ingredient_id = i.id
            WHERE ri.recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $receta_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $receta = [];
    while ($row = $result->fetch_assoc()) {
        $receta[] = $row;
    }

    echo json_encode($receta);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => "Error en el servidor",
        "detalle" => $e->getMessage()
    ]);
}