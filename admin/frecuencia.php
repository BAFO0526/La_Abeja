<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/login.php");
    exit();
}

$user_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Usuario';

// Incluir el archivo de conexión
include '../admin/conexion.php';  // Asegúrate de que la ruta sea correcta

// Parámetros de búsqueda y filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

// Parámetros de paginación
$items_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Consulta para obtener el total de registros
$total_query = "
    SELECT COUNT(*) as total
    FROM registro_actividades ra
    JOIN administradores a ON ra.administrador_id = a.id
    WHERE (a.nombre LIKE ? OR a.apellido LIKE ?)
    AND (ra.fecha LIKE ?)
";
$stmt = $conn->prepare($total_query);
$search_wildcard = "%$search%";
$filter_date_wildcard = "%$filter_date%";
$stmt->bind_param('sss', $search_wildcard, $search_wildcard, $filter_date_wildcard);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];
$total_pages = ceil($total_items / $items_per_page);

// Consulta para obtener los registros con filtros y paginación
$query = "
    SELECT a.nombre, a.apellido, ra.tabla_modificada, ra.accion, ra.fecha
    FROM registro_actividades ra
    JOIN administradores a ON ra.administrador_id = a.id
    WHERE (a.nombre LIKE ? OR a.apellido LIKE ?)
    AND (ra.fecha LIKE ?)
    ORDER BY ra.fecha DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param('sssii', $search_wildcard, $search_wildcard, $filter_date_wildcard, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die('Error en la consulta: ' . $conn->error); // Mostrar error si la consulta falla
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Actividades</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <link rel="stylesheet" href="../css/maximo.css">
    <link rel="stylesheet" href="css/filtrar.css">
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
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
                <h2>Registro de Actividades</h2>
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Buscar" value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="filter_date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Tabla Modificada</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                    <td><?php echo htmlspecialchars($row['tabla_modificada']); ?></td>
                                    <td><?php echo htmlspecialchars($row['accion']); ?></td>
                                    <td><?php echo htmlspecialchars($row['fecha']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No hay actividades registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                </div>
                <!-- Paginación -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&filter_date=<?php echo urlencode($filter_date); ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&filter_date=<?php echo urlencode($filter_date); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&filter_date=<?php echo urlencode($filter_date); ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </main>
        </div>
    </div>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
