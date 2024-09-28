<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

$user_name = isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Usuario';

// Incluir el archivo de conexión
include 'conexion.php';

// Variables para manejo de errores y éxito
$errors = [];
$success = '';

// Actualizar Contacto
if (isset($_POST['update_contact'])) {
    $id = $_POST['id'];
    $ubicacion = $_POST['ubicacion'];
    $telefono1 = $_POST['telefono1'];
    $telefono2 = $_POST['telefono2'];
    $mapa_iframe = $_POST['mapa_iframe'];

    // Validar entradas
    if (empty($ubicacion)) {
        $errors[] = "La ubicación es obligatoria.";
    }
    if (!preg_match('/^[0-9]{9}$/', $telefono1)) {
        $errors[] = "El teléfono 1 debe ser un número de 9 dígitos.";
    }
    if (!preg_match('/^[0-9]{9}$/', $telefono2) && !empty($telefono2)) {
        $errors[] = "El teléfono 2 debe ser un número de 9 dígitos.";
    }

    if (empty($errors)) {
        // Actualizar datos en la tabla contacto
        $query = "UPDATE contacto SET ubicacion = ?, telefono1 = ?, telefono2 = ?, mapa_iframe = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $ubicacion, $telefono1, $telefono2, $mapa_iframe, $id);

        if ($stmt->execute()) {
            $success = "Contacto actualizado correctamente.";

            // Registro de la actividad
            $tabla_modificada = "contacto";
            $accion = "EDITAR";
            $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            
            if ($stmt === false) {
                // Mostrar errores de preparación de la consulta
                $errors[] = "Error al preparar la consulta de registro de actividad: " . $conn->error;
            } else {
                $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                if ($stmt->execute()) {
                    $success .= " Actividad registrada correctamente.";
                } else {
                    $errors[] = "Error al registrar la actividad: " . $stmt->error;
                }
            }
        } else {
            $errors[] = "Error al actualizar contacto: " . $stmt->error;
        }
    }
}

// Obtener todos los contactos
$query = "SELECT * FROM contacto";
$result = $conn->query($query);
$contactos = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Contactos</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="../css/maximo.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <script src="js/menu.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-tachometer-alt"></i> Inicio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logo.php">
                    <i class="fas fa-pen-fancy"></i> Logo
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="portada.php">
                    <i class="fas fa-image"></i> Portada
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="servicio.php">
                    <i class="fas fa-concierge-bell"></i> Servicios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="producto.php">
                    <i class="fas fa-box"></i> Productos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="galeria.php">
                    <i class="fas fa-images"></i> Galería
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="contacto.php">
                    <i class="fas fa-address-book"></i> Contacto
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="frecuencia.php">
                    <i class="fas fa-calendar-alt"></i> Frecuencia de Cambios
                </a>
            </li>
        </ul>
    </div>
</nav>
            <main class="col-md-9 ms-sm-auto col-lg-9 px-md-4">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>

                <!-- Mensajes de éxito o error -->
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <!-- Tabla de contactos -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Ubicación</th>
                            <th>Teléfono 1</th>
                            <th>Teléfono 2</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contactos as $contacto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contacto['ubicacion']); ?></td>
                                <td><?php echo htmlspecialchars($contacto['telefono1']); ?></td>
                                <td><?php echo htmlspecialchars($contacto['telefono2']); ?></td>
                                <td>
                                    <!-- Botón para abrir modal de edición -->
                                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editContactModal<?php echo $contacto['id']; ?>">Editar</button>
                                </td>
                            </tr>

                            <!-- Modal para editar contacto -->
                            <div class="modal fade" id="editContactModal<?php echo $contacto['id']; ?>" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editContactModalLabel">Editar Contacto</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $contacto['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="ubicacion" class="form-label">Ubicación</label>
                                                    <input type="text" class="form-control" name="ubicacion" value="<?php echo htmlspecialchars($contacto['ubicacion']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="telefono1" class="form-label">Teléfono 1</label>
                                                    <input type="text" class="form-control" name="telefono1" value="<?php echo htmlspecialchars($contacto['telefono1']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="telefono2" class="form-label">Teléfono 2</label>
                                                    <input type="text" class="form-control" name="telefono2" value="<?php echo htmlspecialchars($contacto['telefono2']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="mapa_iframe" class="form-label">Mapa (Iframe)</label>
                                                    <textarea class="form-control" name="mapa_iframe" required><?php echo htmlspecialchars($contacto['mapa_iframe']); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" name="update_contact" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal para eliminar contacto -->
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mapa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contactos as $contacto): ?>
                            <tr>
                                <td>
                                    <iframe src="<?php echo htmlspecialchars($contacto['mapa_iframe']); ?>" width="100%" height="300"></iframe>
                                </td>
                            </tr>

                            <!-- Modal para editar contacto -->
                            <div class="modal fade" id="editContactModal<?php echo $contacto['id']; ?>" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editContactModalLabel">Editar Contacto</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?php echo $contacto['id']; ?>">
                                                <div class="mb-3">
                                                    <label for="ubicacion" class="form-label">Ubicación</label>
                                                    <input type="text" class="form-control" name="ubicacion" value="<?php echo htmlspecialchars($contacto['ubicacion']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="telefono1" class="form-label">Teléfono 1</label>
                                                    <input type="text" class="form-control" name="telefono1" value="<?php echo htmlspecialchars($contacto['telefono1']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="telefono2" class="form-label">Teléfono 2</label>
                                                    <input type="text" class="form-control" name="telefono2" value="<?php echo htmlspecialchars($contacto['telefono2']); ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="mapa_iframe" class="form-label">Mapa (Iframe)</label>
                                                    <textarea class="form-control" name="mapa_iframe" required><?php echo htmlspecialchars($contacto['mapa_iframe']); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                <button type="submit" name="update_contact" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal para eliminar contacto -->
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>