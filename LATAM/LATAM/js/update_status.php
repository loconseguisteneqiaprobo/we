<?php
// Incluir la configuración de la base de datos
require 'db.php';

// Crear la conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar la conexión
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Conexión fallida: " . $conn->connect_error]));
}

// Verificar si el session_id y el estado están presentes
if (isset($_GET['session_id']) && isset($_GET['status'])) {
    // Sanitizar los datos recibidos
    $session_id = $conn->real_escape_string($_GET['session_id']);
    $status = $conn->real_escape_string($_GET['status']);

    // Imprimir para verificar los datos recibidos (solo para depuración)
    error_log("session_id: " . $session_id . " status: " . $status);

    // Preparar la consulta para actualizar el estado basado en session_id
    $sql = "UPDATE usuarios SET `estado` = ? WHERE `session_id` = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["status" => "error", "message" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }

    // Asociar los parámetros y ejecutar la consulta
    $stmt->bind_param("ss", $status, $session_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Estado actualizado exitosamente."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al actualizar el estado: " . $stmt->error]);
    }

    // Cerrar la consulta
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Session ID o estado no especificado."]);
}

// Cerrar la conexión
$conn->close();
?>
