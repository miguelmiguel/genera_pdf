CREATE TABLE `ot_proceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_proceso` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `archivo_entrada` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `ruta_salida` varchar(250) COLLATE utf8_spanish_ci NOT NULL DEFAULT '',
  `plantilla` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `cliente` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `estado_proceso` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fecha_proceso` (`fecha_proceso`),
  KEY `archivo_entrada` (`archivo_entrada`),
  KEY `plantilla` (`plantilla`),
  KEY `cliente` (`cliente`),
  KEY `ruta_salida` (`ruta_salida`),
  KEY `estado_proceso` (`estado_proceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Recopila los procesos ejecutados para la generacion de pdf';

CREATE TABLE `ot_documento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proceso` int(11) NOT NULL,
  `archivo_salida` varchar(250) COLLATE utf8_spanish_ci NOT NULL,
  `estado_archivo` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_hora_generado` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `proceso` (`proceso`),
  KEY `archivo_salida` (`archivo_salida`),
  KEY `estado_archivo` (`estado_archivo`),
  KEY `fecha_hora_generado` (`fecha_hora_generado`),
  CONSTRAINT `ot_documento_ibfk_1` FOREIGN KEY (`proceso`) REFERENCES `ot_proceso` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci COMMENT='Recopila los archivos generados en pdf';

