<?php
//Esta API trabaja sin validación de autenticación, se asume que el acceso es público
// y que los datos son proporcionados por un cliente confiable.
require_once 'funcionesconsultor.php';
require_once 'funcionesveterinario.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // ==============================================
    // ENDPOINTS PARA CARTILLAS DE VACUNACIÓN
    // ==============================================
    case 'GET':
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

    // ==============================================
    // ENDPOINTS PARA CREACIÓN DE REGISTROS
    // ==============================================
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
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

    // ==============================================
    // ENDPOINTS PARA ACTUALIZACIÓN
    // ==============================================
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        
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

    // ==============================================
    // ENDPOINTS PARA ELIMINACIÓN
    // ==============================================
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        
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
}