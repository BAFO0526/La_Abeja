<?php
session_start();
include '../conexion.php';
// Verificar si se ha pasado un ID válido en la URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Verificar si $_SESSION['admin_id'] está definido
    if (!isset($_SESSION['admin_id'])) {
        echo "No se ha definido el ID del administrador en la sesión.";
        exit();
    }

    // Obtener información del administrador antes de eliminarlo (para el registro de actividad)
    $stmt = $conn->prepare("SELECT nombre, apellido FROM administradores WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Luego, eliminar el administrador en la tabla `administradores`
        $sql_delete_admin = "DELETE FROM administradores WHERE id = ?";
        if ($stmt2 = $conn->prepare($sql_delete_admin)) {
            $stmt2->bind_param("i", $id);
            if ($stmt2->execute()) {
                // Registro de la actividad
                $tabla_modificada = "administradores";
                $accion = "ELIMINAR";
                $query = "INSERT INTO registro_actividades (administrador_id, tabla_modificada, accion, fecha) VALUES (?, ?, ?, NOW())";
                $stmt3 = $conn->prepare($query);
                
                if ($stmt3 === false) {
                    echo "Error al preparar la consulta de registro de actividad: " . $conn->error;
                } else {
                    $stmt3->bind_param('iss', $_SESSION['admin_id'], $tabla_modificada, $accion);
                    if (!$stmt3->execute()) {
                        echo "Error al registrar la actividad: " . $stmt3->error;
                    } else {
                        echo "Actividad registrada correctamente.";
                    }
                }

                // Redirigir de nuevo a la página del panel de administración después de la eliminación
                header("Location: ../admin.php");
                exit();
            } else {
                echo "Error al eliminar el administrador: " . $stmt2->error;
            }
            $stmt2->close();
        }
    } else {
        echo "ID de administrador no válido.";
    }

    $stmt->close();
} else {
    echo "ID de administrador no válido.";
}

$conn->close(); // Cerrar la conexión
?>
