<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>La Estación Bistro - Recetas</title>
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
                <li class="nav-item"><a href="recipes.php" class="nav-link text-white active">📖 Recetas</a></li>
                <li class="nav-item"><a href="products.php" class="nav-link text-white">🍔 Productos</a></li>
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
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h2 mb-0">📖 Recetas</h1>
                <button class="btn btn-primary" id="btnNuevaReceta">Nueva receta</button>
            </div>

            <!-- Filtro -->
            <div class="input-group mb-3">
                <span class="input-group-text">Buscar</span>
                <input type="text" id="buscarReceta" class="form-control" placeholder="Nombre de receta...">
            </div>

            <!-- Tabla -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Tipo</th>
                                <th>Ingredientes</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaRecetas">
                            <tr><td colspan="5">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal receta -->
            <div class="modal fade" id="modalReceta" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="formReceta">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalRecetaTitulo">Nueva receta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="recetaId">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" id="recetaNombre" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Descripción (opcional)</label>
                                        <input type="text" id="recetaDescripcion" class="form-control">
                                    </div>
                                </div>

                                <!-- Nuevo campo tipo -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo de receta</label>
                                        <select id="recetaTipo" class="form-select" required>
                                            <option value="ingrediente" selected>Ingrediente</option>
                                            <option value="producto">Producto</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0">Ingredientes</h6>
                                    <button type="button" class="btn btn-sm btn-secondary" id="btnAgregarIngrediente">Agregar ingrediente</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%">Ingrediente</th>
                                                <th style="width: 20%">Unidad</th>
                                                <th style="width: 20%">Cantidad</th>
                                                <th style="width: 20%" class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaRecetaIngredientes">
                                            <!-- filas dinámicas -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="small text-muted">
                                    Tip: Usa cantidades en la unidad base del ingrediente (ej. gramos, ml, piezas).
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar receta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/recipes.js"></script>
</body>
</html>