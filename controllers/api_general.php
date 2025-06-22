<?php
// API pública para gestión de cartillas, consultas, exámenes y búsquedas mixtas
require_once 'funcionesconsultor.php';
require_once 'funcionesveterinario.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // =========================
    // GET: Consultas y búsquedas
    // =========================
    case 'GET':
        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                // Buscar mascotas por DNI de propietario
                case 'mascotas_por_dni':
                    if (isset($_GET['dni_propietario'])) {
                        echo json_encode(getMascotasPorPropietario($_GET['dni_propietario']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta dni_propietario']);
                    }
                    break;

                // Buscar cartilla por id_mascota
                case 'cartilla':
                    if (isset($_GET['id_mascota'])) {
                        echo json_encode(consultarCartilla($_GET['id_mascota']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta id_mascota']);
                    }
                    break;

                // Consultar citas con filtros mixtos
                case 'citas':
                    $filtros = [];
                    if (isset($_GET['fecha'])) $filtros['fecha'] = $_GET['fecha'];
                    if (isset($_GET['nombre_mascota'])) $filtros['nombre_mascota'] = $_GET['nombre_mascota'];
                    if (isset($_GET['dni_propietario'])) $filtros['dni_propietario'] = $_GET['dni_propietario'];
                    if (!empty($filtros)) {
                        echo json_encode(getCitasFiltradas($filtros));
                    } else {
                        echo json_encode(getCitasProgramadas());
                    }
                    break;

                // Consultar historial médico con criterios mixtos
                case 'historial':
                    $filtros = [];
                    if (isset($_GET['dni_propietario'])) $filtros['dni_propietario'] = $_GET['dni_propietario'];
                    if (isset($_GET['nombre_mascota'])) $filtros['nombre_mascota'] = $_GET['nombre_mascota'];
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
    // POST: Creación de registros
    // =========================
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                // Crear cartilla (requiere id_mascota e id_consultor)
                case 'cartilla':
                    if (isset($data['id_mascota']) && isset($data['id_consultor'])) {
                        echo json_encode(crearCartilla($data['id_mascota'], $data['id_consultor']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Faltan datos requeridos']);
                    }
                    break;

                // Crear consulta médica
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

                // Crear examen médico
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
    // PUT: Actualización de registros
    // =========================
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                // Editar consulta médica
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

                // Editar examen médico
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
    // DELETE: Eliminación de registros
    // =========================
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_GET['endpoint'])) {
            switch ($_GET['endpoint']) {
                // Eliminar cartilla
                case 'cartilla':
                    if (isset($data['id_cartilla'])) {
                        echo json_encode(eliminarCartilla($data['id_cartilla']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta id_cartilla']);
                    }
                    break;

                // Eliminar consulta médica
                case 'consulta':
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        $success = deleteConsulta($_GET['id']);
                        echo json_encode(['success' => $success]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID de consulta requerido']);
                    }
                    break;

                // Eliminar examen médico
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
?>