<?php
// editar.php

// 🔹 Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$db   = "prac22";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("❌ Error de conexión a la base de datos.");

// 🔹 Obtener el ID del ticket a editar
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("❌ ID de ticket inválido.");

// 🔹 Obtener datos actuales del ticket
$sql = "SELECT * FROM cine WHERE id=$id";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) die("❌ Ticket no encontrado.");
$ticket = $res->fetch_assoc();

// 🔹 Precios base
$precios = ["Normal"=>75, "3D"=>110, "VIP"=>145];

// 🔹 Si se envió el formulario, actualizar el ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = isset($_POST['nombre']) ? $conn->real_escape_string($_POST['nombre']) : $ticket['nombre'];
    $pelicula   = isset($_POST['pelicula']) ? $conn->real_escape_string($_POST['pelicula']) : $ticket['pelicula'];
    $tipo_sala  = isset($_POST['tipo_sala']) ? trim($conn->real_escape_string($_POST['tipo_sala'])) : $ticket['tipo_sala'];
    $boleto_ad  = isset($_POST['boleto_ad']) ? (int)$_POST['boleto_ad'] : (int)$ticket['boleto_ad'];
    $boleto_ni  = isset($_POST['boleto_ni']) ? (int)$_POST['boleto_ni'] : (int)$ticket['boleto_ni'];

    // 🔹 Validar que los boletos de niños no sean mayores que los de adultos
    if ($boleto_ni > $boleto_ad) {
        echo "<div class='alert error'>❌ El número de boletos de niños no puede ser mayor que el de adultos.</div>";
    } else {
        // 🔹 Asegurar tipo de sala válido
        if (!array_key_exists($tipo_sala, $precios)) $tipo_sala = "Normal";

        // 🔹 Calcular precios
        $precio_adulto = $precios[$tipo_sala];
        $precio_nino = $precio_adulto * 0.85;

        $subtotal_adultos = $boleto_ad * $precio_adulto;
        $subtotal_ninos   = $boleto_ni * $precio_nino;
        $total_sin_iva    = $subtotal_adultos + $subtotal_ninos;
        $iva              = $total_sin_iva * 0.16;
        $total            = $total_sin_iva + $iva;

        // 🔹 Actualizar en la base de datos
        $sql = "UPDATE cine SET 
            nombre='$nombre',
            pelicula='$pelicula',
            tipo_sala='$tipo_sala',
            boleto_ad=$boleto_ad,
            boleto_ni=$boleto_ni,
            subtotal=$total_sin_iva,
            iva=$iva,
            total=$total
            WHERE id=$id";

        if ($conn->query($sql)) {
            echo "<div class='alert success'>✅ Ticket actualizado correctamente.</div>";
            echo "<a class='btn' href='consultar.php'>Volver a Consultar</a>";
        } else {
            echo "<div class='alert error'>❌ Error al actualizar: " . $conn->error . "</div>";
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>🎬 Editar Ticket</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
body {
    background: linear-gradient(135deg, #1c1c1c, #0d0d0d);
    color: #fff;
    padding: 40px;
}
h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #ff0040;
    text-shadow: 2px 2px #000;
}
form {
    background: #222;
    padding: 30px;
    border-radius: 15px;
    width: 450px;
    margin: 0 auto;
    box-shadow: 0 10px 25px rgba(0,0,0,0.7);
    transition: transform 0.2s;
}
form:hover { transform: scale(1.02); }
label { font-weight: bold; margin-top: 10px; display: block; }
input, select {
    width: 100%;
    padding: 10px;
    margin: 8px 0 20px 0;
    border-radius: 8px;
    border: none;
    outline: none;
    font-size: 16px;
}
input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; }
input[type="submit"] {
    background: linear-gradient(135deg, #ff0040, #ff2f6a);
    color: #fff;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
input[type="submit"]:hover {
    background: linear-gradient(135deg, #ff2f6a, #ff0040);
    transform: scale(1.05);
}
.alert {
    text-align: center;
    padding: 15px;
    margin: 20px auto;
    width: 450px;
    border-radius: 10px;
    font-weight: bold;
}
.alert.success { background: #28a745; color: #fff; }
.alert.error   { background: #dc3545; color: #fff; }
a.btn {
    display: inline-block;
    text-decoration: none;
    background: #ff0040;
    color: #fff;
    padding: 12px 25px;
    border-radius: 8px;
    text-align: center;
    margin: 10px auto;
    display: block;
    width: 200px;
    transition: 0.3s;
}
a.btn:hover { background: #ff2f6a; transform: scale(1.05); }
</style>
</head>
<body>

<h1>Editar Ticket</h1>

<form method="POST" action="">
    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($ticket['nombre']); ?>" required>

    <label>Película:</label>
    <select name="pelicula" required>
        <?php
        $peliculas = [
          "El conjuro: Últimos ritos","Los tipos malos 2","Una batalla tras otra",
          "El payaso del maíz","Los extraños: Capítulo 2","Camina o muere",
          "Batman Azteca: Choque de imperios","Teléfono negro 2","Cacería de brujas","Un mundo mejor"
        ];
        foreach ($peliculas as $p) {
            $sel = ($ticket['pelicula'] == $p) ? "selected" : "";
            echo "<option value=\"$p\" $sel>$p</option>";
        }
        ?>
    </select>

    <label>Tipo de sala:</label>
    <select name="tipo_sala" required>
        <?php
        $tipos = ["Normal","3D","VIP"];
        foreach ($tipos as $t) {
            $sel = ($ticket['tipo_sala'] == $t) ? "selected" : "";
            echo "<option value=\"$t\" $sel>$t</option>";
        }
        ?>
    </select>

    <label>No. de boletos adultos:</label>
    <input type="number" name="boleto_ad" min="1" value="<?php echo (int)$ticket['boleto_ad']; ?>" required>

    <label>No. de boletos niños:</label>
    <input type="number" name="boleto_ni" min="0" value="<?php echo (int)$ticket['boleto_ni']; ?>">

    <input type="submit" value="💾 Modificar Ticket">
</form>

</body>
</html>
