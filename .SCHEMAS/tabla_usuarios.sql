CREATE TABLE `teccheck`.`dw_usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(124) NULL DEFAULT '',
  `apellido` VARCHAR(124) NULL DEFAULT '',
  `correo` VARCHAR(255) NULL DEFAULT '',
  `contrasena` VARCHAR(300) NULL DEFAULT '',
  PRIMARY KEY (`id`));

INSERT INTO `teccheck`.`dw_usuarios` (`usuario`, `apellido`, `correo`, `contrasena`) VALUES ('Andrew', 'Gonzalez', 'dev_andrew@devworms.com', 'df733656293a19c54f69093ba916f0a1a2a3c151fc95c13f3a794c2631eeb3a6');
INSERT INTO `teccheck`.`dw_usuarios` (`usuario`, `apellido`, `correo`, `contrasena`) VALUES ('Ricardo', 'Osorio', 'dev_ricardo@devworms.com', 'df733656293a19c54f69093ba916f0a1a2a3c151fc95c13f3a794c2631eeb3a6');
