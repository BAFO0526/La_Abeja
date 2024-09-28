<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

// Obtén el nombre del usuario de la sesión
$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Usuario';

// Incluir el archivo de conexión
include 'conexion.php';

// Manejo de carga del nuevo logo
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "../img/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Crea el directorio si no existe
    }
    $target_file = $target_dir . basename($_FILES["logoImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verifica si el archivo es una imagen
    $check = getimagesize($_FILES["logoImage"]["tmp_name"]);
    if ($check === false) {
        $errors[] = "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verifica el tamaño del archivo
    if ($_FILES["logoImage"]["size"] > 153600) {
        $errors[] = "El archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Solo se permiten ciertos formatos de archivo
    if ($imageFileType != "png" && $imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "jfif" && $imageFileType != "gif") {
        $errors[] = "Solo se permiten archivos PNG, JPG, JPEG, jfif y gif.";
        $uploadOk = 0;
    }

    // Verifica si $uploadOk es 0 debido a un error
    if ($uploadOk == 0) {
        $errors[] = "El archivo no se ha cargado.";
    } else {
        // Si todo está bien, intenta cargar el archivo
        if (move_uploaded_file($_FILES["logoImage"]["tmp_name"], $target_file)) {
            // Actualiza la ruta del logo en la base de datos
            $query = "INSERT INTO logo (ruta_imagen, administrador_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('si', $target_file, $_SESSION['admin_id']);
            if ($stmt->execute()) {
                // Registrar la acción en registro_actividades
                $actividad_query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, 'logo', 'AGREGAR', NOW())";
                $actividad_stmt = $conn->prepare($actividad_query);
                $actividad_stmt->bind_param('i', $_SESSION['admin_id']);
                if ($actividad_stmt->execute()) {
                    $success = "El logo ha sido actualizado y la acción ha sido registrada.";
                } else {
                    $errors[] = "Error al registrar la actividad: " . $actividad_stmt->error;
                }
            } else {
                $errors[] = "Error al actualizar el logo: " . $stmt->error;
            }
        } else {
            $errors[] = "Hubo un error al cargar el archivo.";
        }
    }
}

// Obtiene la ruta actual del logo
$query = "SELECT ruta_imagen FROM logo ORDER BY fecha_actualizacion DESC LIMIT 1";
$result = $conn->query($query);
$logoPath = '';
if ($result && $row = $result->fetch_assoc()) {
    $logoPath = str_replace('../', '', $row['ruta_imagen']);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <link rel="stylesheet" href="../css/maximo.css">
    <link rel="stylesheet" href="css/logo_admin.css">
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

            <!-- Contenido principal -->
            <main class="col-md-8 ms-sm-auto col-lg-9 px-8">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
                <div class="container mt-5">
                    <h2>Administrar Logo</h2>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form action="logo.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="logoImage" class="form-label">Cargar Nuevo Logo</label>
                            <input type="file" class="form-control" id="logoImage" name="logoImage" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Logo</button>
                    </form>
                    <?php include 'config.php'; ?>
                    <div class="mt-4">
                        <h4>Logo Actual:</h4>
                        <?php if (file_exists("../img/" . $logoPath)): ?>
                            <img src="../img/<?php echo htmlspecialchars($logoPath); ?>" alt="Logo Actual" style="width: 400px;">
                        <?php else: ?>
                            <p>El logo actual no está disponible.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <script src="../js/bootstrap.min.js"></script>
            </main>
        </div>
    </div>
</body>

</html>
