<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

$user_name = isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Usuario';

// Incluir el archivo de conexión
include 'conexion.php';

$errors = [];
$success = '';

// Tamaños máximos permitidos
$maxSize_icono = 50 * 1024; // 50 KB
$maxSize_fondo_imagen = 150 * 1024; // 150 KB

// Agregar nuevo servicio
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $target_dir = "../img/"; // Carpeta ../img/ en la raíz del proyecto
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $icono_file = $target_dir . basename($_FILES["icono"]["name"]);
        $fondo_imagen_file = $target_dir . basename($_FILES["fondo_imagen"]["name"]);

        $uploadOk = 1;
        $allowed_image_types = ['jpg', 'jpeg', 'png', 'svg', 'gif'];
        $imageFileType_icono = strtolower(pathinfo($_FILES["icono"]["name"], PATHINFO_EXTENSION));
        $imageFileType_fondo_imagen = strtolower(pathinfo($_FILES["fondo_imagen"]["name"], PATHINFO_EXTENSION));

        // Validación del ícono
        if (!in_array($imageFileType_icono, $allowed_image_types)) {
            $errors[] = "Solo se permiten imágenes JPG, JPEG, PNG, SVG y GIF para el ícono.";
            $uploadOk = 0;
        } elseif (!getimagesize($_FILES["icono"]["tmp_name"])) {
            $errors[] = "El archivo del ícono no es una imagen.";
            $uploadOk = 0;
        } elseif ($_FILES["icono"]["size"] > $maxSize_icono) {
            $errors[] = "El ícono es demasiado grande. El tamaño máximo permitido es 50 KB.";
            $uploadOk = 0;
        }

        // Validación del fondo de imagen
        if (!in_array($imageFileType_fondo_imagen, $allowed_image_types)) {
            $errors[] = "Solo se permiten imágenes JPG, JPEG, PNG, SVG y GIF para el fondo de imagen.";
            $uploadOk = 0;
        } elseif (!getimagesize($_FILES["fondo_imagen"]["tmp_name"])) {
            $errors[] = "El archivo de fondo de imagen no es una imagen.";
            $uploadOk = 0;
        } elseif ($_FILES["fondo_imagen"]["size"] > $maxSize_fondo_imagen) {
            $errors[] = "La imagen de fondo es demasiado grande. El tamaño máximo permitido es 150 KB.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["icono"]["tmp_name"], $icono_file) && move_uploaded_file($_FILES["fondo_imagen"]["tmp_name"], $fondo_imagen_file)) {
                $stmt = $conn->prepare("INSERT INTO servicios (icono, subtitulo, fondo_imagen, texto, administrador_id) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->bind_param("ssssi", $icono_file, $_POST['subtitulo'], $fondo_imagen_file, $_POST['texto'], $_SESSION['admin_id']) && $stmt->execute()) {
                    $success = "Servicio añadido exitosamente.";

                    // Registro de la actividad
                    $tabla_modificada = "servicios";
                    $accion = "AGREGAR";
                    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    $stmt->execute();
                } else {
                    $errors[] = "Error al añadir el servicio en la base de datos.";
                }
            } else {
                $errors[] = "Hubo un error al subir los archivos.";
            }
        }
    }

    // Editar servicio
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $subtitulo = $_POST['subtitulo'];
        $texto = $_POST['texto'];

        $target_dir = "../img/";

        // Manejo del archivo ícono
        $icono_path = $_POST['current_icono'];
        if (isset($_FILES['icono']) && $_FILES['icono']['error'] == UPLOAD_ERR_OK) {
            $icono_file = $target_dir . basename($_FILES["icono"]["name"]);
            if ($_FILES["icono"]["size"] <= $maxSize_icono) {
                if (move_uploaded_file($_FILES["icono"]["tmp_name"], $icono_file)) {
                    // Eliminar el archivo antiguo
                    if (file_exists($icono_path) && $icono_path !== $icono_file) {
                        unlink($icono_path);
                    }
                    $icono_path = $icono_file;
                }
            } else {
                $errors[] = "El ícono es demasiado grande. El tamaño máximo permitido es 50 KB.";
            }
        }

        // Manejo del archivo fondo_imagen
        $fondo_imagen_path = $_POST['current_fondo_imagen'];
        if (isset($_FILES['fondo_imagen']) && $_FILES['fondo_imagen']['error'] == UPLOAD_ERR_OK) {
            $fondo_imagen_file = $target_dir . basename($_FILES["fondo_imagen"]["name"]);
            if ($_FILES["fondo_imagen"]["size"] <= $maxSize_fondo_imagen) {
                if (move_uploaded_file($_FILES["fondo_imagen"]["tmp_name"], $fondo_imagen_file)) {
                    // Eliminar el archivo antiguo
                    if (file_exists($fondo_imagen_path) && $fondo_imagen_path !== $fondo_imagen_file) {
                        unlink($fondo_imagen_path);
                    }
                    $fondo_imagen_path = $fondo_imagen_file;
                }
            } else {
                $errors[] = "La imagen de fondo es demasiado grande. El tamaño máximo permitido es 150 KB.";
            }
        }

        $stmt = $conn->prepare("UPDATE servicios SET subtitulo = ?, icono = ?, fondo_imagen = ?, texto = ? WHERE id = ?");
        if ($stmt->bind_param("ssssi", $subtitulo, $icono_path, $fondo_imagen_path, $texto, $id) && $stmt->execute()) {
            $success = "Servicio editado exitosamente.";

            // Registro de la actividad
            $tabla_modificada = "servicios";
            $accion = "EDITAR";
            $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
            $stmt->execute();
        } else {
            $errors[] = "Error al editar el servicio en la base de datos.";
        }
    }

    // Eliminar servicio
    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = intval($_POST['id']);

        // Obtener el archivo actual para eliminarlo
        $stmt = $conn->prepare("SELECT icono, fondo_imagen FROM servicios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();

        if ($service) {
            // Eliminar los archivos del servidor
            if (file_exists($service['icono'])) {
                unlink($service['icono']);
            }
            if (file_exists($service['fondo_imagen'])) {
                unlink($service['fondo_imagen']);
            }

            // Eliminar el servicio de la base de datos
            $stmt = $conn->prepare("DELETE FROM servicios WHERE id = ?");
            if ($stmt->bind_param("i", $id) && $stmt->execute()) {
                $success = "Servicio eliminado exitosamente.";

                // Registro de la actividad
                $tabla_modificada = "servicios";
                $accion = "ELIMINAR";
                $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                $stmt->execute();
            } else {
                $errors[] = "Error al eliminar el servicio de la base de datos.";
            }
        } else {
            $errors[] = "El servicio no existe.";
        }
    }
}

// Obtener la lista de servicios para visualización
$stmt = $conn->query("SELECT * FROM servicios");
$servicios = $stmt->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Servicios</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <link rel="stylesheet" href="../css/maximo.css">
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
            <main class="col-md-9 ms-sm-auto col-lg-9 px-4">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                        Agregar Servicio
                    </button>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
                <h2 class="my-4">Servicios</h2>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ícono</th>
                                <th>Subtítulo</th>
                                <th>Imagen de Fondo</th>
                                <th>Texto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($servicios as $servicio): ?>
                                <tr>
                                    <td><img src="<?php echo htmlspecialchars($servicio['icono']); ?>" alt="Ícono" style="width: 50px;"></td>
                                    <td><?php echo htmlspecialchars($servicio['subtitulo']); ?></td>
                                    <td><img src="<?php echo htmlspecialchars($servicio['fondo_imagen']); ?>" alt="Imagen de Fondo" style="width: 50px;"></td>
                                    <td><?php echo htmlspecialchars($servicio['texto']); ?></td>
                                    <td>
                                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editServiceModal<?php echo htmlspecialchars($servicio['id']); ?>">
                                            Editar
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteServiceModal<?php echo htmlspecialchars($servicio['id']); ?>">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Editar Servicio -->
                                <div class="modal fade" id="editServiceModal<?php echo htmlspecialchars($servicio['id']); ?>" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editServiceModalLabel">Editar Servicio</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($servicio['id']); ?>">
                                                    <input type="hidden" name="current_icono" value="<?php echo htmlspecialchars($servicio['icono']); ?>">
                                                    <input type="hidden" name="current_fondo_imagen" value="<?php echo htmlspecialchars($servicio['fondo_imagen']); ?>">

                                                    <div class="mb-3">
                                                        <label for="subtitulo" class="form-label">Subtítulo</label>
                                                        <input type="text" class="form-control" id="subtitulo" name="subtitulo" value="<?php echo htmlspecialchars($servicio['subtitulo']); ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="icono" class="form-label">Ícono (dejar en blanco si no se desea cambiar)</label>
                                                        <input class="form-control" type="file" id="icono" name="icono">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="fondo_imagen" class="form-label">Imagen de Fondo (dejar en blanco si no se desea cambiar)</label>
                                                        <input class="form-control" type="file" id="fondo_imagen" name="fondo_imagen">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="texto" class="form-label">Texto</label>
                                                        <textarea class="form-control" id="texto" name="texto" rows="3" required><?php echo htmlspecialchars($servicio['texto']); ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Eliminar Servicio -->
                                <div class="modal fade" id="deleteServiceModal<?php echo htmlspecialchars($servicio['id']); ?>" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteServiceModalLabel">Eliminar Servicio</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($servicio['id']); ?>">
                                                    ¿Estás seguro de que deseas eliminar este servicio?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
            </main>
        </div>
    </div>

    <!-- Modal Agregar Servicio -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-labelledby="addServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addServiceModalLabel">Agregar Servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label for="subtitulo" class="form-label">Subtítulo</label>
                            <input type="text" class="form-control" id="subtitulo" name="subtitulo" required>
                        </div>

                        <div class="mb-3">
                            <label for="icono" class="form-label">Ícono</label>
                            <input class="form-control" type="file" id="icono" name="icono" required>
                        </div>

                        <div class="mb-3">
                            <label for="fondo_imagen" class="form-label">Imagen de Fondo</label>
                            <input class="form-control" type="file" id="fondo_imagen" name="fondo_imagen" required>
                        </div>

                        <div class="mb-3">
                            <label for="texto" class="form-label">Texto</label>
                            <textarea class="form-control" id="texto" name="texto" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Agregar Servicio</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>
</body>

</html>