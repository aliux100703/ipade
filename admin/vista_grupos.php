
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

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>