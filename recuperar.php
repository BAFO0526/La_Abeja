<?php
session_start();
$showSuccessModal = false;
$showErrorModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'admin/acciones/cambiar.php';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/ojo.css">
    <link rel="stylesheet" href="css/maximo.css">
    <link rel="stylesheet" href="css/modal-login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .password-container {
            position: relative;
        }
        .password-container i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }
        .input-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php include 'vista/menu.php'; ?>
    <div class="login-container">
        <h2>Recuperar Contraseña</h2>
        <form action="recuperar.php" method="POST" id="recoverForm">
            <div class="input-group">
                <label for="correo">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" placeholder="Correo electrónico" required>
            </div>
            <div class="input-group">
                <label for="pregunta">Pregunta de seguridad:</label>
                <input type="text" id="pregunta" name="pregunta" placeholder="Pregunta de seguridad" required>
            </div>
            <div class="input-group">
                <label for="respuesta">Respuesta a la pregunta de seguridad:</label>
                <div class="password-container">
                    <input type="password" id="respuesta" name="respuesta" placeholder="Respuesta" required>
                    <i class="fa fa-eye" id="toggleRespuesta"></i>
                </div>
            </div>
            <div class="input-group">
                <label for="nueva_contrasena">Nueva Contraseña:</label>
                <div class="password-container">
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="Nueva Contraseña" required>
                    <i class="fa fa-eye" id="toggleNuevaContrasena"></i>
                </div>
                <small id="passwordHelp" class="form-text text-muted">
                    La contraseña debe tener al menos 6 caracteres.
                </small>
            </div>
            <div class="input-group">
                <button type="submit">Cambiar Contraseña</button>
            </div>
        </form>
    </div>

    <?php include 'vista/footer.php'; ?>

    <!-- Modal de éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Éxito</h5>
                </div>
                <div class="modal-body">
                    La contraseña ha sido cambiada exitosamente. Puedes iniciar sesión con tu nueva contraseña.
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de error -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    Hubo un error al intentar cambiar la contraseña. Por favor, verifica tus datos e intenta nuevamente.
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleRespuesta = document.getElementById('toggleRespuesta');
            const toggleNuevaContrasena = document.getElementById('toggleNuevaContrasena');
            const respuestaInput = document.getElementById('respuesta');
            const nuevaContrasenaInput = document.getElementById('nueva_contrasena');

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

            toggleRespuesta.addEventListener('click', function() {
                togglePasswordVisibility(respuestaInput, toggleRespuesta);
            });

            toggleNuevaContrasena.addEventListener('click', function() {
                togglePasswordVisibility(nuevaContrasenaInput, toggleNuevaContrasena);
            });
        });

        document.getElementById('recoverForm').addEventListener('submit', function(e) {
            const nuevaContrasena = document.getElementById('nueva_contrasena').value;
            if (nuevaContrasena.length < 6) {
                e.preventDefault();
                alert('La nueva contraseña debe tener al menos 6 caracteres.');
            }
        });

        <?php if ($showSuccessModal): ?>
        $(document).ready(function() {
            $('#successModal').modal('show');
        });
        <?php endif; ?>

        <?php if ($showErrorModal): ?>
        $(document).ready(function() {
            $('#errorModal').modal('show');
        });
        <?php endif; ?>
    </script>
</body>
</html>
