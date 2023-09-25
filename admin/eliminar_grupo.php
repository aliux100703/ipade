<?php
include 'conexion.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $grupo_id = $_GET['id'];

    try {
        // Iniciar una transacción
        $conn->beginTransaction();

        // Eliminar las reuniones asociadas al grupo
        $sql_eliminar_reuniones = "DELETE FROM reuniones WHERE grupo_id = :grupo_id";
        $stmt_eliminar_reuniones = $conn->prepare($sql_eliminar_reuniones);
        $stmt_eliminar_reuniones->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
        $stmt_eliminar_reuniones->execute();

        // Eliminar el grupo
        $sql_eliminar_grupo = "DELETE FROM grupos WHERE id = :grupo_id";
        $stmt_eliminar_grupo = $conn->prepare($sql_eliminar_grupo);
        $stmt_eliminar_grupo->bindParam(':grupo_id', $grupo_id, PDO::PARAM_INT);
        $stmt_eliminar_grupo->execute();

        // Confirmar la transacción
        $conn->commit();

        header("Location: vista_grupos.php");
        exit();
    } catch(PDOException $e) {
        // Revertir la transacción en caso de error
        $conn->rollBack();
        echo "Error al eliminar el grupo: " . $e->getMessage();
    }
} else {
    echo "Parámetro de identificación de grupo no válido.";
    exit();
}
?>

