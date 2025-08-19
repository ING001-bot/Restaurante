<?php
session_start();
include("conexion.php");

// Validar acceso solo para empleados y administradores
if (!isset($_SESSION["rol"])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION["rol"];

// Crear Mesa
if (isset($_POST["crear"])) {
    $nombre = $_POST["nombre"];
    $capacidad = $_POST["capacidad"];
    $estado = $_POST["estado"];

    $sql = "INSERT INTO Mesa (Nombre, Capacidad, Estado) VALUES ('$nombre', '$capacidad', '$estado')";
    $conn->query($sql);
    header("Location: mesas.php");
    exit();
}

// Editar Mesa
if (isset($_POST["editar"])) {
    $id = $_POST["id"];
    $nombre = $_POST["nombre"];
    $capacidad = $_POST["capacidad"];
    $estado = $_POST["estado"];

    $sql = "UPDATE Mesa SET Nombre='$nombre', Capacidad='$capacidad', Estado='$estado' WHERE IdMesa=$id";
    $conn->query($sql);
    header("Location: mesas.php");
    exit();
}

// Eliminar Mesa
if (isset($_GET["eliminar"])) {
    $id = $_GET["eliminar"];
    $sql = "DELETE FROM Mesa WHERE IdMesa=$id";
    $conn->query($sql);
    header("Location: mesas.php");
    exit();
}

$result = $conn->query("SELECT * FROM Mesa");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Mesas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-4">

    <h2 class="mb-4">Gestión de Mesas</h2>

    <?php if ($rol == "admin") { ?>
    <!-- Formulario Crear Mesa -->
    <form method="post" class="mb-4">
        <div class="row">
            <div class="col">
                <input type="text" name="nombre" placeholder="Nombre Mesa" class="form-control" required>
            </div>
            <div class="col">
                <input type="number" name="capacidad" placeholder="Capacidad" class="form-control" required>
            </div>
            <div class="col">
                <select name="estado" class="form-control" required>
                    <option value="Libre">Libre</option>
                    <option value="Ocupada">Ocupada</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" name="crear" class="btn btn-success">Agregar</button>
            </div>
        </div>
    </form>
    <?php } ?>

    <!-- Tabla Mesas -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <?php if ($rol == "admin") { ?><th>Acciones</th><?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila["IdMesa"] ?></td>
                <td><?= $fila["Nombre"] ?></td>
                <td><?= $fila["Capacidad"] ?></td>
                <td><?= $fila["Estado"] ?></td>
                <?php if ($rol == "admin") { ?>
                <td>
                    <!-- Botón Editar -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editar<?= $fila["IdMesa"] ?>">Editar</button>
                    <a href="mesas.php?eliminar=<?= $fila["IdMesa"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar mesa?')">Eliminar</a>
                </td>
                <?php } ?>
            </tr>

            <!-- Modal Editar -->
            <div class="modal fade" id="editar<?= $fila["IdMesa"] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Mesa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $fila["IdMesa"] ?>">
                            <input type="text" name="nombre" value="<?= $fila["Nombre"] ?>" class="form-control mb-2" required>
                            <input type="number" name="capacidad" value="<?= $fila["Capacidad"] ?>" class="form-control mb-2" required>
                            <select name="estado" class="form-control" required>
                                <option <?= $fila["Estado"] == "Libre" ? "selected" : "" ?>>Libre</option>
                                <option <?= $fila["Estado"] == "Ocupada" ? "selected" : "" ?>>Ocupada</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="editar" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
