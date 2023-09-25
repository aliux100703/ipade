<?php
session_start();
echo "Script ejecutado correctamente.";
if ($_SESSION['rol'] === 2 && isset($_POST['grupo_id'])) {
    $grupoId = $_POST['grupo_id'];
    $usuarioId = $_SESSION['id'];

    include 'conexion.php';

    try {
        $stmt = $conn->prepare("INSERT INTO usuarios_grupos (usuario_id, grupo_id, estado) VALUES (?, ?, 'pendiente')");
        $stmt->execute([$usuarioId, $grupoId]);

        // Enviar notificación al administrador
        $mensaje = "El usuario {$_SESSION['nombre']} ha enviado una solicitud para unirse al grupo {$grupoId}.";
        $stmt = $conn->prepare("INSERT INTO notificaciones (mensaje, usuario_id) VALUES (?, 1)"); // 1 es el ID del administrador
        $stmt->execute([$mensaje]);

        echo "Solicitud enviada con éxito";
    } catch (PDOException $e) {
        echo "Error al enviar la solicitud: " . $e->getMessage();
    }
} else {
    echo "No tienes permiso para realizar esta acción";
}
