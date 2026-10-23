-- MIRA: datos de prueba (MySQL 8.0)
-- Generado con mysqldump a partir de los seeders (php artisan db:seed):
-- RolSeeder, AreaCategoriaSeeder y UsuarioDemoSeeder.
-- Correr después de esquema.sql.
--
-- Usuarios demo: ciudadano, operador, jefe, ejecutor y admin @mira.local.
-- La contraseña de todos es "password" (guardada con BCrypt). Solo para
-- desarrollo y pruebas: en producción el seeder no crea usuarios demo.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- rol
INSERT INTO `rol` (`id`, `nombre`) VALUES (1,'ciudadano');
INSERT INTO `rol` (`id`, `nombre`) VALUES (2,'operador');
INSERT INTO `rol` (`id`, `nombre`) VALUES (3,'jefe_area');
INSERT INTO `rol` (`id`, `nombre`) VALUES (4,'ejecutor');
INSERT INTO `rol` (`id`, `nombre`) VALUES (5,'admin');

-- area
INSERT INTO `area` (`id`, `nombre`, `email_contacto`) VALUES (1,'Limpieza Urbana',NULL);

-- categoria
INSERT INTO `categoria` (`id`, `nombre`, `area_id`, `radio_duplicado_m`, `activa`) VALUES (1,'Contenedor desbordado',1,50,1);
INSERT INTO `categoria` (`id`, `nombre`, `area_id`, `radio_duplicado_m`, `activa`) VALUES (2,'Residuos fuera del contenedor',1,50,1);
INSERT INTO `categoria` (`id`, `nombre`, `area_id`, `radio_duplicado_m`, `activa`) VALUES (3,'Acumulación de basura',1,50,1);
INSERT INTO `categoria` (`id`, `nombre`, `area_id`, `radio_duplicado_m`, `activa`) VALUES (4,'Contenedor fuera de lugar',1,50,1);
INSERT INTO `categoria` (`id`, `nombre`, `area_id`, `radio_duplicado_m`, `activa`) VALUES (5,'Ramas y malezas',1,50,1);

-- usuario
INSERT INTO `usuario` (`id`, `nombre`, `email`, `cedula`, `password`, `telefono`, `rol_id`, `activo`, `remember_token`, `creado_en`) VALUES (1,'Ciudadano Demo','ciudadano@mira.local',NULL,'$2y$12$KPkfA2QujCitdnOPn8EAa.A3cMQTN5aH/sWIl0jHVHTHkxptxXtD.',NULL,1,1,NULL,'2026-10-23 22:35:19');
INSERT INTO `usuario` (`id`, `nombre`, `email`, `cedula`, `password`, `telefono`, `rol_id`, `activo`, `remember_token`, `creado_en`) VALUES (2,'Operador Demo','operador@mira.local',NULL,'$2y$12$yuV5Z46nMYQsD5kAHeE2ROP3UFUmkzgZafZSVhMCFZ8nYVjMoFjEe',NULL,2,1,NULL,'2026-10-23 22:35:19');
INSERT INTO `usuario` (`id`, `nombre`, `email`, `cedula`, `password`, `telefono`, `rol_id`, `activo`, `remember_token`, `creado_en`) VALUES (3,'Jefe de Área Demo','jefe@mira.local',NULL,'$2y$12$wdzSoTsRwNolM5H2lf43b.gCP1Yd8.4/auA0cwqANK2WxA5zBm3Z6',NULL,3,1,NULL,'2026-10-23 22:35:19');
INSERT INTO `usuario` (`id`, `nombre`, `email`, `cedula`, `password`, `telefono`, `rol_id`, `activo`, `remember_token`, `creado_en`) VALUES (4,'Ejecutor Demo','ejecutor@mira.local',NULL,'$2y$12$mej3HXo/VFy7Nv8jvz6Zseo.5fpzKEjETzVX1yNiZgTLJF2Tl77pa',NULL,4,1,NULL,'2026-10-23 22:35:22');
INSERT INTO `usuario` (`id`, `nombre`, `email`, `cedula`, `password`, `telefono`, `rol_id`, `activo`, `remember_token`, `creado_en`) VALUES (5,'Administrador Demo','admin@mira.local',NULL,'$2y$12$T14VNljqy6kYXIAJ4Zl3XeDI53uEwQn20Puzk3XnU1..Aynpu58MC',NULL,5,1,NULL,'2026-10-23 22:35:23');

-- usuario_area
INSERT INTO `usuario_area` (`usuario_id`, `area_id`) VALUES (3,1);
INSERT INTO `usuario_area` (`usuario_id`, `area_id`) VALUES (4,1);

SET FOREIGN_KEY_CHECKS = 1;
