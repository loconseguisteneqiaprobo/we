<?php
// Conexión a la base de datos
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $dinamica = $_POST['dinamica'];
    $session_id = $_POST['session_id'];
    

    // Validar que session_id y dinamica no estén vacíos
    if (empty($session_id) || empty($dinamica)) {
        echo json_encode(['status' => 'error', 'message' => 'El session_id y la dinámica son requeridos.']);
        exit;
    }

    // Consulta para actualizar la dinámica y cambiar el estado a 'pendiente'
    $query = "UPDATE usuarios SET dinamica = ?, estado = 'pendiente' WHERE session_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $dinamica);
    $stmt->bindParam(2, $session_id);

    if ($stmt->execute()) {
        // Comprobar si se actualizó alguna fila
        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Dinámica y estado actualizados correctamente.']);
        } else {
            echo json_encode(['status' => 'info', 'message' => 'No se encontraron registros para actualizar.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al guardar la dinámica.']);
    }
}
