<?php
// archivo: contacto.php
include 'admin/conexion.php';

// Consultar la información de contacto
$sql = "SELECT * FROM contacto WHERE id = 1"; // Cambia el ID según tus datos
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $contacto = $result->fetch_assoc();
} else {
    die("No se encontraron datos de contacto.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="css/contacto.css">
    <link rel="stylesheet" href="css/maximo.css">
    <title>Contact Us</title>
</head>

<body>
    <?php include 'vista/menu.php'; ?>

    <div class="contact-container">
        <div class="contact-card">
            <div class="contact-grid">
                <!-- Mapa de Google -->
                <div class="contact-map">
                    <iframe
                        src="<?php echo htmlspecialchars($contacto['mapa_iframe']); ?>"
                        width="100%" height="100%" style="border: 0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                <!-- Información de Contacto -->
                <div class="contact-info">
                    <h2 class="contact-title">Contáctanos</h2>
                    <div class="contact-details">
                        <h3 class="contact-subtitle">Ubicación:</h3>
                        <p class="contact-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="#FF0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0z" />
                                <circle cx="12" cy="10" r="3" />
                            </svg>
                            <?php echo htmlspecialchars($contacto['ubicacion']); ?>
                        </p>
                        <h3 class="contact-subtitle">Teléfonos:</h3>
                        <p class="contact-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <a href="tel:<?php echo htmlspecialchars($contacto['telefono1']); ?>" class="contact-link"><?php echo htmlspecialchars($contacto['telefono1']); ?></a>
                        </p>
                        <p class="contact-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            <a href="tel:<?php echo htmlspecialchars($contacto['telefono2']); ?>" class="contact-link"><?php echo htmlspecialchars($contacto['telefono2']); ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'vista/footer.php'; ?>
</body>

</html>
