<?php
include 'conexion.php';

// Después de iniciar la sesión
// Después de iniciar la sesión
session_start();

// Genera un token solo si no existe
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32)); // Genera un token de 256 bits
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Verificar el token de sesión
    if (!hash_equals($_SESSION['token'], $_POST['token'])) {
        // El token no coincide, lo cual puede indicar una solicitud CSRF
        // Puedes manejar esto de la manera que consideres apropiada
        die('Acceso no autorizado');
    }

    try {
        $stmt = $conn->prepare('SELECT * FROM usuarios WHERE correo = :correo');
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData) {
            if (password_verify($contrasena, $userData['contrasena'])) {
                $_SESSION['id'] = $userData['id']; // Asigna el ID del usuario autenticado
                // Asigna el ID del usuario autenticado
                $_SESSION['rol'] = $userData['rol'];
                $_SESSION['username'] = $userData['nombre'];

                switch ($_SESSION['rol']) {
                    case 1:
                        header('location: ../admin/admin.php');
                        break;
                    case 2:
                        header('location: ../user/user.php');
                        break;
                }
            } else {
                echo '<script>alert("Contraseña incorrecta");</script>';
            }
        } else {
            echo '<script>alert("Usuario no encontrado");</script>';
        }
    } catch (PDOException $e) {
        echo 'Error al ejecutar la consulta: ' . $e->getMessage();
    }
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    
    $conn = null;
}
?>

<!-- ... tu formulario ... -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="Website Icon" type="png" href="icon.png">
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.login-container {
    background-color: #fff;
    border: 1px solid #ccc;
    padding: 20px;
    text-align: center;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 300px;
    border-radius: 10%;
    box-shadow: 5px 5px 5px #123263;
}

.logo {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
    display: block;
}

h2 {
    margin: 0 0 20px;
}

form {
    text-align: left;
}

label {
    display: block;
    margin-bottom: 5px;
    font-size: 13px;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 15px;    
    width: 280px;
}

button {
    background-color: #123263;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

a {
    text-decoration: none;
    color: #007BFF;
    display: block;
    margin-top: 10px;
}

a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
    <div class="login-container">
        <img src="ipade.png" alt="Logo de la Escuela de Negocios" class="logo">
        <h2>Iniciar Sesión</h2>
        <form method="post">
    <label for="correo">Correo Electrónico:</label>
    <input type="email" id="correo" name="correo" required><br>

    <label for="contrasena">Contraseña:</label>
    <input type="password" id="contrasena" name="contrasena" required><br>

    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
    <button type="submit">Iniciar Sesión</button>
</form>
        <br>
        <a href="restableser.php">¿Olvidaste tu contraseña?</a>
    </div>
</body>
</html>
