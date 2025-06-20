<?php
    require 'funcionesbasicas.php';
    function insertConsulta($diagnostico, $sintomas, $observaciones, $tratamiento, $tipo_consulta_id_tipo_consulta, $cita_id_cita) {
        $pdo = conectar();
        $sql = "INSERT INTO consulta (diagnostico, sintomas, observaciones, tratamiento, tipo_consulta_id_tipo_consulta, cita_id_cita)
                VALUES (:diagnostico, :sintomas, :observaciones, :tratamiento, :tipo_consulta_id_tipo_consulta, :cita_id_cita)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":diagnostico", $diagnostico, PDO::PARAM_STR);
        $stmt->bindValue(":sintomas", $sintomas, PDO::PARAM_STR);
        $stmt->bindValue(":observaciones", $observaciones, PDO::PARAM_STR);
        $stmt->bindValue(":tratamiento", $tratamiento, PDO::PARAM_STR);
        $stmt->bindValue(":tipo_consulta_id_tipo_consulta", $tipo_consulta_id_tipo_consulta, PDO::PARAM_INT);
        $stmt->bindValue(":cita_id_cita", $cita_id_cita, PDO::PARAM_INT);
        
        $stmt->execute();
        return $pdo->lastInsertId();
    }

    //--MODIFICAR CONSULTA --//
    function updateConsulta($id_consulta, $diagnostico, $sintomas, $observaciones, $tratamiento, $tipo_consulta_id_tipo_consulta) {
        $pdo = conectar();
        $sql = "UPDATE consulta SET 
                diagnostico = :diagnostico,
                sintomas = :sintomas,
                observaciones = :observaciones,
                tratamiento = :tratamiento,
                tipo_consulta_id_tipo_consulta = :tipo_consulta_id_tipo_consulta
                WHERE id_consulta = :id_consulta";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":diagnostico", $diagnostico, PDO::PARAM_STR);
        $stmt->bindValue(":sintomas", $sintomas, PDO::PARAM_STR);
        $stmt->bindValue(":observaciones", $observaciones, PDO::PARAM_STR);
        $stmt->bindValue(":tratamiento", $tratamiento, PDO::PARAM_STR);
        $stmt->bindValue(":tipo_consulta_id_tipo_consulta", $tipo_consulta_id_tipo_consulta, PDO::PARAM_INT);
        $stmt->bindValue(":id_consulta", $id_consulta, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    //-- ELIMINAR CONSULTA --//
    function deleteConsulta($id_consulta) {
        $pdo = conectar();
        $sql = "DELETE FROM consulta WHERE id_consulta = :id_consulta";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id_consulta", $id_consulta, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    //-- GESTIONAR DETALLE DE LOS EXAMENES --//
    // INSERTAR DetalleExamen
    function insertDetalleExamen($examen_generado, $formato, $fecha, $tipo_examen_medico_id_tipo_examen_medico, $consulta_id_consulta) {
        $pdo = conectar();
        $sql = "INSERT INTO detalle_examen_consulta (examen_generado, formato, fecha, tipo_examen_medico_id_tipo_examen_medico, consulta_id_consulta)
                VALUES (:examen_generado, :formato, :fecha, :tipo_examen_medico_id_tipo_examen_medico, :consulta_id_consulta)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":examen_generado", $examen_generado, PDO::PARAM_STR);
        $stmt->bindValue(":formato", $formato, PDO::PARAM_STR);
        $stmt->bindValue(":fecha", $fecha);
        $stmt->bindValue(":tipo_examen_medico_id_tipo_examen_medico", $tipo_examen_medico_id_tipo_examen_medico, PDO::PARAM_INT);
        $stmt->bindValue(":consulta_id_consulta", $consulta_id_consulta, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // MODIFICAR DetalleExamen
    function updateDetalleExamen($id_detalle_examen_consulta, $examen_generado, $formato) {
        $pdo = conectar();
        $sql = "UPDATE detalle_examen_consulta SET 
                examen_generado = :examen_generado,
                formato = :formato
                WHERE id_detalle_examen_consulta = :id_detalle_examen_consulta";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":examen_generado", $examen_generado, PDO::PARAM_STR);
        $stmt->bindValue(":formato", $formato, PDO::PARAM_STR);
        $stmt->bindValue(":id_detalle_examen_consulta", $id_detalle_examen_consulta, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // ELIMINAR DetalleExamen
    function deleteDetalleExamen($id_detalle_examen_consulta) {
        $pdo = conectar();
        $sql = "DELETE FROM detalle_examen_consulta WHERE id_detalle_examen_consulta = :id_detalle_examen_consulta";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id_detalle_examen_consulta", $id_detalle_examen_consulta, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    //-- CONSULTAR HISTORIAL MEDICO DE LA MASCOTA --//
    function getHistorialMedicoCompleto($filtros = []) {
        $pdo = conectar();
        
        // Consulta base
        $sql = "SELECT 
                m.id_mascota, m.nombre as nombre_mascota,
                p.dni as dni_propietario, CONCAT(p.nombre, ' ', p.apellido) as nombre_propietario,
                c.fecha_cita, 
                co.id_consulta, co.diagnostico, co.sintomas, co.observaciones, co.tratamiento,
                tc.tipo_consulta as tipo_consulta,
                CONCAT(u.nombres, ' ', u.apellidos) as nombre_veterinario,
                cv.id_cartilla_vacunacion,
                GROUP_CONCAT(DISTINCT v.nombre_comercial SEPARATOR ', ') as vacunas_aplicadas,
                GROUP_CONCAT(DISTINCT tem.categoria_examen SEPARATOR ', ') as examenes_realizados
                FROM mascota m
                JOIN propietario_mascota p ON m.id_propietario = p.id_propietario
                JOIN cita c ON m.id_mascota = c.mascota_id_mascota
                JOIN consulta co ON c.id_cita = co.cita_id_cita
                JOIN tipo_consulta tc ON co.tipo_consulta_id_tipo_consulta = tc.id_tipo_consulta
                JOIN usuario u ON c.usuario_dni = u.dni
                JOIN cartilla_vacunacion cv ON m.cartilla_vacunacion_id_cartilla_vacunacion = cv.id_cartilla_vacunacion
                LEFT JOIN registro_vacuna rv ON cv.id_cartilla_vacunacion = rv.cartilla_vacunacion_id_cartilla_vacunacion
                LEFT JOIN vacunas v ON rv.vacunas_id_vacunas = v.id_vacunas
                LEFT JOIN detalle_examen_consulta dec ON co.id_consulta = dec.consulta_id_consulta
                LEFT JOIN tipo_examen_medico tem ON dec.tipo_examen_medico_id_tipo_examen_medico = tem.id_tipo_examen_medico
                WHERE 1=1";
        
        // Aplicar filtros
        $params = [];
        
        if (!empty($filtros['nombre_mascota'])) {
            $sql .= " AND m.nombre LIKE :nombre_mascota";
            $params[':nombre_mascota'] = '%' . $filtros['nombre_mascota'] . '%';
        }
        
        if (!empty($filtros['dni_propietario'])) {
            $sql .= " AND p.dni = :dni_propietario";
            $params[':dni_propietario'] = $filtros['dni_propietario'];
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND c.fecha_cita >= :fecha_desde";
            $params[':fecha_desde'] = $filtros['fecha_desde'];
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND c.fecha_cita <= :fecha_hasta";
            $params[':fecha_hasta'] = $filtros['fecha_hasta'];
        }
        
        if (!empty($filtros['tipo_consulta'])) {
            $sql .= " AND tc.id_tipo_consulta = :tipo_consulta";
            $params[':tipo_consulta'] = $filtros['tipo_consulta'];
        }
        
        if (!empty($filtros['id_mascota'])) {
            $sql .= " AND m.id_mascota = :id_mascota";
            $params[':id_mascota'] = $filtros['id_mascota'];
        }
        
        $sql .= " GROUP BY co.id_consulta ORDER BY c.fecha_cita DESC";
        
        $stmt = $pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>