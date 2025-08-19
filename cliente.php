<?php
session_start();
include("conexion.php");

// Validación de acceso
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$cargo = $_SESSION['cargo'];
$mensaje = "";

// Variables para edición
$editar = false;
$idEditar = "";
$nombreEditar = "";
$apellidoEditar = "";
$telefonoEditar = "";
$direccionEditar = "";

// Crear cliente
if (isset($_POST['guardar'])) {
    $nombre    = trim(mysqli_real_escape_string($conn, $_POST['nombre']));
    $apellido  = trim(mysqli_real_escape_string($conn, $_POST['apellido']));
    $telefono  = trim(mysqli_real_escape_string($conn, $_POST['telefono']));
    $direccion = trim(mysqli_real_escape_string($conn, $_POST['direccion']));

    if ($nombre === '' || $apellido === '') {
        $mensaje = "❌ Debes ingresar nombre y apellido";
    } else {
        $sql = "INSERT INTO Cliente (Nombre, Apellido, Direccion, Telefono) 
                VALUES ('$nombre', '$apellido', '$direccion', '$telefono')";
        if ($conn->query($sql)) {
            $mensaje = "✅ Cliente registrado correctamente";
        } else {
            $mensaje = "❌ Error al registrar cliente: " . $conn->error;
        }
    }
}

// Eliminar cliente
if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $sql = "DELETE FROM Cliente WHERE IdCliente=$id";
    if ($conn->query($sql)) {
        $mensaje = "🗑️ Cliente eliminado correctamente";
    } else {
        $mensaje = "❌ Error al eliminar cliente: " . $conn->error;
    }
}

// Preparar edición
if (isset($_GET['editar'])) {
    $idEditar = (int) $_GET['editar'];
    $res = $conn->query("SELECT * FROM Cliente WHERE IdCliente=$idEditar");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $editar = true;
        $nombreEditar = $row['Nombre'];
        $apellidoEditar = $row['Apellido'];
        $telefonoEditar = $row['Telefono'];
        $direccionEditar = $row['Direccion'];
    }
}

// Guardar cambios de edición
if (isset($_POST['actualizar'])) {
    $id        = (int) $_POST['id'];
    $nombre    = mysqli_real_escape_string($conn, $_POST['nombre']);
    $apellido  = mysqli_real_escape_string($conn, $_POST['apellido']);
    $telefono  = mysqli_real_escape_string($conn, $_POST['telefono']);
    $direccion = mysqli_real_escape_string($conn, $_POST['direccion']);

    $sql = "UPDATE Cliente 
            SET Nombre='$nombre', Apellido='$apellido', 
                Telefono='$telefono', Direccion='$direccion'
            WHERE IdCliente=$id";

    if ($conn->query($sql)) {
        $mensaje = "✏️ Cliente actualizado correctamente";
    } else {
        $mensaje = "❌ Error al actualizar cliente: " . $conn->error;
    }
}

// Listado de clientes
$result = $conn->query("
    SELECT IdCliente, Nombre, Apellido, Telefono, Direccion
    FROM Cliente
    ORDER BY IdCliente DESC
");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Gestión de Clientes - Diseño Mejorado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .header-custom {
            background: #5a4aa7;
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(90, 74, 167, 0.4);
            transition: background 0.3s ease;
        }
        .header-custom:hover {
            background: #473d8a;
        }
        .header-custom h2 {
            font-weight: 700;
            font-size: 1.8rem;
        }

        .btn-light:hover {
            background-color: #ded6ff;
            color: #473d8a;
        }

        .alert {
            max-width: 600px;
            margin: 0 auto 25px auto;
            border-radius: 12px;
            font-size: 1.1rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        .card {
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.2);
        }

        .card-header {
            border-radius: 18px 18px 0 0;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
        }
        .card-header i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        /* Formulario */
        form input.form-control {
            border-radius: 10px;
            border: 2px solid #ddd;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        form input.form-control:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 8px #6f42c1aa;
        }

        button.btn {
            font-weight: 700;
            font-size: 1.1rem;
            padding: 12px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(111, 66, 193, 0.5);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        button.btn:hover {
            box-shadow: 0 6px 18px rgba(111, 66, 193, 0.7);
        }

        /* Tabla */
        .table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
            font-size: 0.95rem;
        }
        .table thead {
            background: #6f42c1;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
        }
        .table tbody tr:hover {
            background: #f3eaff;
            cursor: pointer;
            transition: background 0.25s ease;
        }
        .table td, .table th {
            vertical-align: middle;
            text-align: center;
            padding: 14px 12px;
        }

        /* Botones acción */
        .btn-primary {
            background-color: #6f42c1;
            border: none;
            box-shadow: 0 3px 10px #9c6fe8;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5934a9;
        }

        .btn-danger {
            background-color: #d6336c;
            border: none;
            box-shadow: 0 3px 10px #e76f9b;
            transition: background-color 0.3s ease;
        }
        .btn-danger:hover {
            background-color: #b3265a;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .header-custom {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .header-custom div {
                justify-content: center !important;
            }
            .card-header {
                font-size: 1.1rem;
            }
            button.btn {
                font-size: 1rem;
            }
            .table td, .table th {
                padding: 10px 6px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body class="container py-4">

    <!-- Encabezado -->
    <header class="header-custom d-flex justify-content-between align-items-center flex-wrap">
        <h2><i class="bi bi-people-fill"></i> Gestión de Clientes</h2>
        <div class="d-flex align-items-center flex-wrap gap-3 justify-content-center">
            <span class="fw-semibold fs-6 text-white">👤 Usuario: <?php echo htmlspecialchars($_SESSION['usuario']); ?> (<?php echo htmlspecialchars($cargo); ?>)</span>
            <a href="dashboard_admin.php" class="btn btn-secondary btn-sm shadow-sm px-3">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var dashboardBtn = document.querySelector('a.btn.btn-secondary');
                if (dashboardBtn) {
                    dashboardBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var rol = '<?php echo $_SESSION['cargo']; ?>';
                        if (rol === 'Admin') {
                            window.location.href = 'dashboard_admin.php';
                        } else {
                            window.location.href = 'dashboard_empleado.php';
                        }
                    });
                }
            });
            </script>
            <a href="logout.php" class="btn btn-light btn-sm shadow-sm px-3">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </a>
        </div>
    </header>

    <!-- Mensajes -->
    <?php if (!empty($mensaje)) { ?>
        <div class="alert alert-info text-center fw-bold shadow-lg">
            <?php echo $mensaje; ?>
        </div>
    <?php } ?>

    <!-- Formulario registrar / editar cliente -->
    <div class="card shadow mb-5 mx-auto" style="max-width: 720px;">
        <div class="card-header bg-gradient">
            <?php if ($editar) { ?>
                <i class="bi bi-pencil-square"></i> Editar Cliente
            <?php } else { ?>
                <i class="bi bi-person-plus"></i> Registrar Cliente
            <?php } ?>
        </div>
        <div class="card-body">
            <form method="post" autocomplete="off" novalidate>
                <?php if ($editar) { ?>
                    <input type="hidden" name="id" value="<?php echo $idEditar; ?>">
                <?php } ?>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" name="nombre" class="form-control form-control-lg" placeholder="Nombre" 
                               value="<?php echo $editar ? htmlspecialchars($nombreEditar) : ''; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="apellido" class="form-control form-control-lg" placeholder="Apellido" 
                               value="<?php echo $editar ? htmlspecialchars($apellidoEditar) : ''; ?>" required>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="tel" name="telefono" class="form-control form-control-lg" placeholder="Teléfono"
                               value="<?php echo $editar ? htmlspecialchars($telefonoEditar) : ''; ?>">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="direccion" class="form-control form-control-lg" placeholder="Dirección"
                               value="<?php echo $editar ? htmlspecialchars($direccionEditar) : ''; ?>">
                    </div>
                </div>
                <?php if ($editar) { ?>
                    <button type="submit" name="actualizar" class="btn btn-warning w-100 shadow">
                        <i class="bi bi-save"></i> Actualizar Cliente
                    </button>
                <?php } else { ?>
                    <button type="submit" name="guardar" class="btn btn-success w-100 shadow">
                        <i class="bi bi-save"></i> Guardar Cliente
                    </button>
                <?php } ?>
            </form>
        </div>
    </div>

    <!-- Listado de clientes -->
    <div class="card shadow mx-auto" style="max-width: 900px;">
        <div class="card-header bg-secondary d-flex align-items-center justify-content-center">
            <i class="bi bi-list-check me-2"></i> Clientes Registrados
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th style="min-width: 130px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['IdCliente']; ?></td>
                            <td class="text-start ps-4"><?php echo htmlspecialchars($row['Nombre']); ?></td>
                            <td class="text-start ps-4"><?php echo htmlspecialchars($row['Apellido']); ?></td>
                            <td><?php echo htmlspecialchars($row['Telefono']); ?></td>
                            <td class="text-start ps-4"><?php echo htmlspecialchars($row['Direccion']); ?></td>
                            <td>
                                <a href="?editar=<?php echo $row['IdCliente']; ?>" class="btn btn-primary btn-sm me-2" title="Editar Cliente">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="?eliminar=<?php echo $row['IdCliente']; ?>" 
                                   onclick="return confirm('¿Seguro que deseas eliminar este cliente?')" 
                                   class="btn btn-danger btn-sm" title="Eliminar Cliente">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if ($result->num_rows === 0) { ?>
                            <tr>
                                <td colspan="6" class="text-muted fst-italic py-4">No hay clientes registrados</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
