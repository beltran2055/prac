<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "prac22";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Conexión fallida: " . $conn->connect_error);

// Recibir datos del formulario
$nombre = $_POST['nombre'] ?? '';
$pelicula = $_POST['pelicula'] ?? '';
$tipo_sala = $_POST['tipo_sala'] ?? '';
$boleto_ad = (int)($_POST['boleto_ad'] ?? 0);
$boleto_ni = (int)($_POST['boleto_ni'] ?? 0);

// Calcular subtotal, IVA y total
$precio_ad = 120; // Precio adulto
$precio_ni = 80;  // Precio niño
$subtotal = ($boleto_ad*$precio_ad)+($boleto_ni*$precio_ni);
$iva = $subtotal*0.16;
$total = $subtotal+$iva;

// Insertar en la base de datos
$stmt = $conn->prepare("INSERT INTO cine (nombre, pelicula, tipo_sala, boleto_ad, boleto_ni, subtotal, iva, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssiiidd", $nombre, $pelicula, $tipo_sala, $boleto_ad, $boleto_ni, $subtotal, $iva, $total);
$stmt->execute();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎬 Registro de Boletos</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #1f1f1f, #3b3b3b);
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
  }

  .container {
    background: rgba(0,0,0,0.8);
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    text-align: center;
    width: 90%;
    max-width: 500px;
  }

  h1 {
    margin-bottom: 20px;
    color: #ffcc00;
  }

  p {
    font-size: 18px;
    margin: 10px 0;
  }

  .btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    background: #ff6600;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.3s;
  }

  .btn:hover {
    background: #ffcc00;
    color: #000;
  }
</style>
</head>
<body>
  <div class="container">
    <h1>✅ Registro Exitoso</h1>
    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
    <p><strong>Película:</strong> <?php echo htmlspecialchars($pelicula); ?></p>
    <p><strong>Tipo de Sala:</strong> <?php echo htmlspecialchars($tipo_sala); ?></p>
    <p><strong>Boletos Adulto:</strong> <?php echo $boleto_ad; ?></p>
    <p><strong>Boletos Niño:</strong> <?php echo $boleto_ni; ?></p>
    <p><strong>Subtotal:</strong> $<?php echo number_format($subtotal,2); ?></p>
    <p><strong>IVA:</strong> $<?php echo number_format($iva,2); ?></p>
    <p><strong>Total:</strong> $<?php echo number_format($total,2); ?></p>

    <a href="consultar.php" class="btn">Ver Registros</a>
    <a href="index.html" class="btn">Registrar Otro</a>
  </div>
</body>
</html>
