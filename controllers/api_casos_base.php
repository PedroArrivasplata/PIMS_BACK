<?php

require_once __DIR__ . '/../models/tipo_consulta.php';
require_once __DIR__ . '/../models/funcionesconsultor.php';


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
                // Obtener todos los tipos de examen médico con su respectivo id
                case 'tipos_examen':
                    echo json_encode(getTiposExamenMedico());
                    break;

                // Obtener todas las consultas médicas asociadas a una mascota
                case 'consultas_por_mascota':
                    if (isset($_GET['id_mascota'])) {
                        echo json_encode(getConsultasPorMascota($_GET['id_mascota']));
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
        // Aquí puedes agregar lógica para POST si lo necesitas
        http_response_code(405);
        echo json_encode(['error' => 'Método POST no implementado']);
        break;

    // =========================
    // PUT: Actualización de registros
    // =========================
    case 'PUT':
        // Aquí puedes agregar lógica para PUT si lo necesitas
        http_response_code(405);
        echo json_encode(['error' => 'Método PUT no implementado']);
        break;

    // =========================
    // DELETE: Eliminación de registros
    // =========================
    case 'DELETE':
        // Aquí puedes agregar lógica para DELETE si lo necesitas
        http_response_code(405);
        echo json_encode(['error' => 'Método DELETE no implementado']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}