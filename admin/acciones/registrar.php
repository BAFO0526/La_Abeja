<?php
session_start();
include '../conexion.php';

// Asegúrate de que la conexión esté configurada para usar UTF-8
$conn->set_charset("utf8mb4");

if (isset($_POST['add_admin'])) {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $pregunta = trim($_POST['pregunta']);
    $respuesta = trim($_POST['respuesta']);

    // Validación de campos
    $errors = [];

    // Ejemplo de validaciones
    if (empty($nombre) || empty($apellido) || empty($telefono) || empty($correo) || empty($contrasena)) {
        $errors[] = 'Todos los campos son obligatorios.';
    }

    // Si no hay errores, insertar el nuevo administrador
    if (empty($errors)) {
        // Hash de la contraseña y la respuesta
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
        $hashed_answer = password_hash($respuesta, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO administradores (nombre, apellido, telefono, correo, contrasena, pregunta, respuesta_hash) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nombre, $apellido, $telefono, $correo, $hashed_password, $pregunta, $hashed_answer);

        if ($stmt->execute()) {
            // Registrar la actividad en la tabla registro_actividades
            $admin_id = $_SESSION['admin_id']; // ID del administrador que realiza la acción
            $tabla_modificada = 'administradores';
            $accion = 'AGREGAR';

            $stmt_log = $conn->prepare("INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion) VALUES (?, ?, ?)");
            $stmt_log->bind_param("iss", $admin_id, $tabla_modificada, $accion);
            $stmt_log->execute();

            $_SESSION['success'] = 'Administrador agregado correctamente.';
        } else {
            $errors[] = 'Error al agregar el administrador.';
        }

        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    // Redirigir a agregar.php en lugar de admin.php
    header("Location: ../agregar.php");
    exit();
}
?>
