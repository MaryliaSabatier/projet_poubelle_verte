<?php
// Activer les rapports d'erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Indiquer que le contenu est au format JSON
header('Content-Type: application/json');

// Inclure la configuration de la base de données
require_once __DIR__ . '/../../config.php';

try {
    // Récupérer les données envoyées via POST
    $data = json_decode(file_get_contents('php://input'), true);

    // Journal de débogage
    error_log("Recalculate Tours script started");
    error_log("Données POST reçues : " . print_r($data, true));

    // Vérifier que les incidents sont fournis
    if (!$data || !isset($data['incidents']) || !is_array($data['incidents'])) {
        error_log("Erreur : Aucune donnée d'incident valide reçue.");
        echo json_encode(['status' => 'error', 'message' => 'Aucune donnée d\'incident reçue.']);
        exit;
    }

    $incidents = $data['incidents']; // Liste des incidents signalés
    $blockedStops = array_column($incidents, 'stop_id'); // Extraire les IDs des arrêts bloqués

    // Connexion à la base de données
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les arrêts disponibles depuis la base de données
    $stmt = $pdo->query("SELECT id, libelle, latitude, longitude FROM arrets");
    $stops = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stops[$row['id']] = $row;
    }

    // Filtrer les arrêts pour exclure ceux qui sont bloqués
    $validStops = array_filter($stops, function ($stop) use ($blockedStops) {
        return !in_array($stop['id'], $blockedStops);
    });

    // Fonction pour construire le graphe des distances entre les arrêts
    function calculateDistances($stops)
    {
        $graph = [];
        $earthRadius = 6371; // Rayon de la Terre en km

        foreach ($stops as $fromId => $fromStop) {
            foreach ($stops as $toId => $toStop) {
                if ($fromId !== $toId) {
                    $latFrom = deg2rad($fromStop['latitude']);
                    $lonFrom = deg2rad($fromStop['longitude']);
                    $latTo = deg2rad($toStop['latitude']);
                    $lonTo = deg2rad($toStop['longitude']);

                    $latDelta = $latTo - $latFrom;
                    $lonDelta = $lonTo - $lonFrom;

                    $a = sin($latDelta / 2) ** 2 +
                        cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

                    $distance = $earthRadius * $c;
                    $graph[$fromId][$toId] = $distance;
                }
            }
        }

        return $graph;
    }

    // Calcul du graphe des distances
    $graph = calculateDistances($validStops);

    // Fonction pour répartir les arrêts entre les cyclistes
    function assignStopsToCyclists($cyclists, $stops, $graph)
    {
        $assignments = [];
        foreach ($cyclists as $cyclist) {
            if (!empty($stops)) {
                $assignments[$cyclist] = [];
                $currentStop = array_shift($stops); // Prendre le premier arrêt
                $assignments[$cyclist][] = $currentStop;

                while (!empty($stops)) {
                    $nextStop = null;
                    $minDistance = INF;

                    foreach ($stops as $key => $stop) {
                        $distance = $graph[$currentStop['id']][$stop['id']] ?? INF;
                        if ($distance < $minDistance) {
                            $minDistance = $distance;
                            $nextStop = $key;
                        }
                    }

                    if ($nextStop !== null) {
                        $assignments[$cyclist][] = $stops[$nextStop];
                        unset($stops[$nextStop]);
                        $currentStop = $stops[$nextStop];
                    } else {
                        break;
                    }
                }
            }
        }

        return $assignments;
    }

    // Exemple de cyclistes (les cyclistes peuvent être dynamiques selon votre application)
    $cyclists = ['Cycliste1', 'Cycliste2'];

    // Calcul des assignations
    $tours = assignStopsToCyclists($cyclists, $validStops, $graph);

    // Retourner les tournées en JSON
    echo json_encode(['status' => 'success', 'newTours' => $tours]);

} catch (Exception $e) {
    // Gérer les erreurs et retourner un message JSON
    error_log("Erreur : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
