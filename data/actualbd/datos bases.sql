-- Insertar tipos de usuario
INSERT INTO tipo_usuario (id_tipo_usuario, nombre_usuario) VALUES
  (1, 'veterinario'),
  (2, 'recepcionista');

-- Insertar estados lógicos
INSERT INTO estado_logico (id_estado_logico, descripcion_estado) VALUES
  (1, 'Activo'),
  (0, 'Inactivo');

-- Insertar usuarios de ejemplo
INSERT INTO usuario (
  dni, nombres, password, celular, correo_electronico, direccion, firma,
  tipo_usuario_id_tipo_usuario, estado_logico_id_estado_logico, apellidos
) VALUES
  (1001, 'Pedro', '123456', '999888777', 'pe783845@gmail.com', 'Av. Salud 123', NULL, 1, 1, 'Arrivasplata Mera'),
  (1002, 'David', '123456', '988877766', 'ana@recepcion.com', 'Calle Mascota 456', NULL, 1, 1, 'Paz Aguilar'),
  (1003, 'Kiara', '123456', '977766655', 'carlos@vet.com', 'Jr. Animal 789', NULL, 2, 0, 'Cruz Talledo');