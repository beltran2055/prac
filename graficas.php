<?php
// 🔹 Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "prac22"; // Cambia si tu base se llama distinto
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("❌ Error de conexión a la base de datos.");

// 🔹 Obtener lista de películas
$peliculas = [];
$res = $conn->query("SELECT DISTINCT pelicula FROM cine");
while ($row = $res->fetch_assoc()) {
  $peliculas[] = $row['pelicula'];
}

// 🔹 Inicializar arreglos
$compras = [];
$boletos_totales = [];
$cantidad = [];
$adultos = [];
$ninos = [];

// 🔹 Consultar datos por película
foreach ($peliculas as $pelicula) {
  // Total de compras
  $sql1 = "SELECT COUNT(*) AS compras FROM cine WHERE pelicula='$pelicula'";
  $compras[] = $conn->query($sql1)->fetch_assoc()['compras'] ?? 0;

  // Total de boletos (adultos + niños)
  $sql2 = "SELECT SUM(boleto_ad + boleto_ni) AS total FROM cine WHERE pelicula='$pelicula'";
  $boletos_totales[] = $conn->query($sql2)->fetch_assoc()['total'] ?? 0;

  // Total boletos adultos
  $sql3 = "SELECT SUM(boleto_ad) AS ad FROM cine WHERE pelicula='$pelicula'";
  $adultos[] = $conn->query($sql3)->fetch_assoc()['ad'] ?? 0;

  // Total boletos niños
  $sql4 = "SELECT SUM(boleto_ni) AS ni FROM cine WHERE pelicula='$pelicula'";
  $ninos[] = $conn->query($sql4)->fetch_assoc()['ni'] ?? 0;

  // 💰 Cantidad recaudada (Adultos $70, Niños $50)
  $sql5 = "SELECT SUM((boleto_ad * 70) + (boleto_ni * 50)) AS cant FROM cine WHERE pelicula='$pelicula'";
  $cantidad[] = $conn->query($sql5)->fetch_assoc()['cant'] ?? 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎬 Gráfica General del Cine</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<style>
body {
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #000, #1a1a1a);
  color: white;
  text-align: center;
  padding: 30px;
}
h1 {
  color: #ffcc00;
  text-shadow: 0 0 10px #ffcc00;
  margin-bottom: 40px;
}
canvas {
  background: #111;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 0 25px rgba(255, 255, 255, 0.3);
}
.boton {
  display: inline-block;
  margin-top: 40px;
  background: #00ffc8;
  color: #000;
  padding: 15px 35px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: bold;
  font-size: 18px;
  box-shadow: 0 0 15px #00ffc8;
  transition: 0.3s;
}
.boton:hover {
  background: #00cc99;
  transform: scale(1.08);
}
</style>
</head>
<body>

<h1>📊 Gráfica General del Cine (Compras, Boletos, Cantidad $, Adultos, Niños)</h1>

<canvas id="graficaGeneral" width="1000" height="500"></canvas>

<a href="consultar.php" class="boton">🎬 Volver a Consultar</a>

<script>
Chart.register(ChartDataLabels);

const ctx = document.getElementById('graficaGeneral').getContext('2d');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($peliculas) ?>,
    datasets: [
      {
        label: 'Compras',
        data: <?= json_encode($compras) ?>,
        backgroundColor: 'rgba(255, 99, 132, 0.8)',
      },
      {
        label: 'Boletos Totales',
        data: <?= json_encode($boletos_totales) ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.8)',
      },
      {
        label: 'Cantidad $',
        data: <?= json_encode($cantidad) ?>,
        backgroundColor: 'rgba(255, 206, 86, 0.8)',
      },
      {
        label: 'Boletos Adultos',
        data: <?= json_encode($adultos) ?>,
        backgroundColor: 'rgba(75, 192, 192, 0.8)',
      },
      {
        label: 'Boletos Niños',
        data: <?= json_encode($ninos) ?>,
        backgroundColor: 'rgba(153, 102, 255, 0.8)',
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'top',
        labels: { color: 'white', font: { size: 14 } }
      },
      title: {
        display: true,
        text: 'Comparativa General por Película',
        color: '#00ffc8',
        font: { size: 20, weight: 'bold' }
      },
      datalabels: {
        color: 'white',
        font: { weight: 'bold', size: 12 },
        anchor: 'end',
        align: 'start',
        formatter: function(value, context) {
          return context.dataset.label + ': ' + value;
        }
      }
    },
    scales: {
      x: {
        ticks: { color: 'white', font: { size: 14, weight: 'bold' } },
        grid: { color: '#333' }
      },
      y: {
        beginAtZero: true,
        ticks: { color: 'white', font: { size: 14, weight: 'bold' } },
        grid: { color: '#333' }
      }
    }
  }
});
</script>

</body>
</html>
