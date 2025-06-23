use test;
-- datos_prueba.sql

-- Estados lógicos
INSERT INTO estado_logico (id_estado_logico, descripcion_estado) VALUES
  (1, 'Activo'),
  (0, 'Inactivo');

-- Tipos de usuario
INSERT INTO tipo_usuario (id_tipo_usuario, nombre_usuario) VALUES
  (1, 'veterinario'),
  (2, 'recepcionista'),
  (3, 'asistente');

-- Usuarios
INSERT INTO usuario (dni, nombres, password, celular, correo_electronico, direccion, firma, tipo_usuario_id_tipo_usuario, estado_logico_id_estado_logico, apellidos) VALUES
  (1001, 'Juan', 'pass123', '999888777', 'juan@vet.com', 'Av. Salud 123', NULL, 1, 1, 'Pérez'),
  (1002, 'Ana', 'pass456', '988877766', 'ana@recepcion.com', 'Calle Mascota 456', NULL, 2, 1, 'García'),
  (1003, 'Pedro', 'pass789', '977766655', 'pedro@asistente.com', 'Jr. Animal 789', NULL, 3, 1, 'Ramírez'),
  (1004, 'Laura', 'pass321', '966555444', 'laura@vet.com', 'Av. Canina 321', NULL, 1, 0, 'Torres');

-- Especies
INSERT INTO especie (id_especie, nombre_especie, estado_logico_id_estado_logico) VALUES
  (1, 'Canino', 1),
  (2, 'Felino', 1),
  (3, 'Ave', 1);

-- Razas
INSERT INTO raza (id_raza, nombre_raza, especie_id_especie, especie_estado_logico_id_estado_logico) VALUES
  (1, 'Labrador', 1, 1),
  (2, 'Siames', 2, 1),
  (3, 'Pastor Alemán', 1, 1),
  (4, 'Persa', 2, 1),
  (5, 'Canario', 3, 1);

-- Propietarios
INSERT INTO propietario_mascota (id_propietario, nombre, apellido, dni, celular, direccion, correo_electronico, fecha_registro, estado_logico_id_estado_logico) VALUES
  (1, 'Carlos', 'Ramírez', '12345678', '987654321', 'Av. Principal 123', 'carlos@mail.com', NOW(), 1),
  (2, 'Lucía', 'Torres', '87654321', '912345678', 'Jr. Secundario 456', 'lucia@mail.com', NOW(), 1),
  (3, 'Miguel', 'Soto', '11223344', '934567890', 'Calle Ficticia 789', 'miguel@mail.com', NOW(), 1);

-- Cartillas de vacunación
INSERT INTO cartilla_vacunacion (id_cartilla_vacunacion, fecha_creacion) VALUES
  (1, '2024-01-10'),
  (2, '2024-02-15'),
  (3, '2024-03-20');

-- Mascotas
INSERT INTO mascota (id_mascota, nombre, fecha_nacimiento, color, sexo, caracteristicas_fisicas, tamano, peso, id_propietario, id_estado_logico, raza_id_raza, cartilla_vacunacion_id_cartilla_vacunacion) VALUES
  (1, 'Firulais', '2020-05-10', 'Marrón', 1, 'Orejas largas', 'Grande', 30.5, 1, 1, 1, 1),
  (2, 'Michi', '2022-03-20', 'Blanco', 0, 'Ojos azules', 'Pequeño', 4.2, 2, 1, 2, 2),
  (3, 'Rocky', '2019-11-05', 'Negro', 1, 'Cola corta', 'Mediano', 25.0, 1, 1, 3, 1),
  (4, 'Pelusa', '2021-07-15', 'Gris', 0, 'Pelaje largo', 'Pequeño', 3.8, 3, 1, 4, 3),
  (5, 'Piolín', '2023-01-01', 'Amarillo', 1, 'Alas grandes', 'Pequeño', 0.2, 3, 1, 5, 3);

-- Métodos de pago
INSERT INTO metodo_pago (id_metodo_pago, tipo_pago) VALUES
  (1, 'Efectivo'),
  (2, 'Tarjeta'),
  (3, 'Transferencia');

-- Estados de cita
INSERT INTO estado_cita (id_estado_cita, estado_cita, descripcion) VALUES
  (1, 'Pendiente', 'Cita pendiente de atención'),
  (2, 'Atendida', 'Cita ya atendida'),
  (3, 'Cancelada', 'Cita cancelada por el cliente');

-- Tipos de consulta
INSERT INTO tipo_consulta (id_tipo_consulta, tipo_consulta, definicion_consulta) VALUES
  (1, 'General', 'Consulta general veterinaria'),
  (2, 'Vacunación', 'Consulta para vacunación'),
  (3, 'Emergencia', 'Consulta por emergencia');

-- Citas
INSERT INTO cita (id_cita, fecha_cita, hora_cita, motivo, mascota_id_mascota, usuario_dni, estado_cita_id_estado_cita, metodo_pago_id_metodo_pago, pagado) VALUES
  (1, '2024-06-01', '10:00:00', 'Chequeo anual', 1, 1001, 1, 1, 'SI'),
  (2, '2024-06-02', '11:30:00', 'Vacunación', 2, 1002, 2, 2, 'NO'),
  (3, '2024-06-03', '09:00:00', 'Emergencia por caída', 3, 1001, 1, 1, 'SI'),
  (4, '2024-06-04', '15:00:00', 'Consulta general', 4, 1003, 3, 3, 'NO'),
  (5, '2024-06-05', '16:30:00', 'Chequeo de ave', 5, 1001, 2, 1, 'SI');

-- Consultas
INSERT INTO consulta (id_consulta, diagnostico, sintomas, observaciones, tratamiento, tipo_consulta_id_tipo_consulta, cita_id_cita) VALUES
  (1, 'Saludable', 'Ninguno', 'Todo normal', 'Ninguno', 1, 1),
  (2, 'Vacunado', 'Ninguno', 'Aplicada vacuna', 'Reposo 1 día', 2, 2),
  (3, 'Fractura leve', 'Cojea pata trasera', 'Reposo y analgésico', 'Reposo 7 días', 3, 3),
  (4, 'Alergia', 'Picazón', 'Posible alergia alimentaria', 'Cambio de dieta', 1, 4),
  (5, 'Saludable', 'Ninguno', 'Ave en buen estado', 'Ninguno', 1, 5);

-- Tipos de examen médico
INSERT INTO tipo_examen_medico (id_tipo_examen_medico, categoria_examen, archivo_formato, laboratorio) VALUES
  (1, 'Sangre', '', 'LabVet'),
  (2, 'Orina', '', 'LabPet'),
  (3, 'Radiografía', '', 'LabXRay');

-- Detalle de examen en consulta
INSERT INTO detalle_examen_consulta (id_detalle_examen_consulta, examen_generado, formato, fecha, tipo_examen_medico_id_tipo_examen_medico, consulta_id_consulta) VALUES
  (1, 'Hemograma', 'PDF', '2024-06-01', 1, 1),
  (2, 'Urianálisis', 'PDF', '2024-06-02', 2, 2),
  (3, 'Radiografía de pata', 'JPG', '2024-06-03', 3, 3);

-- Vacunas
INSERT INTO vacunas (id_vacunas, nombre_comercial, codigo_vacuna, contraindicaciones, advertencias, efectos_secundarios) VALUES
  (1, 'Rabican', 'RBC001', 'No aplicar en enfermos', 'Mantener refrigerado', 'Fiebre leve'),
  (2, 'Felivax', 'FLV002', 'No aplicar en gestantes', 'Agitar antes de usar', 'Somnolencia'),
  (3, 'Canarivac', 'CNR003', 'Solo aves sanas', 'No exponer al sol', 'Ninguno');

-- Registro de vacunas
INSERT INTO registro_vacuna (id_registro_vacuna, fecha_administracion, fecha_proxima_dosis, cartilla_vacunacion_id_cartilla_vacunacion, vacunas_id_vacunas, usuario_id_usuario, usuario_tipo_usuario_id_tipo_usuario, usuario_estado_logico_id_estado_logico, consulta_id_consulta) VALUES
  (1, '2024-06-01', '2025-06-01', 1, 1, 1001, 1, 1, 1),
  (2, '2024-06-02', '2025-06-02', 2, 2, 1002, 2, 1, 2),
  (3, '2024-06-05', '2025-06-05', 3, 3, 1001, 1, 1, 5);-- Insertar tipos de usuario


-- Insertar usuarios de ejemplo
INSERT INTO usuario (
  dni, nombres, password, celular, correo_electronico, direccion, firma,
  tipo_usuario_id_tipo_usuario, estado_logico_id_estado_logico, apellidos
) VALUES
  (1005, 'Pedro', '123456', '999888777', 'pe783845@gmail.com', 'Av. Salud 123', NULL, 1, 1, 'Arrivasplata Mera'),
  (1006, 'David', '123456', '988877766', 'ana@recepcion.com', 'Calle Mascota 456', NULL, 1, 1, 'Paz Aguilar'),
  (1007, 'Kiara', '123456', '977766655', 'carlos@vet.com', 'Jr. Animal 789', NULL, 2, 0, 'Cruz Talledo');

    -- Consultas (4 por cada mascota)
  INSERT INTO consulta (id_consulta, diagnostico, sintomas, observaciones, tratamiento, tipo_consulta_id_tipo_consulta, cita_id_cita) VALUES
    -- Mascota 1 (Firulais)
    (1, 'Saludable', 'Ninguno', 'Todo normal', 'Ninguno', 1, 1),
    (2, 'Otitis', 'Rascado de orejas', 'Orejas enrojecidas', 'Gotas óticas', 1, 1),
    (3, 'Vacunado', 'Ninguno', 'Aplicada vacuna anual', 'Reposo 1 día', 2, 1),
    (4, 'Desparasitado', 'Ninguno', 'Desparasitación interna', 'Dosis única', 1, 1),
  
    -- Mascota 2 (Michi)
    (5, 'Vacunado', 'Ninguno', 'Aplicada vacuna', 'Reposo 1 día', 2, 2),
    (6, 'Gastritis', 'Vómitos', 'Dieta blanda', 'Omeprazol', 1, 2),
    (7, 'Chequeo', 'Ninguno', 'Todo normal', 'Ninguno', 1, 2),
    (8, 'Alergia', 'Picazón', 'Posible alergia alimentaria', 'Cambio de dieta', 1, 2),
  
    -- Mascota 3 (Rocky)
    (9, 'Fractura leve', 'Cojea pata trasera', 'Reposo y analgésico', 'Reposo 7 días', 3, 3),
    (10, 'Vacunado', 'Ninguno', 'Aplicada vacuna', 'Reposo 1 día', 2, 3),
    (11, 'Chequeo', 'Ninguno', 'Todo normal', 'Ninguno', 1, 3),
    (12, 'Otitis', 'Rascado de orejas', 'Orejas inflamadas', 'Gotas óticas', 1, 3),
  
    -- Mascota 4 (Pelusa)
    (13, 'Alergia', 'Picazón', 'Posible alergia alimentaria', 'Cambio de dieta', 1, 4),
    (14, 'Vacunado', 'Ninguno', 'Aplicada vacuna', 'Reposo 1 día', 2, 4),
    (15, 'Chequeo', 'Ninguno', 'Todo normal', 'Ninguno', 1, 4),
    (16, 'Desparasitado', 'Ninguno', 'Desparasitación interna', 'Dosis única', 1, 4),
  
    -- Mascota 5 (Piolín)
    (17, 'Saludable', 'Ninguno', 'Ave en buen estado', 'Ninguno', 1, 5),
    (18, 'Vacunado', 'Ninguno', 'Aplicada vacuna', 'Reposo 1 día', 2, 5),
    (19, 'Chequeo', 'Ninguno', 'Todo normal', 'Ninguno', 1, 5),
    (20, 'Alergia', 'Plumas erizadas', 'Posible alergia ambiental', 'Cambio de jaula', 1, 5);