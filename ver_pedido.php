<?php
session_start();
include("db.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT p.PedidoID, c.Nombre as Cliente, e.Nombre as Empleado, p.Fecha 
        FROM Pedidos p
        JOIN Clientes c ON p.ClienteID=c.ClienteID
        JOIN Empleados e ON p.EmpleadoID=e.EmpleadoID
        WHERE p.PedidoID=$id";
$pedido = $conn->query($sql)->fetch_assoc();

$detalles = $conn->query("SELECT d.Cantidad, d.PrecioUnitario, pl.Nombre 
                          FROM DetallesPedido d 
                          JOIN Platos pl ON d.PlatoID=pl.PlatoID
                          WHERE d.PedidoID=$id");

$clientes_result = $conn->query("SELECT * FROM Cliente");
$clientes_array = [];
while($c = $clientes_result->fetch_assoc()) {
    $clientes_array[] = $c;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle Pedido</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
  <h2>Detalle del Pedido #<?php echo $pedido['PedidoID']; ?></h2>
  <p><strong>Cliente:</strong> <?php echo $pedido['Cliente']; ?></p>
  <p><strong>Empleado:</strong> <?php echo $pedido['Empleado']; ?></p>
  <p><strong>Fecha:</strong> <?php echo $pedido['Fecha']; ?></p>

  <h4>Platos</h4>
  <table class="table">
    <thead>
      <tr>
        <th>Plato</th>
        <th>Cantidad</th>
        <th>Precio Unitario</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $total = 0;
      while($d = $detalles->fetch_assoc()) {
        $sub = $d['Cantidad'] * $d['PrecioUnitario'];
        $total += $sub;
      ?>
      <tr>
        <td><?php echo $d['Nombre']; ?></td>
        <td><?php echo $d['Cantidad']; ?></td>
        <td>S/. <?php echo number_format($d['PrecioUnitario'],2); ?></td>
        <td>S/. <?php echo number_format($sub,2); ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3">TOTAL</th>
        <th>S/. <?php echo number_format($total,2); ?></th>
      </tr>
    </tfoot>
  </table>

  <a href="pedidos.php" class="btn btn-secondary">⬅️ Volver</a>

  <h4>Clientes</h4>
  <select class="form-select">
    <?php foreach($clientes_array as $c) { ?>
        <option value="<?php echo $c['IdCliente']; ?>"><?php echo $c['Nombre']." ".$c['Apellido']; ?></option>
    <?php } ?>
  </select>
</body>
</html>
