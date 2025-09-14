<?php
// Conexión
$host = "localhost";
$dbname = "transportehistorico";
$user = "root"; 
$pass = "";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST["titulo"];
    $relato = $_POST["relato"];
    $fecha = date("Y-m-d");

    $sql = "INSERT INTO historia (titulo, fecha, relato) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $titulo, $fecha, $relato);

     if (!empty($titulo) && !empty($relato)) {
        // Consulta preparada
        $sql = "INSERT INTO historia (titulo, fecha, relato) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $titulo, $fecha, $relato);

        if ($stmt->execute()) {
            echo "<!DOCTYPE html>
            <html lang='es'>
            <head>
                <meta charset='UTF-8'>
                <title>Relato guardado</title>
            </head>
            <body>
                <h1>¡Gracias por compartir tu relato sobre ONDA!</h1>
                <p><strong>Título:</strong> " . htmlspecialchars($titulo) . "</p>
                <p><strong>Fecha:</strong> " . $fecha . "</p>
                <p><strong>Relato:</strong><br>" . nl2br(htmlspecialchars($relato)) . "</p>
            </body>
            </html>";
        } else {
            echo "<p>Error al guardar el relato: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Por favor completá todos los campos.</p>";
    }
}

$conn->close();
?>
        