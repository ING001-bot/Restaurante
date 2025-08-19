<?php
session_start();
include("conexion.php");

// Validar que sea Admin
if (!isset($_SESSION['usuario']) || $_SESSION['cargo'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Crear empleado
if (isset($_POST['guardar'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cargo = $_POST['cargo'];
    $clave = $_POST['clave'];
    $telefono = $_POST['telefono'];
    $estado = 'Activo';

    $sql = "INSERT INTO Empleado (Nombre, Apellido, Cargo, contraseña, Telefono, Estado) 
            VALUES ('$nombre','$apellido','$cargo','$clave','$telefono','$estado')";
    $conn->query($sql);
    header("Location: empleado.php");
}

// Eliminar empleado
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $sql = "DELETE FROM Empleado WHERE IdEmpleado=$id";
    $conn->query($sql);
    header("Location: empleado.php");
}

// Editar empleado
if (isset($_POST['editar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cargo = $_POST['cargo'];
    $clave = $_POST['clave'];
    $telefono = $_POST['telefono'];
    $estado = $_POST['estado'];

    $sql = "UPDATE Empleado 
            SET Nombre='$nombre', Apellido='$apellido', Cargo='$cargo', contraseña='$clave', Telefono='$telefono', Estado='$estado' 
            WHERE IdEmpleado=$id";
    $conn->query($sql);
    header("Location: empleado.php");
}

// Listado
$result = $conn->query("SELECT * FROM Empleado");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark p-3">
    <span class="navbar-brand">👨‍🍳 Administración de Empleados</span>
    <a href="dashboard_admin.php" class="btn btn-secondary">Volver</a>
    <a href="logout.php" class="btn btn-danger">Salir</a>
</nav>

<div class="container mt-4">
    <h2>Gestión de Empleados</h2>

    <!-- Formulario nuevo empleado -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-2">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
        </div>
        <div class="col-md-2">
            <input type="password" name="clave" class="form-control" placeholder="Contraseña" required>
        </div>
        <div class="col-md-2">
            <input type="text" name="telefono" class="form-control" placeholder="Teléfono" required>
        </div>
        <div class="col-md-2">
            <select name="cargo" class="form-control" required>
                <option value="Empleado">Empleado</option>
                <option value="Admin">Admin</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" name="guardar" class="btn btn-success w-100">➕</button>
        </div>
    </form>

    <!-- Tabla empleados -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Cargo</th>
                <th>Contraseña</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['IdEmpleado']; ?></td>
                <td><?php echo $row['Nombre']; ?></td>
                <td><?php echo $row['Apellido']; ?></td>
                <td><?php echo $row['Cargo']; ?></td>
                <td><?php echo $row['contraseña']; ?></td>
                <td><?php echo $row['Telefono']; ?></td>
                <td><?php echo $row['Estado']; ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" 
                        onclick="editar('<?php echo $row['IdEmpleado']; ?>','<?php echo $row['Nombre']; ?>','<?php echo $row['Apellido']; ?>','<?php echo $row['Cargo']; ?>','<?php echo $row['contraseña']; ?>','<?php echo $row['Telefono']; ?>','<?php echo $row['Estado']; ?>')">✏️</button>
                    <a href="empleado.php?eliminar=<?php echo $row['IdEmpleado']; ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar empleado?')">🗑️</a>
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
          <h5 class="modal-title">Editar Empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control">
            </div>
            <div class="mb-3">
                <label>Apellido</label>
                <input type="text" name="apellido" id="edit_apellido" class="form-control">
            </div>
            <div class="mb-3">
                <label>Cargo</label>
                <select name="cargo" id="edit_cargo" class="form-control">
                    <option value="Empleado">Empleado</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Contraseña</label>
                <input type="text" name="clave" id="edit_clave" class="form-control">
            </div>
            <div class="mb-3">
                <label>Teléfono</label>
                <input type="text" name="telefono" id="edit_telefono" class="form-control">
            </div>
            <div class="mb-3">
                <label>Estado</label>
                <input type="text" name="estado" id="edit_estado" class="form-control">
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
function editar(id,nombre,apellido,cargo,clave,telefono,estado){
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_nombre").value = nombre;
    document.getElementById("edit_apellido").value = apellido;
    document.getElementById("edit_cargo").value = cargo;
    document.getElementById("edit_clave").value = clave;
    document.getElementById("edit_telefono").value = telefono;
    document.getElementById("edit_estado").value = estado;
    var modal = new bootstrap.Modal(document.getElementById("modalEditar"));
    modal.show();
}
</script>
</body>
</html>
