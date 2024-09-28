<?php
include 'conexion.php';

$query = "SELECT ruta_imagen FROM logo ORDER BY fecha_actualizacion DESC LIMIT 1";
$result = $conn->query($query);
$logoPath = '';

if ($result && $row = $result->fetch_assoc()) {
    $logoPath = $row['ruta_imagen'];
} else {
    // Ruta por defecto si no se encuentra un logo en la base de datos
    $logoPath = 'img/logo.png';
}
?>
