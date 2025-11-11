<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "prac22";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("❌ Error de conexión.");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mensaje = ""; $clase = "";

if ($id > 0) {
  $sql = "DELETE FROM cine WHERE id=$id";
  if ($conn->query($sql) === TRUE) {
    $mensaje = "🗑️ Registro eliminado correctamente.";
    $clase = "exito";
  } else {
    $mensaje = "❌ Error al eliminar.";
    $clase = "error";
  }
} else {
  $mensaje = "⚠️ ID no válido.";
  $clase = "advertencia";
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>🗑️ Eliminación</title>
<style>
body {
  display:flex;justify-content:center;align-items:center;
  height:100vh;margin:0;
  background:linear-gradient(135deg,#000,#0b1a33,#1e3a8a);
  font-family:'Poppins',sans-serif;color:#fff;
}
.caja {
  text-align:center;
  background:rgba(255,255,255,0.1);
  padding:40px;border-radius:20px;
  box-shadow:0 0 30px #00bfff;
}
.mensaje {margin:15px 0;padding:15px;border-radius:10px;}
.exito {background:rgba(0,255,128,0.2);border:1px solid #00ff99;color:#00ff99;}
.error {background:rgba(255,80,80,0.2);border:1px solid #ff5050;color:#ff9f9f;}
.advertencia {background:rgba(255,200,0,0.2);border:1px solid #ffcc00;color:#ffe066;}
a {
  display:inline-block;margin-top:10px;padding:10px 20px;
  background:#00bfff;color:#fff;text-decoration:none;
  border-radius:8px;transition:0.3s;
}
a:hover {background:#0099cc;transform:scale(1.05);}
</style>
</head>
<body>
<div class="caja">
  <h2>🎬 Resultado de la Eliminación</h2>
  <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje; ?></div>
  <a href="consultar.php">🔙 Volver</a>
</div>
</body>
</html>
