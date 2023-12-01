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
    <link rel="Website Icon" type="png" href="icon.png">
    <!-- Agregar Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />

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

        <div class="collapse navbar-collapse" id="navbarNav1">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="vista_grupos.php">Grupos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vista_usuarios.php">Usuarios</a>
                </li>


            </ul>
        </div>
        <div class="collapse navbar-collapse" id="navbarNav2">
            <ul class="navbar-nav ml-auto">
                <!-- Sección de Notificaciones -->
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#notificacionesModal">
                        <i class="fas fa-bell"></i>
                        <span id="contadorNotificaciones" class="badge badge-danger"></span>
                    </a>
                </li>

                <!-- Opción de Cerrar Sesión -->
                <li class="nav-item">
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalCerrarSesion">
                        Cerrar Sesión
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Modal -->
    <div class="modal fade" id="modalCerrarSesion" tabindex="-1" role="dialog" aria-labelledby="modalCerrarSesionLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCerrarSesionLabel">Confirmar Cierre de Sesión</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas cerrar sesión?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <a href="./../login/cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="notificacionesModal" tabindex="-1" role="dialog" aria-labelledby="notificacionesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificacionesModalLabel">Notificaciones</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí puedes mostrar la lista de notificaciones -->
                    <ul class="list-group">
                        <?php
                        include 'conexion.php';
                        try {
                            $stmt = $conn->prepare("
                            SELECT usuarios.nombre as nombre_usuario, grupos.nombre_grupo as nombre_grupo, usuarios_grupos.id
                            FROM usuarios_grupos
                            INNER JOIN usuarios ON usuarios_grupos.usuario_id = usuarios.id
                            INNER JOIN grupos ON usuarios_grupos.grupo_id = grupos.id
                            WHERE usuarios_grupos.estado = 'pendiente'
                        ");
                            $stmt->execute();
                            $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Mostrar notificaciones
                            if (count($notificaciones) > 0) {
                                foreach ($notificaciones as $notificacion) {
                                    echo "<li class='list-group-item'>El usuario {$notificacion['nombre_usuario']} ha solicitado unirse al grupo {$notificacion['nombre_grupo']}";

                                    echo "<form method='post' action='procesar_solicitud.php'>";
                                    echo "<input type='hidden' name='solicitud_id' value='{$notificacion['id']}'>";
                                    echo "<button type='submit' name='accion' value='aceptar' class='btn btn-primary'>Aceptar</button>";
                                    echo "<button type='submit' name='accion' value='rechazar' class='btn btn-danger'>Rechazar</button>";
                                    echo "</form>";

                                    echo "</li>";
                                }
                            } else {
                                echo "<li class='list-group-item'>No hay notificaciones pendientes.</li>";
                            }
                        } catch (PDOException $e) {
                            echo "<li class='list-group-item'>Error al obtener notificaciones: " . $e->getMessage() . "</li>";
                        }



                        // Cerrar la conexión
                        $conn = null;
                        ?>
                    </ul>
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--contenido-->
    <div class="container">
        <a href="vista_grupos.php" class="btn btn-primary mt-3">Volver </a>
        <h1 class="mt-4">Formulario de Reunión</h1>

        <!--formulario que tiene que hacer el pendejo del donovan 🤑-->
        <br>




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
                    echo "<tr><th>ID</th><th>Zoom URL</th><th>Meeting Name</th><th>Meeting Date</th><th>Grupo ID</th></tr>";

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . $row['meeting_id'] . "</td>";
                        echo "<td>" . $row['zoom_url'] . "</td>";
                        echo "<td>" . $row['meeting_name'] . "</td>";
                        echo "<td>" . $row['meeting_date'] . "</td>";
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


        <?php
        include 'conexion.php';

        try {
            $grupo_id = $_GET['id']; // Suponiendo que recibes el ID del grupo por GET

            $stmt = $conn->prepare("
        SELECT usuarios.nombre as nombre_usuario
        FROM usuarios_grupos
        INNER JOIN usuarios ON usuarios_grupos.usuario_id = usuarios.id
        WHERE usuarios_grupos.grupo_id = :grupo_id AND usuarios_grupos.estado = 'aprobada'
    ");
            $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
            $stmt->execute();
            $usuarios_en_grupo = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Mostrar los usuarios en el grupo
            if (count($usuarios_en_grupo) > 0) {
                echo "<h2>Usuarios en el Grupo</h2>";
                echo "<ul>";
                foreach ($usuarios_en_grupo as $usuario) {
                    echo "<li>{$usuario['nombre_usuario']}</li>";
                }
                echo "</ul>";
            } else {
                echo "No hay usuarios en este grupo.";
            }
        } catch (PDOException $e) {
            echo "Error al obtener usuarios del grupo: " . $e->getMessage();
        }

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
    <script>
        $(document).ready(function() {
            // Utilizando jQuery para la petición AJAX
            $.ajax({
                url: 'obtener_pendientes.php', // Ruta al archivo PHP que acabamos de crear
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.total !== undefined) {
                        // Actualizar el contador de notificaciones
                        document.getElementById('contadorNotificaciones').textContent = response.total;
                    } else {
                        console.error('Error al obtener la cantidad de usuarios pendientes:', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición AJAX:', error);
                }
            });
        });
    </script>

    <!-- Agregar Bootstrap JS y dependencias -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>