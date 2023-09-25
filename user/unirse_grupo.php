<?php
include 'conexion.php';
// ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verifica si se han recibido los IDs necesarios
    if(isset($_POST['grupo_id']) && isset($_POST['usuario_id'])) {
        $grupo_id = $_POST['grupo_id'];
        $usuario_id = $_POST['usuario_id'];

        // Validar y sanitizar los datos antes de ejecutar la consulta
        // Asegúrate de que $usuario_id es un número entero válido
        $usuario_id = filter_var($usuario_id, FILTER_VALIDATE_INT);

        if ($usuario_id === false) {
            $error = "El ID de usuario no es válido.";
        } else {
            try {
                // Insertar los IDs en tu base de datos utilizando una consulta preparada
                $stmt = $conn->prepare("INSERT INTO usuarios_grupos (usuario_id, grupo_id) VALUES (:usuario_id, :grupo_id)");
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $mensaje = "Te has unido al grupo correctamente";
                } else {
                    $error = "Error al unirte al grupo: " . $stmt->errorInfo()[2];
                }
            } catch (PDOException $e) {
                $error = "Error al unirte al grupo: " . $e->getMessage();
            }
        }
    } else {
        $error = "Los IDs de grupo y usuario no son válidos.";
    }
} else {
    $error = "Se ha intentado acceder a este archivo de forma incorrecta.";
}

// ...
?>
