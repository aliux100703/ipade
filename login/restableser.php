<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <!-- Agrega tus estilos CSS aquí si es necesario -->
</head>
<body>
    <h2>Recuperar Contraseña</h2>
    <p>Ingresa tu dirección de correo electrónico para restablecer tu contraseña.</p>
    
    <form action="reset_password_process.php" method="post">
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <button type="submit">Enviar Solicitud</button>
    </form>
</body>
</html>
