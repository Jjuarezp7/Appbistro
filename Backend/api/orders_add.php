<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
include("../includes/db.php");
include("../includes/logger.php"); // 👈 importante

$data = json_decode(file_get_contents("php://input"), true);
error_log("Payload recibido en orders_add: " . print_r($data, true));

$cliente   = trim($data["cliente"] ?? "");
$estado    = trim($data["estado"] ?? "pendiente");
$productos = $data["productos"] ?? [];

if (!$cliente || empty($productos)) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan datos obligatorios"]);
    exit;
}

$conn->begin_transaction();

try {
    error_log("Paso 1: Calcular total");
    $total = 0;
    foreach ($productos as $prod) {
        $pid  = intval($prod["producto_id"] ?? 0);
        $cant = intval($prod["cantidad"] ?? 0);

        if ($pid <= 0 || $cant <= 0) {
            throw new Exception("Producto inválido en payload: " . print_r($prod, true));
        }

        $res = $conn->query("SELECT precio FROM products WHERE id = $pid");
        if (!$res || $res->num_rows === 0) {
            throw new Exception("Producto con ID $pid no encontrado en BD");
        }
        $row = $res->fetch_assoc();
        $total += $row["precio"] * $cant;
    }

    error_log("Paso 2: Insertar cabecera");
    $stmt = $conn->prepare("INSERT INTO orders (cliente, total, estado, creado_en) VALUES (?, ?, ?, NOW())");
    if (!$stmt) throw new Exception("Error preparando INSERT orders: " . $conn->error);
    $stmt->bind_param("sds", $cliente, $total, $estado);
    if (!$stmt->execute()) throw new Exception("Error ejecutando INSERT orders: " . $stmt->error);
    $order_id = $stmt->insert_id;

    error_log("Paso 3: Preparar statements detalle/receta");
    $stmtDetalle = $conn->prepare("INSERT INTO order_products (order_id, product_id, cantidad) VALUES (?, ?, ?)");
    $stmtReceta  = $conn->prepare("SELECT receta_id FROM products WHERE id = ?");
    $stmtIng     = $conn->prepare("SELECT ingredient_id, cantidad FROM recipe_ingredients WHERE recipe_id = ?");
    $stmtStock   = $conn->prepare("UPDATE ingredients SET cantidad = cantidad - ? WHERE id = ?");

    if (!$stmtDetalle || !$stmtReceta || !$stmtIng || !$stmtStock) {
        throw new Exception("Error preparando statements: " . $conn->error);
    }

    error_log("Paso 4: Insertar detalle y descontar inventario");
    foreach ($productos as $prod) {
        $pid  = intval($prod["producto_id"]);
        $cant = intval($prod["cantidad"]);

        // Insertar detalle
        $stmtDetalle->bind_param("iii", $order_id, $pid, $cant);
        if (!$stmtDetalle->execute()) throw new Exception("Error insertando detalle: " . $stmtDetalle->error);

        // Buscar receta asociada
        $receta_id = null;
        $stmtReceta->bind_param("i", $pid);
        $stmtReceta->execute();
        $resRec = $stmtReceta->get_result();
        if ($resRec && $rowRec = $resRec->fetch_assoc()) {
            $receta_id = $rowRec['receta_id'];
        }

        if ($receta_id) {
            error_log("Producto $pid tiene receta $receta_id");
            $stmtIng->bind_param("i", $receta_id);
            $stmtIng->execute();
            $resIng = $stmtIng->get_result();

            while ($row = $resIng->fetch_assoc()) {
                $ingredient_id = $row['ingredient_id'];
                $cantNecesaria = $row['cantidad'] * $cant;

                $stmtStock->bind_param("di", $cantNecesaria, $ingredient_id);
                if (!$stmtStock->execute()) {
                    throw new Exception("Error actualizando stock: " . $stmtStock->error);
                }
                error_log("Descontado ingrediente $ingredient_id: $cantNecesaria unidades");
            }
        } else {
            error_log("Producto $pid no tiene receta asociada");
        }
    }

    $conn->commit();

    error_log("Paso 5: Registrar en bitácora");
    registrarBitacora(
        $conn,
        $_SESSION['user_id'] ?? null,
        "crear",
        "Orden ID $order_id creada (cliente: $cliente, estado: $estado, total: $total)"
    );

    echo json_encode([
        "status"  => "ok",
        "id"      => $order_id,
        "cliente" => $cliente,
        "estado"  => $estado,
        "total"   => $total
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error en orders_add: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "error" => "Error al crear orden",
        "detalle" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
}