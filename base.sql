Drop database pagina;
CREATE DATABASE pagina;
USE pagina;
-- Tabla de administradores
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    apellido VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(255) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    pregunta VARCHAR(255) NOT NULL,
    respuesta_hash VARCHAR(255) NOT NULL,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


INSERT INTO administradores (nombre, apellido, telefono, correo, contrasena, pregunta, respuesta_hash)
VALUES ('Brad Andrew', 'Flores Olivares', '915191773', '73132386@institutocajas.info', '$2y$10$IiVUC64g11fBRG1Msgr2buebnl920wMxUqvfIjoKgYVvYTAVl0.au', 'Nombre de tu primer mascota', '$2y$10$EjemploDeHashDeRespuesta');

-- Tabla de logo
CREATE TABLE logo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_imagen VARCHAR(512) NOT NULL,
    alt_texto TEXT DEFAULT '',
    administrador_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id),
    INDEX (administrador_id)
);

-- Tabla de carrusel
CREATE TABLE carrusel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imagen_fondo VARCHAR(255) NOT NULL,
    texto_encabezado VARCHAR(255) NOT NULL,
    texto_descripcion TEXT NOT NULL,
    texto_boton VARCHAR(255) NOT NULL,
    enlace_boton VARCHAR(255) NOT NULL,
    administrador_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id)
);
INSERT INTO carrusel (imagen_fondo, texto_encabezado, texto_descripcion, texto_boton, enlace_boton, administrador_id, fecha_actualizacion)
VALUES 
('../img/fondo3.jpg', 'Exclusividades', 'Mira nuestros productos naturales', 'Contactanos', 'http://localhost/agricultura/contacto.php', 1, '2024-09-03 00:24:41'),
('../img/fondo2.jpg', 'La abeja reina', 'Bienvenido a "La Abeja reina"', 'Mas información...', 'http://localhost/agricultura/productos.php', 1, '2024-09-03 00:25:29');


CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    icono VARCHAR(255) NOT NULL,
    subtitulo VARCHAR(255) NOT NULL,
    fondo_imagen VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    administrador_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id)
);
INSERT INTO servicios (icono, subtitulo, fondo_imagen, texto, administrador_id, fecha_actualizacion)
VALUES 
('../img/bien2.gif', 'Colmenas', '../img/fondo2.jpg', 'asdfghjklñlkjhgfdsa', 1, '2024-09-02 23:49:43'),
('../img/bien2.gif', 'Abejas', '../img/fondo.jpg', 'asdfghjk', 1, '2024-09-02 23:51:35'),
('../img/bien2.gif', 'talvez', '../img/fondo2.jpg', 'asdfghjkljhgfds', 1, '2024-09-02 23:53:19');

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imagen VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    marca VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    telefono1 VARCHAR(255) NOT NULL,
    telefono2 VARCHAR(255) NOT NULL,
    administrador_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id)
);

-- Tabla de galería
CREATE TABLE galeria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imagen VARCHAR(255) NOT NULL,
    administrador_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id)
);

-- Tabla de contacto
CREATE TABLE contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ubicacion VARCHAR(255) NOT NULL,
    telefono1 VARCHAR(20),
    telefono2 VARCHAR(20),
    mapa_iframe TEXT,
    administrador_id INT,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id)
);
INSERT INTO contacto (ubicacion, telefono1, telefono2, mapa_iframe, administrador_id, fecha_actualizacion)
VALUES ('Raimondi Xd Xd Xd Xd', '915191773', '992062943', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.7395588901836!2d-75.29062179011808!3d-12.061432088127175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x910e95992f5a5679%3A0xa9e2c9e771d33204!2sMiguel%20Grau%20300%2C%20Chupaca%2012456!5e0!3...', 1, '2024-08-31 15:24:35');


-- Tabla de registro de actividades
CREATE TABLE registro_actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    administrador_id INT,
    tabla_modificada VARCHAR(255) NOT NULL,
    accion ENUM('AGREGAR', 'EDITAR', 'ELIMINAR') NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (administrador_id) REFERENCES administradores(id)
);

SELECT a.nombre, a.apellido, ra.tabla_modificada, ra.accion, ra.fecha
    FROM registro_actividades ra
    JOIN administradores a ON ra.administrador_id = a.id
    ORDER BY ra.fecha DESC
    
