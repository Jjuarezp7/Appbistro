<?php
include("../Backend/includes/db.php");

// --- Ventas de hoy ---
$ventasHoy = $conn->query("
    SELECT IFNULL(SUM(total),0) AS ventas_hoy
    FROM orders
    WHERE DATE(creado_en) = CURDATE()
      AND estado = 'completada'
")->fetch_assoc()['ventas_hoy'];

// --- Órdenes de hoy ---
$ordenesHoy = $conn->query("
    SELECT COUNT(*) AS ordenes_hoy
    FROM orders
    WHERE DATE(creado_en) = CURDATE()
      AND estado = 'completada'
")->fetch_assoc()['ordenes_hoy'];

// --- Top productos del día ---
$topProductosDia = $conn->query("
    SELECT p.nombre, SUM(op.cantidad) AS vendidos
    FROM order_products op
    JOIN products p ON op.product_id = p.id
    JOIN orders o ON op.order_id = o.id
    WHERE DATE(o.creado_en) = CURDATE()
      AND o.estado = 'completada'
    GROUP BY p.nombre
    ORDER BY vendidos DESC
    LIMIT 3
");

// --- Ingredientes bajo stock ---
$ingredientesBajo = $conn->query("
    SELECT i.nombre, ci.nombre AS categoria, i.cantidad, i.unidad
    FROM ingredients i
    LEFT JOIN categorias_ingredientes ci ON i.categoria_id = ci.id
    WHERE i.cantidad < 10
    ORDER BY i.cantidad ASC
");

// --- Ventas por día del mes ---
$ventasMes = $conn->query("
    SELECT DATE(creado_en) AS fecha, SUM(total) AS ventas
    FROM orders
    WHERE MONTH(creado_en) = MONTH(CURDATE())
      AND YEAR(creado_en) = YEAR(CURDATE())
      AND estado = 'completada'
    GROUP BY DATE(creado_en)
    ORDER BY fecha ASC
");

$labels = [];
$data = [];
while($row = $ventasMes->fetch_assoc()) {
    $labels[] = $row['fecha'];
    $data[] = $row['ventas'];
}

// --- Ventas por mes del año ---
$ventasAnuales = $conn->query("
    SELECT MONTH(creado_en) AS mes, SUM(total) AS ventas
    FROM orders
    WHERE YEAR(creado_en) = YEAR(CURDATE())
      AND estado = 'completada'
    GROUP BY MONTH(creado_en)
    ORDER BY mes ASC
");

$meses = [];
$ventasMeses = [];
while($row = $ventasAnuales->fetch_assoc()) {
    $nombreMes = date("F", mktime(0, 0, 0, $row['mes'], 1));
    $meses[] = $nombreMes;
    $ventasMeses[] = $row['ventas'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - La Estación Bistro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="icon" type="image/png" href="assets/img/logo.png">
</head>
<body class="bg-light">
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 bg-dark text-white min-vh-100 p-3">
    <div class="d-flex align-items-center mb-4">
        <img src="assets/img/logo.png" alt="Logo" style="height: 40px;" class="me-3">
        <h1 class="h2 mb-0">La Estación Bistro</h1>
    </div>

            <h2>La Estación Bistro</h2>
            <ul class="nav flex-column mt-4">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">🏠 Dashboard</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link text-white">👤 Usuarios</a></li>
                    <li class="nav-item"><a href="ingredients.php" class="nav-link text-white active">🥬 Ingredientes</a></li>
                    <li class="nav-item"><a href="recipes.php" class="nav-link text-white">📖 Recetas</a></li>
                    <li class="nav-item"><a href="products.php" class="nav-link text-white">🍔 Productos</a></li>
                    <li class="nav-item"><a href="orders.php" class="nav-link text-white">🧾 Órdenes</a></li>
                    <li class="nav-item"><a href="inventory.php" class="nav-link text-white">📦 Inventario</a></li>
                    <li class="nav-item"><a href="reports.php" class="nav-link text-white">📊 Reportes</a></li>
                    <li class="nav-item"><a href="bitacora.php" class="nav-link text-white">📝 Bitácora</a></li>
                    <li class="nav-item"><a href="unidades.php" class="nav-link text-white active">⚖️ Unidades</a></li>                
                    <li class="nav-item"><a href="categorias_ingredientes.php" class="nav-link text-white active">🥗 Categorías Ingredientes</a></li>
                    <li class="nav-item"><a href="categorias_productos.php" class="nav-link text-white active">🍱 Categorías Productos</a></li>
            </ul>
            <a href="../Backend/api/logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
        </nav>

        <!-- Contenido principal -->
        <main class="col-md-10 p-4">
            <h1 class="mb-4">Dashboard</h1>

            <!-- KPIs -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Ventas de hoy</h5>
                            <h3>Q <?php echo number_format($ventasHoy, 2); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5>Órdenes de hoy</h5>
                            <h3><?php echo $ordenesHoy; ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficas -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <canvas id="ventasDia"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="ventasAnuales"></canvas>
                </div>
            </div>

            <!-- Tablas -->
            <div class="row">
                <div class="col-md-6">
                    <h4>📈 Top productos del día</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Producto</th><th>Vendidos</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = $topProductosDia->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['vendidos']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>⚠️ Ingredientes bajos</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr><th>Ingrediente</th><th>Categoría</th><th>Cantidad</th><th>Unidad</th></tr>
                        </thead>
                        <tbody>
                            <?php while($row = $ingredientesBajo->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['categoria']; ?></td>
                                    <td><?php echo $row['cantidad']; ?></td>
                                    <td><?php echo $row['unidad']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
// Ventas por día del mes
new Chart(document.getElementById('ventasDia'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Ventas diarias',
            data: <?php echo json_encode($data); ?>,
            borderColor: 'blue',
            fill: false
        }]
    }
});

// Ventas por mes del año
new Chart(document.getElementById('ventasAnuales'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($meses); ?>,
        datasets: [{
            label: 'Ventas mensuales',
            data: <?php echo json_encode($ventasMeses); ?>,
            backgroundColor: 'green'
        }]
    }
});
</script>
</body>
</html>