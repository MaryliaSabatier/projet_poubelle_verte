<?php
require_once '../src/Config/Database.php';
require_once '../src/Controllers/BaseController.php';
require_once '../src/Controllers/VehicleController.php';

// Simple routeur
$route = $_GET['route'] ?? 'home';

switch($route) {
    case 'vehicles':
        $controller = new VehicleController();
        $controller->index();
        break;
    default:
        // Page d'accueil par défaut
        require_once '../src/Views/home.php';
} 