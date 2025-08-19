<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['cargo'] != 'Empleado') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand">🍽 Restaurante - Empleado</span>
    <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
</nav>

<div class="container mt-4">
    <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?> (Empleado)</h2>

    <div class="row mt-4">
        <div class="col-md-4">
            <a href="pedidos.php" class="btn btn-outline-primary w-100 mb-2">Gestionar Pedidos</a>
        </div>
        <div class="col-md-4">
            <a href="clientes.php" class="btn btn-outline-primary w-100 mb-2">Clientes</a>
        </div>
        <div class="col-md-4">
            <a href="mesas.php" class="btn btn-outline-primary w-100 mb-2">Mesas</a>
        </div>
    </div>
</div>
</body>
</html>
