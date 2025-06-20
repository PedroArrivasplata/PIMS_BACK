<?php
  function conectar(){
        $dsn = "mysql:host=localhost;dbname=mydb;charset=utf8";
        $usuario = "root";
        $clave = "";
        try {
            $pdo= new PDO($dsn, $usuario, $clave);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }catch(PDOException $e) {
            die("Error al conectar :".$e->getMessage());
        }
    }

?>