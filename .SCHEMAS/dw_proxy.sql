-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 19-10-2017 a las 21:37:15
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

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `dw_proxy`
--
ALTER TABLE `dw_proxy`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `dw_proxy`
--
ALTER TABLE `dw_proxy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
