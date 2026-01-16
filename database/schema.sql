-- Crear base de datos
CREATE DATABASE IF NOT EXISTS proyecto
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE proyecto;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    es_admin BOOLEAN DEFAULT FALSE,
    estado ENUM('activo', 'suspendido') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de mensajes
CREATE TABLE mensajes (
    id_mensaje INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    visible BOOLEAN DEFAULT TRUE,
    me_gusta INT UNSIGNED DEFAULT 0,
    risa INT UNSIGNED DEFAULT 0,
    triste INT UNSIGNED DEFAULT 0,
    enfado INT UNSIGNED DEFAULT 0,
    caca INT UNSIGNED DEFAULT 0,
    sorpresa INT UNSIGNED DEFAULT 0,
    rezar INT UNSIGNED DEFAULT 0,
    calavera INT UNSIGNED DEFAULT 0,
    corazon INT UNSIGNED DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);


-- Tabla de reportes
CREATE TABLE reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_mensaje INT NOT NULL,
    motivo TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_mensaje) REFERENCES mensajes(id_mensaje) ON DELETE CASCADE,
    UNIQUE (id_usuario, id_mensaje)
);

-- (Opcional) Tabla de bloqueos
CREATE TABLE bloqueos (
    id_bloqueo INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    motivo VARCHAR(255),
    fecha_bloqueo DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de reacciones (Para controlar 1 voto por usuario/mensaje)
CREATE TABLE reacciones (
    id_reaccion INT AUTO_INCREMENT PRIMARY KEY,
    id_mensaje INT NOT NULL,
    id_usuario INT NULL,
    cookie_id VARCHAR(64) NULL,
    tipo VARCHAR(20) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mensaje) REFERENCES mensajes(id_mensaje) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    UNIQUE KEY unique_user_msg (id_mensaje, id_usuario),
    UNIQUE KEY unique_guest_msg (id_mensaje, cookie_id)
);
-- Tabla de ecos (respuestas anónimas)
CREATE TABLE ecos (
    id_eco INT AUTO_INCREMENT PRIMARY KEY,
    id_mensaje INT NOT NULL,
    id_usuario INT NOT NULL,
    contenido TEXT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    visible BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_mensaje) REFERENCES mensajes(id_mensaje) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);
