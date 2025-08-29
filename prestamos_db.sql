-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 29-08-2025 a las 17:38:26
-- Versión del servidor: 8.0.43
-- Versión de PHP: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prestamos_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amortizaciones`
--

CREATE TABLE `amortizaciones` (
  `id` int NOT NULL,
  `id_prestamo` int NOT NULL,
  `numero_cuota` int NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `capital` decimal(10,2) NOT NULL,
  `interes` decimal(10,2) NOT NULL,
  `saldo_pendiente` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Pagada','Mora') NOT NULL DEFAULT 'Pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `amortizaciones`
--

INSERT INTO `amortizaciones` (`id`, `id_prestamo`, `numero_cuota`, `fecha_pago`, `monto_cuota`, `capital`, `interes`, `saldo_pendiente`, `estado`) VALUES
(49, 9, 1, '2025-09-28', 5307.83, 4024.50, 1283.33, 50975.50, 'Pagada'),
(50, 9, 2, '2025-10-28', 5307.83, 4118.40, 1189.43, 46857.10, 'Pendiente'),
(51, 9, 3, '2025-11-28', 5307.83, 4214.50, 1093.33, 42642.61, 'Pendiente'),
(52, 9, 4, '2025-12-28', 5307.83, 4312.84, 994.99, 38329.77, 'Pendiente'),
(53, 9, 5, '2026-01-28', 5307.83, 4413.47, 894.36, 33916.30, 'Pendiente'),
(54, 9, 6, '2026-02-28', 5307.83, 4516.45, 791.38, 29399.85, 'Pendiente'),
(55, 9, 7, '2026-03-28', 5307.83, 4621.83, 686.00, 24778.02, 'Pendiente'),
(56, 9, 8, '2026-04-28', 5307.83, 4729.68, 578.15, 20048.35, 'Pendiente'),
(57, 9, 9, '2026-05-28', 5307.83, 4840.03, 467.79, 15208.31, 'Pendiente'),
(58, 9, 10, '2026-06-28', 5307.83, 4952.97, 354.86, 10255.34, 'Pendiente'),
(59, 9, 11, '2026-07-28', 5307.83, 5068.54, 239.29, 5186.80, 'Pendiente'),
(60, 9, 12, '2026-08-28', 5307.83, 5186.80, 121.03, 0.00, 'Pendiente'),
(61, 10, 1, '2025-09-28', 5080.12, 4045.12, 1035.00, 49954.88, 'Pendiente'),
(62, 10, 2, '2025-10-28', 5080.12, 4122.65, 957.47, 45832.23, 'Pendiente'),
(63, 10, 3, '2025-11-28', 5080.12, 4201.67, 878.45, 41630.56, 'Pendiente'),
(64, 10, 4, '2025-12-28', 5080.12, 4282.20, 797.92, 37348.35, 'Pendiente'),
(65, 10, 5, '2026-01-28', 5080.12, 4364.28, 715.84, 32984.08, 'Pendiente'),
(66, 10, 6, '2026-02-28', 5080.12, 4447.93, 632.19, 28536.15, 'Pendiente'),
(67, 10, 7, '2026-03-28', 5080.12, 4533.18, 546.94, 24002.97, 'Pendiente'),
(68, 10, 8, '2026-04-28', 5080.12, 4620.06, 460.06, 19382.91, 'Pendiente'),
(69, 10, 9, '2026-05-28', 5080.12, 4708.62, 371.51, 14674.29, 'Pendiente'),
(70, 10, 10, '2026-06-28', 5080.12, 4798.86, 281.26, 9875.43, 'Pendiente'),
(71, 10, 11, '2026-07-28', 5080.12, 4890.84, 189.28, 4984.58, 'Pendiente'),
(72, 10, 12, '2026-08-28', 5080.12, 4984.58, 95.54, 0.00, 'Pendiente'),
(73, 11, 1, '2025-09-28', 1010.87, 990.87, 20.00, 11009.13, 'Pendiente'),
(74, 11, 2, '2025-10-28', 1010.87, 992.52, 18.35, 10016.62, 'Pendiente'),
(75, 11, 3, '2025-11-28', 1010.87, 994.17, 16.69, 9022.44, 'Pendiente'),
(76, 11, 4, '2025-12-28', 1010.87, 995.83, 15.04, 8026.61, 'Pendiente'),
(77, 11, 5, '2026-01-28', 1010.87, 997.49, 13.38, 7029.13, 'Pendiente'),
(78, 11, 6, '2026-02-28', 1010.87, 999.15, 11.72, 6029.97, 'Pendiente'),
(79, 11, 7, '2026-03-28', 1010.87, 1000.82, 10.05, 5029.16, 'Pendiente'),
(80, 11, 8, '2026-04-28', 1010.87, 1002.48, 8.38, 4026.67, 'Pendiente'),
(81, 11, 9, '2026-05-28', 1010.87, 1004.16, 6.71, 3022.52, 'Pendiente'),
(82, 11, 10, '2026-06-28', 1010.87, 1005.83, 5.04, 2016.69, 'Pendiente'),
(83, 11, 11, '2026-07-28', 1010.87, 1007.51, 3.36, 1009.18, 'Pendiente'),
(84, 11, 12, '2026-08-28', 1010.87, 1009.18, 1.68, 0.00, 'Pendiente'),
(85, 31, 1, '2025-09-28', 4679.72, 3763.05, 916.67, 46236.95, 'Pendiente'),
(86, 31, 2, '2025-10-28', 4679.72, 3832.04, 847.68, 42404.91, 'Pendiente'),
(87, 31, 3, '2025-11-28', 4679.72, 3902.30, 777.42, 38502.61, 'Pendiente'),
(88, 31, 4, '2025-12-28', 4679.72, 3973.84, 705.88, 34528.77, 'Pendiente'),
(89, 31, 5, '2026-01-28', 4679.72, 4046.69, 633.03, 30482.08, 'Pendiente'),
(90, 31, 6, '2026-02-28', 4679.72, 4120.88, 558.84, 26361.20, 'Pendiente'),
(91, 31, 7, '2026-03-28', 4679.72, 4196.43, 483.29, 22164.77, 'Pendiente'),
(92, 31, 8, '2026-04-28', 4679.72, 4273.36, 406.35, 17891.41, 'Pendiente'),
(93, 31, 9, '2026-05-28', 4679.72, 4351.71, 328.01, 13539.70, 'Pendiente'),
(94, 31, 10, '2026-06-28', 4679.72, 4431.49, 248.23, 9108.20, 'Pendiente'),
(95, 31, 11, '2026-07-28', 4679.72, 4512.74, 166.98, 4595.47, 'Pendiente'),
(96, 31, 12, '2026-08-28', 4679.72, 4595.47, 84.25, 0.00, 'Pendiente'),
(97, 32, 1, '2025-09-28', 3182.21, 2558.88, 623.33, 31441.12, 'Pagada'),
(98, 32, 2, '2025-10-28', 3182.21, 2605.79, 576.42, 28835.34, 'Pendiente'),
(99, 32, 3, '2025-11-28', 3182.21, 2653.56, 528.65, 26181.78, 'Pendiente'),
(100, 32, 4, '2025-12-28', 3182.21, 2702.21, 480.00, 23479.57, 'Pendiente'),
(101, 32, 5, '2026-01-28', 3182.21, 2751.75, 430.46, 20727.82, 'Pendiente'),
(102, 32, 6, '2026-02-28', 3182.21, 2802.20, 380.01, 17925.62, 'Pendiente'),
(103, 32, 7, '2026-03-28', 3182.21, 2853.57, 328.64, 15072.04, 'Pendiente'),
(104, 32, 8, '2026-04-28', 3182.21, 2905.89, 276.32, 12166.16, 'Pendiente'),
(105, 32, 9, '2026-05-28', 3182.21, 2959.16, 223.05, 9206.99, 'Pendiente'),
(106, 32, 10, '2026-06-28', 3182.21, 3013.41, 168.79, 6193.58, 'Pendiente'),
(107, 32, 11, '2026-07-28', 3182.21, 3068.66, 113.55, 3124.92, 'Pendiente'),
(108, 32, 12, '2026-08-28', 3182.21, 3124.92, 57.29, 0.00, 'Pendiente'),
(109, 33, 1, '2025-09-29', 9650.60, 7317.27, 2333.33, 92682.73, 'Pagada'),
(110, 33, 2, '2025-10-29', 9650.60, 7488.00, 2162.60, 85194.73, 'Pendiente'),
(111, 33, 3, '2025-11-29', 9650.60, 7662.72, 1987.88, 77532.01, 'Pendiente'),
(112, 33, 4, '2025-12-29', 9650.60, 7841.52, 1809.08, 69690.49, 'Pendiente'),
(113, 33, 5, '2026-01-29', 9650.60, 8024.49, 1626.11, 61666.01, 'Pendiente'),
(114, 33, 6, '2026-03-01', 9650.60, 8211.73, 1438.87, 53454.28, 'Pendiente'),
(115, 33, 7, '2026-03-29', 9650.60, 8403.33, 1247.27, 45050.95, 'Pendiente'),
(116, 33, 8, '2026-04-29', 9650.60, 8599.41, 1051.19, 36451.54, 'Pendiente'),
(117, 33, 9, '2026-05-29', 9650.60, 8800.06, 850.54, 27651.47, 'Pendiente'),
(118, 33, 10, '2026-06-29', 9650.60, 9005.40, 645.20, 18646.08, 'Pendiente'),
(119, 33, 11, '2026-07-29', 9650.60, 9215.52, 435.08, 9430.55, 'Pendiente'),
(120, 33, 12, '2026-08-29', 9650.60, 9430.55, 220.05, 0.00, 'Pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int NOT NULL,
  `nombre_completo` varchar(150) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creado_por_empleado_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_completo`, `cedula`, `telefono`, `direccion`, `fecha_registro`, `creado_por_empleado_id`) VALUES
(1, 'Gabriel Francisco', '402-1935657-9', '809-317-7671', 'Calle 5', '2025-08-28 20:45:21', 1),
(4, 'Angel Sosa', '000-0000000-0', '809-121-1221', '', '2025-08-28 20:56:24', 1),
(5, 'Jose Fermin', '000-0000000-1', '849-090-9080', '', '2025-08-28 18:59:50', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `usuario`, `password`, `id_rol`, `estado`, `fecha_creacion`) VALUES
(1, 'Gabriel Francisco Herrera', 'admin', '$2y$10$Dw4g7T4FM6tu8Hhh9TawD.SRxJUgNWNeGesAwZ7fFxnjbcIPthgsq', 1, 'Activo', '2025-08-28 17:56:10'),
(2, 'Gerente', 'gerente', '$2y$10$Dw4g7T4FM6tu8Hhh9TawD.SRxJUgNWNeGesAwZ7fFxnjbcIPthgsq', 2, 'Activo', '2025-08-28 17:56:10'),
(3, 'Cajero', 'cajero', '$2y$10$Dw4g7T4FM6tu8Hhh9TawD.SRxJUgNWNeGesAwZ7fFxnjbcIPthgsq', 4, 'Activo', '2025-08-28 17:56:10'),
(4, 'Servicio al Cliente', 'servicio', '$2y$10$Dw4g7T4FM6tu8Hhh9TawD.SRxJUgNWNeGesAwZ7fFxnjbcIPthgsq', 3, 'Activo', '2025-08-28 17:56:10'),
(5, 'Angel Sosa', 'angel', '$2y$10$5kA7weZnlflRrwgiG6nlZuzbYVIdRDqVsRdXemCB9GjHEna8ERe1W', 1, 'Activo', '2025-08-28 20:51:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int NOT NULL,
  `id_amortizacion` int NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_cajero` int NOT NULL,
  `metodo_pago` enum('Efectivo','Transferencia') NOT NULL DEFAULT 'Efectivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `id_amortizacion`, `monto_pagado`, `fecha_pago`, `id_cajero`, `metodo_pago`) VALUES
(2, 49, 5307.83, '2025-08-28 23:01:02', 1, 'Efectivo'),
(3, 97, 3182.21, '2025-08-28 23:38:59', 1, 'Efectivo'),
(4, 109, 9650.60, '2025-08-29 00:08:40', 1, 'Efectivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int NOT NULL,
  `id_cliente` int NOT NULL,
  `monto_aprobado` decimal(10,2) NOT NULL,
  `tasa_interes_anual` decimal(5,2) NOT NULL,
  `plazo` int NOT NULL,
  `frecuencia_pago` enum('diario','semanal','mensual') NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_desembolso` date DEFAULT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado','Desembolsado','Saldado') NOT NULL DEFAULT 'Pendiente',
  `id_empleado_registra` int DEFAULT NULL,
  `id_empleado_aprueba` int DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `id_cliente`, `monto_aprobado`, `tasa_interes_anual`, `plazo`, `frecuencia_pago`, `monto_cuota`, `fecha_solicitud`, `fecha_desembolso`, `estado`, `id_empleado_registra`, `id_empleado_aprueba`, `fecha_creacion`) VALUES
(9, 1, 55000.00, 28.00, 12, 'mensual', 5307.83, '2025-08-28', NULL, 'Desembolsado', 1, NULL, '2025-08-28 22:38:00'),
(10, 4, 54000.00, 23.00, 12, 'mensual', 5080.12, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 22:45:03'),
(11, 4, 12000.00, 2.00, 12, 'mensual', 1010.87, '2025-08-28', NULL, 'Aprobado', 1, NULL, '2025-08-28 22:51:05'),
(12, 4, 4000.00, 12.00, 12, 'mensual', 355.40, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:09:08'),
(13, 4, 4000.00, 12.00, 12, 'mensual', 355.40, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:09:11'),
(14, 4, 4000.00, 12.00, 12, 'mensual', 355.40, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:09:18'),
(15, 4, 23.00, 23.00, 23, 'mensual', 1.25, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:11:09'),
(16, 1, 34444.00, 23.00, 12, 'mensual', 3240.36, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:15:21'),
(17, 1, 50000.00, 28.00, 12, 'mensual', 4825.30, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:18:21'),
(18, 1, 50000.00, 28.00, 12, 'mensual', 4825.30, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:18:50'),
(19, 1, 50000.00, 28.00, 12, 'mensual', 4825.30, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:18:51'),
(20, 4, 34000.00, 23.00, 12, 'mensual', 3198.59, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:19:11'),
(21, 4, 23333.00, 23.00, 12, 'mensual', 2195.08, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:20:55'),
(22, 4, 23333.00, 23.00, 12, 'mensual', 2195.08, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:21:01'),
(23, 1, 232333.00, 12.00, 12, 'mensual', 20642.51, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:22:51'),
(24, 1, 122222.00, 12.00, 12, 'mensual', 10859.28, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:24:42'),
(25, 1, 122222.00, 12.00, 12, 'mensual', 10859.28, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:25:59'),
(26, 1, 50000.00, 22.00, 12, 'mensual', 4679.72, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:26:18'),
(27, 1, 60000.00, 22.00, 12, 'mensual', 5615.66, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:28:16'),
(28, 1, 5000.00, 23.00, 23, 'mensual', 270.86, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:28:45'),
(29, 1, 50000.00, 22.00, 12, 'mensual', 4679.72, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:32:08'),
(30, 1, 50000.00, 22.00, 12, 'mensual', 4679.72, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:33:51'),
(31, 1, 50000.00, 22.00, 12, 'mensual', 4679.72, '2025-08-28', NULL, 'Rechazado', 1, NULL, '2025-08-28 23:35:57'),
(32, 5, 34000.00, 22.00, 12, 'mensual', 3182.21, '2025-08-28', NULL, 'Desembolsado', 1, NULL, '2025-08-28 23:38:27'),
(33, 4, 100000.00, 28.00, 12, 'mensual', 9650.60, '2025-08-29', NULL, 'Desembolsado', 1, NULL, '2025-08-29 00:07:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `nombre_rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre_rol`) VALUES
(1, 'Admin'),
(4, 'Cajero'),
(2, 'Gerente'),
(3, 'Servicio al Cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `amortizaciones`
--
ALTER TABLE `amortizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_prestamo` (`id_prestamo`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `creado_por_empleado_id` (`creado_por_empleado_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_amortizacion` (`id_amortizacion`),
  ADD KEY `id_cajero` (`id_cajero`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_empleado_registra` (`id_empleado_registra`),
  ADD KEY `id_empleado_aprueba` (`id_empleado_aprueba`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_rol` (`nombre_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `amortizaciones`
--
ALTER TABLE `amortizaciones`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `amortizaciones`
--
ALTER TABLE `amortizaciones`
  ADD CONSTRAINT `amortizaciones_ibfk_1` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`creado_por_empleado_id`) REFERENCES `empleados` (`id`);

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_amortizacion`) REFERENCES `amortizaciones` (`id`),
  ADD CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_cajero`) REFERENCES `empleados` (`id`);

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`id_empleado_registra`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `prestamos_ibfk_3` FOREIGN KEY (`id_empleado_aprueba`) REFERENCES `empleados` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
