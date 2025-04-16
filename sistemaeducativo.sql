-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-04-2025 a las 02:25:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistemaeducativo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grados`
--

CREATE DATABASE IF NOT EXISTS `sistemaeducativo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistemaeducativo`;

CREATE TABLE `grados` (
  `id_grado` int(11) NOT NULL,
  `nombre_grado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grados`
--

INSERT INTO `grados` (`id_grado`, `nombre_grado`) VALUES
(1, '1ER AÑO'),
(2, '2DO AÑO'),
(3, '3ER AÑO'),
(4, '1ER GRADO'),
(5, '2DO GRADO'),
(6, '3ER GRADO'),
(7, '4TO GRADO'),
(8, '5TO GRADO'),
(9, '6TO GRADO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id_horario` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre_materia` varchar(100) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `id_seccion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre_materia`, `id_grado`, `id_seccion`) VALUES
(2, 'MATEMATICA', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notas`
--

CREATE TABLE `notas` (
  `id_nota` int(11) NOT NULL,
  `id_prueba` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `calificacion` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notas`
--

INSERT INTO `notas` (`id_nota`, `id_prueba`, `id_usuario`, `calificacion`) VALUES
(1, 1, 3, 20.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opciones`
--

CREATE TABLE `opciones` (
  `id_opcion` int(11) NOT NULL,
  `id_pregunta` int(11) NOT NULL,
  `texto_opcion` text NOT NULL,
  `es_correcta` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id_pregunta` int(11) NOT NULL,
  `id_prueba` int(11) NOT NULL,
  `enunciado` text NOT NULL,
  `tipo_pregunta` enum('simple','verdadero_falso','seleccion_simple','seleccion_multiple') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id_pregunta`, `id_prueba`, `enunciado`, `tipo_pregunta`) VALUES
(1, 1, '2+2=4', 'verdadero_falso'),
(2, 1, '5-3=3', 'verdadero_falso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor_materias`
--

CREATE TABLE `profesor_materias` (
  `id_profesor_materia` int(11) NOT NULL,
  `id_profesor` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor_materias`
--

INSERT INTO `profesor_materias` (`id_profesor_materia`, `id_profesor`, `id_materia`) VALUES
(1, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pruebas`
--

CREATE TABLE `pruebas` (
  `id_prueba` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pruebas`
--

INSERT INTO `pruebas` (`id_prueba`, `titulo`, `id_materia`, `fecha`) VALUES
(1, 'PRUEBA 1', 2, '2025-04-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id_respuesta` int(11) NOT NULL,
  `id_pregunta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `respuesta_dada` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `id_pregunta`, `id_usuario`, `respuesta_dada`) VALUES
(1, 1, 3, 'verdadero'),
(2, 2, 3, 'falso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'administrador', 'Administrador del sistema'),
(2, 'profesor', 'Profesor o Docente'),
(3, 'estudiante', 'Estudiante o Alumno');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `id_seccion` int(11) NOT NULL,
  `nombre_seccion` varchar(50) NOT NULL,
  `id_grado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id_seccion`, `nombre_seccion`, `id_grado`) VALUES
(1, 'A', 1),
(2, 'B', 1),
(3, 'A', 2),
(4, 'B', 2),
(5, 'A', 3),
(6, 'B', 3),
(7, 'A', 4),
(9, 'A', 5),
(10, 'A', 6),
(11, 'A', 7),
(12, 'A', 8),
(13, 'A', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `contrasena_hash`, `id_rol`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nacimiento`, `correo`, `telefono`, `activo`) VALUES
(1, '12345678', '$2y$10$v7rZOmsdNhwKBv4V.caHquRTzWqZ7Rw.f4zp2NvbABVB4ZnymWzUC', 1, 'ADMIN', 'A', 'DIAZ', 'G', '1960-01-01', '12345678@gmail.com', '04121234567', 1),
(2, '123456789', '$2y$10$40ZKcKqUrpfqrw9oUnG5yO9KQeCDrDI8GO08eG8v2SUA1sRcs3cYm', 2, 'PROF', 'A', 'DIAZ', 'G', '1950-06-01', '123456789@gmail.com', '04121234567', 1),
(3, '1234567890', '$2y$10$XdmSYfAiVVlkUxftVYkZqeLjw0uLnPU6cXDKD1a1hWd7PwAPEP4jm', 3, 'ESTU', 'A', 'DIAZ', 'G', '1950-01-01', '1234567890@gmail.com', '04121234567', 1),
(4, '17952404', '$2y$10$SQDF0IGjW2hgJyz8YgXw8.0a6wtquu/o4rP3hVZaHDzKDyhVvGrjC', 1, 'JOSE', 'A', 'SOTO', 'G', '1985-07-22', 'soviwebs@gmail.com', '04126487258', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_grado_seccion`
--

CREATE TABLE `usuario_grado_seccion` (
  `id_relacion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_grado` int(11) NOT NULL,
  `id_seccion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_grado_seccion`
--

INSERT INTO `usuario_grado_seccion` (`id_relacion`, `id_usuario`, `id_grado`, `id_seccion`) VALUES
(1, 3, 1, 1),
(4, 3, 2, 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `grados`
--
ALTER TABLE `grados`
  ADD PRIMARY KEY (`id_grado`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`),
  ADD KEY `id_grado` (`id_grado`),
  ADD KEY `id_seccion` (`id_seccion`);

--
-- Indices de la tabla `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id_nota`),
  ADD KEY `id_prueba` (`id_prueba`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `opciones`
--
ALTER TABLE `opciones`
  ADD PRIMARY KEY (`id_opcion`),
  ADD KEY `id_pregunta` (`id_pregunta`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_pregunta`),
  ADD KEY `id_prueba` (`id_prueba`);

--
-- Indices de la tabla `profesor_materias`
--
ALTER TABLE `profesor_materias`
  ADD PRIMARY KEY (`id_profesor_materia`),
  ADD KEY `id_profesor` (`id_profesor`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `pruebas`
--
ALTER TABLE `pruebas`
  ADD PRIMARY KEY (`id_prueba`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_pregunta` (`id_pregunta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`id_seccion`),
  ADD KEY `id_grado` (`id_grado`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `usuario_grado_seccion`
--
ALTER TABLE `usuario_grado_seccion`
  ADD PRIMARY KEY (`id_relacion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_grado` (`id_grado`),
  ADD KEY `id_seccion` (`id_seccion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `grados`
--
ALTER TABLE `grados`
  MODIFY `id_grado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `notas`
--
ALTER TABLE `notas`
  MODIFY `id_nota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `opciones`
--
ALTER TABLE `opciones`
  MODIFY `id_opcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `profesor_materias`
--
ALTER TABLE `profesor_materias`
  MODIFY `id_profesor_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pruebas`
--
ALTER TABLE `pruebas`
  MODIFY `id_prueba` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `id_seccion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `usuario_grado_seccion`
--
ALTER TABLE `usuario_grado_seccion`
  MODIFY `id_relacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `materias`
--
ALTER TABLE `materias`
  ADD CONSTRAINT `materias_ibfk_1` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`),
  ADD CONSTRAINT `materias_ibfk_2` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id_seccion`);

--
-- Filtros para la tabla `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`id_prueba`) REFERENCES `pruebas` (`id_prueba`),
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `opciones`
--
ALTER TABLE `opciones`
  ADD CONSTRAINT `opciones_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`);

--
-- Filtros para la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD CONSTRAINT `preguntas_ibfk_1` FOREIGN KEY (`id_prueba`) REFERENCES `pruebas` (`id_prueba`);

--
-- Filtros para la tabla `profesor_materias`
--
ALTER TABLE `profesor_materias`
  ADD CONSTRAINT `profesor_materias_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `profesor_materias_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pruebas`
--
ALTER TABLE `pruebas`
  ADD CONSTRAINT `pruebas_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`);

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_pregunta`) REFERENCES `preguntas` (`id_pregunta`),
  ADD CONSTRAINT `respuestas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD CONSTRAINT `secciones_ibfk_1` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);

--
-- Filtros para la tabla `usuario_grado_seccion`
--
ALTER TABLE `usuario_grado_seccion`
  ADD CONSTRAINT `usuario_grado_seccion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_grado_seccion_ibfk_2` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_grado_seccion_ibfk_3` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id_seccion`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
