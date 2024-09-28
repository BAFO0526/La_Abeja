<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Usuario';
$admin_id = $_SESSION['admin_id']; // Obtener el ID del administrador actual

include '../admin/conexion.php';

// Función para registrar la actividad
function registrarActividad($conn, $admin_id, $accion)
{
    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES ($admin_id, 'galeria', '$accion', NOW())";
    $conn->query($query);
}

// Variables para manejo de errores y éxito
$errors = [];
$success = '';

// Extensiones permitidas
$allowed_extensions = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
$max_file_size = 150 * 1024; // 150 KB en bytes


// Función para obtener la extensión del archivo
function getFileExtension($filename)
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Manejo de creación de imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen']['name'];
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_size = $_FILES['imagen']['size'];
        $file_extension = getFileExtension($imagen);

        if (in_array($file_extension, $allowed_extensions) && $file_size <= $max_file_size) {
            $ruta_imagen = '../img/' . basename($imagen);
            if (move_uploaded_file($file_tmp, $ruta_imagen)) {
                $query = "INSERT INTO galeria (imagen, administrador_id) VALUES ('$ruta_imagen', $admin_id)";
                if ($conn->query($query)) {
                    $success = 'Imagen agregada exitosamente.';
                    registrarActividad($conn, $admin_id, 'AGREGAR'); // Registrar la actividad
                } else {
                    $errors[] = 'Error al agregar la imagen: ' . $conn->error;
                }
            } else {
                $errors[] = 'Error al subir la imagen.';
            }
        } else {
            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = 'Formato de imagen no permitido. Los formatos aceptados son: png, jpg, jpeg, gif, svg.';
            }
            if ($file_size > $max_file_size) {
                $errors[] = 'El archivo es demasiado grande. El tamaño máximo permitido es 150KB.';
            }
        }
    } else {
        $errors[] = 'Debe seleccionar una imagen.';
    }
}

// Manejo de actualización de imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'editar') {
    $id = $_POST['id'];
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen']['name'];
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_size = $_FILES['imagen']['size'];
        $file_extension = getFileExtension($imagen);

        if (in_array($file_extension, $allowed_extensions) && $file_size <= $max_file_size) {
            $ruta_imagen = '../img/' . basename($imagen);
            if (move_uploaded_file($file_tmp, $ruta_imagen)) {
                $query = "UPDATE galeria SET imagen='$ruta_imagen' WHERE id=$id";
                if ($conn->query($query)) {
                    $success = 'Imagen actualizada exitosamente.';
                    registrarActividad($conn, $admin_id, 'EDITAR'); // Registrar la actividad
                } else {
                    $errors[] = 'Error al actualizar la imagen: ' . $conn->error;
                }
            } else {
                $errors[] = 'Error al subir la nueva imagen.';
            }
        } else {
            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = 'Formato de imagen no permitido. Los formatos aceptados son: png, jpg, jpeg, gif, svg.';
            }
            if ($file_size > $max_file_size) {
                $errors[] = 'El archivo es demasiado grande. El tamaño máximo permitido es 5MB.';
            }
        }
    } else {
        $errors[] = 'Debe seleccionar una imagen.';
    }
}

// Manejo de eliminación de imagen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['id'];

    $query = "DELETE FROM galeria WHERE id=$id";
    if ($conn->query($query)) {
        $success = 'Imagen eliminada exitosamente.';
        registrarActividad($conn, $admin_id, 'ELIMINAR'); // Registrar la actividad
    } else {
        $errors[] = 'Error al eliminar la imagen: ' . $conn->error;
    }
}

// Obtener todas las imágenes
$query = "SELECT * FROM galeria";
$resultado = $conn->query($query);
$imagenes = $resultado->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Galería</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="../css/maximo.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <script src="js/menu.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
    </style>
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
            <main class="col-md-9 ms-sm-auto col-lg-9 px-4">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>

                <h1 class="h2">Gestión de Galería</h1>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p><?php echo htmlspecialchars($success); ?></p>
                    </div>
                <?php endif; ?>

                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between">
                        <h3>Imágenes en Galería</h3>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">Agregar Nueva Imagen</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($imagenes as $imagen): ?>
                                <div class="col-md-4">
                                    <div class="card mt-4">
                                        <img src="<?php echo htmlspecialchars($imagen['imagen']); ?>" class="card-img-top" alt="Imagen">
                                        <div class="card-body">
                                            <button class="btn btn-warning mt-3" data-bs-toggle="modal" data-bs-target="#editarModal-<?php echo $imagen['id']; ?>">Editar Imagen</button>
                                            <button class="btn btn-danger mt-3" data-bs-toggle="modal" data-bs-target="#eliminarModal-<?php echo $imagen['id']; ?>">Eliminar Imagen</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Editar -->
                                <div class="modal fade" id="editarModal-<?php echo $imagen['id']; ?>" tabindex="-1" aria-labelledby="editarModalLabel-<?php echo $imagen['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editarModalLabel-<?php echo $imagen['id']; ?>">Editar Imagen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" enctype="multipart/form-data">
                                                    <div class="form-group">
                                                        <label for="imagen">Seleccionar nueva imagen</label>
                                                        <input type="file" class="form-control" id="imagen" name="imagen">
                                                    </div>
                                                    <input type="hidden" name="accion" value="editar">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($imagen['id']); ?>">
                                                    <button type="submit" class="btn btn-warning mt-3">Actualizar Imagen</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Eliminar -->
                                <div class="modal fade" id="eliminarModal-<?php echo $imagen['id']; ?>" tabindex="-1" aria-labelledby="eliminarModalLabel-<?php echo $imagen['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="eliminarModalLabel-<?php echo $imagen['id']; ?>">Eliminar Imagen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Estás seguro de que quieres eliminar esta imagen?</p>
                                                <form method="POST">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($imagen['id']); ?>">
                                                    <button type="submit" class="btn btn-danger">Eliminar Imagen</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Modal Crear -->
                <div class="modal fade" id="crearModal" tabindex="-1" aria-labelledby="crearModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="crearModalLabel">Agregar Nueva Imagen</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="imagen">Seleccionar imagen</label>
                                        <input type="file" class="form-control" id="imagen" name="imagen">
                                    </div>
                                    <input type="hidden" name="accion" value="crear">
                                    <button type="submit" class="btn btn-primary mt-3">Agregar Imagen</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>