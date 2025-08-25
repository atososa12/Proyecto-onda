
-- Tabla Omnibus
CREATE TABLE IF NOT EXISTS omnibus (
            id INT AUTO_INCREMENT PRIMARY KEY,
            modelo ENUM(" . $_ENV['DB_MODELS'] . ") NOT NULL,
            anio INT(4) NOT NULL,
            estado ENUM('activo', 'vintage', 'baja') DEFAULT 'vintage'
);
-- Tabla agencia
CREATE TABLE agencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    ciudad VARCHAR(50) NOT NULL
);

-- Tabla trayecto (con claves foráneas)
CREATE TABLE trayecto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origen VARCHAR(50) NOT NULL,
    destino VARCHAR(50) NOT NULL,
    duracion VARCHAR(20) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    omnibus_id INT,
    agencia_id INT,
    FOREIGN KEY (omnibus_id) REFERENCES omnibus(id),
    FOREIGN KEY (agencia_id) REFERENCES agencia(id)
);



CREATE TABLE relato (
id_relato INT AUTO_INCREMENT PRIMARY KEY,
contenido TEXT,
id_trayecto INT,
FOREIGN KEY (id_trayecto) REFERENCES trayecto (id_trayecto)
);