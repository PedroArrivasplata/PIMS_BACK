<?php
header("Content-Type: application/json");
require "models/funcionesbasicas.php";
require "models/funcionesveterinario.php";
require "models/funcionesUsuario.php"; // Para validación de usuarios

// Incluir controladores de autenticación
require "controllers/api_login.php";
require "controllers/api_usuarios.php";

$method = $_SERVER['REQUEST_METHOD'];
$ruta = '';
if(isset($_GET['ruta'])) {
    $ruta = $_GET['ruta'];
}

// Primero manejar rutas de autenticación
switch($ruta) {
    case 'login':
        handleLoginRequest($method);
        exit;
    case 'usuarios':
        handleUsuariosRequest($method);
        exit;
}

// Para las demás rutas, verificar token JWT
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Token de autorización no proporcionado']);
    exit;
}

$token = str_replace('Bearer ', '', $headers['Authorization']);
$usuario = validarToken($token); // Función de api_login.php

if (!$usuario || $usuario['tipo'] !== 'veterinario') {
    http_response_code(403);
    echo json_encode(['error' => 'Acceso no autorizado para veterinarios']);
    exit;
}

// Rutas específicas del veterinario
switch($ruta) {
    case 'citas':
        switch($method) {
            case 'GET':
                if(isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                    $cita = getCitaPorId($id);
                    if($cita) {
                        echo json_encode($cita);
                    } else {
                        http_response_code(404);
                        echo json_encode(['error' => 'Cita no encontrada']);
                    }
                } else {
                    if(isset($_GET['filtro'])) {
                        $filtros = [
                            'fecha' => $_GET['fecha'] ?? null,
                            'nombre_mascota' => $_GET['nombre_mascota'] ?? null,
                            'dni_propietario' => $_GET['dni_propietario'] ?? null,
                            'estado' => $_GET['estado'] ?? null
                        ];
                        $resultado = getCitasFiltradas(array_filter($filtros));
                    } else {
                        $resultado = getCitasProgramadas();
                    }
                    echo json_encode($resultado);
                }
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if(isset($data['mascota_id']) && isset($data['fecha']) && isset($data['hora']) && isset($data['motivo'])) {
                    $resultado = programarCita(
                        $data['mascota_id'],
                        $data['fecha'],
                        $data['hora'],
                        $data['motivo'],
                        $usuario['dni'] // Usamos el DNI del usuario autenticado
                    );
                    http_response_code(201);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Datos incompletos para programar cita']);
                }
                break;
                
            case 'PUT':
                if(isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                    $data = json_decode(file_get_contents('php://input'), true);
                    $resultado = actualizarCita($id, $data);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de cita no proporcionado']);
                }
                break;
                
            case 'DELETE':
                if(isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                    $resultado = cancelarCita($id);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de cita no proporcionado']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
        break;
        
    case 'cartillas':
        switch($method) {
            case 'GET':
                if(isset($_GET['id_mascota'])) {
                    $id_mascota = intval($_GET['id_mascota']);
                    $resultado = consultarCartilla($id_mascota);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de mascota no proporcionado']);
                }
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if(isset($data['id_mascota'])) {
                    $resultado = crearCartilla(
                        $data['id_mascota'],
                        $usuario['dni'] // DNI del veterinario autenticado
                    );
                    http_response_code(201);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de mascota no proporcionado']);
                }
                break;
                
            case 'DELETE':
                if(isset($_GET['id'])) {
                    $id_cartilla = intval($_GET['id']);
                    $resultado = eliminarCartilla($id_cartilla);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de cartilla no proporcionado']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
        break;
        
    case 'vacunas':
        switch($method) {
            case 'GET':
                if(isset($_GET['id_mascota'])) {
                    $id_mascota = intval($_GET['id_mascota']);
                    $resultado = getCartillaVacunacion($id_mascota);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID de mascota no proporcionado']);
                }
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if(isset($data['id_mascota']) && isset($data['id_vacuna']) && isset($data['fecha_aplicacion'])) {
                    $resultado = registrarVacuna(
                        $data['id_mascota'],
                        $data['id_vacuna'],
                        $data['fecha_aplicacion'],
                        $usuario['dni'], // DNI del veterinario
                        $data['fecha_proxima'] ?? null
                    );
                    http_response_code(201);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Datos incompletos para registrar vacuna']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
        break;
        
    case 'mascotas':
        switch($method) {
            case 'GET':
                if(isset($_GET['dni_propietario'])) {
                    $dni = $_GET['dni_propietario'];
                    $resultado = getMascotasPorPropietario($dni);
                    echo json_encode($resultado);
                } elseif(isset($_GET['id'])) {
                    $id_mascota = intval($_GET['id']);
                    $resultado = getMascotaPorId($id_mascota);
                    echo json_encode($resultado);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Parámetros insuficientes']);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint no encontrado']);
}
?>