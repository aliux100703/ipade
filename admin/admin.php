<?php

session_start();

if (!isset($_SESSION['rol'])) {
    header('location:./../login/login.php');
} else {
    if ($_SESSION['rol'] != 1) {
        header('location:./../login/login.php');
    }
}
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = 'Usuario no encontrado';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Panel de Administrador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <a class="nav-link btn btn-danger" href="./../login/cerrar_sesion.php">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav>


    <!-- Modal de Notificaciones -->
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
                            // Consultar notificaciones pendientes
                            $stmt = $conn->prepare("SELECT * FROM usuarios_grupos WHERE estado = 'pendiente'");
                            $stmt->execute();
                            $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Mostrar notificaciones
                            if (count($notificaciones) > 0) {
                                foreach ($notificaciones as $notificacion) {
                                    echo "<li class='list-group-item'>Nueva solicitud para unirse al grupo {$notificacion['grupo_id']}";

                                    echo "<form method='post' action='procesar_solicitud.php'>";
                                    echo "<input type='hidden' name='solicitud_id' value='{$notificacion['id']}'>";
                                    echo "<button type='submit' name='accion' value='aceptar'>Aceptar</button>";
                                    echo "<button type='submit' name='accion' value='rechazar'>Rechazar</button>";
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

    <div class="container">
        <center>
            <h2>Bienvenido, <?php echo $username; ?></h2>
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
                        echo '<a href="ver_grupo.php?id=' . $row['id'] . '" class="btn btn-primary">Ver Grupo</a>';
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script>
        // Supongamos que tienes una variable `numNotificaciones` que contiene el número de notificaciones
        var numNotificaciones = 3; // Esto debería venir de tu base de datos o de donde obtengas las notificaciones

        // Actualizar el contador
        document.getElementById('contadorNotificaciones').textContent = numNotificaciones;
    </script>

</body>

</html>