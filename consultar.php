<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "prac22";

// Conexión
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("<div style='color:red; font-weight:bold;'>❌ Conexión fallida: " . $conn->connect_error . "</div>");
}

// Eliminar registro si se recibe ID por GET
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    $conn->query("DELETE FROM cine WHERE id = $id_eliminar");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Consulta de registros
$sql = "SELECT * FROM cine ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("<div style='color:red; font-weight:bold;'>❌ Error en la consulta: " . $conn->error . "</div>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎬 Registros de Boletos - Cine</title>
<style>
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #000000, #0b1a33, #1e3a8a);
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px;
    min-height: 100vh;
}
h2 {
    text-align: center;
    color: #00bfff;
    font-size: 2em;
    margin-bottom: 25px;
    text-shadow: 0 0 10px #00bfff;
}
table {
    width: 95%;
    max-width: 1200px;
    border-collapse: collapse;
    box-shadow: 0 0 20px rgba(0, 191, 255, 0.3);
    border-radius: 10px;
    overflow: hidden;
    backdrop-filter: blur(6px);
}
th {
    background: #00bfff;
    color: #fff;
    text-transform: uppercase;
    padding: 12px;
}
td {
    background: rgba(255, 255, 255, 0.08);
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
tr:hover td { background: rgba(0, 191, 255, 0.15); transition: 0.3s; }
.boton {
    display: inline-block;
    margin-top: 25px;
    background: #00bfff;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 0 10px #00bfff;
    transition: 0.3s;
}
.boton:hover {
    background: #0099cc;
    transform: scale(1.05);
}
.btn-accion {
    background-color: #00ff99;
    color: #003366;
    padding: 5px 12px;
    margin: 2px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 0 5px #00ff99;
    transition: 0.3s;
}
.btn-accion:hover {
    background-color: #00cc7a;
    transform: scale(1.1);
}
.boton-ver-graficas {
    background: #ff0080;
    box-shadow: 0 0 10px #ff0080;
}
.boton-ver-graficas:hover {
    background: #cc0066;
}
</style>
</head>
<body>

<h2>🎬 Registros de Boletos</h2>

<table>
<tr>
<th>ID</th><th>Nombre</th><th>Película</th><th>Sala</th><th>Adultos</th><th>Niños</th><th>Subtotal</th><th>IVA</th><th>Total</th><th>Acciones</th>
</tr>

<?php
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>".htmlspecialchars($row['nombre'])."</td>
            <td>".htmlspecialchars($row['pelicula'])."</td>
            <td>".htmlspecialchars($row['tipo_sala'])."</td>
            <td>{$row['boleto_ad']}</td>
            <td>{$row['boleto_ni']}</td>
            <td>$".number_format($row['subtotal'], 2)."</td>
            <td>$".number_format($row['iva'], 2)."</td>
            <td>$".number_format($row['total'], 2)."</td>
            <td>
                <a class='btn-accion' href='editar.php?id={$row['id']}'>✏️ Modificar</a>
                <a class='btn-accion' href='?eliminar={$row['id']}' onclick=\"return confirm('¿Seguro que quieres eliminar este registro?');\">🗑 Eliminar</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='10'>No hay registros</td></tr>";
}
$conn->close();
?>
</table>

<!-- 🔹 Botones inferiores -->
<a href="index.html" class="boton">🎫 Volver al Formulario</a>
<a href="graficas.php" class="boton boton-ver-graficas">📊 Ver Gráficas</a>

</body>
</html>
