<?php
    require_once 'funcionesbasicas.php';

// Obtener todos los tipos de examen médico con su respectivo id
function getTiposExamenMedico() {
    $pdo = conectar();
    $sql = "SELECT id_tipo_examen_medico, categoria_examen FROM tipo_examen_medico";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>