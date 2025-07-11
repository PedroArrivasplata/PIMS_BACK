<?php
    require_once 'funcionesbasicas.php';
    //-- CONSULTAR CITAS PROGRAMADAS --//
    // Consultar todas las citas programadas (para el consultor)
    function getCitasProgramadas() {
        $pdo = conectar();
        $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.motivo, 
                m.nombre as nombre_mascota, 
                CONCAT(p.nombre, ' ', p.apellido) as nombre_propietario,
                ec.estado_cita
                FROM cita c
                JOIN mascota m ON c.mascota_id_mascota = m.id_mascota
                JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
                JOIN estado_cita ec ON c.estado_cita_id_estado_cita = ec.id_estado_cita
                ORDER BY c.fecha_cita DESC, c.hora_cita DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Consultar citas con filtros
    function getCitasFiltradas($filtros = []) {
        $pdo = conectar();
        $sql = "SELECT c.id_cita, c.fecha_cita, c.hora_cita, c.motivo, 
                m.nombre as nombre_mascota, 
                CONCAT(p.nombre, ' ', p.apellido) as nombre_propietario,
                ec.estado_cita
                FROM cita c
                JOIN mascota m ON c.mascota_id_mascota = m.id_mascota
                JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
                JOIN estado_cita ec ON c.estado_cita_id_estado_cita = ec.id_estado_cita
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por fecha
        if (!empty($filtros['fecha'])) {
            $sql .= " AND c.fecha_cita = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }
        
        // Filtro por nombre de mascota
        if (!empty($filtros['nombre_mascota'])) {
            $sql .= " AND m.nombre LIKE :nombre_mascota";
            $params[':nombre_mascota'] = '%' . $filtros['nombre_mascota'] . '%';
        }
        
        // Filtro por DNI del propietario
        if (!empty($filtros['dni_propietario'])) {
            $sql .= " AND p.dni = :dni_propietario";
            $params[':dni_propietario'] = $filtros['dni_propietario'];
        }
        
        $sql .= " ORDER BY c.fecha_cita DESC, c.hora_cita DESC";
        
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener cartilla de vacunación por mascota
    function getCartillaVacunacion($id_mascota) {
        $pdo = conectar();
        $sql = "SELECT v.id_vacunas, v.nombre_comercial, 
                rv.fecha_administracion, rv.fecha_proxima_dosis,
                CONCAT(u.nombres, ' ', u.apellidos) as nombre_veterinario
                FROM registro_vacuna rv
                JOIN vacunas v ON rv.vacunas_id_vacunas = v.id_vacunas
                JOIN usuario u ON rv.usuario_id_usuario = u.dni
                WHERE rv.cartilla_vacunacion_id_cartilla_vacunacion = 
                (SELECT cartilla_vacunacion_id_cartilla_vacunacion FROM mascota WHERE id_mascota = :id_mascota)
                ORDER BY rv.fecha_administracion DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id_mascota", $id_mascota, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener mascotas por propietario (para filtros)
    function getMascotasPorPropietario($dni_propietario) {
        $pdo = conectar();
        $sql = "SELECT 
                m.id_mascota, 
                m.nombre, 
                TIMESTAMPDIFF(YEAR, m.fecha_nacimiento, CURDATE()) AS edad,
                r.nombre_raza AS raza,
                p.nombre as nombre_propietario,
                p.apellido as apellido_propietario
            FROM mascota m
            JOIN raza r ON m.raza_id_raza = r.id_raza
            JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
            WHERE p.dni = :dni_propietario";
    
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":dni_propietario", $dni_propietario, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Obtener mascotas por coincidencia parcial en el nombre de la mascota
    function getMascotasPorNombreParcial($nombre_mascota) {
        $pdo = conectar();
        $sql = "SELECT 
                    m.id_mascota, 
                    m.nombre, 
                    TIMESTAMPDIFF(YEAR, m.fecha_nacimiento, CURDATE()) AS edad,
                    r.nombre_raza AS raza,
                    e.nombre_especie AS especie,
                    CONCAT(p.nombre, ' ', p.apellido) AS propietario,
                    p.dni AS dni_propietario
                FROM mascota m
                JOIN raza r ON m.raza_id_raza = r.id_raza
                JOIN especie e ON r.especie_id_especie = e.id_especie
                JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
                WHERE m.nombre LIKE :nombre_mascota";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nombre_mascota', '%' . $nombre_mascota . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener mascotas por propietario, filtrando por DNI o nombre
function getMascotasPorPropietarioFlexible($filtro) {
    $pdo = conectar();
    $sql = "SELECT 
                m.id_mascota, 
                m.nombre, 
                TIMESTAMPDIFF(YEAR, m.fecha_nacimiento, CURDATE()) AS edad,
                r.nombre_raza AS raza,
                e.nombre_especie AS especie,
                CONCAT(p.nombre, ' ', p.apellido) AS propietario,
                p.dni AS dni_propietario,
                p.nombre as nombre_propietario,
                p.apellido as apellido_propietario
            FROM mascota m
            JOIN raza r ON m.raza_id_raza = r.id_raza
            JOIN especie e ON r.especie_id_especie = e.id_especie
            JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
            WHERE 1=1";

    $params = [];

    // Filtro por nombre de mascota (parcial)
    if (!empty($filtro['nombre_mascota'])) {
        $sql .= " AND m.nombre LIKE :nombre_mascota";
        $params[':nombre_mascota'] = '%' . $filtro['nombre_mascota'] . '%';
    }

    // Filtro por DNI de propietario (parcial)
    if (!empty($filtro['dni'])) {
        $sql .= " AND p.dni LIKE :dni";
        $params[':dni'] = '%' . $filtro['dni'] . '%';
    }

    // Filtro por nombre o apellido de propietario (parcial)
    if (!empty($filtro['nombre'])) {
        $sql .= " AND (p.nombre LIKE :nombre OR p.apellido LIKE :nombre)";
        $params[':nombre'] = '%' . $filtro['nombre'] . '%';
    }

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// CREAR NUEVA CARTILLA
    function crearCartilla($id_mascota, $id_consultor) {
        $pdo = conectar();
        
        try {
            $pdo->beginTransaction();
            
            // 1. Verificar si la mascota ya tiene cartilla
            $sqlCheck = "SELECT cartilla_vacunacion_id_cartilla_vacunacion 
                        FROM mascota 
                        WHERE id_mascota = :id_mascota";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindValue(":id_mascota", $id_mascota, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            if ($stmtCheck->fetch()) {
                throw new Exception("La mascota ya tiene una cartilla asignada");
            }
            
            // 2. Crear la cartilla
            $sqlInsert = "INSERT INTO cartilla_vacunacion (fecha_creacion) 
                        VALUES (NOW())";
            $stmtInsert = $pdo->prepare($sqlInsert);
            
            if (!$stmtInsert->execute()) {
                throw new Exception("Error al crear cartilla");
            }
            
            $id_cartilla = $pdo->lastInsertId();
            
            // 3. Asignar a la mascota
            $sqlUpdate = "UPDATE mascota 
                        SET cartilla_vacunacion_id_cartilla_vacunacion = :id_cartilla 
                        WHERE id_mascota = :id_mascota";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->bindValue(":id_cartilla", $id_cartilla, PDO::PARAM_INT);
            $stmtUpdate->bindValue(":id_mascota", $id_mascota, PDO::PARAM_INT);
            
            if (!$stmtUpdate->execute()) {
                throw new Exception("Error al asignar cartilla a mascota");
            }

            $pdo->commit();
            return [
                'success' => true,
                'id_cartilla' => $id_cartilla
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // CONSULTAR CARTILLA COMPLETA (CON VACUNAS)
    function consultarCartilla($id_mascota) {
        $pdo = conectar();
        
        try {
            // 1. Obtener información básica de la cartilla
            $sqlCartilla = "SELECT cv.id_cartilla_vacunacion, cv.fecha_creacion,
                        m.nombre as nombre_mascota, 
                        CONCAT(p.nombre, ' ', p.apellido) as propietario
                        FROM cartilla_vacunacion cv
                        JOIN mascota m ON cv.id_cartilla_vacunacion = m.cartilla_vacunacion_id_cartilla_vacunacion
                        JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
                        WHERE m.id_mascota = :id_mascota";
            
            $stmtCartilla = $pdo->prepare($sqlCartilla);
            $stmtCartilla->bindValue(":id_mascota", $id_mascota, PDO::PARAM_INT);
            $stmtCartilla->execute();
            
            $cartilla = $stmtCartilla->fetch(PDO::FETCH_ASSOC);
            
            if (!$cartilla) {
                return [
                    'success' => false,
                    'message' => 'No se encontró cartilla para esta mascota'
                ];
            }
            
            // 2. Obtener todas las vacunas asociadas
            $sqlVacunas = "SELECT rv.id_registro_vacuna, v.nombre_comercial, 
                        rv.fecha_administracion, rv.fecha_proxima_dosis,
                        CONCAT(u.nombres, ' ', u.apellidos) as aplicado_por
                        FROM registro_vacuna rv
                        JOIN vacunas v ON rv.vacunas_id_vacunas = v.id_vacunas
                        JOIN usuario u ON rv.usuario_id_usuario = u.dni
                        WHERE rv.cartilla_vacunacion_id_cartilla_vacunacion = :id_cartilla
                        ORDER BY rv.fecha_administracion DESC";
            
            $stmtVacunas = $pdo->prepare($sqlVacunas);
            $stmtVacunas->bindValue(":id_cartilla", $cartilla['id_cartilla_vacunacion'], PDO::PARAM_INT);
            $stmtVacunas->execute();
            
            $vacunas = $stmtVacunas->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'cartilla' => $cartilla,
                'vacunas' => $vacunas
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // ELIMINAR CARTILLA (Y SUS REGISTROS DE VACUNAS)
    function eliminarCartilla($id_cartilla) {
        $pdo = conectar();
        
        try {
            $pdo->beginTransaction();
            
            // 1. Verificar que no esté asignada a una mascota
            $sqlCheck = "SELECT id_mascota FROM mascota 
                        WHERE cartilla_vacunacion_id_cartilla_vacunacion = :id_cartilla";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindValue(":id_cartilla", $id_cartilla, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            if ($stmtCheck->rowCount() > 0) {
                throw new Exception("No se puede eliminar: la cartilla está asignada a una mascota");
            }
            
            // 2. Eliminar registros de vacunas primero
            $sqlDeleteVacunas = "DELETE FROM registro_vacuna 
                                WHERE cartilla_vacunacion_id_cartilla_vacunacion = :id_cartilla";
            $stmtDeleteVacunas = $pdo->prepare($sqlDeleteVacunas);
            $stmtDeleteVacunas->bindValue(":id_cartilla", $id_cartilla, PDO::PARAM_INT);
            
            if (!$stmtDeleteVacunas->execute()) {
                throw new Exception("Error al eliminar registros de vacunas");
            }
            
            // 3. Eliminar la cartilla
            $sqlDeleteCartilla = "DELETE FROM cartilla_vacunacion 
                                WHERE id_cartilla_vacunacion = :id_cartilla";
            $stmtDeleteCartilla = $pdo->prepare($sqlDeleteCartilla);
            $stmtDeleteCartilla->bindValue(":id_cartilla", $id_cartilla, PDO::PARAM_INT);
            
            if (!$stmtDeleteCartilla->execute()) {
                throw new Exception("Error al eliminar cartilla");
            }
            
            $pdo->commit();
            return [
                'success' => true,
                'message' => 'Cartilla y registros asociados eliminados correctamente'
            ];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Obtener todas las mascotas registradas
    function getTodasLasMascotas() {
        $pdo = conectar();
        $sql = "SELECT 
                    m.id_mascota,
                    m.nombre AS nombre,
                    m.fecha_nacimiento,
                    TIMESTAMPDIFF(YEAR, m.fecha_nacimiento, CURDATE()) AS edad,
                    m.color,
                    m.sexo,
                    m.caracteristicas_fisicas,
                    m.tamano,
                    m.peso,
                    r.nombre_raza as raza,
                    e.nombre_especie as especie,
                    CONCAT(p.nombre, ' ', p.apellido) AS propietario,
                    p.dni AS dni_propietario
                FROM mascota m
                JOIN raza r ON m.raza_id_raza = r.id_raza
                JOIN especie e ON r.especie_id_especie = e.id_especie
                JOIN propietario_mascota p ON m.id_propietario = p.id_propietario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Obtener todas las consultas médicas asociadas a una mascota por su id
function getConsultasPorMascota($id_mascota) {
    $pdo = conectar();
    $sql = "SELECT 
                c.id_consulta,
                c.diagnostico,
                c.sintomas,
                c.observaciones,
                c.tratamiento,
                c.tipo_consulta_id_tipo_consulta,
                tc.tipo_consulta,
                tc.definicion_consulta,
                c.cita_id_cita,
                ci.fecha_cita,
                ci.hora_cita,
                ci.motivo
            FROM consulta c
            JOIN tipo_consulta tc ON c.tipo_consulta_id_tipo_consulta = tc.id_tipo_consulta
            JOIN cita ci ON c.cita_id_cita = ci.id_cita
            WHERE ci.mascota_id_mascota = :id_mascota
            ORDER BY ci.fecha_cita DESC, ci.hora_cita DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id_mascota", $id_mascota, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener todos los exámenes médicos registrados
function getTodosLosExamenesMedicos() {
    $pdo = conectar();
    $sql = "SELECT 
                d.id_detalle_examen_consulta,
                d.filename,
                d.examen_generado,
                d.formato,
                d.fecha,
                d.tipo_examen_medico_id_tipo_examen_medico,
                t.categoria_examen,
                d.consulta_id_consulta
            FROM detalle_examen_consulta d
            JOIN tipo_examen_medico t ON d.tipo_examen_medico_id_tipo_examen_medico = t.id_tipo_examen_medico
            ORDER BY d.fecha DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener todos los exámenes médicos asociados a una mascota por su id
function getExamenesPorMascota($id_mascota) {
    $pdo = conectar();
    $sql = "SELECT 
                d.id_detalle_examen_consulta,
                d.filename,
                d.examen_generado,
                d.formato,
                d.fecha,
                d.tipo_examen_medico_id_tipo_examen_medico,
                t.categoria_examen,
                d.consulta_id_consulta
            FROM detalle_examen_consulta d
            JOIN tipo_examen_medico t ON d.tipo_examen_medico_id_tipo_examen_medico = t.id_tipo_examen_medico
            JOIN consulta c ON d.consulta_id_consulta = c.id_consulta
            JOIN cita ci ON c.cita_id_cita = ci.id_cita
            WHERE ci.mascota_id_mascota = :id_mascota
            ORDER BY d.fecha DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id_mascota", $id_mascota, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener el detalle de un examen médico por su id_detalle_examen_consulta
function getDetalleExamenPorId($id_detalle_examen_consulta) {
    $pdo = conectar();
    $sql = "SELECT 
                d.id_detalle_examen_consulta,
                d.filename,
                d.examen_generado,
                d.formato,
                d.fecha,
                d.tipo_examen_medico_id_tipo_examen_medico,
                t.categoria_examen,
                d.consulta_id_consulta
            FROM detalle_examen_consulta d
            JOIN tipo_examen_medico t ON d.tipo_examen_medico_id_tipo_examen_medico = t.id_tipo_examen_medico
            WHERE d.id_detalle_examen_consulta = :id_detalle_examen_consulta";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":id_detalle_examen_consulta", $id_detalle_examen_consulta, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>