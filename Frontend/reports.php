<?php 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>La Estacion Bistro - Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block bg-dark sidebar text-white min-vh-100 p-3">
<div class="d-flex align-items-center mb-4">
    <img src="assets/img/logo.png" alt="Logo" style="height: 40px;" class="me-3">
    <h1 class="h2 mb-0">La Estación Bistro</h1>
</div>
                <ul class="nav flex-column mt-4">
                    <li class="nav-item"><a href="index.php" class="nav-link text-white">🏠 Dashboard</a></li>
                    <li class="nav-item"><a href="users.php" class="nav-link text-white">👤 Usuarios</a></li>
                    <li class="nav-item"><a href="ingredients.php" class="nav-link text-white">🥬 Ingredientes</a></li>
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

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h1 class="h2 mb-4">Reportes</h1>

                <div class="row">
                    <!-- Ventas por día -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">Ventas por día</div>
                            <div class="card-body">
                                <canvas id="ventasChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Ventas por día -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Ventas por día</span>
                                <form id="filtroVentas" class="d-flex gap-2">
                                    <input type="date" id="ventasInicio" class="form-control form-control-sm">
                                    <input type="date" id="ventasFin" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <canvas id="ventasChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Productos más vendidos -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Productos más vendidos</span>
                                <form id="filtroProductos" class="d-flex gap-2">
                                    <input type="date" id="productosInicio" class="form-control form-control-sm">
                                    <input type="date" id="productosFin" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <canvas id="productosChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Inventario crítico -->
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">Inventario crítico</div>
                                <div class="card-body">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Ingrediente</th>
                                                <th>Cantidad</th>
                                                <th>Unidad</th>
                                            </tr>
                                        </thead>
                                        <tbody id="critical-inventory">
                                            <tr>
                                                <td colspan="3">Cargando...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Consumo de ingredientes</span>
                                <form id="filtroIngredientes" class="d-flex gap-2">
                                    <input type="date" id="fechaInicio" class="form-control form-control-sm">
                                    <input type="date" id="fechaFin" class="form-control form-control-sm">
                                    <button type="submit" class="btn btn-sm btn-primary">Filtrar</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <canvas id="ingredientesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header">Consumo de ingredientes</div>
                            <div class="card-body">
                                <canvas id="ingredientesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                    <script src="./assets/js/reports.js"></script>
            </main>
        </div>
    </div>
</body>

</html>