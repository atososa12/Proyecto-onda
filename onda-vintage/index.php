<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ONDA Vintage - Viajes 1980-1990</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: url('fondo-vintage.jpg');
            color: #333;
        }
        .mapa {
            width: 80%;
            margin: 0 auto;
            border: 3px solid #8B4513; /* Marrón vintage */
        }
        .omnibus-card {
            background: #FFF8DC;
            border: 1px dashed #8B4513;
            padding: 10px;
            margin: 10px;
        }
    </style>
</head>
<body>
    <h1>ONDA - Rutas Vintage (1980-1990)</h1>
    
    <!-- Mapa Interactivo (imagen clickeable) -->
    <div class="mapa">
        <img src="mapa-uruguay-vintage.png" usemap="#rutas">
        <map name="rutas">
            <area shape="rect" coords="100,200,150,250" href="trayecto.php?id=1" title="Montevideo a Paysandú">
            <area shape="rect" coords="300,300,350,350" href="trayecto.php?id=2" title="Colonia a Salto">
        </map>
    </div>

    <!-- Lista de Ómnibus Vintage -->
    <h2>Nuestra Flota</h2>
    <?php
    // Ejemplo de conexión a DB y muestra de ómnibus
    $db = new PDO('mysql:host=localhost;dbname=onda_vintage', 'root', '');
    $query = $db->query("SELECT * FROM omnibus WHERE estado = 'vintage'");
    while ($omnibus = $query->fetch(PDO::FETCH_ASSOC)) {
        echo '<div class="omnibus-card">';
        echo '<h3>' . $omnibus['modelo'] . ' (' . $omnibus['anio'] . ')</h3>';
        echo '<p>Matrícula: ' . $omnibus['matricula'] . '</p>';
        echo '</div>';
    }
    ?>
</body>
</html>
