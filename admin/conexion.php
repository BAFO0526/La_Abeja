<?php
// Archivo: admin/conexion.php

$host = 'localhost'; // El nombre del servidor
$usuario = 'root';   // El nombre de usuario de MySQL
$contrasena = '';    // La contraseña del usuario de MySQL (en tu caso, es una contraseña vacía)
$base_de_datos = 'pagina'; // El nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres para la conexión
$conn->set_charset("utf8");
?>
