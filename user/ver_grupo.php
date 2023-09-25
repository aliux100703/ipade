<?php

session_start();

if (!isset($_SESSION['rol'])) {
    header('location:./../login/login.php');
} else {
    if ($_SESSION['rol'] != 2) {
        header('location:./../login/login.php');
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Grupos</title>
    <!-- Agregar Bootstrap CSS -->
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
        <!-- Menu 1 
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
    -->
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




        <!-- Despliegue de los campos -->
        <?php
        include 'conexion.php';

        // Verificar si se ha proporcionado un ID de grupo
        if (isset($_GET['id'])) {
            $grupo_id = $_GET['id'];

            try {
                // Preparar la consulta SQL
                $stmt = $conn->prepare("SELECT * FROM reuniones WHERE grupo_id = :grupo_id");

                // Vincular el parámetro
                $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);

                // Ejecutar la consulta
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo "<h2>Reuniones del Grupo:</h2>";
                    echo "<table class='table'>";
                    echo "<tr><th>ID</th><th>Display Name</th><th>Meeting Number</th><th>Meeting Password</th><th>Meeting Email</th><th>Meeting Role</th><th>Meeting China</th><th>Meeting Lang</th><th>Grupo ID</th></tr>";

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['display_name'] . "</td>";
                        echo "<td>" . $row['meeting_number'] . "</td>";
                        echo "<td>" . $row['meeting_pwd'] . "</td>";
                        echo "<td>" . $row['meeting_email'] . "</td>";
                        echo "<td>" . $row['meeting_role'] . "</td>";
                        echo "<td>" . $row['meeting_china'] . "</td>";
                        echo "<td>" . $row['meeting_lang'] . "</td>";
                        echo "<td>" . $row['grupo_id'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "No hay reuniones disponibles para este grupo.";
                }
            } catch (PDOException $e) {
                echo "Error al ejecutar la consulta: " . $e->getMessage();
            }
        } else {
            echo "No se proporcionó un ID de grupo.";
        }

        // Cerrar la conexión
        $conn = null;
        ?>

    </div>
    <script>
        document.getElementById('show-test-tool-btn').addEventListener("click", function(e) {
            var textContent = e.target.textContent;
            if (textContent === 'Show') {
                document.getElementById('nav-tool').style.display = 'block';
                document.getElementById('show-test-tool-btn').textContent = 'Hide';
            } else {
                document.getElementById('nav-tool').style.display = 'none';
                document.getElementById('show-test-tool-btn').textContent = 'Show';
            }
        })
    </script>

    <!-- Agregar Bootstrap JS y dependencias -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>