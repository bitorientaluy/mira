-- MIRA: esquema de la base de datos (MySQL 8.0)
-- Generado con mysqldump --no-data a partir de las migraciones de Laravel
-- (php artisan migrate sobre una base vacía). Las migraciones son la fuente:
-- si cambian, este archivo se vuelve a generar.
--
-- Uso (borra y recrea las 10 tablas del dominio):
--   mysql -u root -p mira < database/sql/esquema.sql
--   mysql -u root -p mira < database/sql/datos_prueba.sql
--
-- Las tablas propias de Laravel (sesiones, cache, colas, migraciones) no se
-- incluyen: las crean las migraciones del framework.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `rol`;
CREATE TABLE `rol` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rol_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `email_contacto` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `cedula` char(8) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `rol_id` int unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_email_unique` (`email`),
  UNIQUE KEY `usuario_cedula_unique` (`cedula`),
  KEY `usuario_rol_id_foreign` (`rol_id`),
  CONSTRAINT `usuario_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `usuario_area`;
CREATE TABLE `usuario_area` (
  `usuario_id` bigint unsigned NOT NULL,
  `area_id` int unsigned NOT NULL,
  PRIMARY KEY (`usuario_id`,`area_id`),
  KEY `usuario_area_area_id_foreign` (`area_id`),
  CONSTRAINT `usuario_area_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usuario_area_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(60) NOT NULL,
  `area_id` int unsigned NOT NULL,
  `radio_duplicado_m` smallint unsigned NOT NULL DEFAULT '50',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoria_nombre_unique` (`nombre`),
  KEY `categoria_area_id_foreign` (`area_id`),
  CONSTRAINT `categoria_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `barrio`;
CREATE TABLE `barrio` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `limite` polygon NOT NULL SRID 4326,
  PRIMARY KEY (`id`),
  UNIQUE KEY `barrio_nombre_unique` (`nombre`),
  SPATIAL KEY `barrio_limite_spatialindex` (`limite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `incidencia`;
CREATE TABLE `incidencia` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `ciudadano_id` bigint unsigned NOT NULL,
  `categoria_id` int unsigned NOT NULL,
  `area_id` int unsigned NOT NULL,
  `ejecutor_id` bigint unsigned DEFAULT NULL,
  `barrio_id` int unsigned DEFAULT NULL,
  `duplicada_de_id` bigint unsigned DEFAULT NULL,
  `descripcion` text NOT NULL,
  `ubicacion` point NOT NULL SRID 4326,
  `direccion` varchar(200) DEFAULT NULL,
  `foto_ruta` varchar(255) DEFAULT NULL,
  `estado` enum('Pendiente','Validada','En proceso','Resuelto','Descartado') NOT NULL DEFAULT 'Pendiente',
  `motivo_descarte` enum('Falso','Duplicado','No corresponde') DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `asignado_en` datetime DEFAULT NULL,
  `resuelto_en` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incidencia_codigo_unique` (`codigo`),
  KEY `incidencia_ciudadano_id_foreign` (`ciudadano_id`),
  KEY `incidencia_ejecutor_id_foreign` (`ejecutor_id`),
  KEY `incidencia_duplicada_de_id_foreign` (`duplicada_de_id`),
  SPATIAL KEY `incidencia_ubicacion_spatialindex` (`ubicacion`),
  KEY `incidencia_barrio_id_foreign` (`barrio_id`),
  KEY `incidencia_categoria_id_index` (`categoria_id`),
  KEY `incidencia_area_id_index` (`area_id`),
  KEY `incidencia_estado_index` (`estado`),
  KEY `incidencia_creado_en_index` (`creado_en`),
  CONSTRAINT `incidencia_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`),
  CONSTRAINT `incidencia_barrio_id_foreign` FOREIGN KEY (`barrio_id`) REFERENCES `barrio` (`id`),
  CONSTRAINT `incidencia_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  CONSTRAINT `incidencia_ciudadano_id_foreign` FOREIGN KEY (`ciudadano_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `incidencia_duplicada_de_id_foreign` FOREIGN KEY (`duplicada_de_id`) REFERENCES `incidencia` (`id`),
  CONSTRAINT `incidencia_ejecutor_id_foreign` FOREIGN KEY (`ejecutor_id`) REFERENCES `usuario` (`id`),
  CONSTRAINT `chk_incidencia_motivo` CHECK (((`estado` = _utf8mb4'Descartado') = (`motivo_descarte` is not null)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `apoyo_incidencia`;
CREATE TABLE `apoyo_incidencia` (
  `usuario_id` bigint unsigned NOT NULL,
  `incidencia_id` bigint unsigned NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usuario_id`,`incidencia_id`),
  KEY `apoyo_incidencia_incidencia_id_foreign` (`incidencia_id`),
  CONSTRAINT `apoyo_incidencia_incidencia_id_foreign` FOREIGN KEY (`incidencia_id`) REFERENCES `incidencia` (`id`) ON DELETE CASCADE,
  CONSTRAINT `apoyo_incidencia_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `historial_incidencia`;
CREATE TABLE `historial_incidencia` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `incidencia_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `estado_anterior` enum('Pendiente','Validada','En proceso','Resuelto','Descartado') DEFAULT NULL,
  `estado_nuevo` enum('Pendiente','Validada','En proceso','Resuelto','Descartado') NOT NULL,
  `comentario` text,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `historial_incidencia_incidencia_id_foreign` (`incidencia_id`),
  KEY `historial_incidencia_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `historial_incidencia_incidencia_id_foreign` FOREIGN KEY (`incidencia_id`) REFERENCES `incidencia` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `historial_incidencia_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notificacion`;
CREATE TABLE `notificacion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned NOT NULL,
  `incidencia_id` bigint unsigned NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `enviada_en` datetime DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT '0',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notificacion_usuario_id_foreign` (`usuario_id`),
  KEY `notificacion_incidencia_id_foreign` (`incidencia_id`),
  CONSTRAINT `notificacion_incidencia_id_foreign` FOREIGN KEY (`incidencia_id`) REFERENCES `incidencia` (`id`),
  CONSTRAINT `notificacion_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
