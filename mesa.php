<?php
session_start();
if (!isset($_SESSION["empleado"])) { header("Location: index.php"); exit(); }
include "db.php";

// Insertar
if (isset($_POST["add"])) {
  $nombre=$_POST["nombre"]; $cap=$_POST["capacidad"]; $estado=$_POST["estado"];
  $conn->query("INSERT INTO Mesa (Nombre,Capacidad,Estado) VALUES ('$nombre','$cap','$estado')");
}
// Eliminar
if (isset($_GET["del"])) {
  $id=$_GET["del"];
  $conn->query("DELETE FROM Mesa WHERE IdMesa=$id");
}
$mesas=$conn->query("SELECT * FROM Mesa");
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Mesas</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="p-6 bg-gray-100">
<h2 class="text-2xl font-bold mb-4">🪑 Mesas</h2>
<form method="POST" class="mb-6 grid grid-cols-3 gap-2">
  <input name="nombre" placeholder="Nombre" class="p-2 border rounded" required>
  <input name="capacidad" placeholder="Capacidad" type="number" class="p-2 border rounded">
  <select name="estado" class="p-2 border rounded"><option>Disponible</option><option>Ocupada</option></select>
  <button name="add" class="col-span-3 bg-green-600 text-white p-2 rounded mt-2">Agregar</button>
</form>
<table class="w-full bg-white shadow rounded">
<tr class="bg-red-600 text-white"><th>ID</th><th>Nombre</th><th>Capacidad</th><th>Estado</th><th>Acciones</th></tr>
<?php while($m=$mesas->fetch_assoc()): ?>
<tr class="border">
  <td><?= $m["IdMesa"] ?></td>
  <td><?= $m["Nombre"] ?></td>
  <td><?= $m["Capacidad"] ?></td>
  <td><?= $m["Estado"] ?></td>
  <td><a href="?del=<?= $m["IdMesa"] ?>" class="text-red-500">Eliminar</a></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
