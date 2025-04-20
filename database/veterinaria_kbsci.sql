-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2025 at 11:51 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `veterinaria_kbsci`
--

-- --------------------------------------------------------

--
-- Table structure for table `bitacora`
--

CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `modulo` varchar(30) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bitacora`
--

INSERT INTO `bitacora` (`id`, `usuario_id`, `accion`, `modulo`, `descripcion`, `ip`, `fecha`) VALUES
(90, 6, 'login', 'autenticacion', 'Inicio de sesión exitoso', '::1', '2025-04-15 21:48:46'),
(91, 6, 'Edición', 'Administración', 'Editó usuario ID: 7', '::1', '2025-04-15 21:49:34'),
(92, 6, 'Edición', 'Administración', 'Editó usuario ID: 6', '::1', '2025-04-15 21:49:41'),
(93, 6, 'Eliminación', 'Administracion', 'Eliminó usuario ID: 5', '::1', '2025-04-15 21:50:01'),
(94, 6, 'Creación', 'Administración', 'Agregó usuario: Kirian Luna Quirós (703170536)', '::1', '2025-04-15 21:50:16'),
(95, 6, 'Creación', 'Administración', 'Agregó usuario: Steven Gutierrez Alvarado (703170535)', '::1', '2025-04-15 21:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `motivo` text NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `estado` enum('pendiente','completada','cancelada') DEFAULT 'pendiente',
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `configuracion`
--

CREATE TABLE `configuracion` (
  `id` int(11) NOT NULL,
  `iva` decimal(5,2) NOT NULL DEFAULT 13.00,
  `tipo_cambio_dolar` decimal(8,2) NOT NULL DEFAULT 500.00,
  `dias_alerta_citas` int(11) DEFAULT 1,
  `alerta_stock_minimo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facturas`
--

CREATE TABLE `facturas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `cliente_identificacion` varchar(20) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','pagada','anulada') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `factura_detalles`
--

CREATE TABLE `factura_detalles` (
  `id` int(11) NOT NULL,
  `factura_id` int(11) NOT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `descripcion` varchar(100) NOT NULL,
  `tipo` enum('producto','servicio') NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `categoria` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_minimo` int(11) NOT NULL DEFAULT 5,
  `precio_compra` decimal(10,2) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `proveedor` varchar(100) DEFAULT NULL,
  `fecha_ultima_entrada` timestamp NULL DEFAULT NULL,
  `fecha_ultima_salida` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `inventario`
--

INSERT INTO `inventario` (`id`, `codigo`, `nombre`, `descripcion`, `categoria`, `stock`, `stock_minimo`, `precio_compra`, `precio_venta`, `proveedor`, `fecha_ultima_entrada`, `fecha_ultima_salida`) VALUES
(1, 'VET-003', 'desparasitante para perros', 'Ataca y elimina a los Parásitos que entran en contacto con el animal', 'medicamento', 95, 20, 1900.00, 2200.00, 'Purina', NULL, NULL),
(2, 'VET-001', 'Alimento purina', 'Alimento de marca Purina', 'alimento', 99, 5, 3500.00, 4500.00, 'Purina', NULL, NULL),
(3, 'VET-002', 'Alimento dog chow', 'Alimento de 1kg de dog chow', 'alimento', 100, 5, 3600.00, 4000.00, 'Dog Chow', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `movimientos_inventario`
--

CREATE TABLE `movimientos_inventario` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida') NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` varchar(100) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `notas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_animal` varchar(50) NOT NULL,
  `raza` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `duenio_nombre` varchar(100) NOT NULL,
  `duenio_telefono` varchar(20) NOT NULL,
  `duenio_email` varchar(100) DEFAULT NULL,
  `alergias` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pacientes`
--

INSERT INTO `pacientes` (`id`, `nombre`, `tipo_animal`, `raza`, `edad`, `peso`, `duenio_nombre`, `duenio_telefono`, `duenio_email`, `alergias`, `fecha_registro`) VALUES
(1, 'Erenzo', 'Perro', 'Labrador', 4, 25.50, 'Carlos Pérez', '8888-1234', 'carlos.perez@gmail.com', 'Ninguna', '2025-04-11 03:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `factura_id` int(11) NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `tipo_cambio` decimal(8,2) DEFAULT NULL,
  `vuelto` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','veterinario','cajero') NOT NULL,
  `bloqueado` tinyint(1) DEFAULT 0,
  `intentos_login` int(11) DEFAULT 0,
  `ultimo_intento` datetime DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `cedula`, `nombre`, `password`, `rol`, `bloqueado`, `intentos_login`, `ultimo_intento`, `fecha_creacion`, `foto`) VALUES
(6, '703190455', 'Cesar Salas Bermudez', '$2y$10$AEngnUPq38EQZCx2HcARsuxIlgAH/.KisGUWh/5d.S0tn7J372yaO', 'admin', 0, 0, NULL, '2025-04-15 21:47:39', 'foto_67fed47546c53.jpg'),
(7, '703170537', 'Ayleen Araya Sanchez', '$2y$10$ZlF.A2OD2pqJMwtOI/9ujuyFUzf8SpnHpVfZU1vMlNvXA1vntonNa', 'veterinario', 0, 0, NULL, '2025-04-15 21:48:21', 'foto_67fed46e3f746.jpg'),
(8, '703170536', 'Kirian Luna Quirós', '$2y$10$CeejOSseO0Wjz0JldaAnfuGQeuTAeLkLFmQd6RaXSIf/Wnir4NY/O', 'admin', 0, 0, NULL, '2025-04-15 21:50:16', 'foto_67fed49871c26.jpg'),
(9, '703170535', 'Steven Gutierrez Alvarado', '$2y$10$scG7kzexjeTLRmtAojRGoOJxp6T8MsLBNIrBKd44SjbZwzlsVSWS2', 'cajero', 0, 0, NULL, '2025-04-15 21:50:59', 'foto_67fed4c315cc4.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paciente_id` (`paciente_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `fecha` (`fecha`);

--
-- Indexes for table `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `factura_detalles`
--
ALTER TABLE `factura_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_id` (`factura_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indexes for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indexes for table `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `factura_id` (`factura_id`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facturas`
--
ALTER TABLE `facturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `factura_detalles`
--
ALTER TABLE `factura_detalles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `bitacora_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `citas`
--
ALTER TABLE `citas`
  ADD CONSTRAINT `citas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `citas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `factura_detalles`
--
ALTER TABLE `factura_detalles`
  ADD CONSTRAINT `factura_detalles_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `factura_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `inventario` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `movimientos_inventario`
--
ALTER TABLE `movimientos_inventario`
  ADD CONSTRAINT `movimientos_inventario_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `inventario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimientos_inventario_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
