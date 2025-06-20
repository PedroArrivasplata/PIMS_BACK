<?php
require 'funcionesbasicas.php';
function obtenerUsuarioPorCorreoYClave($correo, $clave) {
    $pdo = conectar();
    $sql = "SELECT u.nombres, u.apellidos, u.dni, u.celular, t.nombre_usuario, u.password
            FROM usuario u
            INNER JOIN tipo_usuario t ON u.tipo_usuario_id_tipo_usuario = t.id_tipo_usuario
            WHERE u.correo_electronico = :correo
            limit 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // No se encontró el correo
        return ['estado' => 'correo_no_encontrado'];
    }

    if ($clave === ($usuario['password'] ?? '')) {
        // Eliminamos la contraseña antes de devolver
        unset($usuario['password']);
        $usuario['tipo_usuario'] = $usuario['nombre_usuario'];
        unset($usuario['nombre_usuario']);
        return ['estado' => 'ok', 'usuario' => $usuario];
    } else {
        return ['estado' => 'clave_incorrecta'];
    }
}


//Kiara hizo esto
function registrarNuevoUsuario($dni, $nombres, $apellidos, $correo, $clave, $tipo_usuario_id, $estado_logico_id) {
    $pdo = conectar(); 
    $sql = "INSERT INTO usuario (
                dni,
                nombres,
                apellidos,
                correo_electronico,
                password,
                tipo_usuario_id_tipo_usuario,
                estado_logico_id_estado_logico
            ) VALUES (
                :dni, :nombres, :apellidos, :correo, :password, :tipo_usuario_id, :estado_logico_id
            )";

    $stmt = $pdo->prepare($sql); 

    try {
        $stmt->execute([
            ':dni' => $dni,
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':correo' => $correo,
            ':password' => $clave,
            ':tipo_usuario_id' => $tipo_usuario_id,
            ':estado_logico_id' => $estado_logico_id
        ]);
        return ['estado' => 'ok', 'mensaje' => 'Usuario registrado con éxito'];
    } catch (PDOException $e) {
        // Manejo de errores de clave foránea o duplicados
        if ($e->getCode() == 23000) {
            return ['estado' => 'error', 'mensaje' => 'El correo o el DNI ya están registrados'];
        }
        return ['estado' => 'error', 'mensaje' => 'Error al registrar usuario: ' . $e->getMessage()];
    }
}

?>
