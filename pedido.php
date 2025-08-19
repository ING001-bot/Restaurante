<?php
session_start();
include("conexion.php");

// Verificar login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['cargo'];
$empleado_id = $_SESSION['IdEmpleado'] ?? null;

$mensaje = "";

// Crear pedido
if (isset($_POST['crear_pedido'])) {
    $cliente_id = $_POST['cliente_id'];
    $mesa_id = $_POST['mesa_id'];
    $fecha = date("Y-m-d");
    $hora  = date("H:i:s");

    $stmt = $conn->prepare("INSERT INTO Pedido (IdCliente, IdEmpleado, IdMesa, Fecha, Hora, Estado) VALUES (?, ?, ?, ?, ?, 'Pendiente')");
    $stmt->bind_param("iiiss", $cliente_id, $empleado_id, $mesa_id, $fecha, $hora);
    if ($stmt->execute()) {
        $pedido_id = $stmt->insert_id;
        $stmt->close();

        if(isset($_POST['producto_id'])) {
            foreach ($_POST['producto_id'] as $key => $producto_id) {
                $cantidad = (int) $_POST['cantidad'][$key];
                if ($cantidad > 0) {
                    $stmt_det = $conn->prepare("INSERT INTO DetallePedido (IdPedido, IdProducto, Cantidad, Estado) VALUES (?, ?, ?, 'Pendiente')");
                    $stmt_det->bind_param("iii", $pedido_id, $producto_id, $cantidad);
                    $stmt_det->execute();
                    $stmt_det->close();
                }
            }
        }
        $mensaje = "✅ Pedido registrado correctamente";
    } else {
        $mensaje = "❌ Error al registrar pedido: " . $conn->error;
    }
}

// Eliminar pedido (solo Admin)
if (isset($_GET['eliminar']) && $rol == "Admin") {
    $id = (int) $_GET['eliminar'];
    $conn->query("DELETE FROM DetallePedido WHERE IdPedido=$id");
    $conn->query("DELETE FROM Pedido WHERE IdPedido=$id");
    $mensaje = "🗑️ Pedido eliminado correctamente";
}

// Consultar pedidos
$sql = "SELECT p.IdPedido, c.Nombre AS Cliente, e.Nombre AS Empleado, m.Nombre AS Mesa, p.Fecha, p.Hora, p.Estado
    FROM Pedido p
    LEFT JOIN Cliente c ON p.IdCliente=c.IdCliente
    LEFT JOIN Empleado e ON p.IdEmpleado=e.IdEmpleado
    LEFT JOIN Mesa m ON p.IdMesa=m.IdMesa
    ORDER BY p.Fecha DESC, p.Hora DESC";
$pedidos = $conn->query($sql);

// Consultar clientes, mesas y productos
$clientes_result = $conn->query("SELECT * FROM Cliente");
$clientes_array = [];
while($c = $clientes_result->fetch_assoc()) {
    $clientes_array[] = $c;
}

$mesas_result = $conn->query("SELECT * FROM Mesa");
$mesas_array = [];
while($m = $mesas_result->fetch_assoc()) {
    $mesas_array[] = $m;
}

$productos_result = $conn->query("SELECT * FROM Producto");
$productos_array = [];
while($p = $productos_result->fetch_assoc()) {
    $productos_array[] = $p;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel Pedidos - Restaurante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark p-3 d-flex justify-content-between">
    <span class="navbar-brand fs-4"><i class="bi bi-receipt-cutoff"></i> Panel Pedidos</span>
    <div>
        <a href="dashboard_<?php echo strtolower($rol); ?>.php" class="btn btn-secondary me-2">Volver</a>
        <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</nav>

<div class="container mt-5">

<?php if($mensaje!="") { ?>
  <div class="alert alert-info text-center"><?php echo $mensaje; ?></div>
<?php } ?>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm p-3 mb-4">
            <h5>Crear Nuevo Pedido</h5>
            <form method="POST">
                <div class="mb-3">
                    <label>Cliente</label>
                    <select name="cliente_id" class="form-control" required <?php echo empty($clientes_array) ? 'disabled' : ''; ?>>
                        <?php if(empty($clientes_array)) { ?>
                            <option value="">No hay clientes registrados</option>
                        <?php } else { foreach($clientes_array as $c) { ?>
                            <option value="<?php echo $c['IdCliente']; ?>"><?php echo $c['Nombre']." ".$c['Apellido']; ?></option>
                        <?php }} ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Mesa</label>
                    <select name="mesa_id" class="form-control" required <?php echo empty($mesas_array) ? 'disabled' : ''; ?>>
                        <?php if(empty($mesas_array)) { ?>
                            <option value="">No hay mesas registradas</option>
                        <?php } else { foreach($mesas_array as $m) { ?>
                            <option value="<?php echo $m['IdMesa']; ?>"><?php echo $m['Nombre']; ?></option>
                        <?php }} ?>
                    </select>
                </div>

                <!-- Selección dinámica de productos -->
                <h6>Productos</h6>
                <div class="mb-3 d-flex">
                    <select id="productoSelect" class="form-control me-2">
                        <option value="">-- Selecciona un producto --</option>
                        <?php foreach($productos_array as $p) { ?>
                            <option value="<?php echo $p['IdProducto'].'|'.$p['Nombre'].'|'.$p['Precio']; ?>">
                                <?php echo $p['Nombre'].' (S/. '.number_format($p['Precio'],2).')'; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button type="button" class="btn btn-primary" onclick="agregarProducto()">Agregar</button>
                </div>

                <table class="table table-bordered table-sm" id="productosTable">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Productos seleccionados aparecerán aquí -->
                    </tbody>
                </table>

                <button type="submit" name="crear_pedido" class="btn btn-success w-100 mt-3" <?php echo (empty($clientes_array) || empty($mesas_array) || empty($productos_array)) ? 'disabled' : ''; ?>>Guardar Pedido</button>
<?php if(empty($clientes_array) || empty($mesas_array) || empty($productos_array)) { ?>
    <div class="alert alert-warning mt-3 text-center">
        <?php if(empty($clientes_array)) echo '⚠️ Debes registrar al menos un cliente.<br>';
              if(empty($mesas_array)) echo '⚠️ Debes registrar al menos una mesa.<br>';
              if(empty($productos_array)) echo '⚠️ Debes registrar al menos un producto.'; ?>
    </div>
<?php } ?>
            </form>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm p-3 mb-4">
            <h5>Pedidos Recientes</h5>
            <div class="table-responsive" style="max-height:400px; overflow-y:auto;">
                <table class="table table-hover table-sm text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th><th>Cliente</th><th>Empleado</th><th>Mesa</th><th>Fecha</th><th>Hora</th><th>Estado</th><th>Productos</th>
                            <?php if($rol=="Admin"){ ?><th>Acciones</th><?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $pedidos->data_seek(0);
                    if ($pedidos->num_rows > 0) {
                        while($row = $pedidos->fetch_assoc()) {
                            // Consultar productos del pedido
                            $productos = [];
                            $detalles = $conn->query("SELECT d.Cantidad, p.Nombre, p.Precio FROM DetallePedido d JOIN Producto p ON d.IdProducto=p.IdProducto WHERE d.IdPedido=".$row['IdPedido']);
                            if ($detalles && $detalles->num_rows > 0) {
                                while($det = $detalles->fetch_assoc()) {
                                    $productos[] = $det['Nombre']." (S/. ".number_format($det['Precio'],2)." x ".$det['Cantidad'].")";
                                }
                            }
                    ?>
                        <tr>
                            <td><?php echo $row['IdPedido']; ?></td>
                            <td><?php echo $row['Cliente']; ?></td>
                            <td><?php echo !empty($row['Empleado']) ? $row['Empleado'] : '<span class="text-muted">Sin empleado</span>'; ?></td>
                            <td><?php echo $row['Mesa']; ?></td>
                            <td><?php echo $row['Fecha']; ?></td>
                            <td><?php echo $row['Hora']; ?></td>
                            <td><?php echo $row['Estado']; ?></td>
                            <td><?php echo !empty($productos) ? implode('<br>', $productos) : '<span class="text-muted">Sin productos</span>'; ?></td>
                            <?php if($rol=="Admin"){ ?>
                            <td>
                                <a href="pedido.php?eliminar=<?php echo $row['IdPedido']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar pedido?')">Eliminar</a>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php } 
                    } else { ?>
                        <tr><td colspan="<?php echo ($rol=="Admin") ? 9 : 8; ?>" class="text-muted">No hay pedidos registrados</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<a href="dashboard_empleado.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>

<script>
function agregarProducto() {
    const select = document.getElementById('productoSelect');
    const valor = select.value;
    if (!valor) return;

    const [id, nombre, precio] = valor.split('|');

    const tbody = document.getElementById('productosTable').getElementsByTagName('tbody')[0];
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${nombre}<input type="hidden" name="producto_id[]" value="${id}"></td>
        <td>S/. ${parseFloat(precio).toFixed(2)}</td>
        <td><input type="number" name="cantidad[]" value="1" min="1" class="form-control"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">Eliminar</button></td>
    `;
    tbody.appendChild(row);

    select.selectedIndex = 0;
}
</script>

</body>
</html>

<?php
// Alterar tabla Producto para agregar columna Estado
$conn->query("ALTER TABLE Producto ADD Estado VARCHAR(20) DEFAULT 'Pendiente'");
?>
