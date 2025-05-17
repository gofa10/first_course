<?php
namespace App\Controller;

use App\Model\Client;
use Twig\Environment;

class ClientController {
    private Client $clientModel;
    private Environment $twig;

    public function __construct(Environment $twig) {
        $this->clientModel = new Client();
        $this->twig = $twig;
    }

    public function form(): void {
        echo $this->twig->render("/clients/form.twig");
    }

    public function table(): void {
        $columnsMap = [
            "client_id" => "ID",
            "client_full_name" => "ФИО",
            "client_phone" => "Номер телефона",
            "client_birth_date" => "Дата рождения"
        ];
        $clients = $this->clientModel->getAll();
        echo $this->twig->render("/clients/table.twig", [
            "clients" => $clients,
            "columnsMap" => $columnsMap
        ]);
    }

    public function save(): void {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $fullName = strip_tags($_POST["full_name"] ?? "");
            $phone = strip_tags($_POST["phone"] ?? "");
            $birthDate = strip_tags($_POST["birth_date"] ?? "");
            if (empty($fullName) || empty($phone)) {
                exit(json_encode(["message" => "Заполните обязательные поля ввода"]));
            }
            $nameFormat = "/^[a-zA-Zа-яА-ЯёЁ\s]*(?:-[a-zA-Zа-яА-ЯёЁ\s]*)?$/";
            $phoneFormat = "/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/";
            $dateInput = date_create($birthDate);
            if (!preg_match($nameFormat, $fullName) || !preg_match($phoneFormat, $phone) || !$dateInput) {
                exit(json_encode(["message" => "Введены некорректные данные"]));
            }
            $birthDate = empty($birthDate) ? null : $birthDate;
            if ($this->clientModel->add($fullName, $phone, $birthDate)) {
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
                if (count($data) < 2) {
                    continue;
                }
                $fullName = strip_tags($data[0]);
                $phone = strip_tags($data[1]);
                $birthDate = strip_tags($data[2]);
                if (empty($fullName) || empty($phone)) {
                    continue;
                }
                $birthDate = empty($birthDate) ? null : $birthDate;
                $this->clientModel->add($fullName, $phone, $birthDate);
            }
            fclose($handle);
            echo json_encode(["message" => "Импорт завершен"]);
        }
    }
}
?>