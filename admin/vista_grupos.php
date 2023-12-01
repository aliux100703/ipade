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
    <title>Grupos </title>
    <link rel="Website Icon" type="png" href="icon.png">
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
    <!--container-->
    <div class="container">
        <?php
        include 'conexion.php';

        // Verificar si se ha enviado un formulario para crear un grupo
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre_grupo = $_POST['nombre_grupo'];

            $sql = "INSERT INTO grupos (nombre_grupo) VALUES (:nombre_grupo)";

            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':nombre_grupo', $nombre_grupo, PDO::PARAM_STR);
                $stmt->execute();
                header("Location: vista_grupos.php");
                exit();
            } catch (PDOException $e) {
                $error = "Error al crear el grupo: " . $e->getMessage();
            }
        }

        // Obtener la lista de grupos
        $sql = "SELECT * FROM grupos";
        $result = $conn->query($sql);

        ?>


        <h1 class="mt-4">Crear Grupo</h1>

        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nombre_grupo">Nombre del Grupo:</label>
                <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo" required>
            </div>
            <button type="submit" class="btn btn-primary">Crear Grupo</button>
        </form>

        <!-- Desplegar la lista de grupos en una tabla -->
        <h2 class="mt-4">Lista de Grupos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Grupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'conexion.php';

                // Obtener la lista de grupos
                $sql = "SELECT * FROM grupos";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($result) > 0) {
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['nombre_grupo']}</td>";
                        echo "<td>
        <a href='ver_grupo.php?id={$row['id']}' class='btn btn-info'>Ver Grupo</a>
        <button type='button' class='btn btn-danger' onclick='confirmarEliminar({$row['id']})'>Eliminar</button>
      </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay grupos registrados</td></tr>";
                }
                ?>





            </tbody>
        </table>

        <!-- Modal de Confirmación de Eliminación -->
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
                        ¿Estás seguro de que quieres eliminar este grupo?
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
                    window.location.href = "eliminar_grupo.php?id=" + id;
                });
            }
        </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>