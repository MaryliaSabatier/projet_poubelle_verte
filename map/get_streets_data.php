<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de configuration
require_once __DIR__ . '/../config.php';

try {
    // Connexion à la base de données en utilisant PDO
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparer la requête pour récupérer les rues et arrêts associés
    $query = "
        SELECT r.libelle AS rue, a.libelle AS arret, a.latitude, a.longitude, ar.ordre
        FROM arrets a
        JOIN arret_rues ar ON a.id = ar.arret_id
        JOIN rues r ON ar.rue_id = r.id
        ORDER BY r.libelle, ar.ordre;
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Organiser les données en un tableau associatif
    $rues = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rue = $row['rue'];

        // Validation des données pour éviter les coordonnées invalides
        if (is_numeric($row['latitude']) && is_numeric($row['longitude'])) {
            if (!isset($rues[$rue])) {
                $rues[$rue] = ['stops' => []];
            }
            $rues[$rue]['stops'][] = [
                'name' => $row['arret'],
                'lat' => (float)$row['latitude'],
                'lon' => (float)$row['longitude'],
                'order' => (int)$row['ordre']
            ];
        } else {
            error_log("Données invalides pour l'arrêt {$row['arret']} (Latitude: {$row['latitude']}, Longitude: {$row['longitude']})");
        }
    }

    // Suppression des doublons dans chaque rue
    foreach ($rues as $rue => $data) {
        $rues[$rue]['stops'] = array_unique($data['stops'], SORT_REGULAR);
    }

    // Retourner les données en JSON
    header('Content-Type: application/json');
    echo json_encode($rues, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    // En cas d'erreur, renvoyer un message d'erreur JSON
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]);
} catch (Exception $e) {
    // Gestion des autres types d'erreurs
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Erreur inattendue : ' . $e->getMessage()]);
}
