-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 17-05-2026 a las 19:02:51
-- Versión del servidor: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agendify_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('pendiente','proxima','realizada') DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta') DEFAULT 'media',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `realizada` tinyint(1) NOT NULL DEFAULT 0,
  `recordatorio_enviado` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `usuario_id`, `titulo`, `descripcion`, `fecha`, `hora`, `fecha_limite`, `estado`, `prioridad`, `created_at`, `realizada`, `recordatorio_enviado`) VALUES
(2, 1, 'dsasdfds', 'afdfa', '2026-05-06', '13:37:00', NULL, 'realizada', 'media', '2026-05-14 07:33:26', 1, 0),
(3, 1, 'google', 'dada', '2026-05-13', '10:39:00', NULL, 'realizada', 'baja', '2026-05-14 07:38:31', 0, 0),
(4, 1, 'sdadsda', 'dsadasds', '2026-05-01', '11:43:00', NULL, 'realizada', 'baja', '2026-05-14 07:41:48', 0, 0),
(5, 1, 'Examen', '', '2026-05-27', '17:21:00', NULL, 'proxima', 'alta', '2026-05-15 12:18:10', 0, 0),
(6, 1, 'dfsafdafd', '', '2026-05-23', '17:21:00', NULL, 'proxima', 'baja', '2026-05-15 12:18:27', 0, 0),
(8, 1, 'sadasdad', '', '2026-05-06', '15:20:00', NULL, 'realizada', 'media', '2026-05-15 12:19:10', 1, 0),
(9, 1, 'sdadds', 'asdsad', '2026-05-06', '16:21:00', NULL, 'realizada', 'alta', '2026-05-15 12:19:21', 0, 0),
(10, 1, 'sadads', 'adasd', '2026-05-06', '16:21:00', NULL, 'realizada', 'media', '2026-05-15 12:19:31', 0, 0),
(11, 1, 'dfsafdsaf', 'dsfsf', '2026-05-06', '16:21:00', NULL, 'realizada', 'media', '2026-05-15 12:19:53', 0, 0),
(12, 1, 'Batman', 'sadfsfsdfsafds', '2026-05-16', '19:33:00', NULL, 'pendiente', 'media', '2026-05-16 15:31:56', 0, 0),
(13, 1, 'hola', 'hola', '2026-05-16', '18:27:00', NULL, 'pendiente', 'media', '2026-05-16 15:33:26', 0, 1),
(14, 1, 'dasfsf', 'fsfadsfdsafdsa', '2026-03-02', '20:59:00', NULL, 'realizada', 'media', '2026-05-16 15:56:26', 0, 0),
(15, 1, 'asdasda', 'fdasfsad', '2026-05-17', '16:36:00', NULL, 'pendiente', 'alta', '2026-05-17 12:34:16', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  `aviso_minutos` int(11) NOT NULL DEFAULT 60 COMMENT 'Minutos antes del evento para enviar el recordatorio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `fecha_registro`, `aviso_minutos`) VALUES
(1, 'Iaroslav', 'iaroslav.nevalenov-udilov@iesruizgijon.com', '$2y$10$TZt3fF/AwP9xhFHKwzkpGO7bUOKY54R6w5y1d/olsjHFWzfY2uAia', '2026-05-02 12:00:43', 2880);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario` (`usuario_id`),
  ADD KEY `idx_recordatorio` (`recordatorio_enviado`,`fecha`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
