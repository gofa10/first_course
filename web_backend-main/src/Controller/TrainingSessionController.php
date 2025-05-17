<?php
namespace App\Controller;

use App\Model\TrainingSession;
use Twig\Environment;

class TrainingSessionController {
    private TrainingSession $trainingSessionModel;
    private Environment $twig;

    public function __construct(Environment $twig) {
        $this->trainingSessionModel = new TrainingSession();
        $this->twig = $twig;
    }

    public function form(): void {
        echo $this->twig->render("/training_sessions/form.twig");
    }

    public function table(): void {
        $columnsMap = [
            "training_session_id" => "ID",
            "training_session_name" => "Название",
            "training_session_datetime" => "Дата и время",
            "training_session_duration" => "Длительность, ч.",
            "training_session_description" => "Описание"
        ];
        $trainingSessions = $this->trainingSessionModel->getAll();
        echo $this->twig->render("/training_sessions/table.twig", [
            "trainingSessions" => $trainingSessions,
            "columnsMap" => $columnsMap
        ]);
    }

    public function save(): void {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = strip_tags($_POST["name"] ?? "");
            $datetime = strip_tags($_POST["datetime"] ?? "");
            $duration = strip_tags($_POST["duration"] ?? "");
            $description = strip_tags($_POST['description'] ?? "");
            if (empty($name) || empty($datetime) || empty($duration)) {
                exit(json_encode(["message" => "Заполните обязательные поля ввода"]));
            }
            $nameFormat = "/^[a-zA-Zа-яА-ЯёЁ\s-]+$/";
            $datetimeInput = date_create($datetime);
            $durationInput = floatval($duration);
            if (!preg_match($nameFormat, $name) || !$datetimeInput || !$durationInput) {
                exit(json_encode(["message" => "Введены некорректные данные"]));
            }
            if ($durationInput <= 0) {
                exit(json_encode(["message" => "Длительность должна быть больше 0"]));
            }
            if ($this->trainingSessionModel->add($name, $datetime, $duration, $description)) {
                echo json_encode(["message" => "Данные успешно обработаны"]);
            } else {
                echo json_encode(["message" => "Ошибка при выполнении запроса"]);
            }
        }
    }

    public function upload(): void {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
            $file = $_FILES["file"];
            if ($file["error"] !== UPLOAD_ERR_OK) {
                exit(json_encode(["message" => "Ошибка при загрузке файла"]));
            }
            $handle = fopen($file['tmp_name'], "r");
            if (!$handle) {
                exit(json_encode(["message" => "Не удалось открыть файл"]));
            }
            while (($data = fgetcsv($handle, null, ";", "\"", "\\")) !== false) {
                if (count($data) < 3) {
                    continue;
                }
                $name = strip_tags($data[0]);
                $datetime = strip_tags($data[1]);
                $duration = strip_tags($data[2]);
                $description = strip_tags($data[3]);
                if (empty($name) || empty($datetime) || empty($duration)) {
                    continue;
                }
                $description = empty($description) ? null : $description;
                $this->trainingSessionModel->add($name, $datetime, $duration, $description);
            }
            fclose($handle);
            echo json_encode(["message" => "Импорт завершен"]);
        }
    }
}
?>