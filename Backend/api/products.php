<?php
header('Content-Type: application/json; charset=utf-8');
include("../includes/db.php");

$sql = "
    SELECT p.id, p.nombre, p.precio, p.categoria_id, c.nombre AS categoria, p.receta_id, r.nombre AS receta_nombre
    FROM products p
    LEFT JOIN categorias_productos c ON p.categoria_id = c.id
    LEFT JOIN recipes r ON p.receta_id = r.id
    ORDER BY p.id DESC
";

$result = $conn->query($sql);

$productos = [];
while ($row = $result->fetch_assoc()) {
    $producto = $row;

    // Si el producto tiene receta asociada, traer ingredientes
    if (!empty($row['receta_id'])) {
        $sqlIng = "SELECT ri.ingredient_id, i.nombre, ri.cantidad, i.unidad
                   FROM recipe_ingredients ri
                   JOIN ingredients i ON ri.ingredient_id = i.id
                   WHERE ri.recipe_id = ?";
        $stmtIng = $conn->prepare($sqlIng);
        $stmtIng->bind_param("i", $row['receta_id']);
        $stmtIng->execute();
        $resIng = $stmtIng->get_result();

        $ingredientes = [];
        while ($ing = $resIng->fetch_assoc()) {
            $ingredientes[] = $ing;
        }
        $producto['ingredientes'] = $ingredientes;
    } else {
        $producto['ingredientes'] = [];
    }

    $productos[] = $producto;
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);