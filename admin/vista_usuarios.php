<?php

session_start();

if (!isset($_SESSION['rol'])) {
    header('location:./../login/login.php');
} else {
    if ($_SESSION['rol'] != 1) {
        header('location:./../login/login.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #a29475;
            height: 90px;
        }

        .navbar img {
            width: 100px;
        }

        .btn-group {
            background-color: #123263;
            color: #FFFFFF;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="admin.php">
            <img src="ipd.png" alt="Logo">
        </a>
        <!-- Botón de menú 1 -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav1" aria-controls="navbarNav1" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Botón de menú 2 -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav2" aria-controls="navbarNav2" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav1">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="vista_grupos.php" class="nav-link">Grupos</a>
                </li>
                <li class="nav-item">
                    <a href="vista_usuarios.php" class="nav-link">Usuarios</a>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav2">
            <ul class="navbar-nav ml-auto">
                <!-- Opción 2 (sin modificar los estilos) -->
                <li class="nav-item">
                    <a href="./../login/cerrar_sesion.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt">Cerrar Sesión</i>
                    </a>
                </li>
            </ul>
        </div>
    </nav>


    <div class="container">

        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['nombre'];
            $correo = $_POST['correo'];
            $contrasena = $_POST['contrasena'];
            $rol = $_POST['rol'];

            // Hashear la contraseña
            $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);

            try {
                // Preparar la consulta SQL
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena,rol) VALUES (:nombre, :correo, :contrasena, :rol)");

                // Vincular los parámetros
                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
                $stmt->bindParam(':contrasena', $contrasena_hasheada, PDO::PARAM_STR);
                $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);

                // Ejecutar la consulta
                if ($stmt->execute()) {
                    $mensaje = "Usuario registrado correctamente";
                } else {
                    $error = "Error al registrar el usuario";
                }
            } catch (PDOException $e) {
                $error = "Error al ejecutar la consulta: " . $e->getMessage();
            }
        }

        // Cerrar la conexión
        $conn = null;
        ?>

        <!-- Sección de mensajes -->
        <?php if (isset($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>


        <h1 class="mt-4">Formulario de Registro</h1>

        <!-- Formulario -->
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena" required>
            </div>

            <select class="form-select" name="rol" aria-label="Rol">
                <option value="2" selected>Usuario</option>
                <option value="1">Administrador</option>
            </select>

            <center> <button type="submit" class="btn btn-primary">Registrar</button></center>
        </form>

        <?php
include 'conexion.php';

try {
    $stmt = $conn->query("SELECT * FROM usuarios");

    if ($stmt->rowCount() > 0) {
        echo "<h2 class='mt-4'>Usuarios Registrados</h2>";
        echo "<table class='table table-bordered'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Contraseña (Encriptada)</th><th>Rol</th><th>Acciones</th></tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["nombre"] . "</td>";
            echo "<td>" . $row["correo"] . "</td>";
            echo "<td>" . $row["contrasena"] . " <button type='button' class='btn btn-info' onclick='verContrasena(this)'>Ver</button></td>";
            echo "<td>" . $row["rol"] . "</td>";
            echo "<td>
                <button type='button' class='btn btn-danger' onclick='confirmarEliminar(" . $row["id"] . ")'>Eliminar</button>
            </td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No hay usuarios registrados";
    }
} catch (PDOException $e) {
    echo "Error al ejecutar la consulta: " . $e->getMessage();
}

$conn = null;
?>


    </div>
    <!-- Modal de Confirmación -->
    <div class="modal fade" id="confirmarEliminarModal" tabindex="-1" role="dialog" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que quieres eliminar este usuario?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminarBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmarEliminar(id) {
            $('#confirmarEliminarModal').modal('show');

            $('#confirmarEliminarBtn').on('click', function() {
                $('#confirmarEliminarModal').modal('hide');
                window.location.href = "eliminar_usuario.php?id=" + id;
            });
        }
    </script>

<script>
    function verContrasena() {
        // Obtener el elemento de contraseña
        var contrasenaElement = document.getElementById("contrasena");
        
        // Obtener el valor de la contraseña
        var contrasena = contrasenaElement.value;

        // Mostrar la contraseña en un cuadro de diálogo
        alert("Contraseña desencriptada: " + contrasena);
    }
</script>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>