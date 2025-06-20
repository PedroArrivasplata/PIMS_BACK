<?php
// requires de las funciones

require_once '../models/funcionesconsultor.php';
require_once '../models/funcionesveterinario.php';
require_once '../models/funcionesUsuario.php';
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Función de autenticación unificada (usa la misma lógica que api_login.php)
function verificarAutenticacion() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="Sistema Veterinario"');
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(['error' => 'Autenticación requerida']);
        exit;
    }

    $correo = $_SERVER['PHP_AUTH_USER'];
    $clave = $_SERVER['PHP_AUTH_PW'];

    // Usa la función existente de funcionesUsuario.php
    $resultado = obtenerUsuarioPorCorreoYClave($correo, $clave);

    if ($resultado['estado'] !== 'ok') {
        header('HTTP/1.0 403 Forbidden');
        echo json_encode(['error' => 'Credenciales inválidas']);
        exit;
    }

    // Devuelve el tipo de usuario desde la BD (consultor/veterinario)
    return $resultado['usuario']['tipo_usuario']; 
}

// Resto del código de apivet.php (sin cambios)
$userRole = verificarAutenticacion();
// ... (mantén todo lo demás igual)
// Procesar la solicitud
try {
    $resource = $uriSegments[2] ?? '';
    $id = $uriSegments[3] ?? null;
    
    switch ("$requestMethod:$resource") {
        // API para Consultor
        case 'GET:citas':
            if ($userRole !== 'consultor') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            $filtros = $_GET;
            $citas = getCitasFiltradas($filtros);
            echo json_encode($citas);
            break;
            
        case 'GET:cartillas':
            if ($userRole !== 'consultor') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            if (!$id) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Se requiere ID de mascota']);
                exit;
            }
            
            $resultado = consultarCartilla($id);
            echo json_encode($resultado);
            break;
            
        case 'POST:cartillas':
            if ($userRole !== 'consultor') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || !isset($data['id_mascota'])) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Datos inválidos']);
                exit;
            }
            
            $resultado = crearCartilla($data['id_mascota'], $_SERVER['PHP_AUTH_USER']);
            echo json_encode($resultado);
            break;
            
        case 'DELETE:cartillas':
            if ($userRole !== 'consultor') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            if (!$id) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Se requiere ID de cartilla']);
                exit;
            }
            
            $resultado = eliminarCartilla($id);
            echo json_encode($resultado);
            break;
            
        // API para Veterinario
        case 'GET:historial':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            $filtros = $_GET;
            $historial = getHistorialMedicoCompleto($filtros);
            echo json_encode($historial);
            break;
            
        case 'POST:consultas':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['diagnostico']) || empty($data['cita_id_cita'])) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Datos incompletos']);
                exit;
            }
            
            $idConsulta = insertConsulta(
                $data['diagnostico'],
                $data['sintomas'] ?? '',
                $data['observaciones'] ?? '',
                $data['tratamiento'] ?? '',
                $data['tipo_consulta_id_tipo_consulta'] ?? 1,
                $data['cita_id_cita']
            );
            
            echo json_encode(['success' => true, 'id_consulta' => $idConsulta]);
            break;
            
        case 'PUT:consultas':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            if (!$id) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Se requiere ID de consulta']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Datos inválidos']);
                exit;
            }
            
            $success = updateConsulta(
                $id,
                $data['diagnostico'] ?? '',
                $data['sintomas'] ?? '',
                $data['observaciones'] ?? '',
                $data['tratamiento'] ?? '',
                $data['tipo_consulta_id_tipo_consulta'] ?? 1
            );
            
            echo json_encode(['success' => $success]);
            break;
            
        case 'DELETE:consultas':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            if (!$id) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Se requiere ID de consulta']);
                exit;
            }
            
            $success = deleteConsulta($id);
            echo json_encode(['success' => $success]);
            break;
            
        case 'POST:examenes':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data || empty($data['consulta_id_consulta']) || empty($data['tipo_examen_medico_id_tipo_examen_medico'])) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Datos incompletos']);
                exit;
            }
            
            $success = insertDetalleExamen(
                $data['examen_generado'] ?? '',
                $data['formato'] ?? 'pdf',
                $data['fecha'] ?? date('Y-m-d'),
                $data['tipo_examen_medico_id_tipo_examen_medico'],
                $data['consulta_id_consulta']
            );
            
            echo json_encode(['success' => $success]);
            break;
            
        case 'PUT:examenes':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            if (!$id) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Se requiere ID de examen']);
                exit;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Datos inválidos']);
                exit;
            }
            
            $success = updateDetalleExamen(
                $id,
                $data['examen_generado'] ?? '',
                $data['formato'] ?? 'pdf'
            );
            
            echo json_encode(['success' => $success]);
            break;
            
        case 'DELETE:examenes':
            if ($userRole !== 'veterinario') {
                header("HTTP/1.1 403 Forbidden");
                echo json_encode(['error' => 'No tienes permisos para acceder a este recurso']);
                exit;
            }
            
            if (!$id) {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(['error' => 'Se requiere ID de examen']);
                exit;
            }
            
            $success = deleteDetalleExamen($id);
            echo json_encode(['success' => $success]);
            break;
            
        default:
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Endpoint no encontrado']);
            break;
    }
} catch (Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['error' => $e->getMessage()]);
}