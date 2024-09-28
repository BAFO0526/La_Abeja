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

// Definir el tamaño máximo permitido para las imágenes (150 KB en bytes)
$max_file_size = 150 * 1024; // 150 KB en bytes

// Validación de teléfono
function validarTelefono($telefono)
{
    return preg_match('/^[0-9]{9}$/', $telefono);
}

// Manejo de formulario de agregar producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        // Obtener y validar datos del formulario
        $titulo = trim($_POST['titulo'] ?? '');
        $marca = trim($_POST['marca'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = trim($_POST['precio'] ?? '');
        $telefono1 = trim($_POST['telefono1'] ?? '');
        $telefono2 = trim($_POST['telefono2'] ?? '');

        // Validar campos
        if (empty($titulo) || empty($marca) || empty($descripcion) || empty($precio) || empty($telefono1) || empty($telefono2)) {
            $errors[] = 'Todos los campos son obligatorios.';
        }
        if (!is_numeric($precio)) {
            $errors[] = 'El precio debe ser un número válido.';
        }
        if (!validarTelefono($telefono1) || !validarTelefono($telefono2)) {
            $errors[] = 'Los números de teléfono deben tener exactamente 9 dígitos.';
        }

        // Validar que se haya subido una imagen
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Es obligatorio subir una imagen.';
        } else {
            // Manejo de imagen
            $imagen_tmp = $_FILES['imagen']['tmp_name'];
            $imagen_nombre = $_FILES['imagen']['name'];
            $imagen_ruta = '../img/' . basename($imagen_nombre);

            // Verificar el tamaño del archivo
            if ($_FILES['imagen']['size'] > $max_file_size) {
                $errors[] = 'La imagen no debe exceder los 150 KB.';
            } else {
                // Mover el archivo al directorio de destino
                if (move_uploaded_file($imagen_tmp, $imagen_ruta)) {
                    $imagen = $imagen_ruta;
                } else {
                    $errors[] = 'Error al subir la imagen.';
                }
            }
        }

        if (empty($errors)) {
            // Insertar datos en la base de datos
            $stmt = $conn->prepare("INSERT INTO productos (imagen, titulo, marca, descripcion, precio, telefono1, telefono2, administrador_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sssssssi", $imagen, $titulo, $marca, $descripcion, $precio, $telefono1, $telefono2, $_SESSION['admin_id']);
                if ($stmt->execute()) {
                    $success = 'Producto agregado con éxito.';

                    // Registro de la actividad
                    $tabla_modificada = "productos";
                    $accion = "AGREGAR";
                    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    $stmt->execute();
                } else {
                    $errors[] = 'Error al agregar el producto.';
                }
                $stmt->close();
            } else {
                $errors[] = 'Error en la preparación de la consulta.';
            }
        }
    }

    // Manejo de formulario de editar producto
    if ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $marca = $_POST['marca'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $precio = $_POST['precio'] ?? '';
        $telefono1 = $_POST['telefono1'] ?? '';
        $telefono2 = $_POST['telefono2'] ?? '';

        $imagen = $_POST['current_imagen'] ?? '';
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $imagen_tmp = $_FILES['imagen']['tmp_name'];
            $imagen_nombre = $_FILES['imagen']['name'];
            $imagen_ruta = '../img/' . basename($imagen_nombre);

            // Verificar el tamaño del archivo
            if ($_FILES['imagen']['size'] > $max_file_size) {
                $errors[] = 'La imagen no debe exceder los 150 KB.';
            } else {
                if (move_uploaded_file($imagen_tmp, $imagen_ruta)) {
                    // Eliminar el archivo antiguo
                    if (file_exists($imagen) && $imagen !== $imagen_ruta) {
                        unlink($imagen);
                    }
                    $imagen = $imagen_ruta;
                } else {
                    $errors[] = 'Error al subir la imagen.';
                }
            }
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE productos SET imagen = ?, titulo = ?, marca = ?, descripcion = ?, precio = ?, telefono1 = ?, telefono2 = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("sssssssi", $imagen, $titulo, $marca, $descripcion, $precio, $telefono1, $telefono2, $id);
                if ($stmt->execute()) {
                    $success = 'Producto actualizado con éxito.';

                    // Registro de la actividad
                    $tabla_modificada = "productos";
                    $accion = "EDITAR";
                    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    $stmt->execute();
                } else {
                    $errors[] = 'Error al actualizar el producto.';
                }
                $stmt->close();
            } else {
                $errors[] = 'Error en la preparación de la consulta.';
            }
        }
    }

    // Manejo de formulario de eliminar producto
    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        // Obtener la imagen actual para eliminarla
        $stmt = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();

        if ($producto) {
            // Eliminar la imagen del servidor
            if (file_exists($producto['imagen'])) {
                unlink($producto['imagen']);
            }

            $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $success = 'Producto eliminado con éxito.';

                    // Registro de la actividad
                    $tabla_modificada = "productos";
                    $accion = "ELIMINAR";
                    $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    $stmt->execute();
                } else {
                    $errors[] = 'Error al eliminar el producto.';
                }
                $stmt->close();
            } else {
                $errors[] = 'Error en la preparación de la consulta.';
            }
        } else {
            $errors[] = 'El producto no existe.';
        }
    }
}

// Obtener todos los productos
$result = $conn->query("SELECT * FROM productos");
$productos = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <!-- Incluye tu CSS y JS aquí -->
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="css/nav_lateral1.css">
    <link rel="stylesheet" href="../css/maximo.css">
    <link rel="stylesheet" href="css/tabla_admin.css">
    <link rel="stylesheet" href="css/modal_admin.css">
    <script src="js/menu.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="../js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editProductModal = document.getElementById('editProductModal');

            editProductModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const product = button.dataset;

                document.getElementById('edit_product_id').value = product.id;
                document.getElementById('edit_titulo').value = product.titulo;
                document.getElementById('edit_marca').value = product.marca;
                document.getElementById('edit_descripcion').value = product.descripcion;
                document.getElementById('edit_precio').value = product.precio;
                document.getElementById('edit_telefono1').value = product.telefono1;
                document.getElementById('edit_telefono2').value = product.telefono2;

                const imagePreview = document.getElementById('edit_image_preview');
                imagePreview.src = product.imagen ? product.imagen : '';
                imagePreview.style.display = product.imagen ? 'block' : 'none';
            });
        });
    </script>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        Agregar Producto
                    </button>
                    <a class="btn btn-danger" href="acciones/cerrar_sesion.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestión de Productos</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                    </div>
                </div>

                <?php if ($errors) : ?>
                    <div class="alert alert-danger">
                        <?php echo implode('<br>', $errors); ?>
                    </div>
                <?php elseif ($success) : ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Título</th>
                                <th>Marca</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Teléfono 1</th>
                                <th>Teléfono 2</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto) : ?>
                                <tr>
                                    <td><img src="<?php echo $producto['imagen']; ?>" alt="Imagen de Producto" width="50"></td>
                                    <td><?php echo $producto['titulo']; ?></td>
                                    <td><?php echo $producto['marca']; ?></td>
                                    <td><?php echo $producto['descripcion']; ?></td>
                                    <td><?php echo $producto['precio']; ?></td>
                                    <td><?php echo $producto['telefono1']; ?></td>
                                    <td><?php echo $producto['telefono2']; ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal" data-id="<?php echo $producto['id']; ?>" data-imagen="<?php echo $producto['imagen']; ?>" data-titulo="<?php echo $producto['titulo']; ?>" data-marca="<?php echo $producto['marca']; ?>" data-descripcion="<?php echo $producto['descripcion']; ?>" data-precio="<?php echo $producto['precio']; ?>" data-telefono1="<?php echo $producto['telefono1']; ?>" data-telefono2="<?php echo $producto['telefono2']; ?>">Editar</button>
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteProductModal" data-id="<?php echo $producto['id']; ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para Agregar Producto -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Agregar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca</label>
                                    <input type="text" class="form-control" id="marca" name="marca" required>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="precio" class="form-label">Precio</label>
                                    <input type="number" class="form-control" id="precio" name="precio" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telefono1" class="form-label">Teléfono 1</label>
                                    <input type="text" class="form-control" id="telefono1" name="telefono1" pattern="\d{9}" title="Debe tener 9 dígitos" required>
                                </div>
                                <div class="mb-3">
                                    <label for="telefono2" class="form-label">Teléfono 2</label>
                                    <input type="text" class="form-control" id="telefono2" name="telefono2" pattern="\d{9}" title="Debe tener 9 dígitos" required>
                                </div>
                                <div class="mb-3">
                                    <label for="imagen" class="form-label">Imagen del Producto</label>
                                    <input type="file" class="form-control" id="imagen" name="imagen">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                    <input type="hidden" name="action" value="add">
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Producto -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">Editar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="edit_titulo" name="titulo" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_marca" class="form-label">Marca</label>
                                    <input type="text" class="form-control" id="edit_marca" name="marca" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="edit_descripcion" name="descripcion" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_precio" class="form-label">Precio</label>
                                    <input type="number" class="form-control" id="edit_precio" name="precio" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_telefono1" class="form-label">Teléfono 1</label>
                                    <input type="text" class="form-control" id="edit_telefono1" name="telefono1" pattern="\d{9}" title="Debe tener 9 dígitos" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_telefono2" class="form-label">Teléfono 2</label>
                                    <input type="text" class="form-control" id="edit_telefono2" name="telefono2" pattern="\d{9}" title="Debe tener 9 dígitos" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_imagen" class="form-label">Imagen del Producto</label>
                                    <input type="file" class="form-control" id="edit_imagen" name="imagen">
                                    <input type="hidden" name="current_imagen" id="edit_current_imagen">
                                    <img id="edit_image_preview" src="" alt="Imagen actual" style="max-width: 100px; margin-top: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_product_id">
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para Eliminar Producto -->
    <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProductModalLabel">Eliminar Producto</h5>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas eliminar este producto?
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_product_id">
                </form>
            </div>
        </div>
    </div>

    <script>
        // Edit Modal
        document.getElementById('editProductModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var titulo = button.getAttribute('data-titulo');
            var marca = button.getAttribute('data-marca');
            var descripcion = button.getAttribute('data-descripcion');
            var precio = button.getAttribute('data-precio');
            var telefono1 = button.getAttribute('data-telefono1');
            var telefono2 = button.getAttribute('data-telefono2');
            var imagen = button.getAttribute('data-imagen');

            var modal = this;
            modal.querySelector('#edit_product_id').value = id;
            modal.querySelector('#edit_titulo').value = titulo;
            modal.querySelector('#edit_marca').value = marca;
            modal.querySelector('#edit_descripcion').value = descripcion;
            modal.querySelector('#edit_precio').value = precio;
            modal.querySelector('#edit_telefono1').value = telefono1;
            modal.querySelector('#edit_telefono2').value = telefono2;
            modal.querySelector('#edit_current_imagen').value = imagen;
            modal.querySelector('#edit_image_preview').src = imagen;
        });

        // Delete Modal
        document.getElementById('deleteProductModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');

            var modal = this;
            modal.querySelector('#delete_product_id').value = id;
        });
    </script>
</body>

</html>