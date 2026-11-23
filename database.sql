-- ============================================================================
-- Agenda Personal de Contactos - Script SQL Actualizado
-- Incluye mejoras:
--   * Opción A: Búsqueda por nombre, correo y ciudad (con índices)
--   * Opción D: Campo favorito (Sí / No) para filtrar contactos favoritos
-- ============================================================================
-- NOTA IMPORTANTE PARA INFINITYFREE / HOSTING GRATUITO:
--   En InfinityFree primero debes crear la base de datos desde el Control Panel
--   (sección "MySQL Databases"), luego abrir phpMyAdmin, seleccionar tu base
--   de datos y finalmente importar este archivo.
--
-- NOTA PARA XAMPP (LOCAL):
--   Crea una base de datos llamada "agenda_contactos" en phpMyAdmin y luego
--   importa este archivo (o descomenta las dos líneas siguientes).
-- ============================================================================

-- CREATE DATABASE IF NOT EXISTS agenda_contactos
--   CHARACTER SET utf8mb4
--   COLLATE utf8mb4_unicode_ci;
-- USE agenda_contactos;

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    ciudad VARCHAR(100) NOT NULL,
    favorito TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = No, 1 = Sí',
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_correo (correo),
    INDEX idx_ciudad (ciudad),
    INDEX idx_favorito (favorito)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo iniciales para verificar el funcionamiento inmediato
INSERT INTO contactos (nombre, telefono, correo, ciudad, favorito) VALUES
('María Fernanda Paredes', '0991234567', 'maria.paredes@ejemplo.ec', 'Cuenca', 1),
('Carlos Andrés Mendoza', '0987654321', 'carlos.mendoza@ejemplo.ec', 'Quito', 1),
('Sofía Valentina Rojas', '0994561230', 'sofia.rojas@ejemplo.ec', 'Loja', 0),
('Diego Alejandro Castro', '0978901234', 'diego.castro@ejemplo.ec', 'Guayaquil', 0),
('Ana Gabriela Ortiz', '0996547891', 'ana.ortiz@ejemplo.ec', 'Cuenca', 1);
