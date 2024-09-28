<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

// Obtén el nombre del usuario de la sesión
$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Usuario';

// Obtener los mensajes de error o éxito
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';

// Limpiar los mensajes de la sesión para que no se muestren en futuros refrescos
unset($_SESSION['errors'], $_SESSION['success']);

// Incluir el archivo de conexión
include 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <script src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <link rel="stylesheet" href="../css/maximo.css">
    <script src="js/menu.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/ojo.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Navbar -->
            <nav class="navbar navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">
                        <img src="../img/logo.png" alt="" class="navbar-logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>

            <!-- Menú lateral -->
            <nav id="sidebar" class="col-md-2 col-lg-2 bg-light sidebar">
                <div class="position-sticky">
                    <h4 class="sidebar-title text-center py-3">
                        <img src="../img/logo.png" alt="Logo">
                    </h4>
                    <ul class="nav flex-column">
                        <!-- Enlaces del menú lateral -->
                        <li class="nav-item"><a class="nav-link" href="admin.php"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="logo.php"><i class="fas fa-pen-fancy"></i> Logo</a></li>
                        <li class="nav-item"><a class="nav-link" href="portada.php"><i class="fas fa-image"></i> Portada</a></li>
                        <li class="nav-item"><a class="nav-link" href="servicio.php"><i class="fas fa-concierge-bell"></i> Servicios</a></li>
                        <li class="nav-item"><a class="nav-link" href="producto.php"><i class="fas fa-box"></i> Productos</a></li>
                        <li class="nav-item"><a class="nav-link" href="galeria.php"><i class="fas fa-images"></i> Galería</a></li>
                        <li class="nav-item"><a class="nav-link" href="contacto.php"><i class="fas fa-address-book"></i> Contacto</a></li>
                        <li class="nav-item"><a class="nav-link" href="frecuencia.php"><i class="fas fa-calendar-alt"></i> Frecuencia de Cambios</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-9 px-4">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <a class="btn btn-success" href="admin.php"><i class="fas fa-arrow-left"></i> Volver</a>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </div>

                <h2 class="my-4">Administradores</h2>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Agregar Administrador</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="acciones/registrar.php">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre" class="form-label">Nombre:</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="apellido" class="form-label">Apellido:</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="telefono" class="form-label">Teléfono:</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="correo" class="form-label">Correo:</label>
                                            <input type="email" class="form-control" id="correo" name="correo" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="contrasena" class="form-label">Contraseña:</label>
                                            <div class="password-container">
                                                <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="**********" required>
                                                <i class="fa fa-eye" id="togglePassword"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="pregunta" class="form-label">Pregunta de Seguridad:</label>
                                            <input type="text" class="form-control" id="pregunta" name="pregunta" placeholder="¿Cómo eres?" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="respuesta" class="form-label">Respuesta de Seguridad:</label>
                                            <div class="password-container">
                                                <input type="password" class="form-control" id="respuesta" name="respuesta" placeholder="***********" required>
                                                <i class="fa fa-eye" id="togglePassword2"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="add_admin" class="btn btn-primary">Agregar Administrador</button>
                                </form>

                                <!-- Modales para errores y éxito -->
                                <?php if (!empty($errors)) : ?>
                                    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="errorModalLabel">
                                                        <i class="fas fa-exclamation-triangle" style="color: #dc3545; margin-right: 8px;"></i>
                                                        Error
                                                    </h5>
                                                </div>
                                                <div class="modal-body">
                                                    <?php foreach ($errors as $error) : ?>
                                                        <p><?php echo htmlspecialchars($error); ?></p>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($success)) : ?>
                                    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="successModalLabel">
                                                        <i class="fas fa-check-circle" style="color: #28a745; margin-right: 8px;"></i>
                                                        Éxito
                                                    </h5>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <p style="font-size: 2rem;"><b><?php echo htmlspecialchars($success); ?></b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts para mostrar el modal y redireccionar si es necesario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($success)) : ?>
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Redirigir después de 3 segundos
                setTimeout(function() {
                    window.location.href = 'admin.php'; // Redirigir después de mostrar el modal
                }, 3000);
            <?php endif; ?>

            <?php if (!empty($errors)) : ?>
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            <?php endif; ?>
        });
    </script>
</body>

</html>
