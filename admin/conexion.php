<?php
$servername = "127.0.0.1";
$username = "root";
$password = "100703al";
$dbname = "ipade";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Habilitar excepciones en caso de errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
} catch(PDOException $e) {
    echo "Conexión fallida: " . $e->getMessage();
}
?>
