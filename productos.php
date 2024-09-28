<?php
include 'admin/conexion.php';

// Obtener productos de la base de datos
$query = "SELECT * FROM productos";
$result = mysqli_query($conn, $query);

$productos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $productos[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/produ.css">
    <link rel="stylesheet" href="css/maximo.css">
    <title>Productos</title>
</head>
<body>
<?php include 'vista/menu.php'; ?>
<div class="product-section">
    <div class="container">
        <div class="row">
            <?php foreach ($productos as $index => $producto): ?>
                <!-- Producto -->
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="product-card">
                        <div class="product-image-container">
                            <img src="img/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['titulo']; ?>" class="product-image">
                        </div>
                        <h2 class="product-name"><?php echo $producto['titulo']; ?></h2>
                        <p class="product-brand"><?php echo $producto['marca']; ?></p>
                        <p class="product-price">S/. <?php echo number_format($producto['precio'], 2); ?></p>
                        <button type="button" class="btn btn-contact" data-bs-toggle="modal" data-bs-target="#productModal<?php echo $producto['id']; ?>">Más detalles</button>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="productModal<?php echo $producto['id']; ?>" tabindex="-1" aria-labelledby="productModalLabel<?php echo $producto['id']; ?>" aria-hidden="true" data-bs-backdrop="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-body d-flex">
                                <div class="modal-image-container me-3">
                                    <img src="img/<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['titulo']; ?>" class="img-fluid rounded">
                                </div>
                                <div class="modal-details">
                                    <h5 class="modal-title" id="productModalLabel<?php echo $producto['id']; ?>"><?php echo $producto['titulo']; ?></h5>
                                    <p><strong>Marca:</strong> <?php echo $producto['marca']; ?></p>
                                    <p><strong>Descripción:</strong> <?php echo $producto['descripcion']; ?></p>
                                    <p><strong>Precio:</strong> S/. <?php echo number_format($producto['precio'], 2); ?></p>
                                    <p><strong>Contacto:</strong></p>
                                    <p class="contact-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                        <a href="https://wa.me/<?php echo $producto['telefono1']; ?>?text=¿Quisiera más información sobre el producto '<?php echo $producto['titulo']; ?>'?" class="contact-link" style="color: #25D366;"><?php echo $producto['telefono1']; ?></a>
                                    </p>
                                    <p class="contact-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                        <a href="https://wa.me/<?php echo $producto['telefono2']; ?>?text=¿Quisiera más información sobre el producto '<?php echo $producto['titulo']; ?>'?" class="contact-link" style="color: #25D366;"><?php echo $producto['telefono2']; ?></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php include 'vista/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js"></script>
</body>
</html>