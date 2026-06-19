-- database/schema.sql
-- Script de creación de tablas para SGROAS - Práctica 2
-- Motor: PostgreSQL 15

-- Extensión para UUID (opcional)
-- CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ============================================================
-- Tabla de usuarios del sistema
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    id            SERIAL PRIMARY KEY,
    nombre        VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    -- Nunca texto plano: se almacena el hash de password_hash() / bcrypt
    password_hash VARCHAR(255) NOT NULL,
    rol           VARCHAR(20)  NOT NULL DEFAULT 'operador'
                  CHECK (rol IN ('admin', 'operador', 'supervisor')),
    activo        BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at    TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at    TIMESTAMP    NOT NULL DEFAULT NOW()
);

-- ============================================================
-- Tabla de conductores (entidad CRUD principal)
-- ============================================================
CREATE TABLE IF NOT EXISTS conductores (
    id             SERIAL PRIMARY KEY,
    cedula         VARCHAR(13)  NOT NULL UNIQUE,
    nombres        VARCHAR(80)  NOT NULL,
    apellidos      VARCHAR(80)  NOT NULL,
    telefono       VARCHAR(15)  NOT NULL,
    email          VARCHAR(150),
    licencia_tipo  VARCHAR(2)   NOT NULL
                   CHECK (licencia_tipo IN ('A','B','C','D','E','F')),
    licencia_num   VARCHAR(20)  NOT NULL UNIQUE,
    fecha_venc_lic DATE,
    estado         VARCHAR(10)  NOT NULL DEFAULT 'activo'
                   CHECK (estado IN ('activo','inactivo','suspendido')),
    created_at     TIMESTAMP    NOT NULL DEFAULT NOW(),
    updated_at     TIMESTAMP    NOT NULL DEFAULT NOW()
);

-- ============================================================
-- Índices para optimizar búsquedas frecuentes
-- ============================================================
CREATE INDEX IF NOT EXISTS idx_conductores_cedula  ON conductores(cedula);
CREATE INDEX IF NOT EXISTS idx_conductores_estado  ON conductores(estado);
CREATE INDEX IF NOT EXISTS idx_usuarios_email      ON usuarios(email);

-- ============================================================
-- Trigger para actualizar updated_at automáticamente
-- ============================================================
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_conductores_updated_at
    BEFORE UPDATE ON conductores
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_usuarios_updated_at
    BEFORE UPDATE ON usuarios
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ============================================================
-- Datos de prueba
-- ============================================================
-- Usuario admin (contraseña: Admin123!)
-- Hash generado con: password_hash('Admin123!', PASSWORD_ARGON2ID)
INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES
(
  'Administrador SGROAS',
  'admin@sgroas.ec',
  '$argon2id$v=19$m=65536,t=4,p=1$hash_generado_al_ejecutar',
  'admin'
) ON CONFLICT (email) DO NOTHING;

-- Conductores de prueba
INSERT INTO conductores (cedula, nombres, apellidos, telefono, email, licencia_tipo, licencia_num, fecha_venc_lic) VALUES
('0912345678', 'Carlos',   'Mendoza Ruiz',    '0991234567', 'cmendoza@email.com',  'C', 'LIC-001-2020', '2026-12-31'),
('0923456789', 'María',    'López Gómez',     '0992345678', 'mlopez@email.com',    'B', 'LIC-002-2021', '2027-06-15'),
('0934567890', 'Roberto',  'Vera Castillo',   '0993456789', 'rvera@email.com',     'D', 'LIC-003-2019', '2025-08-20'),
('0945678901', 'Patricia', 'Intriago Suárez', '0994567890', 'pintriago@email.com', 'C', 'LIC-004-2022', '2028-03-10')
ON CONFLICT (cedula) DO NOTHING;
