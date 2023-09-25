
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
        <a href="vista_grupos.php" class="btn btn-primary mt-3">Volver </a>
        <h1 class="mt-4">Formulario de Reunión</h1>

        <form method="post">
            <div class="form-group">
                <label for="display_name">Nombre</label>
                <input type="text" class="form-control" id="display_name" name="display_name" value="2.16.0#CDN" maxLength="100" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <label for="meeting_number">Número de Reunión</label>
                <input type="text" class="form-control" id="meeting_number" name="meeting_number" maxLength="200" style="width:150px" placeholder="Número de Reunión" required>
            </div>
            <div class="form-group">
                <label for="meeting_pwd">Contraseña de Reunión</label>
                <input type="text" class="form-control" id="meeting_pwd" name="meeting_pwd" maxLength="32" style="width:150px" placeholder="Contraseña de Reunión">
            </div>
            <div class="form-group">
                <label for="meeting_email">Email de Opción</label>
                <input type="text" class="form-control" id="meeting_email" name="meeting_email" maxLength="32" style="width:150px" placeholder="Email de Opción">
            </div>
            <div class="form-group">
                <label for="meeting_role">Rol en la Reunión</label>
                <select id="meeting_role" class="form-control" name="meeting_role">
                    <option value="0">Attendee</option>
                    <option value="1">Host</option>
                </select>
            </div>
            <div class="form-group">
                <label for="meeting_china">Tipo de Reunión</label>
                <select id="meeting_china" class="form-control" name="meeting_china">
                    <option value="0">Global</option>
                    <option value="1">China</option>
                </select>
            </div>
            <div class="form-group">
                <label for="meeting_lang">Idioma de la Reunión</label>
                <select id="meeting_lang" class="form-control" name="meeting_lang">
                    <option value="en-US">English</option>
                    <option value="de-DE">German Deutsch</option>
                    <!-- Agrega las opciones restantes aquí -->
                </select>
            </div>
            <input type="hidden" name="grupo_id" value="<?php echo $_GET['id']; ?>">

            <button type="submit" class="btn btn-primary">Guardar Reunión</button>
            <input type="hidden" name="copy_link_value" value="">
            <button type="submit" class="btn btn-primary" id="join_meeting">Unirse a la Reunión</button>
            <button type="submit" class="btn btn-primary" id="clear_all">Limpiar</button>
            <button type="button" link="" onclick="window.copyJoinLink('#copy_join_link')" class="btn btn-primary" id="copy_join_link">Copiar Enlace de Unión Directa</button>

        </form>


        <?php
        include 'conexion.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $display_name = htmlspecialchars($_POST['display_name']);
            $meeting_number = htmlspecialchars($_POST['meeting_number']);
            $meeting_pwd = htmlspecialchars($_POST['meeting_pwd']);
            $meeting_email = htmlspecialchars($_POST['meeting_email']);
            $meeting_role = (int)$_POST['meeting_role'];
            $meeting_china = (int)$_POST['meeting_china'];
            $meeting_lang = htmlspecialchars($_POST['meeting_lang']);
            $grupo_id = (int)$_POST['grupo_id'];

            try {
                // Preparar la consulta SQL
                $stmt = $conn->prepare("INSERT INTO reuniones (display_name, meeting_number, meeting_pwd, meeting_email, meeting_role, meeting_china, meeting_lang, grupo_id) 
        VALUES (:display_name, :meeting_number, :meeting_pwd, :meeting_email, :meeting_role, :meeting_china, :meeting_lang, :grupo_id)");

                // Vincular los parámetros
                $stmt->bindParam(':display_name', $display_name, PDO::PARAM_STR);
                $stmt->bindParam(':meeting_number', $meeting_number, PDO::PARAM_STR);
                $stmt->bindParam(':meeting_pwd', $meeting_pwd, PDO::PARAM_STR);
                $stmt->bindParam(':meeting_email', $meeting_email, PDO::PARAM_STR);
                $stmt->bindParam(':meeting_role', $meeting_role, PDO::PARAM_INT);
                $stmt->bindParam(':meeting_china', $meeting_china, PDO::PARAM_INT);
                $stmt->bindParam(':meeting_lang', $meeting_lang, PDO::PARAM_STR);
                $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);

                // Ejecutar la consulta
                $stmt->execute();

                echo "Los datos se han guardado correctamente.";
            } catch (PDOException $e) {
                echo "Error al guardar los datos: " . $e->getMessage();
            }

            $conn = null; // Cerrar la conexión
        }
        ?>



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