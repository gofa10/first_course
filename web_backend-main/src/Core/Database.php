<?php
namespace App\Core;

use PDO;
use PDOException;

class Database {
    public function connect() {
        $host = "db";
        $db = "fitness_club";
        $user = "user";
        $pass = "1234";

        $dsn = "mysql:host=$host;dbname=$db";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        try {
            $conn = new PDO($dsn, $user, $pass, $options);
            return $conn;
        } catch (PDOException $e) {
            exit("Ошибка подключения: {$e->getMessage()}");
        }
    }
}

?>