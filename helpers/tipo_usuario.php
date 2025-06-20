<?php
require_once __DIR__ . '/../models/funcionesbasicas.php';

function obtenerTiposDeUsuario() {
    $pdo = conectar();
    $sql = "SELECT id_tipo_usuario, nombre_usuario FROM tipo_usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}