<?php
session_start();
include("db.php");

// Validar login
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['cargo'];

// Solo Admin puede crear/editar/eliminar
if ($rol == "Admin") {
    // Crear plato
    if (isset($_POST['guardar'])) {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $sql = "INSERT INTO Platos (Nombre, Precio) VALUES ('$nombre','$precio')";
        $conn->query($sql);
        header("Location: platos.php");
    }

    // Eliminar
    if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        $sql = "DELETE FROM Platos WHERE PlatoID=$id";
        $conn->query($sql);
        header("Location: platos.php");
    }

    // Editar
    if (isset($_POST['editar'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $sql = "UPDATE Platos SET Nombre='$nombre', Precio='$precio' WHERE PlatoID=$id";
        $conn->query($sql);
        header("Location: platos.php");
    }
}

$result = $conn->query("SELECT * FROM Platos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú de Platos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand">🍽️ Menú del Restaurante</span>
    <?php if ($rol == "Admin") { ?>
        <a href="dashboard_admin.php" class="btn btn-secondary">Volver</a>
    <?php } else { ?>
        <a href="dashboard_empleado.php" class="btn btn-secondary">Volver</a>
    <?php } ?>
    <a href="logout.php" class="btn btn-danger">Salir</a>
</nav>

<div class="container mt-4">
    <h2>Listado de Platos</h2>

    <?php if ($rol == "Admin") { ?>
    <!-- Formulario nuevo plato -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre del plato" required>
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required>
        </div>
        <div class="col-md-3">
            <button type="submit" name="guardar" class="btn btn-success w-100">➕ Agregar</button>
        </div>
    </form>
    <?php } ?>

    <!-- Tabla de platos -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Plato</th>
                <th>Precio</th>
                <?php if ($rol == "Admin") { ?><th>Acciones</th><?php } ?>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['PlatoID']; ?></td>
                <td><?php echo $row['Nombre']; ?></td>
                <td>S/. <?php echo number_format($row['Precio'], 2); ?></td>
                <?php if ($rol == "Admin") { ?>
                <td>
                    <button class="btn btn-warning btn-sm" 
                        onclick="editar('<?php echo $row['PlatoID']; ?>','<?php echo $row['Nombre']; ?>','<?php echo $row['Precio']; ?>')">✏️</button>
                    <a href="platos.php?eliminar=<?php echo $row['PlatoID']; ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar este plato?')">🗑️</a>
                </td>
                <?php } ?>
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
          <h5 class="modal-title">Editar Plato</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control">
            </div>
            <div class="mb-3">
                <label>Precio</label>
                <input type="number" step="0.01" name="precio" id="edit_precio" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editar(id,nombre,precio){
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_nombre").value = nombre;
    document.getElementById("edit_precio").value = precio;
    var modal = new bootstrap.Modal(document.getElementById("modalEditar"));
    modal.show();
}
</script>
</body>
</html>
