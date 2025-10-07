<?php 
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>La Estacion Bistro - Inventario</title>
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
                <h1 class="h2 mb-4">Inventario</h1>

                <div class="d-flex justify-content-between mb-3">
                    <h4>Lista de ingredientes</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adjustModal">➕ Ajustar
                        inventario</button>
                </div>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ingrediente</th>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Última actualización</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-table">
                        <tr>
                            <td colspan="6">Cargando...</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Modal Ajuste -->
                <div class="modal fade" id="adjustModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="adjustForm">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ajustar inventario</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Ingrediente</label>
                                        <select id="adjustIngredient" class="form-select"></select>
                                    </div>
                                    <div class="mb-3">
                                        <label>Cantidad a ajustar</label>
                                        <input type="number" id="adjustCantidad" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Tipo de ajuste</label>
                                        <select id="adjustTipo" class="form-select">
                                            <option value="entrada">Entrada (+)</option>
                                            <option value="salida">Salida (-)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar ajuste</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <script src="./assets/js/inventory.js"></script>
            </main>
        </div>
    </div>
</body>

</html>