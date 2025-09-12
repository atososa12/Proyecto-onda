-- create_db.sql
DROP DATABASE IF EXISTS transportehistorico;
CREATE DATABASE transportehistorico
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
USE transportehistorico;

-- 1) TABLA: trayecto
DROP TABLE IF EXISTS trayecto;
CREATE TABLE trayecto (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  descripcion_trayecto VARCHAR(500) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2) TABLA: agencia  (incluye columnas trayecto_id y km_en_ruta como en tu dump)
DROP TABLE IF EXISTS agencia;
CREATE TABLE agencia (
  id INT(11) NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  ubicacion VARCHAR(100) DEFAULT NULL,
  link_foto_agencia VARCHAR(500) DEFAULT NULL,
  trayecto_id INT(11) DEFAULT NULL,
  km_en_ruta DECIMAL(8,2) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY fk_agencia_trayecto (trayecto_id),
  CONSTRAINT fk_agencia_trayecto FOREIGN KEY (trayecto_id) REFERENCES trayecto(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3) TABLA: omnibus
DROP TABLE IF EXISTS omnibus;
CREATE TABLE omnibus (
  id INT(11) NOT NULL AUTO_INCREMENT,
  modelo ENUM('Leyland','Scania','Mercedes','Marcopolo','Otros') NOT NULL,
  anio INT(4) NOT NULL,
  estado ENUM('activo','vintage','baja') DEFAULT 'vintage',
  link_foto_omnibus VARCHAR(500) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4) TABLA: historia  (FKs apuntan a agencia/omnibus/trayecto reales)
DROP TABLE IF EXISTS historia;
CREATE TABLE historia (
  id INT(11) NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(200) NOT NULL,
  fecha DATE NOT NULL,
  id_trayecto INT(11) DEFAULT NULL,
  uri_historia VARCHAR(500) DEFAULT NULL,
  uri_fotos VARCHAR(500) DEFAULT NULL,
  id_agencia INT(11) DEFAULT NULL,
  id_agencia_origen INT(11) DEFAULT NULL,
  id_agencia_destino INT(11) DEFAULT NULL,
  id_omnibus INT(11) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_hist_trayecto (id_trayecto),
  KEY fk_hist_agencia (id_agencia),
  KEY fk_hist_origen (id_agencia_origen),
  KEY fk_hist_destino (id_agencia_destino),
  KEY fk_hist_omnibus (id_omnibus),
  CONSTRAINT fk_hist_agencia   FOREIGN KEY (id_agencia)         REFERENCES agencia(id),
  CONSTRAINT fk_hist_origen    FOREIGN KEY (id_agencia_origen)  REFERENCES agencia(id),
  CONSTRAINT fk_hist_destino   FOREIGN KEY (id_agencia_destino) REFERENCES agencia(id),
  CONSTRAINT fk_hist_omnibus   FOREIGN KEY (id_omnibus)         REFERENCES omnibus(id),
  CONSTRAINT fk_hist_trayecto  FOREIGN KEY (id_trayecto)        REFERENCES trayecto(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5) TABLA: trayecto_agencia (relación N:M con km y rol)
DROP TABLE IF EXISTS trayecto_agencia;
CREATE TABLE trayecto_agencia (
  id INT(11) NOT NULL AUTO_INCREMENT,
  trayecto_id INT(11) NOT NULL,
  agencia_id INT(11) NOT NULL,
  km_en_ruta DECIMAL(7,2) DEFAULT NULL,
  rol ENUM('origen','intermedia','destino') DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_trayecto_agencia (trayecto_id, agencia_id),
  KEY idx_ta_agencia (agencia_id),
  CONSTRAINT ta_fk_trayecto FOREIGN KEY (trayecto_id) REFERENCES trayecto(id),
  CONSTRAINT ta_fk_agencia  FOREIGN KEY (agencia_id)  REFERENCES agencia(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
