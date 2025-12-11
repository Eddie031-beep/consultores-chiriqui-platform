-- 1. CONFIGURACIÓN INICIAL
CREATE DATABASE IF NOT EXISTS consultores_chiriqui CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE consultores_chiriqui;

-- 2. TABLAS PRINCIPALES
CREATE TABLE `empresas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo` enum('publica','privada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'privada',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dv` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_contacto` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sitio_web` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sector` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activa','inactiva') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datos_facturacion_completos` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_empresas_ruc` (`ruc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int unsigned DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` int unsigned NOT NULL,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `ultimo_login` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuarios_email` (`email`),
  KEY `fk_usuarios_empresa` (`empresa_id`),
  KEY `fk_usuarios_rol` (`rol_id`),
  CONSTRAINT `fk_usuarios_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABLAS DE CANDIDATOS (FALTABAN EN TU SCHEMA ANTERIOR)
CREATE TABLE `solicitantes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cedula` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_ruta` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_login` datetime DEFAULT NULL,
  `nacionalidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Panamá',
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('masculino','femenino','otro','prefiero_no_decir') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_civil` enum('soltero','casado','unido','divorciado','viudo') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pais` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Panamá',
  `provincia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono_secundario` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perfil_completado` tinyint(1) DEFAULT '0',
  `habilidades` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_solicitantes_email` (`email`),
  UNIQUE KEY `uk_solicitantes_cedula` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `candidato_educacion` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitante_id` int unsigned NOT NULL,
  `institucion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_graduacion` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitante_id` (`solicitante_id`),
  CONSTRAINT `candidato_educacion_ibfk_1` FOREIGN KEY (`solicitante_id`) REFERENCES `solicitantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `candidato_experiencia` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `solicitante_id` int unsigned NOT NULL,
  `puesto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `empresa` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `trabajo_actual` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_exp_solicitante` (`solicitante_id`),
  CONSTRAINT `fk_exp_solicitante` FOREIGN KEY (`solicitante_id`) REFERENCES `solicitantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. VACANTES Y PEAJES
CREATE TABLE `peajes_tarifas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_plan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `rango_desde` int unsigned DEFAULT NULL,
  `rango_hasta` int unsigned DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vacantes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int unsigned NOT NULL,
  `peaje_tarifa_id` int unsigned DEFAULT NULL,
  `titulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad_plazas` int unsigned DEFAULT '0',
  `tipo_contrato` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salario_min` decimal(10,2) DEFAULT NULL,
  `salario_max` decimal(10,2) DEFAULT NULL,
  `costo_por_vista` decimal(10,2) DEFAULT '1.00',
  `ubicacion` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modalidad` enum('presencial','remoto','hibrido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'presencial',
  `fecha_publicacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_cierre` datetime DEFAULT NULL,
  `estado` enum('abierta','cerrada','pausada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_vacantes_slug` (`slug`),
  KEY `fk_vacantes_peaje` (`peaje_tarifa_id`),
  KEY `idx_vacantes_empresa` (`empresa_id`),
  CONSTRAINT `fk_vacantes_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_vacantes_peaje` FOREIGN KEY (`peaje_tarifa_id`) REFERENCES `peajes_tarifas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. INTERACCIONES Y POSTULACIONES
CREATE TABLE `interacciones_vacante` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vacante_id` int unsigned NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo_interaccion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `origen` enum('chatbot','web','compartido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solicitante_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_interacciones_vacante` (`vacante_id`,`fecha_hora`),
  CONSTRAINT `fk_interacciones_vacante` FOREIGN KEY (`vacante_id`) REFERENCES `vacantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `postulaciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `solicitante_id` int unsigned NOT NULL,
  `vacante_id` int unsigned NOT NULL,
  `fecha_postulacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','revisado','aceptado','rechazado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `comentarios` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_postulaciones_unica` (`solicitante_id`,`vacante_id`),
  KEY `fk_postulaciones_solicitante` (`solicitante_id`),
  KEY `fk_postulaciones_vacante` (`vacante_id`),
  CONSTRAINT `fk_postulaciones_solicitante` FOREIGN KEY (`solicitante_id`) REFERENCES `solicitantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_postulaciones_vacante` FOREIGN KEY (`vacante_id`) REFERENCES `vacantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `resenas_vacante` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vacante_id` int unsigned NOT NULL,
  `solicitante_id` int unsigned NOT NULL,
  `calificacion` int NOT NULL DEFAULT '5',
  `comentario` text COLLATE utf8mb4_unicode_ci,
  `reportado` tinyint(1) DEFAULT '0',
  `razon_reporte` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_resena_unica` (`vacante_id`,`solicitante_id`),
  KEY `solicitante_id` (`solicitante_id`),
  CONSTRAINT `resenas_vacante_chk_1` CHECK ((`calificacion` between 1 and 5)),
  CONSTRAINT `resenas_vacante_ibfk_1` FOREIGN KEY (`vacante_id`) REFERENCES `vacantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resenas_vacante_ibfk_2` FOREIGN KEY (`solicitante_id`) REFERENCES `solicitantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. FACTURACIÓN Y CONTRATOS
CREATE TABLE `facturas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int unsigned NOT NULL,
  `numero_fiscal` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_emision` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_vencimiento` date DEFAULT NULL,
  `periodo_desde` date NOT NULL,
  `periodo_hasta` date NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `itbms` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('emitida','pagada','no_pagada','en_revision','anulada') COLLATE utf8mb4_unicode_ci DEFAULT 'no_pagada',
  `token_publico` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cufe` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protocolo_autorizacion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clave_acceso` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_autorizacion` datetime DEFAULT NULL,
  `fecha_recepcion_dgi` datetime DEFAULT NULL,
  `qr_data` text COLLATE utf8mb4_unicode_ci,
  `fecha_confirmacion` datetime DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_facturas_numero` (`numero_fiscal`),
  UNIQUE KEY `uk_facturas_token` (`token_publico`),
  KEY `fk_facturas_empresa` (`empresa_id`),
  CONSTRAINT `fk_facturas_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `facturas_detalle` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `factura_id` int unsigned NOT NULL,
  `vacante_id` int unsigned DEFAULT NULL,
  `tipo_interaccion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad_interacciones` int unsigned NOT NULL DEFAULT '0',
  `tarifa_unitaria` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_linea` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `fk_facturas_detalle_factura` (`factura_id`),
  KEY `fk_facturas_detalle_vacante` (`vacante_id`),
  CONSTRAINT `fk_facturas_detalle_factura` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_facturas_detalle_vacante` FOREIGN KEY (`vacante_id`) REFERENCES `vacantes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contratos_empresas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `empresa_id` int unsigned NOT NULL,
  `version_contrato` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_aceptacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_aceptacion` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `texto_resumen` text COLLATE utf8mb4_unicode_ci,
  `ruta_documento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('vigente','vencido') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vigente',
  PRIMARY KEY (`id`),
  KEY `fk_contratos_empresas_empresa` (`empresa_id`),
  CONSTRAINT `fk_contratos_empresas_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. OTROS (CHATBOT, CONFIG)
CREATE TABLE `chatbot_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pregunta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `respuesta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vacante_id` int unsigned DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_chatbot_logs_vacante` (`vacante_id`),
  KEY `idx_chatbot_session` (`session_id`,`fecha_hora`),
  CONSTRAINT `fk_chatbot_logs_vacante` FOREIGN KEY (`vacante_id`) REFERENCES `vacantes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `configuraciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_config_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos base requeridos
INSERT INTO roles (id, nombre, descripcion) VALUES
(1,'admin_consultora','Usuario interno de la empresa consultora'),
(2,'empresa_admin','Usuario administrador de una empresa cliente'),
(3,'candidato','Persona que busca empleo')
ON DUPLICATE KEY UPDATE nombre=nombre;

INSERT INTO configuraciones (codigo, valor, descripcion) VALUES
('ITBMS','0.07','Impuesto ITBMS 7%'),
('PEAJE_DEFAULT','0.10','B/. 0.10 por interacción si no hay tarifa configurada')
ON DUPLICATE KEY UPDATE valor=valor;