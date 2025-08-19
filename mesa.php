<?php
session_start();
include("conexion.php");

// Validar acceso solo para empleados y administradores
$rol = $_SESSION["cargo"] ?? '';
if (!isset($_SESSION["usuario"]) || ($rol != "Empleado" && $rol != "Admin")) {
    header("Location: login.php");
    exit();
}

// Crear Mesa (solo Admin)
if (isset($_POST["crear"]) && $rol == "Admin") {
    $nombre = $_POST["nombre"];
    $capacidad = $_POST["capacidad"];
    $estado = $_POST["estado"];

    $sql = "INSERT INTO Mesa (Nombre, Capacidad, Estado) VALUES ('$nombre', '$capacidad', '$estado')";
    $conn->query($sql);
    header("Location: mesa.php");
    exit();
}

// Editar Mesa (solo Admin)
if (isset($_POST["editar"]) && $rol == "Admin") {
    $id = $_POST["id"];
    $nombre = $_POST["nombre"];
    $capacidad = $_POST["capacidad"];
    $estado = $_POST["estado"];

    $sql = "UPDATE Mesa SET Nombre='$nombre', Capacidad='$capacidad', Estado='$estado' WHERE IdMesa=$id";
    $conn->query($sql);
    header("Location: mesa.php");
    exit();
}

// Eliminar Mesa (solo Admin)
if (isset($_GET["eliminar"]) && $rol == "Admin") {
    $id = $_GET["eliminar"];
    $sql = "DELETE FROM Mesa WHERE IdMesa=$id";
    $conn->query($sql);
    header("Location: mesa.php");
    exit();
}

// Consultar mesas
$result = $conn->query("SELECT * FROM Mesa");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Mesas Profesional</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 30px 15px;
        }
        h2 {
            font-weight: 800;
            color: #0d6efd;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 2px 6px rgba(13,110,253,0.4);
        }
        form.mb-4 {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto 40px;
        }
        input.form-control, select.form-control {
            border-radius: 10px;
            border: 1.8px solid #ced4da;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-size: 1rem;
            min-height: 45px;
        }
        input.form-control:focus, select.form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 10px #0d6efd80;
            outline: none;
        }
        button.btn-success {
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.15rem;
            padding: 12px 0;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 18px rgba(13,110,253,0.4);
        }
        button.btn-success:hover {
            background-color: #004ecb;
            box-shadow: 0 6px 22px rgba(0,78,203,0.7);
        }
        table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            background: white;
            max-width: 900px;
            margin: 0 auto;
        }
        thead {
            background: #0d6efd;
            color: white;
            font-weight: 700;
        }
        thead th {
            padding: 15px 20px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
        tbody td {
            padding: 15px 20px;
            font-size: 0.95rem;
            vertical-align: middle;
        }
        tbody tr:hover {
            background: #f1f7ff;
            transition: background 0.3s ease;
        }
        .btn-primary, .btn-danger {
            border-radius: 10px;
            padding: 6px 12px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 3px 12px rgba(0,0,0,0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #004ecb;
            box-shadow: 0 6px 16px rgba(0,78,203,0.7);
        }
        .btn-danger:hover {
            background-color: #b32626;
            box-shadow: 0 6px 16px rgba(179,38,38,0.7);
        }
        /* Modal custom */
        .modal-content {
            border-radius: 20px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.15);
            padding: 25px 20px;
        }
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        .modal-title {
            font-weight: 800;
            color: #0d6efd;
            font-size: 1.4rem;
        }
        .btn-close {
            background: transparent;
            border: none;
            font-size: 1.6rem;
            opacity: 0.7;
            transition: opacity 0.2s ease-in-out;
        }
        .btn-close:hover {
            opacity: 1;
            cursor: pointer;
        }
        .modal-body input, .modal-body select {
            margin-top: 12px;
            margin-bottom: 18px;
        }
        .modal-footer {
            border-top: none;
            padding-top: 0;
        }
        .modal-footer button {
            background: #0d6efd;
            border: none;
            font-weight: 700;
            border-radius: 12px;
            padding: 12px 25px;
            box-shadow: 0 6px 22px rgba(13,110,253,0.7);
            transition: background-color 0.3s ease;
            color: white;
            width: 100%;
            font-size: 1.1rem;
        }
        .modal-footer button:hover {
            background-color: #004ecb;
            box-shadow: 0 8px 28px rgba(0,78,203,0.9);
        }
        /* Responsive adjustments */
        @media (max-width: 575.98px) {
            form.mb-4, table {
                max-width: 100%;
                margin: 0 10px 35px;
            }
            .modal-content {
                padding: 20px 15px;
            }
            thead th, tbody td {
                padding: 12px 10px;
                font-size: 0.85rem;
            }
            .modal-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

    <h2>Gestión de Mesas</h2>

    <?php if ($rol == "Admin") { ?>
    <!-- Formulario Crear Mesa -->
    <form method="post" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <input type="text" name="nombre" placeholder="Nombre Mesa" class="form-control" required>
            </div>
            <div class="col-md-4">
                <input type="number" name="capacidad" placeholder="Capacidad" class="form-control" min="1" required>
            </div>
            <div class="col-md-4">
                <select name="estado" class="form-select" required>
                    <option value="Libre">Libre</option>
                    <option value="Ocupada">Ocupada</option>
                </select>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <button type="submit" name="crear" class="btn btn-success w-100 shadow-sm">Agregar Mesa</button>
            </div>
        </div>
    </form>
    <?php } ?>

    <!-- Tabla Mesas -->
    <table class="table shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Estado</th>
                <?php if ($rol == "Admin") { ?><th>Acciones</th><?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $fila["IdMesa"] ?></td>
                <td><?= htmlspecialchars($fila["Nombre"]) ?></td>
                <td><?= $fila["Capacidad"] ?></td>
                <td>
                  <?php if($fila["Estado"] == "Libre"): ?>
                    <span class="badge bg-success">Libre</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Ocupada</span>
                  <?php endif; ?>
                </td>
                <?php if ($rol == "Admin") { ?>
                <td>
                    <!-- Botón Editar -->
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editar<?= $fila["IdMesa"] ?>"><i class="bi bi-pencil-square"></i> Editar</button>
                    <a href="mesa.php?eliminar=<?= $fila["IdMesa"] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar mesa?')"><i class="bi bi-trash"></i> Eliminar</a>
                </td>
                <?php } ?>
            </tr>

            <!-- Modal Editar -->
            <div class="modal fade" id="editar<?= $fila["IdMesa"] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="post" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Mesa</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $fila["IdMesa"] ?>">
                            <input type="text" name="nombre" value="<?= htmlspecialchars($fila["Nombre"]) ?>" class="form-control mb-3" required>
                            <input type="number" name="capacidad" value="<?= $fila["Capacidad"] ?>" class="form-control mb-3" min="1" required>
                            <select name="estado" class="form-select" required>
                                <option value="Libre" <?= $fila["Estado"] == "Libre" ? "selected" : "" ?>>Libre</option>
                                <option value="Ocupada" <?= $fila["Estado"] == "Ocupada" ? "selected" : "" ?>>Ocupada</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="editar" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php } ?>
        </tbody>

    <div class="text-center mt-4">
        <a href="dashboard_empleado.php" class="btn btn-secondary px-4 shadow-sm"><i class="bi bi-arrow-left-circle"></i> Volver al Dashboard</a>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
