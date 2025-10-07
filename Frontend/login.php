<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - La Estación Bistro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/img/logo.png">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Iniciar sesión</h3>
                        <form id="formLogin">
                            <div class="mb-3">
                                <label for="email" class="form-label">Usuario o Email</label>
                                <input type="text" id="email" name="email" class="form-control"
                                       required autocomplete="username">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" id="password" name="password" class="form-control"
                                       required autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
                        </form>
                        <div id="loginError" class="text-danger mt-3 text-center" style="display:none;">
                            Usuario o contraseña incorrectos
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tu script de login -->
    <script src="./assets/js/login.js"></script>
</body>
</html>