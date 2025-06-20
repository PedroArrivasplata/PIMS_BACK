-- Configuracion inicial
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- Eliminar y crear la base de datos
DROP SCHEMA IF EXISTS `mydb`;
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8;
USE `mydb`;

-- Tabla estado_cita
DROP TABLE IF EXISTS `estado_cita`;
CREATE TABLE IF NOT EXISTS `estado_cita` (
  `id_estado_cita` INT NOT NULL,
  `estado_cita` VARCHAR(20) NOT NULL,
  `descripcion` TEXT NOT NULL,
  PRIMARY KEY (`id_estado_cita`)
) ENGINE = InnoDB;

-- Tabla metodo_pago
DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE IF NOT EXISTS `metodo_pago` (
  `id_metodo_pago` INT NOT NULL,
  `tipo_pago` VARCHAR(45) NULL,
  PRIMARY KEY (`id_metodo_pago`)
) ENGINE = InnoDB;

-- Tabla cartilla_vacunacion
DROP TABLE IF EXISTS `cartilla_vacunacion`;
CREATE TABLE IF NOT EXISTS `cartilla_vacunacion` (
  `id_cartilla_vacunacion` INT NOT NULL,
  `fecha_creacion` DATE NULL,
  PRIMARY KEY (`id_cartilla_vacunacion`)
) ENGINE = InnoDB;

-- Tabla cita
DROP TABLE IF EXISTS `cita`;
CREATE TABLE IF NOT EXISTS `cita` (
  `id_cita` INT NOT NULL AUTO_INCREMENT,
  `fecha_cita` DATE NOT NULL,
  `hora_cita` TIME NOT NULL,
  `motivo` TEXT NOT NULL,
  `mascota_id_mascota` INT NOT NULL,
  `usuario_dni` INT NOT NULL,
  `estado_cita_id_estado_cita` INT NOT NULL,
  `metodo_pago_id_metodo_pago` INT NULL,
  `pagado` ENUM('SI', 'NO') NOT NULL,
  PRIMARY KEY (`id_cita`, `mascota_id_mascota`, `usuario_dni`, `estado_cita_id_estado_cita`, `metodo_pago_id_metodo_pago`),
  INDEX `fk_cita_estado_cita_idx` (`estado_cita_id_estado_cita` ASC),
  INDEX `fk_cita_metodo_pago_idx` (`metodo_pago_id_metodo_pago` ASC),
  CONSTRAINT `fk_cita_estado_cita`
    FOREIGN KEY (`estado_cita_id_estado_cita`)
    REFERENCES `estado_cita` (`id_estado_cita`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cita_metodo_pago`
    FOREIGN KEY (`metodo_pago_id_metodo_pago`)
    REFERENCES `metodo_pago` (`id_metodo_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla consulta
DROP TABLE IF EXISTS `consulta`;
CREATE TABLE IF NOT EXISTS `consulta` (
  `id_consulta` INT NOT NULL,
  `diagnostico` VARCHAR(45) NULL,
  `sintomas` TEXT NULL,
  `observaciones` TEXT NULL,
  `tratamiento` TEXT NULL,
  `tipo_consulta_id_tipo_consulta` INT NOT NULL,
  `cita_id_cita` INT NOT NULL,
  PRIMARY KEY (`id_consulta`, `tipo_consulta_id_tipo_consulta`, `cita_id_cita`),
  INDEX `fk_consulta_tipo_consulta_idx` (`tipo_consulta_id_tipo_consulta` ASC),
  INDEX `fk_consulta_cita_idx` (`cita_id_cita` ASC),
  CONSTRAINT `fk_consulta_tipo_consulta`
    FOREIGN KEY (`tipo_consulta_id_tipo_consulta`)
    REFERENCES `tipo_consulta` (`id_tipo_consulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_consulta_cita`
    FOREIGN KEY (`cita_id_cita`)
    REFERENCES `cita` (`id_cita`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla detalle_examen_consulta
DROP TABLE IF EXISTS `detalle_examen_consulta`;
CREATE TABLE IF NOT EXISTS `detalle_examen_consulta` (
  `id_detalle_examen_consulta` INT NOT NULL,
  `examen_generado` TEXT NULL,
  `formato` VARCHAR(20) NULL,
  `fecha` DATE NULL,
  `tipo_examen_medico_id_tipo_examen_medico` INT NOT NULL,
  `consulta_id_consulta` INT NOT NULL,
  PRIMARY KEY (`id_detalle_examen_consulta`, `tipo_examen_medico_id_tipo_examen_medico`, `consulta_id_consulta`),
  INDEX `fk_detalle_examen_consulta_tipo_examen_medico_idx` (`tipo_examen_medico_id_tipo_examen_medico` ASC),
  INDEX `fk_detalle_examen_consulta_consulta_idx` (`consulta_id_consulta` ASC),
  CONSTRAINT `fk_detalle_examen_consulta_tipo_examen_medico`
    FOREIGN KEY (`tipo_examen_medico_id_tipo_examen_medico`)
    REFERENCES `tipo_examen_medico` (`id_tipo_examen_medico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_detalle_examen_consulta_consulta`
    FOREIGN KEY (`consulta_id_consulta`)
    REFERENCES `consulta` (`id_consulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla estado_logico
DROP TABLE IF EXISTS `estado_logico`;
CREATE TABLE IF NOT EXISTS `estado_logico` (
  `id_estado_logico` INT NOT NULL,
  `descripcion_estado` VARCHAR(45) NULL,
  PRIMARY KEY (`id_estado_logico`)
) ENGINE = InnoDB;

-- Tabla especie
DROP TABLE IF EXISTS `especie`;
CREATE TABLE IF NOT EXISTS `especie` (
  `id_especie` INT NOT NULL AUTO_INCREMENT,
  `nombre_especie` VARCHAR(45) NULL,
  `estado_logico_id_estado_logico` INT NOT NULL,
  PRIMARY KEY (`id_especie`, `estado_logico_id_estado_logico`),
  INDEX `fk_especie_estado_logico_idx` (`estado_logico_id_estado_logico` ASC),
  CONSTRAINT `fk_especie_estado_logico`
    FOREIGN KEY (`estado_logico_id_estado_logico`)
    REFERENCES `estado_logico` (`id_estado_logico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla raza
DROP TABLE IF EXISTS `raza`;
CREATE TABLE IF NOT EXISTS `raza` (
  `id_raza` INT NOT NULL,
  `nombre_raza` VARCHAR(45) NULL,
  `especie_id_especie` INT NOT NULL,
  `especie_estado_logico_id_estado_logico` INT NOT NULL,
  PRIMARY KEY (`id_raza`, `especie_id_especie`, `especie_estado_logico_id_estado_logico`),
  INDEX `fk_raza_especie_idx` (`especie_id_especie` ASC, `especie_estado_logico_id_estado_logico` ASC),
  CONSTRAINT `fk_raza_especie`
    FOREIGN KEY (`especie_id_especie` , `especie_estado_logico_id_estado_logico`)
    REFERENCES `especie` (`id_especie` , `estado_logico_id_estado_logico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla propietario_mascota
DROP TABLE IF EXISTS `propietario_mascota`;
CREATE TABLE IF NOT EXISTS `propietario_mascota` (
  `id_propietario` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `apellido` VARCHAR(45) NOT NULL,
  `dni` VARCHAR(8) NOT NULL,
  `celular` VARCHAR(15) NOT NULL,
  `direccion` TEXT NOT NULL,
  `correo_electronico` VARCHAR(100) NULL,
  `fecha_registro` DATETIME NOT NULL,
  `estado_logico_id_estado_logico` INT NOT NULL,
  PRIMARY KEY (`id_propietario`, `estado_logico_id_estado_logico`),
  INDEX `fk_propietario_mascota_estado_logico_idx` (`estado_logico_id_estado_logico` ASC),
  CONSTRAINT `fk_propietario_mascota_estado_logico`
    FOREIGN KEY (`estado_logico_id_estado_logico`)
    REFERENCES `estado_logico` (`id_estado_logico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla mascota
DROP TABLE IF EXISTS `mascota`;
CREATE TABLE IF NOT EXISTS `mascota` (
  `id_mascota` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `color` VARCHAR(45) NULL,
  `sexo` TINYINT(1) NOT NULL,
  `caracteristicas_fisicas` TEXT NULL,
  `tamano` VARCHAR(20) NULL,
  `peso` DECIMAL(10,2) NULL,
  `id_propietario` INT NOT NULL,
  `id_estado_logico` INT NOT NULL,
  `raza_id_raza` INT NOT NULL,
  `cartilla_vacunacion_id_cartilla_vacunacion` INT NOT NULL,
  PRIMARY KEY (`id_mascota`, `id_propietario`, `id_estado_logico`, `raza_id_raza`, `cartilla_vacunacion_id_cartilla_vacunacion`),
  INDEX `fk_mascota_propietario_mascota_idx` (`id_propietario` ASC, `id_estado_logico` ASC),
  INDEX `fk_mascota_raza_idx` (`raza_id_raza` ASC),
  INDEX `fk_mascota_cartilla_vacunacion_idx` (`cartilla_vacunacion_id_cartilla_vacunacion` ASC),
  CONSTRAINT `fk_mascota_propietario_mascota`
    FOREIGN KEY (`id_propietario` , `id_estado_logico`)
    REFERENCES `propietario_mascota` (`id_propietario` , `estado_logico_id_estado_logico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mascota_raza`
    FOREIGN KEY (`raza_id_raza`)
    REFERENCES `raza` (`id_raza`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_mascota_cartilla_vacunacion`
    FOREIGN KEY (`cartilla_vacunacion_id_cartilla_vacunacion`)
    REFERENCES `cartilla_vacunacion` (`id_cartilla_vacunacion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla tipo_consulta
DROP TABLE IF EXISTS `tipo_consulta`;
CREATE TABLE IF NOT EXISTS `tipo_consulta` (
  `id_tipo_consulta` INT NOT NULL,
  `tipo_consulta` VARCHAR(45) NULL,
  `definicion_consulta` TEXT NULL,
  PRIMARY KEY (`id_tipo_consulta`)
) ENGINE = InnoDB;

-- Tabla tipo_examen_medico
DROP TABLE IF EXISTS `tipo_examen_medico`;
CREATE TABLE IF NOT EXISTS `tipo_examen_medico` (
  `id_tipo_examen_medico` INT NOT NULL,
  `categoria_examen` VARCHAR(45) NOT NULL,
  `archivo_formato` LONGBLOB NOT NULL,
  `laboratorio` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id_tipo_examen_medico`)
) ENGINE = InnoDB;

-- Tabla tipo_usuario
DROP TABLE IF EXISTS `tipo_usuario`;
CREATE TABLE IF NOT EXISTS `tipo_usuario` (
  `id_tipo_usuario` INT NOT NULL,
  `nombre_usuario` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_tipo_usuario`)
) ENGINE = InnoDB;

-- Tabla usuario
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `dni` INT NOT NULL,
  `nombres` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `celular` VARCHAR(45) NULL,
  `correo_electronico` VARCHAR(45) NULL,
  `direccion` VARCHAR(45) NULL,
  `firma` TEXT NULL,
  `tipo_usuario_id_tipo_usuario` INT NOT NULL,
  `estado_logico_id_estado_logico` INT NOT NULL,
  `apellidos` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`dni`, `tipo_usuario_id_tipo_usuario`, `estado_logico_id_estado_logico`),
  INDEX `fk_usuario_tipo_usuario_idx` (`tipo_usuario_id_tipo_usuario` ASC),
  INDEX `fk_usuario_estado_logico_idx` (`estado_logico_id_estado_logico` ASC),
  CONSTRAINT `fk_usuario_tipo_usuario`
    FOREIGN KEY (`tipo_usuario_id_tipo_usuario`)
    REFERENCES `tipo_usuario` (`id_tipo_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuario_estado_logico`
    FOREIGN KEY (`estado_logico_id_estado_logico`)
    REFERENCES `estado_logico` (`id_estado_logico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Tabla vacunas
DROP TABLE IF EXISTS `vacunas`;
CREATE TABLE IF NOT EXISTS `vacunas` (
  `id_vacunas` INT NOT NULL,
  `nombre_comercial` VARCHAR(45) NOT NULL,
  `codigo_vacuna` VARCHAR(45) NULL,
  `contraindicaciones` TEXT NULL,
  `advertencias` TEXT NULL,
  `efectos_secundarios` TEXT NULL,
  PRIMARY KEY (`id_vacunas`)
) ENGINE = InnoDB;

-- Tabla registro_vacuna
DROP TABLE IF EXISTS `registro_vacuna`;
CREATE TABLE IF NOT EXISTS `registro_vacuna` (
  `id_registro_vacuna` INT NOT NULL AUTO_INCREMENT,
  `fecha_administracion` DATE NULL,
  `fecha_proxima_dosis` DATE NULL,
  `cartilla_vacunacion_id_cartilla_vacunacion` INT NOT NULL,
  `vacunas_id_vacunas` INT NOT NULL,
  `usuario_id_usuario` INT NOT NULL,
  `usuario_tipo_usuario_id_tipo_usuario` INT NOT NULL,
  `usuario_estado_logico_id_estado_logico` INT NOT NULL,
  `consulta_id_consulta` INT NULL,
  PRIMARY KEY (`id_registro_vacuna`, `cartilla_vacunacion_id_cartilla_vacunacion`, `vacunas_id_vacunas`, `usuario_id_usuario`, `usuario_tipo_usuario_id_tipo_usuario`, `usuario_estado_logico_id_estado_logico`, `consulta_id_consulta`),
  INDEX `fk_registro_vacuna_consulta_idx` (`consulta_id_consulta` ASC),
  INDEX `fk_registro_vacuna_cartilla_vacunacion_idx` (`cartilla_vacunacion_id_cartilla_vacunacion` ASC),
  INDEX `fk_registro_vacuna_vacunas_idx` (`vacunas_id_vacunas` ASC),
  INDEX `fk_registro_vacuna_usuario_idx` (`usuario_id_usuario` ASC, `usuario_tipo_usuario_id_tipo_usuario` ASC, `usuario_estado_logico_id_estado_logico` ASC),
  CONSTRAINT `fk_registro_vacuna_consulta`
    FOREIGN KEY (`consulta_id_consulta`)
    REFERENCES `consulta` (`id_consulta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_registro_vacuna_cartilla_vacunacion`
    FOREIGN KEY (`cartilla_vacunacion_id_cartilla_vacunacion`)
    REFERENCES `cartilla_vacunacion` (`id_cartilla_vacunacion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_registro_vacuna_vacunas`
    FOREIGN KEY (`vacunas_id_vacunas`)
    REFERENCES `vacunas` (`id_vacunas`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_registro_vacuna_usuario`
    FOREIGN KEY (`usuario_id_usuario` , `usuario_tipo_usuario_id_tipo_usuario` , `usuario_estado_logico_id_estado_logico`)
    REFERENCES `usuario` (`dni` , `tipo_usuario_id_tipo_usuario` , `estado_logico_id_estado_logico`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;

-- Restaurar configuracion
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;