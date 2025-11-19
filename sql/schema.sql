-- Crear base de datos
CREATE DATABASE IF NOT EXISTS consultores_chiriqui
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE consultores_chiriqui;

-- =========================
-- TABLA: empresas
-- =========================
CREATE TABLE empresas (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo              ENUM('publica','privada') NOT NULL DEFAULT 'privada',
  nombre            VARCHAR(150)              NOT NULL,
  ruc               VARCHAR(25)               NOT NULL,
  dv                VARCHAR(5)                NOT NULL,
  direccion         VARCHAR(255)              NOT NULL,
  provincia         VARCHAR(100)              NOT NULL,
  telefono          VARCHAR(50),
  email_contacto    VARCHAR(150),
  sitio_web         VARCHAR(200),
  sector            VARCHAR(100),
  estado            ENUM('activa','inactiva') NOT NULL DEFAULT 'activa',
  fecha_registro    DATETIME                  NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_empresas_ruc (ruc)
) ENGINE=InnoDB;

-- =========================
-- TABLA: roles
-- =========================
CREATE TABLE roles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(50)  NOT NULL,
  descripcion VARCHAR(255),
  UNIQUE KEY uk_roles_nombre (nombre)
) ENGINE=InnoDB;

-- =========================
-- TABLA: usuarios
--   - Usuarios de la consultora y de las empresas
-- =========================
CREATE TABLE usuarios (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id     INT UNSIGNED NULL,
  nombre         VARCHAR(100) NOT NULL,
  apellido       VARCHAR(100) NOT NULL,
  email          VARCHAR(150) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  rol_id         INT UNSIGNED NOT NULL,
  estado         ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
  ultimo_login   DATETIME NULL,
  creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NULL,
  UNIQUE KEY uk_usuarios_email (email),
  CONSTRAINT fk_usuarios_empresa
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_usuarios_rol
    FOREIGN KEY (rol_id) REFERENCES roles(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- TABLA: contratos_empresas
-- =========================
CREATE TABLE contratos_empresas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id      INT UNSIGNED NOT NULL,
  version_contrato VARCHAR(50) NOT NULL,
  fecha_aceptacion DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ip_aceptacion   VARCHAR(45),
  texto_resumen   TEXT,
  ruta_documento  VARCHAR(255),
  estado          ENUM('vigente','vencido') NOT NULL DEFAULT 'vigente',
  CONSTRAINT fk_contratos_empresas_empresa
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- TABLA: peajes_tarifas
--   - Tarifa por interacción / plan
-- =========================
CREATE TABLE peajes_tarifas (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre_plan    VARCHAR(100) NOT NULL,
  descripcion    VARCHAR(255),
  precio_unitario DECIMAL(10,2) NOT NULL, -- B/. por interacción
  rango_desde    INT UNSIGNED NULL,
  rango_hasta    INT UNSIGNED NULL,
  activo         TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- =========================
-- TABLA: vacantes
-- =========================
CREATE TABLE vacantes (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id      INT UNSIGNED NOT NULL,
  peaje_tarifa_id INT UNSIGNED NULL,
  titulo          VARCHAR(200) NOT NULL,
  slug            VARCHAR(220) NOT NULL,
  descripcion     TEXT         NOT NULL,
  tipo_contrato   VARCHAR(100) NOT NULL, -- tiempo completo, parcial, etc.
  salario_min     DECIMAL(10,2) NULL,
  salario_max     DECIMAL(10,2) NULL,
  ubicacion       VARCHAR(150) NOT NULL,
  modalidad       ENUM('presencial','remoto','hibrido') NOT NULL DEFAULT 'presencial',
  fecha_publicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_cierre    DATETIME NULL,
  estado          ENUM('abierta','cerrada','pausada') NOT NULL DEFAULT 'abierta',
  CONSTRAINT fk_vacantes_empresa
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_vacantes_peaje
    FOREIGN KEY (peaje_tarifa_id) REFERENCES peajes_tarifas(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  UNIQUE KEY uk_vacantes_slug (slug),
  KEY idx_vacantes_empresa (empresa_id)
) ENGINE=InnoDB;

-- =========================
-- TABLA: interacciones_vacante
--   - Para estadísticas y cálculo de peaje
-- =========================
CREATE TABLE interacciones_vacante (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  vacante_id       INT UNSIGNED NOT NULL,
  fecha_hora       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo_interaccion ENUM('ver_detalle','click_aplicar','chat_consulta')
                     NOT NULL DEFAULT 'ver_detalle',
  origen           ENUM('chatbot','web','compartido')
                     NOT NULL DEFAULT 'web',
  ip               VARCHAR(45),
  user_agent       VARCHAR(255),
  session_id       VARCHAR(100),
  CONSTRAINT fk_interacciones_vacante
    FOREIGN KEY (vacante_id) REFERENCES vacantes(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_interacciones_vacante (vacante_id, fecha_hora)
) ENGINE=InnoDB;

-- =========================
-- TABLA: facturas
-- =========================
CREATE TABLE facturas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  empresa_id      INT UNSIGNED NOT NULL,
  numero_fiscal   VARCHAR(50)  NOT NULL,
  fecha_emision   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  periodo_desde   DATE         NOT NULL,
  periodo_hasta   DATE         NOT NULL,
  subtotal        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  itbms           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total           DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  estado          ENUM('emitida','pagada','anulada') NOT NULL DEFAULT 'emitida',
  token_publico   VARCHAR(100) NOT NULL,
  CONSTRAINT fk_facturas_empresa
    FOREIGN KEY (empresa_id) REFERENCES empresas(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  UNIQUE KEY uk_facturas_numero (numero_fiscal),
  UNIQUE KEY uk_facturas_token (token_publico)
) ENGINE=InnoDB;

-- =========================
-- TABLA: facturas_detalle
-- =========================
CREATE TABLE facturas_detalle (
  id                     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  factura_id             INT UNSIGNED NOT NULL,
  vacante_id             INT UNSIGNED NULL,
  cantidad_interacciones INT UNSIGNED NOT NULL DEFAULT 0,
  tarifa_unitaria        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total_linea            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  CONSTRAINT fk_facturas_detalle_factura
    FOREIGN KEY (factura_id) REFERENCES facturas(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_facturas_detalle_vacante
    FOREIGN KEY (vacante_id) REFERENCES vacantes(id)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- TABLA: chatbot_logs
-- =========================
CREATE TABLE chatbot_logs (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  session_id  VARCHAR(100) NOT NULL,
  pregunta    TEXT         NOT NULL,
  respuesta   TEXT         NOT NULL,
  vacante_id  INT UNSIGNED NULL,
  fecha_hora  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_chatbot_logs_vacante
    FOREIGN KEY (vacante_id) REFERENCES vacantes(id)
    ON DELETE SET NULL ON UPDATE CASCADE,
  KEY idx_chatbot_session (session_id, fecha_hora)
) ENGINE=InnoDB;

-- =========================
-- TABLA: configuraciones
--   - Parámetros globales (ITBMS, etc.)
-- =========================
CREATE TABLE configuraciones (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo      VARCHAR(50)  NOT NULL,
  valor       VARCHAR(255) NOT NULL,
  descripcion VARCHAR(255),
  UNIQUE KEY uk_config_codigo (codigo)
) ENGINE=InnoDB;

-- =========================
-- DATOS INICIALES
-- =========================

-- Roles básicos
INSERT INTO roles (nombre, descripcion) VALUES
('admin_consultora', 'Usuario interno de la empresa consultora'),
('empresa_admin',    'Usuario administrador de una empresa cliente');

-- Configuración básica (ejemplo de ITBMS 7%)
INSERT INTO configuraciones (codigo, valor, descripcion) VALUES
('ITBMS', '0.07', 'Impuesto ITBMS 7%'),
('PEAJE_DEFAULT', '0.10', 'B/. 0.10 por interacción si no hay tarifa configurada');
