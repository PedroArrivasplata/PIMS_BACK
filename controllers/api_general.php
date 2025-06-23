<?php
// API pública para gestión de cartillas, consultas, exámenes y búsquedas mixtas
require_once __DIR__ . '/../models/funcionesconsultor.php';
require_once __DIR__ . '/../models/funcionesveterinario.php';

// SOLO UN HEADER DE ORIGEN
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

// Manejo de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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

                // Buscar mascotas por DNI, nombre de propietario y/o nombre de mascota (flexible)
                case 'mascotas_propietario_flexible':
                    $filtro = [];
                    if (isset($_GET['dni'])) $filtro['dni'] = $_GET['dni'];
                    if (isset($_GET['nombre'])) $filtro['nombre'] = $_GET['nombre'];
                    if (isset($_GET['nombre_mascota'])) $filtro['nombre_mascota'] = $_GET['nombre_mascota'];
                    if (!empty($filtro)) {
                        echo json_encode(getMascotasPorPropietarioFlexible($filtro));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta al menos un filtro: dni, nombre o nombre_mascota']);
                    }
                    break;

                // Obtener todas las mascotas registradas
                case 'todas_mascotas':
                    echo json_encode(getTodasLasMascotas());
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

                // Buscar mascotas por coincidencia parcial en el nombre de la mascota
                case 'mascotas_por_nombre':
                    if (isset($_GET['nombre_mascota'])) {
                        echo json_encode(getMascotasPorNombreParcial($_GET['nombre_mascota']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta nombre_mascota']);
                    }
                    break;

                // Obtener todos los exámenes médicos registrados
                case 'todos_examenes_medicos':
                    echo json_encode(getTodosLosExamenesMedicos());
                    break;

                // Obtener todos los exámenes médicos asociados a una mascota por su id
                case 'examenes_por_mascota':
                    if (isset($_GET['id_mascota'])) {
                        echo json_encode(getExamenesPorMascota($_GET['id_mascota']));
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Falta parámetro id_mascota']);
                    }
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
                    // Verifica que se haya enviado un archivo y los campos requeridos
                    $required = ['examen_generado', 'formato', 'fecha', 'tipo_examen_id', 'consulta_id'];
                    $allFieldsPresent = true;
                    foreach ($required as $field) {
                        if (!isset($_POST[$field])) {
                            $allFieldsPresent = false;
                            break;
                        }
                    }
                    // Validar que consulta_id no sea vacío
                    if ($allFieldsPresent && isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK && trim($_POST['consulta_id']) !== '') {
                        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                        $ext = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
                        if (in_array($ext, $allowed)) {
                            $dir = __DIR__ . '/../data/examenes_medicos/';
                            if (!is_dir($dir)) mkdir($dir, 0777, true);
                            // Usar nombre personalizado si se envía, si no usar uniqid
                            if (isset($_POST['nombre_examen']) && $_POST['nombre_examen']) {
                                $nombreLimpio = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_POST['nombre_examen']);
                                $filename = $nombreLimpio . '.' . $ext;
                            } else {
                                $filename = uniqid('examen_') . '.' . $ext;
                            }
                            $filepath = $dir . $filename;
                            try {
                                if (move_uploaded_file($_FILES['archivo']['tmp_name'], $filepath)) {
                                    try {
                                        $success = insertDetalleExamen(
                                            $filename,
                                            $_POST['examen_generado'],
                                            $_POST['formato'],
                                            $_POST['fecha'],
                                            $_POST['tipo_examen_id'],
                                            $_POST['consulta_id']
                                        );
                                        if ($success) {
                                            echo json_encode(['success' => true, 'archivo' => $filename]);
                                        } else {
                                            http_response_code(500);
                                            echo json_encode(['error' => 'Error al guardar en la base de datos (insertDetalleExamen)']);
                                        }
                                    } catch (Throwable $ex) {
                                        http_response_code(500);
                                        echo json_encode(['error' => 'Excepción: ' . $ex->getMessage()]);
                                    }
                                } else {
                                    http_response_code(500);
                                    echo json_encode(['error' => 'No se pudo guardar el archivo']);
                                }
                            } catch (Throwable $ex) {
                                http_response_code(500);
                                echo json_encode(['error' => 'Excepción: ' . $ex->getMessage()]);
                            }
                        } else {
                            http_response_code(400);
                            echo json_encode(['error' => 'Tipo de archivo no permitido']);
                        }
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Faltan datos requeridos para examen o archivo, o consulta_id vacío']);
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