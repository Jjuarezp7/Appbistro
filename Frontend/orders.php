<?php ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>La Estacion Bistro - Órdenes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
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
                    <li class="nav-item"><a href="orders.php" class="nav-link text-white active">🧾 Órdenes</a></li>
                    <li class="nav-item"><a href="inventory.php" class="nav-link text-white">📦 Inventario</a></li>
                    <li class="nav-item"><a href="reports.php" class="nav-link text-white">📊 Reportes</a></li>
                    <li class="nav-item"><a href="bitacora.php" class="nav-link text-white">📝 Bitácora</a></li>
                    <li class="nav-item"><a href="unidades.php" class="nav-link text-white">⚖️ Unidades</a></li>
                    <li class="nav-item"><a href="categorias_ingredientes.php" class="nav-link text-white">🥗 Categorías Ingredientes</a></li>
                    <li class="nav-item"><a href="categorias_productos.php" class="nav-link text-white">🍱 Categorías Productos</a></li>
                </ul>
                <a href="../Backend/api/logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h1 class="h2 mb-4">Órdenes</h1>

                <div class="d-flex justify-content-between mb-3">
                    <h4>Lista de órdenes</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">➕ Nueva orden</button>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="orders-table">
                        <tr>
                            <td colspan="6">Cargando...</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Modal Orden (crear/editar) -->
                <div class="modal fade" id="orderModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="orderForm">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nueva orden</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="orderId">
                                    <div class="mb-3">
                                        <label>Cliente</label>
                                        <input type="text" id="orderCliente" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Estado</label>
                                        <select id="orderEstado" class="form-select">
                                            <option value="pendiente">Pendiente</option>
                                            <option value="completada">Completada</option>
                                            <option value="cancelada">Cancelada</option>
                                        </select>
                                    </div>

                                    <!-- Productos de la orden -->
                                    <h5>Productos</h5>
                                    <div class="d-flex mb-2">
                                        <select id="orderProducto" class="form-select me-2"></select>
                                        <input type="number" id="orderCantidad" class="form-control me-2" placeholder="Cantidad">
                                        <button type="button" class="btn btn-secondary" onclick="addProductToOrder()">➕</button>
                                    </div>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="orderProductsTable"></tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Detalle de Orden -->
                <div class="modal fade" id="orderDetailModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalle de orden</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body" id="orderDetailBody">
                                Cargando...
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <script src="./assets/js/orders.js"></script>
            </main>
        </div>
    </div>
</body>

</html>