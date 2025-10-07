<?php
include("../Backend/includes/db.php"); // conexión a la BD
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>La Estación Bistro - Productos</title>
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
                    <li class="nav-item"><a href="products.php" class="nav-link text-white active">🍔 Productos</a></li>
                    <li class="nav-item"><a href="orders.php" class="nav-link text-white">🧾 Órdenes</a></li>
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
                <h1 class="h2 mb-4">Productos</h1>

                <div class="d-flex justify-content-between mb-3">
                    <h4>Lista de productos</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">➕ Nuevo producto</button>
                </div>

                <!-- Tabla -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Categoría</th>
                            <th>Receta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="products-table">
                        <tr>
                            <td colspan="6">Cargando...</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Modal Producto -->
                <div class="modal fade" id="productModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="productForm">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nuevo producto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="productId">

                                    <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="productNombre" class="form-label">Nombre</label>
                                        <input type="text" id="productNombre" class="form-control" required>
                                    </div>

                                    <!-- Precio -->
                                    <div class="mb-3">
                                        <label for="productPrecio" class="form-label">Precio</label>
                                        <input type="number" step="0.01" id="productPrecio" class="form-control" required>
                                    </div>

                                    <!-- Categoría -->
                                    <div class="mb-3">
                                        <label for="productCategoria" class="form-label">Categoría</label>
                                        <select id="productCategoria" class="form-select" required>
                                            <option value="">Selecciona una categoría</option>
                                            <?php
                                                $res = $conn->query("SELECT id, nombre FROM categorias_productos ORDER BY nombre ASC");
                                                while ($cat = $res->fetch_assoc()) {
                                                    echo "<option value='{$cat['id']}'>{$cat['nombre']}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Receta asociada -->
                                    <div class="mb-3">
                                        <label for="productReceta" class="form-label">Receta asociada</label>
                                        <select id="productReceta" class="form-select">
                                            <option value="">Sin receta</option>
                                            <?php
                                                $res = $conn->query("SELECT id, nombre FROM recipes WHERE tipo='producto' ORDER BY nombre ASC");
                                                while ($rec = $res->fetch_assoc()) {
                                                    echo "<option value='{$rec['id']}'>{$rec['nombre']}</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <!-- Detalle de ingredientes de la receta -->
                                    <div id="recetaDetalle" class="border rounded p-2 bg-light small">
                                        <em>Selecciona una receta para ver sus ingredientes...</em>
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
                <script src="./assets/js/products.js"></script>
                <script>
                // Mostrar ingredientes de la receta seleccionada
                document.getElementById("productReceta").addEventListener("change", async (e) => {
                    const recetaId = e.target.value;
                    const detalle = document.getElementById("recetaDetalle");
                    if (!recetaId) {
                        detalle.innerHTML = "<em>Sin receta asociada</em>";
                        return;
                    }
                    const res = await fetch(`../Backend/api/recipes_get.php?id=${recetaId}`);
                    const receta = await res.json();
                    if (receta.ingredientes && receta.ingredientes.length) {
                        let html = "<h6>Ingredientes:</h6><ul>";
                        receta.ingredientes.forEach(i => {
                            html += `<li>${i.nombre}: ${i.cantidad} ${i.unidad}</li>`;
                        });
                        html += "</ul>";
                        detalle.innerHTML = html;
                    } else {
                        detalle.innerHTML = "<em>Esta receta no tiene ingredientes</em>";
                    }
                });
                </script>
            </main>
        </div>
    </div>
</body>
</html>