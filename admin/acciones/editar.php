<?php
session_start();

// Incluir la conexión a la base de datos
include '../conexion.php';

// Inicializar variables
$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Usuario';
$success = '';
$errors = [];
$show_modal = false; // Variable para controlar la visualización del modal

// Verificar si se ha enviado el formulario para editar
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $pregunta = trim($_POST['pregunta']);
    $respuesta = trim($_POST['respuesta']);

    // Validar campos
    if (empty($nombre) || empty($apellido) || empty($telefono) || empty($correo) || empty($pregunta)) {
        $errors[] = 'Todos los campos son obligatorios.';
    }

    // Validar el número de teléfono (debe tener exactamente 9 dígitos)
    if (!preg_match('/^\d{9}$/', $telefono)) {
        $errors[] = 'El número de teléfono debe tener exactamente 9 dígitos.';
    }

    if (count($errors) == 0) {
        // Hash de la respuesta de seguridad
        $respuesta_hash = password_hash($respuesta, PASSWORD_DEFAULT);

        // Actualizar los datos del administrador
        $sql_update_admin = "UPDATE administradores SET nombre = ?, apellido = ?, telefono = ?, correo = ?, pregunta = ?, respuesta_hash = ? WHERE id = ?";
        $stmt_admin = $conn->prepare($sql_update_admin);
        $stmt_admin->bind_param("ssssssi", $nombre, $apellido, $telefono, $correo, $pregunta, $respuesta_hash, $id);
        if ($stmt_admin->execute()) {
            $success = 'Administrador actualizado correctamente.';
            $show_modal = true;

            // Registrar la actividad
            $admin_id = $_SESSION['admin_id'];
            $tabla_modificada = "administradores";
            $accion = "EDITAR";
            $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
            $stmt_activity = $conn->prepare($query);
            if ($stmt_activity) {
                $stmt_activity->bind_param('iss', $admin_id, $tabla_modificada, $accion);
                if (!$stmt_activity->execute()) {
                    $errors[] = "Error al registrar la actividad: " . $stmt_activity->error;
                }
                $stmt_activity->close();
            } else {
                $errors[] = "Error al preparar la consulta de registro de actividad: " . $conn->error;
            }
        } else {
            $errors[] = 'Error al actualizar los datos del administrador.';
        }
        $stmt_admin->close();
    }
}

// Obtener los datos del administrador si se envía el id por GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener los datos del administrador
    $sql = "SELECT nombre, apellido, telefono, correo, pregunta
            FROM administradores
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $telefono = $row['telefono'];
        $correo = $row['correo'];
        $pregunta = $row['pregunta'];
        $respuesta = ''; // Dejar vacío, no mostrar respuesta en texto claro
    } else {
        $nombre = $apellido = $telefono = $correo = $pregunta = '';
        $respuesta = '';
    }
    $stmt->close();
} else {
    echo "ID de administrador no válido.";
}

$conn->close(); // Cerrar la conexión
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <script src="../../js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/nav_lateral1.css">
    <link rel="stylesheet" href="../css/tabla_admin.css">
    <link rel="stylesheet" href="../css/modal_admin.css">
    <link rel="stylesheet" href="../../css/maximo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../js/menu.js"></script>
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
                        <img src="../img/logo.png" alt="">
                    </h4>
                    <ul class="nav flex-column">
                        <!-- Enlaces del menú lateral -->
                        <li class="nav-item">
                            <a class="nav-link" href="../admin.php">
                                <i class="fas fa-tachometer-alt"></i> Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../logo.php">
                                <i class="fas fa-pen-fancy"></i> Logo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../portada.php">
                                <i class="fas fa-image"></i> Portada
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../servicio.php">
                                <i class="fas fa-concierge-bell"></i> Servicios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../producto.php">
                                <i class="fas fa-box"></i> Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../galeria.php">
                                <i class="fas fa-images"></i> Galería
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../contacto.php">
                                <i class="fas fa-address-book"></i> Contacto
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../frecuencia.php">
                                <i class="fas fa-calendar-alt"></i> Frecuencia de Cambios
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-9 px-8">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <a class="btn btn-danger" href="../admin.php">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <a class="btn btn-danger" href="cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
                <h2 class="my-4">Editar datos de Administradores</h2>
                <div class="row">
                    <!-- Formulario de Editar Administrador -->
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Editar:</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="apellido" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="correo" class="form-label">Correo</label>
                                            <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pregunta" class="form-label">Pregunta de Seguridad</label>
                                        <input type="text" class="form-control" id="pregunta" name="pregunta" value="<?php echo htmlspecialchars($pregunta); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="respuesta" class="form-label">Respuesta de Seguridad</label>
                                        <input type="password" class="form-control" id="respuesta" name="respuesta">
                                    </div>
                                    <button type="submit" name="editar" class="btn btn-warning">Actualizar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de éxito -->
    <?php if ($show_modal): ?>
        <div class="modal fade" id="modalSuccess" tabindex="-1" aria-labelledby="modalSuccessLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSuccessLabel">Éxito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalSuccess'));
                myModal.show();
            });
        </script>
    <?php endif; ?>

    <!-- Modal de error -->
    <?php if (count($errors) > 0): ?>
        <div class="modal fade" id="modalError" tabindex="-1" aria-labelledby="modalErrorLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalErrorLabel">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalError'));
                myModal.show();
            });
        </script>
    <?php endif; ?>
</body>
</html>
