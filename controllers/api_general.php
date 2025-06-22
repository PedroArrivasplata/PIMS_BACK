<?php
// Esta API trabaja sin autenticación. Se asume acceso público con cliente confiable.

require_once 'funcionesconsultor.php';
require_once 'funcionesveterinario.php';

// Cabeceras
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Funciones auxiliares
function validarCampos($data, $requeridos) {
    return count(array_intersect(array_keys($data), $requeridos)) === count($requeridos);
}

function errorResponse($mensaje, $codigo = 400) {
    http_response_code($codigo);
    echo json_encode(['error' => $mensaje]);
    exit;
}

// Obtener método y endpoint
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';
$ruta = "$method:$endpoint";

// Leer datos JSON
$data = json_decode(file_get_contents("php://input"), true);
if (in_array($method, ['POST', 'PUT', 'DELETE']) && json_last_error() !== JSON_ERROR_NONE) {
    errorResponse('JSON mal formado');
}

switch ($ruta) {
    // GET =======================
    case 'GET:cartilla':
        if (isset($_GET['id_mascota'])) {
            echo json_encode(consultarCartilla($_GET['id_mascota']));
        } else {
            errorResponse('Falta id_mascota');
        }
        break;

    case 'GET:citas':
        if (isset($_GET['filtros'])) {
            $filtros = json_decode($_GET['filtros'], true);
            echo json_encode(getCitasFiltradas($filtros));
        } else {
            echo json_encode(getCitasProgramadas());
        }
        break;

    case 'GET:historial':
        $filtros = $_GET['filtros'] ?? [];
        echo json_encode(getHistorialMedicoCompleto($filtros));
        break;

    // POST =======================
    case 'POST:cartilla':
        if (validarCampos($data, ['id_mascota', 'id_consultor'])) {
            echo json_encode(crearCartilla($data['id_mascota'], $data['id_consultor']));
        } else {
            errorResponse('Faltan datos requeridos');
        }
        break;

    case 'POST:consulta':
        $required = ['diagnostico', 'sintomas', 'observaciones', 'tratamiento', 'tipo_consulta_id', 'cita_id'];
        if (validarCampos($data, $required)) {
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
            errorResponse('Faltan datos requeridos para consulta');
        }
        break;

    case 'POST:examen':
        $required = ['examen_generado', 'formato', 'fecha', 'tipo_examen_id', 'consulta_id'];
        if (validarCampos($data, $required)) {
            $success = insertDetalleExamen(
                $data['examen_generado'],
                $data['formato'],
                $data['fecha'],
                $data['tipo_examen_id'],
                $data['consulta_id']
            );
            echo json_encode(['success' => $success]);
        } else {
            errorResponse('Faltan datos requeridos para examen');
        }
        break;

    // PUT =======================
    case 'PUT:consulta':
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $required = ['diagnostico', 'sintomas', 'observaciones', 'tratamiento', 'tipo_consulta_id'];
            if (validarCampos($data, $required)) {
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
                errorResponse('Faltan datos requeridos para actualizar consulta');
            }
        } else {
            errorResponse('ID de consulta requerido');
        }
        break;

    case 'PUT:examen':
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $required = ['examen_generado', 'formato'];
            if (validarCampos($data, $required)) {
                $success = updateDetalleExamen(
                    $_GET['id'],
                    $data['examen_generado'],
                    $data['formato']
                );
                echo json_encode(['success' => $success]);
            } else {
                errorResponse('Faltan datos requeridos para actualizar examen');
            }
        } else {
            errorResponse('ID de examen requerido');
        }
        break;

    // DELETE =======================
    case 'DELETE:cartilla':
        if (isset($data['id_cartilla'])) {
            echo json_encode(eliminarCartilla($data['id_cartilla']));
        } else {
            errorResponse('Falta id_cartilla');
        }
        break;

    case 'DELETE:consulta':
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $success = deleteConsulta($_GET['id']);
            echo json_encode(['success' => $success]);
        } else {
            errorResponse('ID de consulta requerido');
        }
        break;

    case 'DELETE:examen':
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $success = deleteDetalleExamen($_GET['id']);
            echo json_encode(['success' => $success]);
        } else {
            errorResponse('ID de examen requerido');
        }
        break;

    // MÉTODO NO PERMITIDO
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Ruta o método no permitido']);
        break;
}
