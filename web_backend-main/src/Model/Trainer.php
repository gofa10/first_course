<?php
namespace App\Model;

use App\Core\Database;
use PDO;

class Trainer {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM trainers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(string $fullName, string $phone, string $specialization, ?string $birthDate): bool {
        $stmt = $this->pdo->prepare("INSERT INTO trainers (trainer_full_name, trainer_phone, trainer_birth_date, trainer_specialization) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$fullName, $phone, $birthDate, $specialization]);
    }
}
?>