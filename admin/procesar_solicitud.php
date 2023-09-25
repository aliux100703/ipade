<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if(isset($_POST['solicitud_id']) && isset($_POST['accion'])) {
        $solicitud_id = $_POST['solicitud_id'];
        $accion = $_POST['accion'];

        try {
            if ($accion === 'aceptar') {
                // Cambiar el estado de la solicitud a 'aprobada'
                $stmt = $conn->prepare("UPDATE usuarios_grupos SET estado = 'aprobada' WHERE id = :solicitud_id");
                $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
                $stmt->execute();
            } elseif ($accion === 'rechazar') {
                // Cambiar el estado de la solicitud a 'rechazada'
                $stmt = $conn->prepare("UPDATE usuarios_grupos SET estado = 'rechazada' WHERE id = :solicitud_id");
                $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            header('Location: admin.php'); // Redirecciona a la página de notificaciones
            exit();
        } catch (PDOException $e) {
            echo "Error al procesar la solicitud: " . $e->getMessage();
        }
    } else {
        echo "Datos no válidos recibidos.";
    }
} else {
    echo "Acceso no autorizado.";
}

$conn = null;
?>
