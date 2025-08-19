<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['cargo'] != 'Empleado') {
    header("Location: login.php");
    exit();
}
include("conexion.php");

// Consultas rápidas para estadísticas
$totalClientes = $conn->query("SELECT COUNT(*) AS total FROM Cliente")->fetch_assoc()['total'];
$totalMesas = $conn->query("SELECT COUNT(*) AS total FROM Mesa")->fetch_assoc()['total'];
$totalPedidos = $conn->query("SELECT COUNT(*) AS total FROM Pedido")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Empleado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #222;
        }
        .navbar {
            border-bottom: 4px solid #fff;
            background: rgba(0,0,0,0.7) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            font-weight: 700;
            font-size: 1.3rem;
            letter-spacing: 1.1px;
        }
        .navbar-brand i {
            color: #ffc107;
            margin-right: 10px;
            font-size: 1.6rem;
        }
        .btn-outline-light {
            border-width: 2px;
            font-weight: 600;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .btn-outline-light:hover {
            background-color: #ffc107;
            color: #222;
            border-color: #ffc107;
        }

        .container {
            max-width: 960px;
        }
        .dashboard-title {
            font-weight: 800;
            color: #fff;
            text-shadow: 0 2px 6px rgba(0,0,0,0.4);
            font-size: 2.4rem;
            margin-bottom: 0.2rem;
        }
        .dashboard-subtitle {
            color: #ddd;
            font-size: 1.1rem;
            margin-bottom: 40px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        /* Estadísticas */
        .stat-card {
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: default;
            padding: 30px 0;
            color: #fff;
            text-align: center;
            user-select: none;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }
        .stat-clients {
            background: #0d6efd; /* azul bootstrap */
            background: linear-gradient(135deg, #0d6efd, #3a8dff);
        }
        .stat-tables {
            background: #198754; /* verde bootstrap */
            background: linear-gradient(135deg, #198754, #4ab570);
        }
        .stat-orders {
            background: #ffc107; /* amarillo bootstrap */
            background: linear-gradient(135deg, #ffc107, #ffd454);
            color: #2f2f2f;
        }
        .stat-card i {
            font-size: 3.6rem;
            margin-bottom: 15px;
            text-shadow: 0 2px 6px rgba(0,0,0,0.4);
        }
        .stat-card h4 {
            font-size: 3rem;
            margin-bottom: 8px;
            font-weight: 900;
            letter-spacing: 1.8px;
        }
        .stat-card p {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 1px 4px rgba(0,0,0,0.25);
        }

        /* Cards modulos */
        .card-custom {
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.15);
            padding: 20px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            background: #fff;
        }
        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 18px 35px rgba(0,0,0,0.25);
        }
        .card-custom .card-body {
            text-align: center;
            padding: 2rem 1.5rem;
        }
        .card-custom i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #6f42c1;
            transition: color 0.3s ease;
        }
        .card-custom:hover i {
            color: #533dac;
        }
        .card-title {
            font-weight: 700;
            margin-bottom: 0.7rem;
            font-size: 1.5rem;
            color: #42275a;
        }
        .card p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.6rem;
            min-height: 68px;
        }
        .card-custom a.btn {
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 12px;
            padding: 12px 0;
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom a.btn-primary {
            background: linear-gradient(135deg, #6f42c1, #b084f8);
            border: none;
        }
        .card-custom a.btn-primary:hover {
            background: linear-gradient(135deg, #533dac, #a469e5);
            box-shadow: 0 6px 15px rgba(83,61,172,0.8);
        }
        .card-custom a.btn-success {
            background: linear-gradient(135deg, #198754, #4ab570);
            border: none;
            color: #fff;
        }
        .card-custom a.btn-success:hover {
            background: linear-gradient(135deg, #176e44, #3d8950);
            box-shadow: 0 6px 15px rgba(23,110,68,0.8);
            color: #fff;
        }
        .card-custom a.btn-warning {
            background: linear-gradient(135deg, #ffc107, #ffd454);
            border: none;
            color: #2f2f2f;
        }
        .card-custom a.btn-warning:hover {
            background: linear-gradient(135deg, #d6a100, #f4c82b);
            box-shadow: 0 6px 15px rgba(214,161,0,0.8);
            color: #2f2f2f;
        }

        /* Notificaciones */
        .card-header.bg-dark {
            background: linear-gradient(135deg, #5a4aa7, #423b8a);
            font-weight: 700;
            font-size: 1.2rem;
            color: #fff;
            border-radius: 1rem 1rem 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.3);
        }
        .card-header.bg-dark i {
            font-size: 1.6rem;
            color: #ffc107;
        }
        .list-group-item {
            font-size: 1rem;
            font-weight: 600;
            background: #f9f9fc;
            border: none;
            color: #555;
            letter-spacing: 0.03em;
        }

        footer {
            color: #eee;
            margin-top: 70px;
            font-size: 0.9rem;
            letter-spacing: 0.05em;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }

        @media (max-width: 767.98px) {
            .dashboard-title {
                font-size: 1.8rem;
            }
            .dashboard-subtitle {
                font-size: 1rem;
                margin-bottom: 30px;
            }
            .stat-card h4 {
                font-size: 2.4rem;
            }
            .card-custom .card-body {
                padding: 1.5rem 1rem;
            }
            .card-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark p-3">
    <span class="navbar-brand"><i class="bi bi-building"></i> Restaurante - Panel Empleado</span>
    <a href="logout.php" class="btn btn-outline-light fw-semibold"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a>
</nav>

<div class="container mt-5">

    <div class="text-center mb-5">
        <h2 class="dashboard-title">👋 Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
        <p class="dashboard-subtitle">Rol: <strong>Empleado</strong></p>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card stat-clients">
                <i class="bi bi-people-fill"></i>
                <h4><?php echo $totalClientes; ?></h4>
                <p>Clientes Registrados</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-tables">
                <i class="bi bi-table"></i>
                <h4><?php echo $totalMesas; ?></h4>
                <p>Mesas Disponibles</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card stat-orders">
                <i class="bi bi-receipt-cutoff"></i>
                <h4><?php echo $totalPedidos; ?></h4>
                <p>Pedidos Activos</p>
            </div>
        </div>
    </div>

    <!-- Módulos principales -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-custom shadow-sm">
                <div class="card-body">
                    <i class="bi bi-receipt-cutoff text-primary"></i>
                    <h5 class="card-title">Gestionar Pedidos</h5>
                    <p>Registra y administra los pedidos de los clientes.</p>
                    <a href="pedido.php" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-right-circle"></i> Ir
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people-fill text-success"></i>
                    <h5 class="card-title">Clientes</h5>
                    <p>Accede al registro y gestión de clientes frecuentes.</p>
                    <a href="cliente.php" class="btn btn-success w-100">
                        <i class="bi bi-arrow-right-circle"></i> Ir
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom shadow-sm">
                <div class="card-body">
                    <i class="bi bi-table text-warning"></i>
                    <h5 class="card-title">Mesas</h5>
                    <p>Controla la disponibilidad y asignación de mesas.</p>
                    <a href="mesa.php" class="btn btn-warning w-100">
                        <i class="bi bi-arrow-right-circle"></i> Ir
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notificaciones rápidas -->
    <div class="row mt-5">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-header bg-dark">
                    <i class="bi bi-bell-fill"></i> Notificaciones
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">📌 No hay notificaciones pendientes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<footer class="text-center">
    <small>© 2025 Restaurante | Panel de Gestión Empleado</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
