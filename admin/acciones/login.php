<?php
session_start();
include '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM administradores WHERE correo = ? OR nombre = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['contrasena'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['nombre'];
            header("Location: ../admin.php");
            exit();
        } else {
            $_SESSION['login_error'] = true;
            header("Location: ../../login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = true;
        header("Location: ../../login.php");
        exit();
    }
}
?>
