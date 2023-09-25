<?php
session_start();

if (!isset($_SESSION['rol'])) {
    header('location:./../login/login.php');
} else {
    if ($_SESSION['rol'] != 2) {
        header('location:./../login/login.php');
    }
}
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = 'Usuario no encontrado';
}
if (isset($_SESSION['id'])) {
    $usuario_id = $_SESSION['id'];
} else {
    $usuario_id = 0; 
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
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
        <!--
        <div class="collapse navbar-collapse" id="navbarNav1">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="vista_grupos.php" class="nav-link">Grupos</a>
                </li>
                <li class="nav-item">
                    <a href="vista_usuarios.php" class="nav-link">Usuarios</a>
                </li>
            </ul>
        </div>-->
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

        <center>
            <h2>Bienvenido, <?php echo $username; ?></h2>
            <h2>Bienvenido, <?php echo $usuario_id; ?></h2>
        </center>
        <h1 class="mt-4">Lista de Grupos</h1>

        <div class="row">
            <?php
            include 'conexion.php';

            try {
                $stmt = $conn->prepare("SELECT * FROM grupos");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    foreach ($result as $row) {
                        echo '<div class="col-md-4 mb-4">';
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $row['nombre_grupo'] . '</h5>';

                        // Verificar el estado de la solicitud
                        $stmt = $conn->prepare("SELECT * FROM usuarios_grupos WHERE usuario_id = :usuario_id AND grupo_id = :grupo_id");
                        $stmt->bindParam(':usuario_id', $_SESSION['id'], PDO::PARAM_INT);
                        $stmt->bindParam(':grupo_id', $row['id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

                        if (!$solicitud || $solicitud['estado'] === 'pendiente') {
                            // Agregar el botón "Unirse al Grupo"
                            echo '<a href="#" class="btn btn-primary unirse-btn" data-toggle="modal" data-target="#confirmacionModal" data-grupo-id=' . $row['id'] . '>Unirse al Grupo</a>';
                        } else {
                            echo '<a href="ver_grupo.php?id=' . $row['id'] . '" class="btn btn-success">Ver Grupo</a>';
                        }

                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "No hay grupos disponibles.";
                }
            } catch (PDOException $e) {
                echo "Error al ejecutar la consulta: " . $e->getMessage();
            }

            // Cerrar la conexión (opcional si no se van a realizar más operaciones)
            $conn = null;
            ?>

        </div>
    </div>
    <!-- Agrega el modal de confirmación al final del archivo -->
    <div class="modal fade" id="confirmacionModal" tabindex="-1" role="dialog" aria-labelledby="confirmacionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="unirse_grupo.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmacionModalLabel">Confirmación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="grupo_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['id']; ?>">
                        <p>¿Estás seguro de unirte a este grupo?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exitoModal" tabindex="-1" role="dialog" aria-labelledby="exitoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exitoModalLabel">Solicitud Enviada</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Tu solicitud ha sido enviada con éxito.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Cuando se hace clic en el botón "Confirmar" en el modal de confirmación
            $("#confirmacionModal .btn-confirmar").click(function() {
                // Ocultar el modal de confirmación
                $("#confirmacionModal").modal("hide");

                // Mostrar el modal de éxito
                $("#exitoModal").modal("show");

                // Realizar la solicitud AJAX
                var grupoId = $(this).data("grupo-id");

                $.ajax({
                    type: "POST",
                    url: "procesar_solicitud.php",
                    data: {
                        grupo_id: grupoId
                    },
                    success: function(response) {
                        // Manejar la respuesta del servidor si es necesario
                    },
                    error: function(error) {
                        // Manejar errores si es necesario
                    }
                });
            });
        });
    </script>



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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>