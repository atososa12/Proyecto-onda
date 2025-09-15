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
    $fecha = $_POST["fecha"];//la que elige el cliente
    $nombre = $_POST["nombre"];

    $sql = "INSERT INTO historia (titulo, fecha, relato,nombre) 
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $titulo, $fecha, $relato,$nombre);

    if ($stmt->execute()) {
        echo "<p> Relato guardado correctamente</p>";
    } else {
        echo "<p> Error, relato no guardado: " . $conn->error . "</p>";
    }
}