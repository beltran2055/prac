<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "prac22";

// 🔹 Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("❌ Conexión fallida: " . $conn->connect_error);

// 🔹 Obtener datos del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : 'No especificado';
$pelicula = isset($_POST['pelicula']) ? trim($_POST['pelicula']) : 'No especificada';
$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'Normal';
$boletos = isset($_POST['boletos']) ? max(0, (int)$_POST['boletos']) : 0;
$ninos_opcion = isset($_POST['ninos_opcion']) ? $_POST['ninos_opcion'] : 'no';
$ninos = ($ninos_opcion === 'si' && isset($_POST['ninos'])) ? max(0, (int)$_POST['ninos']) : 0;

// 🔹 Validar que niños < adultos
if ($ninos >= $boletos) {
    $ninos = max(0, $boletos - 1);
}

// 🔹 Calcular precios
switch ($tipo) {
    case '3D': $precio_adulto = 110; break;
    case 'VIP': $precio_adulto = 145; break;
    default: $precio_adulto = 75; break;
}

$precio_nino = $precio_adulto * 0.85;
$boletos_adultos = $boletos - $ninos;

$subtotal_adultos = $boletos_adultos * $precio_adulto;
$subtotal_ninos = $ninos * $precio_nino;
$total = $subtotal_adultos + $subtotal_ninos;
$iva = $total * 0.16;
$subtotal = $total - $iva;

// 🔹 Guardar en base de datos
$stmt = $conn->prepare("INSERT INTO cine (nombre, pelicula, tipo_sala, boleto_ad, boleto_ni, subtotal, iva, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssiiidd", $nombre, $pelicula, $tipo, $boletos_adultos, $ninos, $subtotal, $iva, $total);
$stmt->execute();
$stmt->close();
$conn->close();

// 🔹 Imágenes de películas
$imagenes_peliculas = [
    "El conjuro: Últimos ritos" => "img/con.jpg",
    "Los tipos malos 2" => "img/tipo.jpg",
    "Una batalla tras otra" => "img/ball.jpg",
    "El payaso del maíz" => "img/mai.jpg",
    "Los extraños: Capítulo 2" => "img/ex.jpg",
    "Camina o muere" => "img/cam.jpg",
    "Batman Azteca: Choque de imperios" => "img/aztec.jpg",
    "Teléfono negro 2" => "img/neg.jpg",
    "Cacería de brujas" => "img/bru.jpg",
    "Un mundo mejor" => "img/me.jpg",
    "Default" => "img/default.jpg"
];

$imagen_pelicula = isset($imagenes_peliculas[$pelicula]) ? $imagenes_peliculas[$pelicula] : $imagenes_peliculas['Default'];
$logo = "img/logo.png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎟 Ticket de Cine 🎟</title>
<style>
body { 
    font-family: 'Poppins', sans-serif; 
    background: #0b1c2c; 
    color: #00ff99; 
    display:flex; justify-content:center; align-items:center; 
    min-height:100vh; flex-direction:column; 
}
.ticket { 
    background: rgba(0,30,60,0.95); 
    border-radius:25px; 
    padding:40px 30px; 
    width:420px; 
    text-align:center; 
    border:2px solid #00ff99; 
    box-shadow:0 0 40px #00ff99; 
    position: relative;
}
.ticket img.logo { 
    width:100px; 
    position:absolute; top:-50px; left:calc(50% - 50px); 
    border-radius:50%; 
    border:2px solid #00ff99;
    background:#000;
}
.ticket img.pelicula { 
    width:200px; 
    border-radius:15px; 
    margin:15px 0; 
    box-shadow:0 0 20px #00ff99;
}
.ticket p { margin:8px 0; font-size:1rem; }
.ticket hr { border:none; border-top:1px dashed #00ff99; margin:15px 0; }
.botones-acciones { display:flex; justify-content:center; gap:15px; margin-top:15px; }
.botones-acciones form input { 
    padding:10px 20px; border-radius:10px; border:none; cursor:pointer; 
    font-weight:bold; background:#00ff90; color:#003366; 
    box-shadow:0 0 10px #00ff99; transition:0.3s; 
}
.botones-acciones form input:hover { transform:scale(1.05); box-shadow:0 0 20px #00ffaa; }
a { 
    display:inline-block; margin-top:20px; background-color:#00ff99; color:#003366; 
    padding:10px 25px; border-radius:12px; font-weight:bold; text-decoration:none; 
    box-shadow:0 0 15px #00ff99; transition:0.3s; 
}
a:hover { background-color:#00ffaa; box-shadow:0 0 25px #00ff99; transform:scale(1.05); }
</style>
</head>
<body>

<div class="ticket">
  <img src="<?= $logo ?>" alt="Logo" class="logo">
  <h1>🎟 Ticket de Cine 🎟</h1>
  <img src="<?= $imagen_pelicula ?>" alt="<?= htmlspecialchars($pelicula) ?>" class="pelicula">
  <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
  <p><strong>Película:</strong> <?= htmlspecialchars($pelicula) ?></p>
  <p><strong>Tipo de sala:</strong> <?= htmlspecialchars($tipo) ?></p>
  <p><strong>Boletos adultos:</strong> <?= $boletos_adultos ?></p>
  <p><strong>Boletos niños:</strong> <?= $ninos ?></p>
  <hr>
  <p><strong>Subtotal adultos:</strong> $<?= number_format($subtotal_adultos,2) ?></p>
  <p><strong>Subtotal niños:</strong> $<?= number_format($subtotal_ninos,2) ?></p>
  <p><strong>Subtotal:</strong> $<?= number_format($subtotal,2) ?></p>
  <p><strong>IVA (16%):</strong> $<?= number_format($iva,2) ?></p>
  <p><strong>Total:</strong> $<?= number_format($total,2) ?></p>
  <hr>
  <div class="botones-acciones">
    <form action="consultar.php" method="GET">
      <input type="submit" value="Consultar Todos">
    </form>
    <a href="index.html">🎬 Volver</a>
  </div>
</div>

</body>
</html>
