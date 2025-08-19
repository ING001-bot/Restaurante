<?php
$host = "localhost";
$user = "root";  // tu usuario MySQL
$pass = "";      // tu contraseña MySQL
$db   = "Restaurante";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
