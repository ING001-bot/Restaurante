<?php
session_start();
include("db.php");

// Verificar login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['cargo'];

// Crear pedido (Empleado o Admin)
if (isset($_POST['crear_pedido'])) {
    $cliente_id = $_POST['cliente_id'];
    $empleado_id = $_SESSION['empleado_id']; // empleado logueado
    $fecha = date("Y-m-d H:i:s");

    // Insertar pedido
    $sql = "INSERT INTO Pedidos (ClienteID, EmpleadoID, Fecha) VALUES ('$cliente_id','$empleado_id','$fecha')";
    $conn->query($sql);
    $pedido_id = $conn->insert_id;

    // Insertar detalle
    foreach ($_POST['plato_id'] as $key => $plato_id) {
        $cantidad = $_POST['cantidad'][$key];
        if ($cantidad > 0) {
            $precio = $_POST['precio'][$key];
            $sql = "INSERT INTO DetallesPedido (PedidoID, PlatoID, Cantidad, PrecioUnitario) 
                    VALUES ('$pedido_id','$plato_id','$cantidad','$precio')";
            $conn->query($sql);
        }
    }
    header("Location: pedidos.php");
    exit();
}

// Eliminar pedido (solo Admin)
if (isset($_GET['eliminar']) && $rol == "Admin") {
    $id = $_GET['eliminar'];
    $conn->query("DELETE FROM DetallesPedido WHERE PedidoID=$id");
    $conn->query("DELETE FROM Pedidos WHERE PedidoID=$id");
    header("Location: pedidos.php");
}

// Consultar pedidos
$sql = "SELECT p.PedidoID, c.Nombre as Cliente, e.Nombre as Empleado, p.Fecha 
        FROM Pedidos p 
        JOIN Clientes c ON p.ClienteID=c.ClienteID
        JOIN Empleados e ON p.EmpleadoID=e.EmpleadoID
        ORDER BY p.Fecha DESC";
$pedidos = $conn->query($sql);

// Consultar clientes y platos
$clientes = $conn->query("SELECT * FROM Clientes");
$platos = $conn->query("SELECT * FROM Platos");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Pedidos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
  <span class="navbar-brand">🧾 Pedidos</span>
  <?php if ($rol=="Admin") { ?>
    <a href="dashboard_admin.php" class="btn btn-secondary">Volver</a>
  <?php } else { ?>
    <a href="dashboard_empleado.php" class="btn btn-secondary">Volver</a>
  <?php } ?>
  <a href="logout.php" class="btn btn-danger">Salir</a>
</nav>

<div class="container mt-4">
  <h2>Crear Pedido</h2>
  <form method="POST">
    <div class="mb-3">
      <label>Cliente</label>
      <select name="cliente_id" class="form-control" required>
        <?php while($c = $clientes->fetch_assoc()) { ?>
          <option value="<?php echo $c['ClienteID']; ?>"><?php echo $c['Nombre']; ?></option>
        <?php } ?>
      </select>
    </div>

    <h5>Platos</h5>
    <table class="table">
      <thead>
        <tr>
          <th>Plato</th>
          <th>Precio</th>
          <th>Cantidad</th>
        </tr>
      </thead>
      <tbody>
        <?php while($p = $platos->fetch_assoc()) { ?>
        <tr>
          <td>
            <?php echo $p['Nombre']; ?>
            <input type="hidden" name="plato_id[]" value="<?php echo $p['PlatoID']; ?>">
          </td>
          <td>
            S/. <?php echo number_format($p['Precio'],2); ?>
            <input type="hidden" name="precio[]" value="<?php echo $p['Precio']; ?>">
          </td>
          <td>
            <input type="number" name="cantidad[]" value="0" min="0" class="form-control" style="width:80px;">
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <button type="submit" name="crear_pedido" class="btn btn-success">➕ Guardar Pedido</button>
  </form>

  <hr>
  <h2>Listado de Pedidos</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Empleado</th>
        <th>Fecha</th>
        <th>Detalle</th>
        <?php if ($rol=="Admin") { ?><th>Acciones</th><?php } ?>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $pedidos->fetch_assoc()) { ?>
      <tr>
        <td><?php echo $row['PedidoID']; ?></td>
        <td><?php echo $row['Cliente']; ?></td>
        <td><?php echo $row['Empleado']; ?></td>
        <td><?php echo $row['Fecha']; ?></td>
        <td><a href="ver_pedido.php?id=<?php echo $row['PedidoID']; ?>" class="btn btn-info btn-sm">👁️ Ver</a></td>
        <?php if ($rol=="Admin") { ?>
          <td><a href="pedidos.php?eliminar=<?php echo $row['PedidoID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar pedido?')">🗑️</a></td>
        <?php } ?>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>
