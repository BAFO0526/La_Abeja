<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Usuario';
include 'conexion.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $target_dir = "../img/"; // Definición de la variable target_dir

        if ($_POST['action'] == 'add') {
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES["imagen_fondo"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["imagen_fondo"]["tmp_name"]);
            if ($check === false) {
                $errors[] = "El archivo no es una imagen.";
                $uploadOk = 0;
            }

            if ($_FILES["imagen_fondo"]["size"] > 153600) { // Cambié el tamaño del archivo a 10MB
                $errors[] = "La imagen es demasiado Grande.";
                $uploadOk = 0;
            }

            $valid_extensions = ['png', 'jpg', 'jpeg', 'gif', 'svg'];
            if (!in_array($imageFileType, $valid_extensions)) {
                $errors[] = "Solo se permiten archivos PNG, JPG, JPEG, GIF Y SVG.";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                $errors[] = "El archivo no se ha cargado.";
            } else {
                if (move_uploaded_file($_FILES["imagen_fondo"]["tmp_name"], $target_file)) {
                    $texto_encabezado = $_POST['texto_encabezado'];
                    $texto_descripcion = $_POST['texto_descripcion'];
                    $texto_boton = $_POST['texto_boton'];
                    $enlace_boton = $_POST['enlace_boton'];

                    $query = "INSERT INTO carrusel (imagen_fondo, texto_encabezado, texto_descripcion, texto_boton, enlace_boton, administrador_id) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('sssssi', $target_file, $texto_encabezado, $texto_descripcion, $texto_boton, $enlace_boton, $_SESSION['admin_id']);
                    if ($stmt->execute()) {
                        // Registro de la actividad
                        $tabla_modificada = "carrusel";
                        $accion = "AGREGAR";
                        $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                        $stmt->execute();

                        $success = "La portada ha sido agregada.";
                    } else {
                        $errors[] = "Error al agregar la portada: " . $stmt->error;
                    }
                } else {
                    $errors[] = "Hubo un error al cargar el archivo.";
                }
            }
        }

        if ($_POST['action'] == 'delete') {
            $id = intval($_POST['id']);
            // Verificar si el registro existe
            $query = "SELECT administrador_id FROM carrusel WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($admin_id);
                $stmt->fetch();

                $query = "DELETE FROM carrusel WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    // Registro de la actividad
                    $tabla_modificada = "carrusel";
                    $accion = "ELIMINAR";
                    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    $stmt->execute();

                    $success = "La portada ha sido eliminada.";
                } else {
                    $errors[] = "Error al eliminar la portada: " . $stmt->error;
                }
            } else {
                $errors[] = "La portada no existe.";
            }
        }

        if ($_POST['action'] == 'edit') {
            $id = intval($_POST['id']);
            $texto_encabezado = $_POST['texto_encabezado'];
            $texto_descripcion = $_POST['texto_descripcion'];
            $texto_boton = $_POST['texto_boton'];
            $enlace_boton = $_POST['enlace_boton'];

            // Verificar si el registro existe
            $query = "SELECT administrador_id FROM carrusel WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($admin_id);
                $stmt->fetch();

                $query = "UPDATE carrusel SET texto_encabezado = ?, texto_descripcion = ?, texto_boton = ?, enlace_boton = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ssssi', $texto_encabezado, $texto_descripcion, $texto_boton, $enlace_boton, $id);
                if ($stmt->execute()) {
                    if (!empty($_FILES["imagen_fondo"]["name"])) {
                        $target_file = $target_dir . basename($_FILES["imagen_fondo"]["name"]);
                        if (move_uploaded_file($_FILES["imagen_fondo"]["tmp_name"], $target_file)) {
                            $query = "UPDATE carrusel SET imagen_fondo = ? WHERE id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param('si', $target_file, $id);
                            $stmt->execute();
                        } else {
                            $errors[] = "Hubo un error al cargar el archivo.";
                        }
                    }
                    // Registro de la actividad
                    $tabla_modificada = "carrusel";
                    $accion = "EDITAR";
                    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    $stmt->execute();

                    $success = "La portada ha sido actualizada.";
                } else {
                    $errors[] = "Error al actualizar la portada: " . $stmt->error;
                }
            } else {
                $errors[] = "La portada no existe.";
            }
        }
    }
}

$query = "SELECT * FROM carrusel ORDER BY fecha_actualizacion DESC";
$result = $conn->query($query);
$portadas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $portadas[] = $row;
    }
}
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Portadas</title>
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

        <main class="col-md-8 ms-sm-auto col-lg-9 px-0">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPortadaModal">
                    Agregar Nueva Portada
                </button>
                <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2>Portadas:</h2>
            <!-- Modal de agregar portada -->
            <div class="modal fade" id="addPortadaModal" tabindex="-1" aria-labelledby="addPortadaModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addPortadaModalLabel">Agregar Nueva Portada</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="portada.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="add">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="imagen_fondo" class="form-label">Imagen de Fondo</label>
                                        <input type="file" class="form-control" id="imagen_fondo" name="imagen_fondo" accept=".png, .jpg, .jpeg, .gif" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="texto_encabezado" class="form-label">Texto de Encabezado</label>
                                        <input type="text" class="form-control" id="texto_encabezado" name="texto_encabezado" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="texto_descripcion" class="form-label">Texto de Descripción</label>
                                        <textarea class="form-control" id="texto_descripcion" name="texto_descripcion" rows="3" required></textarea>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="texto_boton" class="form-label">Texto del Botón</label>
                                        <input type="text" class="form-control" id="texto_boton" name="texto_boton" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="enlace_boton" class="form-label">Enlace del Botón</label>
                                    <input type="text" class="form-control" id="enlace_boton" name="enlace_boton" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de portadas -->
            <div class="table-responsive">
                <table class="table table-striped table-hover mt-3">
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Encabezado</th>
                            <th>Descripción</th>
                            <th>Texto del Botón</th>
                            <th>Enlace del Botón</th>
                            <th>Fecha de Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portadas as $portada): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($portada['imagen_fondo']); ?>" alt="Imagen de Fondo" style="max-width: 100px;"></td>
                                <td><?php echo htmlspecialchars($portada['texto_encabezado']); ?></td>
                                <td><?php echo htmlspecialchars($portada['texto_descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($portada['texto_boton']); ?></td>
                                <td><?php echo htmlspecialchars($portada['enlace_boton']); ?></td>
                                <td><?php echo htmlspecialchars($portada['fecha_actualizacion']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPortadaModal<?php echo $portada['id']; ?>">
                                        Editar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePortadaModal<?php echo $portada['id']; ?>">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal de editar portada -->
                            <div class="modal fade" id="editPortadaModal<?php echo $portada['id']; ?>" tabindex="-1" aria-labelledby="editPortadaModalLabel<?php echo $portada['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editPortadaModalLabel<?php echo $portada['id']; ?>">Editar Portada</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="portada.php" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="action" value="edit">
                                                <input type="hidden" name="id" value="<?php echo $portada['id']; ?>">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="imagen_fondo" class="form-label">Imagen de Fondo</label>
                                                        <input type="file" class="form-control" id="imagen_fondo" name="imagen_fondo" accept=".png, .jpg, .jpeg, .gif">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="texto_encabezado" class="form-label">Texto de Encabezado</label>
                                                        <input type="text" class="form-control" id="texto_encabezado" name="texto_encabezado" value="<?php echo htmlspecialchars($portada['texto_encabezado']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="texto_descripcion" class="form-label">Texto de Descripción</label>
                                                        <textarea class="form-control" id="texto_descripcion" name="texto_descripcion" rows="3" required><?php echo htmlspecialchars($portada['texto_descripcion']); ?></textarea>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="texto_boton" class="form-label">Texto del Botón</label>
                                                        <input type="text" class="form-control" id="texto_boton" name="texto_boton" value="<?php echo htmlspecialchars($portada['texto_boton']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="enlace_boton" class="form-label">Enlace del Botón</label>
                                                    <input type="text" class="form-control" id="enlace_boton" name="enlace_boton" value="<?php echo htmlspecialchars($portada['enlace_boton']); ?>" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal de eliminar portada -->
                            <div class="modal fade" id="deletePortadaModal<?php echo $portada['id']; ?>" tabindex="-1" aria-labelledby="deletePortadaModalLabel<?php echo $portada['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deletePortadaModalLabel<?php echo $portada['id']; ?>">Eliminar Portada</h5>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Estás seguro de que deseas eliminar esta portada?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <form action="portada.php" method="POST">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $portada['id']; ?>">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>
</body>

</html>