<?php
// Ya no necesitas hacer la consulta aquí, solo mantener filtros en la URL
$filtroUsuario = $_GET['usuario'] ?? '';
$filtroAccion  = $_GET['accion'] ?? '';
$filtroFecha   = $_GET['fecha'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora - La Estación Bistro</title>
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
                <li class="nav-item"><a href="bitacora.php" class="nav-link text-white active">📝 Bitácora</a></li>
                <li class="nav-item"><a href="unidades.php" class="nav-link text-white active">⚖️ Unidades</a></li>                
                <li class="nav-item"><a href="categorias_ingredientes.php" class="nav-link text-white active">🥗 Categorías Ingredientes</a></li>
                <li class="nav-item"><a href="categorias_productos.php" class="nav-link text-white active">🍱 Categorías Productos</a></li>
            </ul>
            <a href="../Backend/api/logout.php" class="btn btn-danger mt-3">Cerrar sesión</a>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <h1 class="mb-4">📒 Bitácora del sistema</h1>

            <!-- Filtros -->
            <form method="get" class="row g-3 mb-4">
                <div class="col-md-3">
                    <input type="text" name="usuario" class="form-control" placeholder="Usuario"
                           value="<?php echo htmlspecialchars($filtroUsuario); ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="accion" class="form-control" placeholder="Acción"
                           value="<?php echo htmlspecialchars($filtroAccion); ?>">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha" class="form-control"
                           value="<?php echo htmlspecialchars($filtroFecha); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>

            <!-- Exportar -->
            <div class="mb-3 d-flex justify-content-end gap-2">
                <a id="exportAllBtn" class="btn btn-success">📤 Exportar todo</a>
                <a id="exportPageBtn" class="btn btn-outline-success">📤 Exportar página actual</a>
            </div>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Descripción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="bitacora-table">
                        <tr><td colspan="5">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <nav aria-label="Paginación bitácora" class="mt-3">
                <ul id="pagination" class="pagination justify-content-end"></ul>
            </nav>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/js/bitacora.js"></script>
</body>
</html>