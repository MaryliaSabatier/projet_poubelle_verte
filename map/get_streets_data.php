<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de configuration avec le bon chemin
require_once __DIR__ . '/../config.php';

try {
    // Connexion à la base de données en utilisant PDO avec les informations de config.php
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparer la requête pour récupérer les rues et les arrêts associés
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
        if (!isset($rues[$rue])) {
            $rues[$rue] = ['stops' => []];
        }
        $rues[$rue]['stops'][] = [
            'name' => $row['arret'],
            'lat' => (float)$row['latitude'],
            'lon' => (float)$row['longitude']
        ];
    }

    // Retourner les données en JSON
    header('Content-Type: application/json');
    echo json_encode($rues);

} catch (PDOException $e) {
    // En cas d'erreur, renvoyer un message d'erreur JSON
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'Erreur de connexion à la base de données : ' . $e->getMessage()]);
}
