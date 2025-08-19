<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['password']);

    // Consulta segura con prepare
    $stmt = $conn->prepare("SELECT * FROM Empleado WHERE Nombre = ? AND Estado = 'Activo'");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Aquí podrías usar password_verify si guardas hash en la BD
        if ($password === "1234") {
            $_SESSION['usuario'] = $row['Nombre'];
            $_SESSION['cargo']   = $row['Cargo'];

            // Redirige según cargo
            if ($row['Cargo'] === "Admin") {
                header("Location: dashboard_admin.php");
            } else {
                header("Location: dashboard_empleado.php");
            }
            exit();
        } else {
            $error = "❌ Contraseña incorrecta";
        }
    } else {
        $error = "⚠️ Usuario no encontrado o inactivo";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #f5f7fa);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            width: 380px;
            padding: 30px;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .login-title {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
            color: #0d6efd;
        }
        .login-title i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .btn-primary {
            border-radius: 10px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-title">
        <i class="bi bi-shop"></i>
        <h3>Restaurante Login</h3>
    </div>
    <form method="POST" autocomplete="off">
        <div class="mb-3">
            <label class="form-label fw-bold">Usuario</label>
            <input type="text" name="usuario" class="form-control" placeholder="Ingrese su usuario" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
        </div>
        <?php if(isset($error)) { ?>
            <div class="alert alert-danger text-center p-2"><?php echo $error; ?></div>
        <?php } ?>
        <button type="submit" class="btn btn-primary w-100">Entrar <i class="bi bi-box-arrow-in-right"></i></button>
    </form>
</div>

</body>
</html>
