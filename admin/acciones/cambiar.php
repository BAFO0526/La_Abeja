<?php
session_start();
$showSuccessModal = false;
$showErrorModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'conexion.php';
    
    $correo = $_POST['correo'];
    $pregunta = $_POST['pregunta'];
    $respuesta = $_POST['respuesta'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // Verificar si el correo existe y obtener la pregunta y respuesta hash
    $sql = "SELECT id, pregunta, respuesta_hash FROM administradores WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $respuesta_hash = $row['respuesta_hash'];
        $pregunta_bd = $row['pregunta'];

        // Verificar la respuesta proporcionada y la pregunta
        if (password_verify($respuesta, $respuesta_hash) && $pregunta === $pregunta_bd) {
            // Hash de la nueva contraseña
            $hash_contrasena = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la base de datos
            $sql_update = "UPDATE administradores SET contrasena = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $hash_contrasena, $id);
            if ($stmt_update->execute()) {
                $showSuccessModal = true;
            } else {
                $showErrorModal = true;
            }
        } else {
            $showErrorModal = true;
        }
    } else {
        $showErrorModal = true;
    }
}
?>
