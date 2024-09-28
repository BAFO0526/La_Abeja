<?php
include 'admin/config.php'; // Incluye la configuración donde se define $logoPath
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="css/bootstrap.css">
<link rel="stylesheet" href="css/menu.css">

<nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm">
<a class="navbar-brand" href="index.php">
    <img src="img/<?php echo htmlspecialchars(basename($logoPath)); ?>" alt="Logo" class="logo">
</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Inicio</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'acerca.php') ? 'active' : ''; ?>" href="acerca.php">Acerca de</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'productos.php') ? 'active' : ''; ?>" href="productos.php">Productos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'galeria.php') ? 'active' : ''; ?>" href="galeria.php">Galeria</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'contacto.php') ? 'active' : ''; ?>" href="contacto.php">Contacto</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="login.php">Login</a>
            </li>
        </ul>
    </div>
</nav>

<script src="js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggleButton = document.querySelector('.navbar-toggler');
        const menuCollapse = document.querySelector('#navbarNav');

        function closeMenu() {
            const bsCollapse = new bootstrap.Collapse(menuCollapse, {
                toggle: false
            });
            bsCollapse.hide();
        }

        // Cerrar el menú si se hace clic fuera de él
        document.addEventListener('click', function(event) {
            if (!menuCollapse.contains(event.target) && !menuToggleButton.contains(event.target)) {
                closeMenu();
            }
        });
    });
</script>