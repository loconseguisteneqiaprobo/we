<?php
// Incluir la configuración de la base de datos
include 'db.php';

session_start();

if (!isset($_GET['session_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session ID no proporcionado.', 'user_status' => null]);
    exit;
}

$session_id = $_GET['session_id'];

try {
    // Preparar la consulta para obtener el estado del usuario basado en session_id
    $stmt = $conn->prepare("SELECT estado FROM usuarios WHERE session_id = ?");
    $stmt->bindParam(1, $session_id, PDO::PARAM_STR); // session_id es un string
    $stmt->execute();

    // Verificar si se encontró el usuario
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $status = $row['estado'];

        // Devolver el estado del usuario en formato JSON
        echo json_encode(['status' => 'success', 'user_status' => $status]);
    } else {
        // Si no se encuentra el usuario, devolver error
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado.', 'user_status' => null]);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage(), 'user_status' => null]);
}
?>
