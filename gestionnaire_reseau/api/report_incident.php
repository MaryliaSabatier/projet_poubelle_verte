<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Déclarer que le contenu est JSON
header('Content-Type: application/json');

// Inclure la configuration de la base de données
require_once __DIR__ . '/../config.php';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer et décoder les données reçues via POST
    $data = json_decode(file_get_contents('php://input'), true);

    // Journal de débogage pour les données POST
    error_log("Données POST reçues : " . print_r($data, true));

    // Vérifier la méthode HTTP
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validation des données reçues
        $type = $data['type'] ?? null;
        $details = $data['details'] ?? null;
        $stop = $data['stop'] ?? null; // ID de l'arrêt
        $bike = $data['bike'] ?? null; // ID du vélo

        // Vérifier que les champs obligatoires sont présents
        if (empty($type) || empty($details)) {
            echo json_encode(['status' => 'error', 'message' => 'Type et détails sont requis.']);
            exit;
        }

        // Préparer et exécuter la requête SQL pour insérer un incident
        $stmt = $pdo->prepare("INSERT INTO incidents (type, details, stop_id, bike_id) VALUES (:type, :details, :stop, :bike)");
        $stmt->execute([
            ':type' => $type,
            ':details' => $details,
            ':stop' => $stop,
            ':bike' => $bike,
        ]);

        // Répondre avec un message de succès
        echo json_encode(['status' => 'success', 'message' => 'Incident signalé avec succès.']);
    } else {
        // Si la méthode HTTP n'est pas POST, renvoyer une erreur
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
    }
} catch (Exception $e) {
    // Gestion des erreurs et réponse JSON
    error_log("Erreur : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
