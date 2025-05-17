<?php
namespace App\Controller;

use App\Model\Trainer;
use Twig\Environment;

class TrainerController {
    private Trainer $trainerModel;
    private Environment $twig;

    public function __construct(Environment $twig) {
        $this->trainerModel = new Trainer();
        $this->twig = $twig;
    }

    public function form(): void {
        echo $this->twig->render("/trainers/form.twig");
    }

    public function table(): void {
        $columnsMap = [
            "trainer_id" => "ID",
            "trainer_full_name" => "ФИО",
            "trainer_phone" => "Номер телефона",
            "trainer_birth_date" => "Дата рождения",
            "trainer_specialization" => "Специализация"
        ];
        $trainers = $this->trainerModel->getAll();
        echo $this->twig->render("/trainers/table.twig", [
            "trainers" => $trainers,
            "columnsMap" => $columnsMap
        ]);
    }

    public function save(): void {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fullName = strip_tags($_POST["full_name"] ?? "");
            $phone = strip_tags($_POST["phone"] ?? "");
            $specialization = strip_tags($_POST["specialization"] ?? "");
            $birthDate = strip_tags($_POST['birth_date'] ?? "");
            if (empty($fullName) || empty($phone) || empty($specialization)) {
                exit(json_encode(["message" => "Заполните обязательные поля ввода"]));
            }
            $nameFormat = "/^[a-zA-Zа-яА-ЯёЁ\s]*(?:-[a-zA-Zа-яА-ЯёЁ\s]*)?$/";
            $phoneFormat = "/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/";
            $specializationFormat = "/^[a-zA-Zа-яА-ЯёЁ\s]+$/";
            $dateInput = date_create($birthDate);
            if (!preg_match($nameFormat, $fullName) || !preg_match($phoneFormat, $phone) || !preg_match($specializationFormat, $specialization) || !$dateInput) {
                exit(json_encode(["message" => "Введены некорректные данные"]));
            }
            $birthDate = empty($birthDate) ? null : $birthDate;
            if ($this->trainerModel->add($fullName, $phone, $specialization, $birthDate)) {
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
                $fullName = strip_tags($data[0]);
                $phone = strip_tags($data[1]);
                $specialization = strip_tags($data[2]);
                $birthDate = strip_tags($data[3]);
                if (empty($fullName) || empty($phone) || empty($specialization)) {
                    continue;
                }
                $birthDate = empty($birthDate) ? null : $birthDate;
                $this->trainerModel->add($fullName, $phone, $specialization, $birthDate);
            }
            fclose($handle);
            echo json_encode(["message" => "Импорт завершен"]);
        }
    }
}
?>