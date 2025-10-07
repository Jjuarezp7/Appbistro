<?php
include("../Backend/includes/db.php"); // conexión a la BD
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Unidades de Medida - La Estación Bistro</title>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h2 mb-0">Unidades de Medida</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unidadModal">➕ Nueva unidad</button>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Abreviatura</th>
                                <th style="width: 160px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="unidades-table">
                            <tr>
                                <td colspan="4">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Crear/Editar -->
                <div class="modal fade" id="unidadModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="unidadForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="unidadModalTitle">Nueva Unidad</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="unidadId">

                                    <div class="mb-3">
                                        <label for="uniNombre" class="form-label">Nombre</label>
                                        <input type="text" id="uniNombre" class="form-control" placeholder="Ej. Gramos, Litros" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="uniAbrev" class="form-label">Abreviatura</label>
                                        <input type="text" id="uniAbrev" class="form-control" placeholder="Ej. g, L, ml, u" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Confirmar Eliminación -->
                <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Eliminar unidad</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <p id="deleteText">¿Seguro que deseas eliminar esta unidad?</p>
                                <input type="hidden" id="deleteUnidadId">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Eliminar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scripts -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                <script src="./assets/js/unidades.js"></script>
            </main>
        </div>
    </div>
</body>
</html>