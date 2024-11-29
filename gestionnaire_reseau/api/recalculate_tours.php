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
    // Log de début de script
    error_log("===========================");
    error_log("Recalculate Tours script started");
    error_log("===========================");

    // Lire les données brutes envoyées par le client
    $rawData = file_get_contents('php://input');
    error_log("Données brutes reçues : " . $rawData);

    // Décoder les données JSON
    $data = json_decode($rawData, true);
    error_log("Données POST décodées : " . print_r($data, true));

    // Vérifier si les incidents sont fournis et valides
    if (!$data || !isset($data['incidents']) || !is_array($data['incidents'])) {
        error_log("Erreur : Aucune donnée d'incident valide reçue.");
        echo json_encode(['status' => 'error', 'message' => 'Aucune donnée d\'incident reçue.']);
        exit;
    }

    // Extraire les incidents et arrêts bloqués
    $incidents = $data['incidents'];
    $blockedStops = array_column($incidents, 'stop_id');
    error_log("Incidents reçus : " . print_r($incidents, true));
    error_log("Arrêts bloqués : " . print_r($blockedStops, true));

    // Connexion à la base de données
    error_log("Connexion à la base de données...");
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Connexion réussie.");

    // Récupérer les arrêts disponibles depuis la base de données
    error_log("Récupération des arrêts depuis la base de données...");
    $stmt = $pdo->query("SELECT id, libelle, latitude, longitude FROM arrets");
    $stops = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $stops[$row['id']] = $row;
    }
    error_log("Tous les arrêts : " . print_r($stops, true));

    // Filtrer les arrêts pour exclure ceux qui sont bloqués
    $validStops = array_filter($stops, function ($stop) use ($blockedStops) {
        return !in_array($stop['id'], $blockedStops);
    });
    error_log("Arrêts valides après filtrage : " . print_r($validStops, true));

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
    error_log("Calcul des distances entre les arrêts valides...");
    $graph = calculateDistances($validStops);
    error_log("Graphe des distances : " . print_r($graph, true));

    // Fonction pour répartir les arrêts entre les cyclistes
    function assignStopsToCyclists($cyclists, $stops, $graph) {
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
                        if (isset($currentStop['id'], $stop['id']) && isset($graph[$currentStop['id']][$stop['id']])) {
                            $distance = $graph[$currentStop['id']][$stop['id']];
                            if ($distance < $minDistance) {
                                $minDistance = $distance;
                                $nextStop = $key;
                            }
                        } else {
                            error_log("Distance introuvable ou clé manquante : currentStop=" . json_encode($currentStop) . ", stop=" . json_encode($stop));
                        }
                    }
    
                    if ($nextStop !== null && isset($stops[$nextStop])) {
                        $assignments[$cyclist][] = $stops[$nextStop];
                        $currentStop = $stops[$nextStop];
                        unset($stops[$nextStop]);
                    } else {
                        break;
                    }
                }
            }
        }
    
        return $assignments;
    }
    

    // Exemple de cyclistes (ces données peuvent être dynamiques selon votre application)
    $cyclists = ['Cycliste1', 'Cycliste2'];
    error_log("Cyclistes disponibles : " . print_r($cyclists, true));

    // Calcul des assignations
    error_log("Calcul des tournées...");
    $tours = assignStopsToCyclists($cyclists, $validStops, $graph);
    error_log("Tournées calculées : " . print_r($tours, true));

    // Retourner les tournées en JSON
    echo json_encode(['status' => 'success', 'newTours' => $tours]);
    error_log("Réponse JSON envoyée avec succès.");

} catch (Exception $e) {
    // Gérer les erreurs et retourner un message JSON
    error_log("Erreur : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
