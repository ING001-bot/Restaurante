<?php
session_start();
include("db.php");

// Validación de acceso
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$cargo = $_SESSION['cargo'];

// Crear cliente
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "INSERT INTO Cliente (Nombre, Telefono, Direccion) 
            VALUES ('$nombre', '$telefono', '$direccion')";
    $conn->query($sql);
    header("Location: clientes.php");
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Cliente WHERE ClienteID=$id";
    $conn->query($sql);
    header("Location: clientes.php");
}

// Editar cliente
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE Cliente 
            SET Nombre='$nombre', Telefono='$telefono', Direccion='$direccion' 
            WHERE ClienteID=$id";
    $conn->query($sql);
    header("Location: clientes.php");
}

// Listado clientes
$result = $conn->query("SELECT * FROM Cliente");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand">🍽 Restaurante - Clientes</span>
    <a href="<?php echo ($cargo=='Admin') ? 'dashboard_admin.php' : 'dashboard_empleado.php'; ?>" class="btn btn-secondary">Volver</a>
    <a href="logout.php" class="btn btn-danger">Salir</a>
</nav>

<div class="container mt-4">
    <h2>Gestión de Clientes</h2>

    <!-- Formulario nuevo cliente -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="telefono" class="form-control" placeholder="Teléfono" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="direccion" class="form-control" placeholder="Dirección" required>
        </div>
        <div class="col-md-1">
            <button type="submit" name="guardar" class="btn btn-success w-100">➕</button>
        </div>
    </form>

    <!-- Tabla clientes -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['ClienteID']; ?></td>
                <td><?php echo $row['Nombre']; ?></td>
                <td><?php echo $row['Telefono']; ?></td>
                <td><?php echo $row['Direccion']; ?></td>
                <td>
                    <!-- Botón editar -->
                    <button class="btn btn-warning btn-sm" 
                        onclick="editar('<?php echo $row['ClienteID']; ?>','<?php echo $row['Nombre']; ?>','<?php echo $row['Telefono']; ?>','<?php echo $row['Direccion']; ?>')">
                        ✏️
                    </button>

                    <!-- Botón eliminar -->
                    <a href="clientes.php?eliminar=<?php echo $row['ClienteID']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar cliente?')">🗑️</a>
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
          <h5 class="modal-title">Editar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control">
            </div>
            <div class="mb-3">
                <label>Teléfono</label>
                <input type="text" name="telefono" id="edit_telefono" class="form-control">
            </div>
            <div class="mb-3">
                <label>Dirección</label>
                <input type="text" name="direccion" id="edit_direccion" class="form-control">
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
function editar(id,nombre,telefono,direccion){
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_nombre").value = nombre;
    document.getElementById("edit_telefono").value = telefono;
    document.getElementById("edit_direccion").value = direccion;
    var modal = new bootstrap.Modal(document.getElementById("modalEditar"));
    modal.show();
}
</script>
</body>
</html>
