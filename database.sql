-- ============================================
-- BASE DE DATOS: ARMADO_TABLEROS
-- ============================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS armado_tableros CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE armado_tableros;

-- ============================================
-- TABLA: CONTACTOS
-- ============================================

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    empresa VARCHAR(255),
    asunto VARCHAR(255) NOT NULL,
    mensaje LONGTEXT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    estado ENUM('nuevo', 'leído', 'respondido', 'cerrado') DEFAULT 'nuevo',
    respuesta LONGTEXT,
    fecha_respuesta DATETIME,
    INDEX idx_email (email),
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: USUARIOS ADMIN (OPCIONAL)
-- ============================================

CREATE TABLE IF NOT EXISTS usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'moderador') DEFAULT 'moderador',
    activo BOOLEAN DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: PROYECTOS (OPCIONAL - para portfolio)
-- ============================================

CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion LONGTEXT,
    categoria VARCHAR(100),
    imagen_url VARCHAR(500),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT 1,
    INDEX idx_categoria (categoria),
    INDEX idx_fecha (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLA: CONFIGURACIÓN GENERAL
-- ============================================

CREATE TABLE IF NOT EXISTS configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(100) NOT NULL UNIQUE,
    valor LONGTEXT,
    tipo ENUM('texto', 'numero', 'booleano', 'json') DEFAULT 'texto',
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar configuración inicial
INSERT INTO configuracion (clave, valor, tipo) VALUES
('empresa_nombre', 'Tu Empresa - Armado de Tableros', 'texto'),
('empresa_email', 'contacto@tuempresa.com', 'texto'),
('empresa_telefono', '+54 9 (Código) XXXX-XXXX', 'texto'),
('empresa_direccion', 'Calle Principal 123, Ciudad', 'texto'),
('empresa_descripcion', 'Soluciones profesionales en armado de tableros eléctricos', 'texto'),
('email_notificaciones_activo', '1', 'booleano'),
('formulario_recaptcha_activo', '0', 'booleano');

-- ============================================
-- VISTA: CONTACTOS PENDIENTES (OPCIONAL)
-- ============================================

CREATE VIEW IF NOT EXISTS contactos_pendientes AS
SELECT id, nombre, email, asunto, fecha_creacion, estado
FROM contactos
WHERE estado IN ('nuevo', 'respondido')
ORDER BY fecha_creacion DESC;
