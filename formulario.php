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

    if ($stmt->execute()) {
        echo "<p> Relato guardado correctamente</p>";
    } else {
        echo "<p> Error, relato no guardado: " . $conn->error . "</p>";
    }
}