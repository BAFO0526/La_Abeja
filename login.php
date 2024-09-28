<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: /agricultura/admin/admin.php");
    exit();
}

$showErrorModal = false;
if (isset($_SESSION['login_error'])) {
    $showErrorModal = true;
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/ojo.css">
    <link rel="stylesheet" href="css/modal-login.css">
    <link rel="stylesheet" href="css/maximo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'vista/menu.php'; ?>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form action="admin/acciones/login.php" method="POST">
            <div class="input-group">
                <label for="username">Correo electrónico o usuario:</label>
                <input type="text" id="username" name="username" placeholder="Correo o Usuario" required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Contraseña" required>
                    <i class="fa fa-eye" id="togglePassword"></i>
                </div>
            </div>
            <div class="input-group">
                <button type="submit">Ingresar</button>
            </div>
            <div class="forgot-password">
                <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
            </div>
        </form>
    </div>
    <?php include 'vista/footer.php'; ?>

    <!-- Modal de error -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    Datos incorrectos. Por favor, verifica tus credenciales e intenta nuevamente.
                </div>
            </div>
        </div>
    </div>

    <!-- Agrega jQuery antes de Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        function togglePasswordVisibility(input, icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        togglePassword.addEventListener('click', function() {
            togglePasswordVisibility(passwordInput, togglePassword);
        });
    });
    </script>

    <?php if ($showErrorModal): ?>
    <script>
        $(document).ready(function() {
            $('#errorModal').modal('show');
        });
    </script>
    <?php endif; ?>
</body>
</html>
