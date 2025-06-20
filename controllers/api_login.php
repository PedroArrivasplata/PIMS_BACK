<?php
require '../models/funcionesUsuario.php';

header("Content-Type: application/json; charset=UTF-8");

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);
$uri = explode("/", trim($_SERVER['REQUEST_URI'], "/"));
$id = end($uri); // Se espera que el ID del usuario venga al final de la URI, ej. /api/usuarios/123

switch ($method) {

    case 'GET':
        break;

    case 'POST':
        if (!isset($input['correo']) || !isset($input['clave'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan parámetros: correo y clave requeridos."]);
        }
        else{  
            $correo = $input['correo'];
            $clave = $input['clave'];

            $resultado = obtenerUsuarioPorCorreoYClave($correo, $clave);

            switch ($resultado['estado']) {
                case 'ok':
                    http_response_code(200);
                    echo json_encode($resultado);
                    break;

                case 'correo_no_encontrado':
                    http_response_code(404);
                    echo json_encode(["error" => "Correo no registrado."]);
                    break;

                case 'clave_incorrecta':
                    http_response_code(401);
                    echo json_encode(["error" => "Contraseña incorrecta."]);
                    break;

                default:
                    http_response_code(500);
                    echo json_encode(["error" => "Error inesperado al procesar la solicitud."]);
                    break;
            }
            
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
