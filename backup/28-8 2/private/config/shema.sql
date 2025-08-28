-- Base de Datos: prestamos_db
-- Este script crea la estructura completa para el sistema de préstamos.

--
-- Estructura de la tabla `roles`
-- Almacena los diferentes tipos de usuarios del sistema.
--
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Datos de ejemplo para la tabla `roles`
--
INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'Admin'),
(2, 'Gerente'),
(3, 'Servicio al Cliente'),
(4, 'Cajero');

-- --------------------------------------------------------

--
-- Estructura de la tabla `empleados`
-- Guarda la información de los usuarios que pueden acceder al sistema.
--
CREATE TABLE `empleados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de la tabla `clientes`
-- Almacena la información de los clientes que solicitan préstamos.
--
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `creado_por_empleado_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`),
  KEY `creado_por_empleado_id` (`creado_por_empleado_id`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`creado_por_empleado_id`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de la tabla `prestamos`
-- Contiene la información general de cada préstamo solicitado.
--
CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `monto_aprobado` decimal(10,2) NOT NULL,
  `tasa_interes_anual` decimal(5,2) NOT NULL,
  `plazo` int(11) NOT NULL,
  `frecuencia_pago` enum('diario','semanal','mensual') NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `fecha_solicitud` date NOT NULL,
  `fecha_desembolso` date DEFAULT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado','Desembolsado','Saldado') NOT NULL DEFAULT 'Pendiente',
  `id_empleado_registra` int(11) DEFAULT NULL,
  `id_empleado_aprueba` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_empleado_registra` (`id_empleado_registra`),
  KEY `id_empleado_aprueba` (`id_empleado_aprueba`),
  CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`),
  CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`id_empleado_registra`) REFERENCES `empleados` (`id`),
  CONSTRAINT `prestamos_ibfk_3` FOREIGN KEY (`id_empleado_aprueba`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de la tabla `amortizaciones`
-- Guarda el plan de pagos detallado para cada préstamo.
--
CREATE TABLE `amortizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_prestamo` int(11) NOT NULL,
  `numero_cuota` int(11) NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `capital` decimal(10,2) NOT NULL,
  `interes` decimal(10,2) NOT NULL,
  `saldo_pendiente` decimal(10,2) NOT NULL,
  `estado` enum('Pendiente','Pagada','Mora') NOT NULL DEFAULT 'Pendiente',
  PRIMARY KEY (`id`),
  KEY `id_prestamo` (`id_prestamo`),
  CONSTRAINT `amortizaciones_ibfk_1` FOREIGN KEY (`id_prestamo`) REFERENCES `prestamos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de la tabla `pagos`
-- Registra cada pago individual que realiza un cliente.
--
CREATE TABLE `pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_amortizacion` int(11) NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL,
  `fecha_pago` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_cajero` int(11) NOT NULL,
  `metodo_pago` enum('Efectivo','Transferencia') NOT NULL DEFAULT 'Efectivo',
  PRIMARY KEY (`id`),
  KEY `id_amortizacion` (`id_amortizacion`),
  KEY `id_cajero` (`id_cajero`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`id_amortizacion`) REFERENCES `amortizaciones` (`id`),
  CONSTRAINT `pagos_ibfk_2` FOREIGN KEY (`id_cajero`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

