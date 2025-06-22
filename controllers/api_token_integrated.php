<?php
//--- NO SE USA ESTA API ---
// --- API TOKEN INTEGRATED ---
// --- HEADERS PARA CORS Y JSON ---
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- INCLUSIÓN DE FUNCIONES Y JWT ---
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload
require_once __DIR__ . '/../models/funcionesUsuario.php';
require_once __DIR__ . '/../models/funcionesconsultor.php';
require_once __DIR__ . '/../models/funcionesveterinario.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// --- CONFIGURACIÓN ---
define('JWT_SECRET', 'tu_clave_secreta'); // Cambia esto por una clave segura

// --- FUNCIONES AUXILIARES JWT ---
function getBearerToken() {
    $headers = [];
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    }
    if (isset($headers['Authorization'])) {
        if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
    }
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        if (preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function authGuard($rolesPermitidos = []) {
    $token = getBearerToken();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Token no proporcionado']);
        exit;
    }
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        if (!empty($rolesPermitidos) && (!isset($decoded->rol) || !in_array($decoded->rol, $rolesPermitidos))) {
            http_response_code(403);
            echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
            exit;
        }
        return $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Token inválido o expirado']);
        exit;
    }
}

// --- API PRINCIPAL ---
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

// --- LOGIN ---
if (isset($_GET['endpoint']) && $_GET['endpoint'] === 'login' && $method === 'POST') {
    if (!isset($input['correo']) || !isset($input['clave'])) {
        http_response_code(400);
        echo json_encode(["error" => "Faltan parámetros: correo y clave requeridos."]);
        exit;
    }
    $correo = $input['correo'];
    $clave = $input['clave'];
    $resultado = obtenerUsuarioPorCorreoYClave($correo, $clave);

    switch ($resultado['estado']) {
        case 'ok':
            $usuario = $resultado['usuario'];
            $payload = [
                'user' => $correo,
                'rol' => $usuario['rol'] ?? 'usuario',
                'iat' => time(),
                'exp' => time() + 3600
            ];
            $token = JWT::encode($payload, JWT_SECRET, 'HS256');
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "token" => $token,
                "usuario" => $usuario
            ]);
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
    exit;
}

// --- ENDPOINTS PROTEGIDOS ---
switch ($method) {
    // =========================
    // GET
    // =========================
    case 'GET':
        authGuard(['consultor', 'veterinario', 'recepcionista', 'usuario']); // Ajusta roles según tu sistema
        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                case 'cartilla':
                    if (isset($_GET['id_mascota'])) {
                        echo json_encode(consultarCartilla($_GET['id_mascota']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta id_mascota']);
                    }
                    break;
                case 'citas':
                    if (isset($_GET['filtros'])) {
                        $filtros = json_decode($_GET['filtros'], true);
                        echo json_encode(getCitasFiltradas($filtros));
                    } else {
                        echo json_encode(getCitasProgramadas());
                    }
                    break;
                case 'historial':
                    $filtros = $_GET['filtros'] ?? [];
                    echo json_encode(getHistorialMedicoCompleto($filtros));
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint GET no reconocido']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parámetro endpoint']);
        }
        break;

    // =========================
    // POST
    // =========================
    case 'POST':
        authGuard(['consultor', 'veterinario']);
        $data = $input;
        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                case 'cartilla':
                    if (isset($data['id_mascota']) && isset($data['id_consultor'])) {
                        echo json_encode(crearCartilla($data['id_mascota'], $data['id_consultor']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Faltan datos requeridos']);
                    }
                    break;
                case 'consulta':
                    $required = ['diagnostico', 'sintomas', 'observaciones', 'tratamiento', 'tipo_consulta_id', 'cita_id'];
                    if (count(array_intersect(array_keys($data), $required)) === count($required)) {
                        $id = insertConsulta(
                            $data['diagnostico'],
                            $data['sintomas'],
                            $data['observaciones'],
                            $data['tratamiento'],
                            $data['tipo_consulta_id'],
                            $data['cita_id']
                        );
                        echo json_encode(['id_consulta' => $id]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Faltan datos requeridos para consulta']);
                    }
                    break;
                case 'examen':
                    $required = ['examen_generado', 'formato', 'fecha', 'tipo_examen_id', 'consulta_id'];
                    if (count(array_intersect(array_keys($data), $required)) === count($required)) {
                        $success = insertDetalleExamen(
                            $data['examen_generado'],
                            $data['formato'],
                            $data['fecha'],
                            $data['tipo_examen_id'],
                            $data['consulta_id']
                        );
                        echo json_encode(['success' => $success]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Faltan datos requeridos para examen']);
                    }
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint POST no reconocido']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parámetro endpoint']);
        }
        break;

    // =========================
    // PUT
    // =========================
    case 'PUT':
        authGuard(['consultor', 'veterinario']);
        $data = $input;
        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                case 'consulta':
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $required = ['diagnostico', 'sintomas', 'observaciones', 'tratamiento', 'tipo_consulta_id'];
                        if (count(array_intersect(array_keys($data), $required)) === count($required)) {
                            $success = updateConsulta(
                                $_GET['id'],
                                $data['diagnostico'],
                                $data['sintomas'],
                                $data['observaciones'],
                                $data['tratamiento'],
                                $data['tipo_consulta_id']
                            );
                            echo json_encode(['success' => $success]);
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Faltan datos requeridos para actualizar consulta']);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID de consulta requerido']);
                    }
                    break;
                case 'examen':
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $required = ['examen_generado', 'formato'];
                        if (count(array_intersect(array_keys($data), $required)) === count($required)) {
                            $success = updateDetalleExamen(
                                $_GET['id'],
                                $data['examen_generado'],
                                $data['formato']
                            );
                            echo json_encode(['success' => $success]);
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Faltan datos requeridos para actualizar examen']);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID de examen requerido']);
                    }
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint PUT no reconocido']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parámetro endpoint']);
        }
        break;

    // =========================
    // DELETE
    // =========================
    case 'DELETE':
        authGuard(['consultor', 'veterinario']);
        $data = $input;
        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                case 'cartilla':
                    if (isset($data['id_cartilla'])) {
                        echo json_encode(eliminarCartilla($data['id_cartilla']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta id_cartilla']);
                    }
                    break;
                case 'consulta':
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $success = deleteConsulta($_GET['id']);
                        echo json_encode(['success' => $success]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID de consulta requerido']);
                    }
                    break;
                case 'examen':
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $success = deleteDetalleExamen($_GET['id']);
                        echo json_encode(['success' => $success]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID de examen requerido']);
                    }
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['error' => 'Endpoint DELETE no reconocido']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Falta parámetro endpoint']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
// --- FIN DEL SCRIPT ---
?>