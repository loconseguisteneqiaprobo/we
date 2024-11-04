<?php
// Conexión a la base de datos
include 'db.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $session_id = $_POST['session_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validar y sanitizar los datos de entrada
    $session_id = htmlspecialchars(trim($session_id));
    $username = htmlspecialchars(trim($username));
    $password = htmlspecialchars(trim($password));

    try {
        // Comprobar si el session_id ya existe
        $query_check = "SELECT * FROM usuarios WHERE session_id = :session_id";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':session_id', $session_id);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            // Si el session_id existe, actualizar username, password y estado
          
            $query_update = "UPDATE usuarios SET username = :username, password = :password, estado = 'pendiente' WHERE session_id = :session_id";
            $stmt_update = $conn->prepare($query_update);
            $stmt_update->bindParam(':username', $username);
            $stmt_update->bindParam(':password', $password);
            $stmt_update->bindParam(':session_id', $session_id);
            
            if ($stmt_update->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Usuario actualizado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el usuario.']);
            }
        } else {
            // Si el session_id no existe, insertar nuevo registro
           
            $query_insert = "INSERT INTO usuarios (session_id, username, password, estado) VALUES (:session_id, :username, :password, 'pendiente')";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bindParam(':session_id', $session_id);
            $stmt_insert->bindParam(':username', $username);
            $stmt_insert->bindParam(':password', $password);
            
            if ($stmt_insert->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Usuario guardado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al guardar el usuario.']);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
    }
}

// La conexión a PDO se cierra automáticamente al final del script
?>
