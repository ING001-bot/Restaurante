<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['cargo'], ['Admin', 'Empleado'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand">🍽 Restaurante - Admin</span>
    <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
</nav>

<div class="container mt-4">
    <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?> (Administrador)</h2>

    <div class="row mt-4">
        <div class="col-md-3">
            <a href="cliente.php" class="btn btn-outline-primary w-100 mb-2">Clientes</a>
            <a href="empleado.php" class="btn btn-outline-primary w-100 mb-2">Empleados</a>
            <a href="producto.php" class="btn btn-outline-primary w-100 mb-2">Productos</a>
            <a href="mesa.php" class="btn btn-outline-primary w-100 mb-2">Mesas</a>
            <a href="estado_producto.php" class="btn btn-outline-warning w-100 mb-2">Cambiar Estado Producto</a>
        </div>
        <div class="col-md-3">
            <a href="pedido.php" class="btn btn-outline-success w-100 mb-2">Pedidos</a>
            <a href="pago.php" class="btn btn-outline-success w-100 mb-2">Pagos</a>
            <a href="factura.php" class="btn btn-outline-success w-100 mb-2">Facturas</a>
            <a href="boleta.php" class="btn btn-outline-success w-100 mb-2">Boletas</a>
        </div>
    </div>
</div>
</body>
</html>
