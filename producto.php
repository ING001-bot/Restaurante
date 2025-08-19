<?php
session_start();
include("conexion.php");

// Solo Admin puede gestionar productos
if (!isset($_SESSION['usuario']) || ($_SESSION['cargo'] != 'Admin' && $_SESSION['cargo'] != 'Empleado')) {
    header("Location: login.php");
    exit();
}

// Crear producto
if (isset($_POST['guardar'])) {
    $nombre = trim($_POST['nombre']);
    $tipo = trim($_POST['tipo']);
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $mensaje = "";

    // Validar duplicado por nombre
    $check = $conn->query("SELECT IdProducto FROM Producto WHERE Nombre='$nombre'");
    if ($check && $check->num_rows > 0) {
        $mensaje = "❌ Ya existe un producto con ese nombre.";
    } else {
        $sql = "INSERT INTO Producto (Nombre, Tipo, Precio, Stock) VALUES ('$nombre','$tipo','$precio','$stock')";
        if ($conn->query($sql)) {
            $mensaje = "✅ Producto registrado correctamente.";
        } else {
            $mensaje = "❌ Error al registrar producto: " . $conn->error;
        }
    }
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Producto WHERE IdProducto=$id";
    $conn->query($sql);
    header("Location: producto.php");
}

// Editar producto
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];

    $sql = "UPDATE Producto SET Nombre='$nombre', Tipo='$tipo', Precio='$precio', Stock='$stock' WHERE IdProducto=$id";
    $conn->query($sql);
    header("Location: producto.php");
}

// Listado
$result = $conn->query("SELECT * FROM Producto ORDER BY IdProducto DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand">🍽️ Administración de Productos</span>
    <a href="dashboard_admin.php" class="btn btn-secondary">Volver</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['IdProducto']; ?></td>
                    <td><?php echo $row['Nombre']; ?></td>
                    <td><?php echo $row['Tipo']; ?></td>
                    <td><?php echo $row['Precio']; ?></td>
                    <td><?php echo $row['Stock']; ?></td>
                    <td><?php echo isset($row['Estado']) ? $row['Estado'] : 'Pendiente'; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_estado" value="<?php echo $row['IdProducto']; ?>">
                            <input type="hidden" name="estado_actual" value="<?php echo isset($row['Estado']) ? $row['Estado'] : 'Pendiente'; ?>">
                            <button type="submit" name="cambiar_estado" class="btn btn-sm btn-secondary">
                                <?php echo (isset($row['Estado']) && $row['Estado'] == 'Pendiente') ? 'Marcar como Pagado' : 'Marcar como Pendiente'; ?>
                            </button>
                        </form>
                        <button class="btn btn-warning btn-sm" 
                            onclick="editar('<?php echo $row['IdProducto']; ?>','<?php echo $row['Nombre']; ?>','<?php echo $row['Tipo']; ?>','<?php echo $row['Precio']; ?>','<?php echo $row['Stock']; ?>')">✏️</button>
                        <a href="producto.php?eliminar=<?php echo $row['IdProducto']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Eliminar producto?')">🗑️</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>

    <!-- Tabla productos -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['IdProducto']; ?></td>
                <td><?php echo $row['Nombre']; ?></td>
                <td><?php echo $row['Tipo']; ?></td>
                <td><?php echo $row['Precio']; ?></td>
                <td><?php echo $row['Stock']; ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" 
                        onclick="editar('<?php echo $row['IdProducto']; ?>','<?php echo $row['Nombre']; ?>','<?php echo $row['Tipo']; ?>','<?php echo $row['Precio']; ?>','<?php echo $row['Stock']; ?>')">✏️</button>
                    <a href="producto.php?eliminar=<?php echo $row['IdProducto']; ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar producto?')">🗑️</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal edición -->
<div class="modal" id="modalEditar" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Editar Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control">
            </div>
            <div class="mb-3">
                <label>Tipo</label>
                <input type="text" name="tipo" id="edit_tipo" class="form-control">
            </div>
            <div class="mb-3">
                <label>Precio</label>
                <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control">
            </div>
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" id="edit_stock" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editar(id,nombre,tipo,precio,stock){
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_nombre").value = nombre;
    document.getElementById("edit_tipo").value = tipo;
    document.getElementById("edit_precio").value = precio;
    document.getElementById("edit_stock").value = stock;
    var modal = new bootstrap.Modal(document.getElementById("modalEditar"));
    modal.show();
}
</script>
</body>
</html>
