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

$errors = [];
$success = '';

// Consultar todos los administradores para mostrar en la tabla
$sql = "SELECT * FROM administradores";
$result = $conn->query($sql);

// Obtener el ID del primer administrador (supuesto primer registro en la tabla)
$sql_first_admin = "SELECT MIN(id) AS id FROM administradores";
$result_first_admin = $conn->query($sql_first_admin);
$row_first_admin = $result_first_admin->fetch_assoc();
$first_admin_id = $row_first_admin['id'];
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
    <script src="../js/bootstrap.min.js"></script>
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
            <main class="col-md-9 ms-sm-auto col-lg-9 px-8">
                <div class="d-flex justify-content-between align-items-center py-3">
                    <div class="text-muted"><b>Usuario: </b><i><?php echo htmlspecialchars($user_name); ?></i></div>
                    <a class="btn btn-success" href="agregar.php">
                        <i class="fas fa-user"></i> Agregar Administrador
                    </a>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
                <h2 class="my-4">Administradores</h2>
                <div class="row">
                    <!-- Tabla de Administradores -->
                    <div class="col-md-12">
                        <div class="card mb-6">
                            <div class="card-header">
                                <h5>Lista de Administradores</h5>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Correo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $is_first_admin = $row['id'] == $first_admin_id;
                                                echo "<tr>
                                                    <td>{$row['nombre']}</td>
                                                    <td>{$row['apellido']}</td>
                                                    <td>{$row['correo']}</td>
                                                    <td>";
                                                if (!$is_first_admin) {
                                                    echo "<a href='acciones/editar.php?id={$row['id']}' class='btn btn-warning btn-sm'>Editar</a>
          <a href='#' class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#confirmDeleteModal' data-id='{$row['id']}' data-name='{$row['nombre']} {$row['apellido']}'>Eliminar</a>";
                                                } else {
                                                    echo "No disponible";
                                                }
                                                echo "</td>
                                                </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4'>No hay administradores registrados.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Botón que activó el modal
            var adminId = button.getAttribute('data-id'); // Extraer la información del atributo data-*
            var adminName = button.getAttribute('data-name'); // Extraer el nombre del administrador

            // Actualizar el contenido del modal
            var modalBodySpan = confirmDeleteModal.querySelector('.modal-body #adminName');
            modalBodySpan.textContent = adminName;

            // Actualizar el enlace de confirmación para que redirija a la página de eliminación correcta
            var confirmDeleteButton = confirmDeleteModal.querySelector('.modal-footer #confirmDeleteButton');
            confirmDeleteButton.setAttribute('href', 'acciones/eliminar.php?id=' + adminId);
        });
    });
</script>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Seguro que quieres eliminar al administrador <span id="adminName"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="confirmDeleteButton">Eliminar</a>
            </div>
        </div>
    </div>
</div>

</html>