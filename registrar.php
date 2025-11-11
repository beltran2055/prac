<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "prac22";

// Conexión a la base de datos
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("<div style='color:red;'>❌ Error de conexión: " . $conn->connect_error . "</div>");
}

// Variables del formulario
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$pelicula = isset($_POST['pelicula']) ? trim($_POST['pelicula']) : '';
$tipo = isset($_POST['tipo']) ? trim($_POST['tipo']) : 'Normal';
$boletos = isset($_POST['boletos']) ? max(0, (int)$_POST['boletos']) : 0;
$ninos_opcion = isset($_POST['ninos_opcion']) ? $_POST['ninos_opcion'] : 'no';
$ninos = ($ninos_opcion === 'si' && isset($_POST['ninos'])) ? max(0, (int)$_POST['ninos']) : 0;

if ($ninos >= $boletos) $ninos = max(0, $boletos - 1);

// Precios
$precio_adulto = match($tipo) {
    'Normal' => 75,
    '3D' => 110,
    'VIP' => 145,
    default => 75
};
$precio_nino = $precio_adulto * 0.85;

// Cálculos
$subtotal_adultos = ($boletos - $ninos) * $precio_adulto;
$subtotal_ninos = $ninos * $precio_nino;
$total = $subtotal_adultos + $subtotal_ninos;
$iva = $total * 0.16;
$subtotal = $total - $iva;

// Insertar en la base de datos con nombres correctos
$sql = "INSERT INTO cine (nombre, pelicula, tipo_sala, boleto_ad, boleto_ni, subtotal, iva, total)
        VALUES ('$nombre', '$pelicula', '$tipo', '$boletos', '$ninos', '$subtotal', '$iva', '$total')";

if ($conn->query($sql) === TRUE) {
    echo "
    <html>
    <head>
    <meta charset='UTF-8'>
    <title>🎟 Registro Exitoso</title>
    <style>
    body {
        background: radial-gradient(circle, #001a33, #000);
        color: #00ff99;
        font-family: 'Poppins', sans-serif;
        text-align: center;
        padding-top: 100px;
    }
    h1 {
        text-shadow: 0 0 15px #00ff99;
    }
    a {
        display: inline-block;
        margin: 20px;
        padding: 10px 20px;
        background: #00ff99;
        color: #001a33;
        font-weight: bold;
        border-radius: 12px;
        text-decoration: none;
        box-shadow: 0 0 15px #00ff99;
        transition: 0.3s;
    }
    a:hover {
        background: #00ffaa;
        transform: scale(1.05);
        box-shadow: 0 0 25px #00ffaa;
    }
    </style>
    </head>
    <body>
        <h1>✅ Registro exitoso</h1>
        <p>Los datos del ticket han sido guardados correctamente.</p>
        <a href='consultar.php'>📋 Consultar registros</a>
        <a href='index.html'>🎬 Volver al inicio</a>
    </body>
    </html>";
} else {
    echo "<div style='color:red;'>❌ Error al registrar: " . $conn->error . "</div>";
}

$conn->close();
?>
