<?php
include 'conexion.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    $user_id = $_POST['userId'];

    // Verifica si las contraseñas coinciden
    if ($nueva_contrasena == $confirmar_contrasena) {
        // Encripta la nueva contraseña
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

        // Actualiza la contraseña en la base de datos
        $stmt = $conn->prepare("UPDATE usuarios SET contrasena = :contrasena WHERE id = :id");
        $stmt->bindParam(':contrasena', $hashed_password);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        echo "¡Contraseña actualizada con éxito!";
    } else {
        echo "Las contraseñas no coinciden. Inténtalo de nuevo.";
    }
}
?>
