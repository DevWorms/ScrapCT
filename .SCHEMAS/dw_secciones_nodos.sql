
CREATE TABLE `dw_secciones_nodos` (
  `id_seccion` int(11) NOT NULL AUTO_INCREMENT,
  `seccion` varchar(45) DEFAULT NULL,
  `nodos` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id_seccion`),
  UNIQUE KEY `seccion_UNIQUE` (`seccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_1');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_2');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_3');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_4');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_5');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_6');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_7');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_8');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_9');
INSERT INTO `teccheck`.`dw_secciones_nodos` (`seccion`) VALUES ('seccion_10');

ALTER TABLE `teccheck`.`dw_secciones_nodos` 
ADD COLUMN `conjunto_paginas` TINYINT(2) NULL DEFAULT 0 AFTER `nodos`;
