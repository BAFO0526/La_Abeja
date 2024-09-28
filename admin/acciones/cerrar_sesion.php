<?php
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión
header("Location: /agricultura/login.php"); // Redirige al usuario al formulario de inicio de sesión
exit();
?>
