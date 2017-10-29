CREATE TABLE `dw_tiendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tienda` varchar(90) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `clase` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
