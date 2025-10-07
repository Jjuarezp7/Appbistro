<?php
include("../Backend/includes/db.php"); // conexión a la BD
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ingredientes - La Estación Bistro</title>
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

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto px-md-4 py-4">
                <h1 class="h2 mb-4">Ingredientes</h1>

                <div class="d-flex justify-content-between mb-3">
                    <h4>Lista de ingredientes</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ingredientModal">➕ Nuevo ingrediente</button>
                </div>

                <!-- Tabla -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ingrediente</th>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ingredients-table">
                        <tr>
                            <td colspan="6">Cargando...</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Modal -->
                <div class="modal fade" id="ingredientModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="ingredientForm">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nuevo Ingrediente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="ingredientId">

                                    <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="ingNombre" class="form-label">Nombre</label>
                                        <input type="text" id="ingNombre" class="form-control" required>
                                    </div>

                                    <!-- Categoría -->
                                    <div class="mb-3">
                                        <label for="ingCategoria" class="form-label">Categoría</label>
                                        <select id="ingCategoria" class="form-select" required>
                                            <option value="">Selecciona una categoría</option>
                                            <?php
                                                $res = $conn->query("SELECT id, nombre FROM categorias_ingredientes ORDER BY nombre");
                                                while ($cat = $res->fetch_assoc()) {
                                                    echo "<option value='{$cat['id']}'>{$cat['nombre']}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="mb-3">
                                        <label for="ingCantidad" class="form-label">Cantidad</label>
                                        <input type="number" id="ingCantidad" class="form-control" required>
                                    </div>

                                    <!-- Unidad -->
                                    <div class="mb-3">
                                        <label for="ingUnidad" class="form-label">Unidad</label>
                                        <select id="ingUnidad" class="form-select" required>
                                            <option value="">Selecciona una unidad</option>
                                            <?php
                                                $resU = $conn->query("SELECT id, nombre, abreviatura FROM unidades_medida ORDER BY nombre");
                                                while ($u = $resU->fetch_assoc()) {
                                                    echo "<option value='{$u['id']}'>{$u['nombre']} ({$u['abreviatura']})</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Scripts -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <script src="./assets/js/ingredients.js"></script>
            </main>
        </div>
    </div>
</body>
</html>