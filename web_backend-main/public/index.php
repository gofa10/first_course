<?php
require __DIR__ . "/../vendor/autoload.php";

use App\Controller\MainController;
use App\Controller\ClientController;
use App\Controller\TrainerController;
use App\Controller\TrainingSessionController;
use App\Core\Router;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$router = new Router();

$loader = new FilesystemLoader(__DIR__ . '/../src/views');
$twig = new Environment($loader);

$mainController = new MainController($twig);
$clientController = new ClientController($twig);
$trainerController = new TrainerController($twig);
$trainingSessionController = new TrainingSessionController($twig);

$router->get("/", [$mainController, "index"]);

$router->get("/clients", [$clientController, "table"]);
$router->get("/clients/add", [$clientController, "form"]);
$router->post("/clients/add", [$clientController, "save"]);
$router->post("/clients/upload", [$clientController, "upload"]);

$router->get("/trainers", [$trainerController, "table"]);
$router->get("/trainers/add", [$trainerController, "form"]);
$router->post("/trainers/add", [$trainerController, "save"]);
$router->post("/trainers/upload", [$trainerController, "upload"]);

$router->get("/training_sessions", [$trainingSessionController, "table"]);
$router->get("/training_sessions/add", [$trainingSessionController, "form"]);
$router->post("/training_sessions/add", [$trainingSessionController, "save"]);
$router->post("/training_sessions/upload", [$trainingSessionController, "upload"]);

$router->resolve();

?>