<?php
// Cargar las credenciales de la base de datos
$servername = "localhost"; // Cambia esto por tu servidor
$username = "root"; // Cambia esto por tu usuario de la base de datos
$password = ""; // Cambia esto por tu contraseña de la base de datos
$dbname = "admin_panel"; // Cambia esto por tu nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener los usuarios incluyendo session_id
$sql = "SELECT id, username, password, estado, dinamica, session_id FROM usuarios"; // Agregando session_id
$result = $conn->query($sql);

$users = array();

if ($result->num_rows > 0) {
    // Recorrer los resultados y agregarlos al array
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($users);

// Cerrar la conexión
$conn->close();
?>
