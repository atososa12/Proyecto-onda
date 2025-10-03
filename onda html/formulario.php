<?php
require_once __DIR__ . '/trayectos/app/Database.php';
$mensaje = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recibir datos del formulario
    $titulo = $_POST['titulo'] ?? '';
    $relato = $_POST['relato'] ?? '';
    $fecha  = $_POST['fecha'] ?? '';
    $nombre = $_POST['nombre'] ?? '';

    // Manejar foto opcional
    $foto_path = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['foto']['name']);
        $rutaDestino = "uploads/" . $nombreArchivo;

        // Crear carpeta uploads si no existe
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            $foto_path = $rutaDestino;
        }
    }

    try {
        $conn = Database::get();

        // Si se subió foto, insertamos con uri_fotos
        if ($foto_path) {
            $sql = "INSERT INTO historia (titulo, fecha, relato, nombre, uri_fotos) 
                    VALUES (:titulo, :fecha, :relato, :nombre, :uri_fotos)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':titulo'    => $titulo,
                ':fecha'     => $fecha,
                ':relato'    => $relato,
                ':nombre'    => $nombre,
                ':uri_fotos' => $foto_path
            ]);
        } else {
            // Si no hay foto
            $sql = "INSERT INTO historia (titulo, fecha, relato, nombre) 
                    VALUES (:titulo, :fecha, :relato, :nombre)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':titulo' => $titulo,
                ':fecha'  => $fecha,
                ':relato' => $relato,
                ':nombre' => $nombre
            ]);
        }

        echo "<p style='color:green;'>Relato compartido correctamente</p>";

    } catch (PDOException $e) {
        echo "<p style='color:red;'>Error al guardar el relato: " . $e->getMessage() . "</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Antonella Sosa, Vanessa Rinaldi, Jean-Pierre Dossou y Nicolás Chaccal">
    <meta name="description" content="Sitio desarrollado homenaje a ONDA.">
    <meta name="copyright" content="© 2025 Sitio desarrollado por estudiantes para el Día del Patrimonio. Todos los derechos reservados.">
    <title>Relato sobre ONDA</title>
    <link href="./css/formulario.css" rel="stylesheet" />
    <link href="./css/style.css" rel="stylesheet" />
</head>
<body>

<header>
    <img src="img/Logo.png" alt="Logo ONDA" />
    <nav>
        <ul>
            <li><a href="index.html">Inicio</a></li>
            <li>
                <a href="historia.html">Quiénes somos</a>
                <ul>
                    <li><a href="historia.html#historia">Historia</a></li>
                    <li><a href="historia.html#perazza">Eloy Perazza</a></li>
                </ul>
            </li>
            <li><a href="galeria.html">Galería</a></li>
            <li><a href="https://ondauruguay.ct.ws/">Trayectos</a></li>
            <li><a href="omnibus.html">Ómnibus</a></li>
            <li><a href="formulario.php" class="active">Relatos</a></li>
            <li><a class="btn-juego" href="juego.html">¡Juega con ONDA!</a></li>
        </ul>
    </nav>
</header>

<section class="formulario-section">
    <div class="formulario-container">
        <h1>Compartí tu relato sobre ONDA</h1>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <form method="POST" action="./formulario.php" enctype="multipart/form-data">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required />

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required />

            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" required />

            <label for="relato">Relato (máx. 500 caracteres):</label>
            <textarea id="relato" name="relato" rows="5" maxlength="500" required></textarea>

            <label for="foto">Compartí una foto (opcional):</label>
            <input type="file" id="foto" name="foto" accept="image/*" />

            <button type="submit">Enviar</button>
        </form>
    </div>
</section>

<footer>
    <img src="img/logo_utu.png" alt="Logo UTU">
    <p>©2025 - Sitio desarrollado por estudiantes para el Día del Patrimonio</p>
</footer>

</body>
</html>