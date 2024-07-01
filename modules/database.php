<?php

class Database {
    private $host = '127.0.0.1';
    private $db_name = 'TALLER_EURO';
    private $username = 'root';
    private $password = '1234';
    private $pdo;

    public function conectar() {
        $this->pdo = null;

        try {
            $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }

        return $this->pdo;
    }
}
?>
