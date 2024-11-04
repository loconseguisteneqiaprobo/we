<?php
session_start();

// Si ya está autenticado, redirigir al panel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_panel.php');
    exit;
}

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'])); // Sanitizar entrada
    $password = trim($_POST['password']); // Recibir la contraseña en texto plano

    // Validar longitud de username y password
    if (strlen($username) > 10 || strlen($password) > 10) {
        $error = "El nombre de usuario y la contraseña deben tener un máximo de 10 caracteres.";
    } else {
        // Conectar a la base de datos
        $conn = new mysqli("localhost", "root", "", "admin_panel");

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Preparar la consulta para buscar el usuario por nombre
        $sql = "SELECT * FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar las credenciales
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            // Verificar la contraseña utilizando MD5
            if (md5($password) === $admin['password_hash']) {
                // Iniciar sesión y redirigir al panel
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username']; // Guardar el username en la sesión
                header('Location: checkData.php');  // Redirigir a checkData.php o admin_panel.php
                exit;
            } else {
                $error = "Nombre de usuario o contraseña incorrectos.";
            }
        } else {
            $error = "Nombre de usuario o contraseña incorrectos.";
        }

        // Cerrar la declaración y la conexión
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Administrativo</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        .container {
            margin-top: 100px;
            max-width: 400px;
            background-color: #282828;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px 5px rgba(0, 255, 128, 0.2);
            color: white;
        }

        .form-control {
            background-color: #333;
            color: white;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #444;
            color: white;
        }

        .btn-primary {
            background-color: #66fcf1;
            border-color: #66fcf1;
            font-weight: bold;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #45b8b3;
            border-color: #45b8b3;
        }

        .alert-danger {
            background-color: #ff4c4c;
            color: white;
            border-color: #ff4c4c;
        }

        h2 {
            color: #66fcf1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Login Administrativo</h2>
        <form action="login.php" method="POST">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" name="username" id="username" class="form-control" maxlength="10" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" class="form-control" maxlength="10" required>
            </div>
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
    </div>
</body>
</html>
