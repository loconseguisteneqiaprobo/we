<?php
$host = 'localhost'; // Cambia esto si es necesario
$db = 'admin_panel';
$user = 'root'; // Cambia esto por tu usuario de base de datos
$pass = ''; // Cambia esto por tu contraseña de base de datos

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Establecer el modo de error de PDO en excepción
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
} catch (PDOException $e) {
  
}
?>
