<?php
namespace App\Model;

use App\Core\Database;
use PDO;

class TrainingSession {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM training_sessions");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(string $name, string $datetime, string $duration, ?string $description): bool {
        $stmt = $this->pdo->prepare("INSERT INTO training_sessions (training_session_name, training_session_datetime, training_session_duration, training_session_description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $datetime, $duration, $description]);
    }
}
?>