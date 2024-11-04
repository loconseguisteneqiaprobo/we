<?php
// Cargar las credenciales de la base de datos
require 'db.php'; // O la ruta correcta

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el ID del usuario a eliminar
// Obtener el ID del usuario a eliminar
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo json_encode(["status" => "error", "message" => "ID no especificado."]);
    exit; // Salir si no se proporciona el ID
}
// Consulta para eliminar el usuario
$sql = "DELETE FROM usuarios WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Usuario eliminado exitosamente."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error al eliminar el usuario."]);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
