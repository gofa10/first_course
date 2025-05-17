<?php
namespace App\Model;

use App\Core\Database;
use PDO;

class Client {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM clients");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(string $fullName, string $phone, ?string $birthDate): bool {
        $stmt = $this->pdo->prepare("INSERT INTO clients (client_full_name, client_phone, client_birth_date) VALUES (?, ?, ?)");
        return $stmt->execute([$fullName, $phone, $birthDate]);
    }
}
?>