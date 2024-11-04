<?php
// edit_message.php

// Token y chat ID de Telegram
$telegram_token = '7151494583:AAGPQX5iMVK9193isjPcQEUVB3iaeinwN4s';
$chat_id = '799134063';

header('Content-Type: application/json');

// Leer la entrada JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar que el JSON se haya decodificado correctamente y que tenga los datos necesarios
if ($data && isset($data['message_id']) && isset($data['text'])) {
    $messageId = $data['message_id'];
    $newText = $data['text'];

    // URL de la API de Telegram para editar el mensaje
    $url = "https://api.telegram.org/bot$telegram_token/editMessageText";

    // Parámetros de la solicitud
    $post_fields = array(
        'chat_id' => $chat_id,
        'message_id' => $messageId,
        'text' => $newText, // Actualizar el texto con el nuevo mensaje
        'parse_mode' => 'HTML' // Para permitir formato HTML (opcional)
    );

    // Iniciar curl para hacer la solicitud a la API de Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la solicitud
    $result = curl_exec($ch);

    // Verificar si hubo errores con curl
    if (curl_errno($ch)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la solicitud CURL: ' . curl_error($ch)
        ]);
        curl_close($ch);
        exit;
    }

    // Cerrar curl
    curl_close($ch);

    // Decodificar la respuesta de Telegram
    $result_data = json_decode($result, true);

    // Verificar si la API de Telegram devolvió una respuesta exitosa
    if (isset($result_data['ok']) && $result_data['ok']) {
        echo json_encode(['status' => 'success', 'result' => $result_data]);
    } else {
        // Manejo de errores si la API de Telegram devuelve un error
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to edit message',
            'telegram_response' => $result_data
        ]);
    }
} else {
    // Error si los datos recibidos no son válidos
    echo json_encode(['status' => 'error', 'message' => 'Invalid data received']);
}
?>
