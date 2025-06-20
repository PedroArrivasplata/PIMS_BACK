<?php
require '../models/funcionesUsuario.php';

header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {

    case 'GET':
        break;

    case 'POST':
                // Validar que todos los campos necesarios estén presentes
        $campos_requeridos = ['dni', 'nombres', 'apellidos', 'correo', 'clave', 'tipo_usuario_id', 'estado_logico_id'];
        foreach ($campos_requeridos as $campo) {
            if (!isset($input[$campo])) {
                http_response_code(400);
                echo json_encode(["error" => "Falta el campo requerido: $campo"]);
                exit;
            }
        }

        // Extraer los datos del input
        $dni = $input['dni'];
        $nombres = $input['nombres'];
        $apellidos = $input['apellidos'];
        $correo = $input['correo'];
        $clave = $input['clave'];
        $tipo_usuario_id = $input['tipo_usuario_id'];
        $estado_logico_id = $input['estado_logico_id'];

        // Llamar a la función de registro
        $resultado = registrarNuevoUsuario($dni, $nombres, $apellidos, $correo, $clave, $tipo_usuario_id, $estado_logico_id);

        if ($resultado['estado'] === 'ok') {
            http_response_code(201); // creado
            echo json_encode(["mensaje" => $resultado['mensaje']]);
        } else {
            http_response_code(400); // solicitud incorrecta
            echo json_encode(["error" => $resultado['mensaje']]);
        }

        break;

    case 'PUT':
        // Aquí se implementará la lógica para actualizar un usuario
        break;

    case 'DELETE':
        // Aquí se implementará la lógica para eliminar un usuario
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido."]);
        break;
}
?>
