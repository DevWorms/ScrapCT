-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 01-11-2017 a las 06:35:39
-- Versión del servidor: 5.7.19-0ubuntu0.16.04.1
-- Versión de PHP: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `teccheck`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dw_historial`
--

CREATE TABLE `dw_historial` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price_old` int(11) NOT NULL,
  `price_new` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `dw_historial`
--

INSERT INTO `dw_historial` (`id`, `product_id`, `price_old`, `price_new`, `created_at`) VALUES
(1, 61, 10000, 9999, '2017-10-31 14:36:01'),
(2, 61, 0, 10000, '2017-10-31 12:36:00'),
(3, 61, 0, 0, '2017-10-31 10:36:00'),
(4, 591, 17447, 17447, '2017-10-31 23:43:33'),
(5, 582, 16249, 16249, '2017-10-31 23:43:39'),
(6, 587, 12213, 10800, '2017-10-31 23:43:41'),
(7, 579, 45087, 45086, '2017-10-31 23:43:54'),
(8, 635, 16571, 16571, '2017-10-31 23:44:01'),
(9, 631, 12999, 12998, '2017-10-31 23:44:17'),
(10, 625, 2379, 2379, '2017-10-31 23:44:23'),
(11, 629, 5531, 5531, '2017-10-31 23:44:23'),
(12, 627, 12612, 12611, '2017-10-31 23:44:25'),
(13, 621, 8908, 8908, '2017-10-31 23:44:32'),
(14, 622, 7985, 7984, '2017-10-31 23:44:32'),
(15, 615, 4702, 4701, '2017-10-31 23:44:40'),
(16, 616, 21704, 21703, '2017-10-31 23:44:40'),
(17, 610, 2778, 2777, '2017-10-31 23:44:47'),
(18, 605, 4019, 4018, '2017-10-31 23:44:50'),
(19, 651, 5725, 5725, '2017-10-31 23:45:08'),
(20, 661, 7536, 7536, '2017-10-31 23:45:10'),
(21, 662, 4500, 4499, '2017-10-31 23:45:10'),
(22, 664, 5723, 5723, '2017-10-31 23:45:12'),
(23, 656, 4240, 4239, '2017-10-31 23:45:13'),
(24, 800, 4094, 4093, '2017-10-31 23:45:18'),
(25, 799, 2664, 2663, '2017-10-31 23:45:18'),
(26, 811, 29544, 29544, '2017-10-31 23:45:18'),
(27, 810, 110200, 110199, '2017-10-31 23:45:18'),
(28, 807, 5624, 5624, '2017-10-31 23:45:18'),
(29, 808, 4360, 4360, '2017-10-31 23:45:18'),
(30, 789, 11882, 11882, '2017-10-31 23:45:29'),
(31, 787, 892, 892, '2017-10-31 23:45:29'),
(32, 791, 34501, 34501, '2017-10-31 23:45:29'),
(33, 905, 7120, 7119, '2017-10-31 23:45:29'),
(34, 902, 8916, 8915, '2017-10-31 23:45:29'),
(35, 903, 8367, 8367, '2017-10-31 23:45:29'),
(36, 959, 3393, 3392, '2017-10-31 23:45:29'),
(37, 961, 2800, 2799, '2017-10-31 23:45:36'),
(38, 1008, 9428, 9427, '2017-10-31 23:45:43'),
(39, 1010, 14999, 14899, '2017-10-31 23:45:46'),
(40, 1019, 940, 939, '2017-10-31 23:45:51'),
(41, 1020, 1015, 1014, '2017-10-31 23:45:51'),
(42, 1021, 829, 829, '2017-10-31 23:45:51'),
(43, 1022, 1519, 1518, '2017-10-31 23:45:53'),
(44, 1023, 2159, 2149, '2017-10-31 23:45:56'),
(45, 1031, 622, 621, '2017-10-31 23:45:58'),
(46, 1033, 354, 353, '2017-10-31 23:46:03'),
(47, 1034, 356, 355, '2017-10-31 23:46:03'),
(48, 1035, 714, 713, '2017-10-31 23:46:03'),
(49, 1036, 1124, 1123, '2017-10-31 23:46:08'),
(50, 1037, 1915, 1915, '2017-10-31 23:46:08'),
(51, 1038, 479, 479, '2017-10-31 23:46:08'),
(52, 1039, 532, 532, '2017-10-31 23:46:09'),
(53, 1040, 464, 463, '2017-10-31 23:46:09'),
(54, 1043, 892, 892, '2017-10-31 23:46:11'),
(55, 1045, 447, 447, '2017-10-31 23:46:13'),
(56, 1050, 2204, 2203, '2017-10-31 23:46:14'),
(57, 1052, 4249, 4249, '2017-10-31 23:46:15'),
(58, 1055, 1093, 599, '2017-10-31 23:46:16'),
(59, 1126, 3499, 3375, '2017-10-31 23:46:24'),
(60, 1129, 5531, 5531, '2017-10-31 23:46:27'),
(61, 1152, 166, 166, '2017-10-31 23:46:38'),
(62, 1165, 12353, 12352, '2017-10-31 23:46:47'),
(63, 1166, 17556, 17556, '2017-10-31 23:46:47'),
(64, 1171, 10193, 7520, '2017-11-01 04:34:21'),
(65, 1172, 48333, 48333, '2017-11-01 04:34:21'),
(66, 1173, 96667, 96666, '2017-11-01 04:34:21'),
(67, 1182, 12497, 12497, '2017-11-01 04:34:28'),
(68, 1255, 63026, 63026, '2017-11-01 04:34:29'),
(69, 1253, 165715, 165714, '2017-11-01 04:34:31'),
(70, 1267, 25917, 25916, '2017-11-01 04:34:34'),
(71, 1271, 10285, 9777, '2017-11-01 04:34:54'),
(72, 1281, 10500, 10499, '2017-11-01 04:35:12'),
(73, 1292, 2149, 2148, '2017-11-01 04:35:20'),
(74, 1293, 1412, 1412, '2017-11-01 04:35:20'),
(75, 1410, 3188, 3187, '2017-11-01 04:35:21'),
(76, 1412, 1203, 1203, '2017-11-01 04:35:21'),
(77, 1413, 1368, 1367, '2017-11-01 04:35:21'),
(78, 1417, 3188, 3187, '2017-11-01 04:35:21'),
(79, 1455, 5470, 5470, '2017-11-01 04:35:23'),
(80, 1483, 6414, 6413, '2017-11-01 04:35:33'),
(81, 1491, 5999, 6, '2017-11-01 04:37:21'),
(82, 1501, 5516, 5515, '2017-11-01 04:37:23'),
(83, 1503, 1167, 1167, '2017-11-01 04:37:23'),
(84, 1509, 7726, 7725, '2017-11-01 04:37:24'),
(85, 1511, 8508, 8507, '2017-11-01 04:37:26'),
(86, 1513, 17839, 17839, '2017-11-01 04:37:27'),
(87, 1515, 8227, 8227, '2017-11-01 04:37:39'),
(88, 1521, 2757, 2756, '2017-11-01 04:37:41'),
(89, 1525, 4979, 4978, '2017-11-01 04:37:41'),
(90, 1527, 4530, 4529, '2017-11-01 04:37:41'),
(91, 1539, 5699, 4990, '2017-11-01 04:37:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dw_paginas`
--

CREATE TABLE `dw_paginas` (
  `id` int(11) NOT NULL,
  `tienda_id` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL DEFAULT '1',
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dw_proxy`
--

CREATE TABLE `dw_proxy` (
  `id` int(11) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `puerto` varchar(10) NOT NULL,
  `protocolo` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `dw_proxy`
--

INSERT INTO `dw_proxy` (`id`, `ip`, `puerto`, `protocolo`, `created_at`) VALUES
(1, '127.0.0.1', '9050', 'socks5', '2017-10-19 00:44:19'),
(2, '127.0.0.1', '8080', 'socks5', '2017-10-19 00:44:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dw_secciones_nodos`
--

CREATE TABLE `dw_secciones_nodos` (
  `id_seccion` int(11) NOT NULL,
  `seccion` varchar(45) DEFAULT NULL,
  `nodos` varchar(500) DEFAULT NULL,
  `conjunto_paginas` tinyint(2) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `dw_secciones_nodos`
--

INSERT INTO `dw_secciones_nodos` (`id_seccion`, `seccion`, `nodos`, `conjunto_paginas`) VALUES
(1, 'seccion_1', ',9482691011,10097939011,10097941011,10098093011,10098028011,10098043011,10097930011,10097931011,10098153011,10097990011,10097997011,10098053011,10098009011,10098010011', 2),
(2, 'seccion_2', '10098109011,10098111011,10098023011,10098027011,10098025011,10098052011,10098020011,10098022011,10098058011,10097946011,10097973011,10097976011,10097968011', 1),
(3, 'seccion_3', '10098113011,10098091011,10098095011,10098092011,10098103011,10098107011,10098108011,10097978011,10097981011,10098135011,9482559011,9687280011,9687472011,9687281011,9687285011', 0),
(4, 'seccion_4', '9687423011,9687893011,9687393011,9687912011,9687606011,9687371011,9687374011,9687382011,9687392011,9687416011,9687417011,9687588011,9687415011,9687414011,9687418011', 0),
(5, 'seccion_5', '9687420011,9687605011,9687835011,9687798011,9687813011,9786531011,9687808011,12005826011,9687820011,9687824011,9687832011,9687831011,9687850011,9687934011,9687851011', 0),
(6, 'seccion_6', '9687857011,9687422011,9687460011,9687458011,9687469011,9687880011,10189658011,10189663011,10189660011,10189677011,10189667011,10189659011,10189666011,10189662011', 0),
(7, 'seccion_7', '10189661011,10189672011,10189678011,10189674011,10189673011,10189669011,10189664011,10189670011,10189671011,10189665011,10189675011,10189676011,9687471011', 0),
(8, 'seccion_8', '9687519011,9687526011,9687561011,9687564011,9705965011,9687565011,9687566011,9687568011,9687578011,9687582011,9687583011,9687589011,9687881011,9687883011,9687882011', 0),
(9, 'seccion_9', '9687884011,9687886011,9687888011,9687889011,9687891011,9687860011,9687877011,9687873011,9786540011,9786541011,9687878011,9687874011,9687875011,9687892011', 0),
(10, 'seccion_10', '9687906011,9687907011,9687908011,15144312011,15144313011,12005606011,15144317011,15144311011,9786544011,9687925011,9687926011,9687928011,9687936011', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dw_tiendas`
--

CREATE TABLE `dw_tiendas` (
  `id` int(11) NOT NULL,
  `tienda` varchar(90) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `clase` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dw_usuarios`
--

CREATE TABLE `dw_usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(124) DEFAULT '',
  `apellido` varchar(124) DEFAULT '',
  `correo` varchar(255) DEFAULT '',
  `contrasena` varchar(300) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `dw_usuarios`
--

INSERT INTO `dw_usuarios` (`id`, `usuario`, `apellido`, `correo`, `contrasena`) VALUES
(1, 'Andrew', 'Gonzalez', 'dev_andrew@devworms.com', 'df733656293a19c54f69093ba916f0a1a2a3c151fc95c13f3a794c2631eeb3a6'),
(2, 'Ricardo', 'Osorio', 'dev_ricardo@devworms.com', 'df733656293a19c54f69093ba916f0a1a2a3c151fc95c13f3a794c2631eeb3a6');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `dw_historial`
--
ALTER TABLE `dw_historial`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dw_paginas`
--
ALTER TABLE `dw_paginas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dw_proxy`
--
ALTER TABLE `dw_proxy`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dw_secciones_nodos`
--
ALTER TABLE `dw_secciones_nodos`
  ADD PRIMARY KEY (`id_seccion`),
  ADD UNIQUE KEY `seccion_UNIQUE` (`seccion`);

--
-- Indices de la tabla `dw_tiendas`
--
ALTER TABLE `dw_tiendas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `dw_usuarios`
--
ALTER TABLE `dw_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `dw_historial`
--
ALTER TABLE `dw_historial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
--
-- AUTO_INCREMENT de la tabla `dw_paginas`
--
ALTER TABLE `dw_paginas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `dw_proxy`
--
ALTER TABLE `dw_proxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `dw_secciones_nodos`
--
ALTER TABLE `dw_secciones_nodos`
  MODIFY `id_seccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT de la tabla `dw_tiendas`
--
ALTER TABLE `dw_tiendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT de la tabla `dw_usuarios`
--
ALTER TABLE `dw_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
