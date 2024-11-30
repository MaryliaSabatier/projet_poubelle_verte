<?php
// Inclure les fichiers nécessaires
include 'rues_et_arrets.php'; // Contient les données des arrêts et rues
include 'calcul_itineraires_logic.php'; // Contient la fonction calculerItineraires

/**
 * Fonction pour calculer les itinéraires en fonction des paramètres donnés.
 * @param array $stops - Liste des arrêts (avec coordonnées).
 * @param int $groupSize - Nombre d'arrêts par itinéraire.
 * @param int $numAgents - Nombre d'agents.
 * @param string $startStop - Point de départ.
 * @return array - Itinéraires calculés répartis entre les agents.
 */
function calculerItineraires($stops, $groupSize = 4, $numAgents = 1, $startStop = 'Porte d\'Ivry') {
    // Initialiser les variables
    $stopsToVisit = array_keys($stops); // Liste de tous les arrêts
    $visitedStops = [];
    $allRoutes = []; // Contiendra toutes les routes calculées

    // Retirer le point de départ de la liste des arrêts à visiter
    if (($key = array_search($startStop, $stopsToVisit)) !== false) {
        unset($stopsToVisit[$key]);
    }

    // Boucle pour générer les routes
    while (!empty($stopsToVisit)) {
        $route = [];
        $route[] = $startStop; // Ajouter le point de départ à la route
        $newStops = [];

        // Ajouter des arrêts à la route jusqu'à atteindre $groupSize
        foreach ($stopsToVisit as $stop) {
            if (count($newStops) < $groupSize) {
                $newStops[] = $stop;
                $visitedStops[] = $stop;
            } else {
                break;
            }
        }

        // Retirer les arrêts visités de la liste des arrêts à visiter
        foreach ($newStops as $stop) {
            if (($key = array_search($stop, $stopsToVisit)) !== false) {
                unset($stopsToVisit[$key]);
            }
        }

        // Ajouter les nouveaux arrêts à la route
        $route = array_merge($route, $newStops);

        // Retourner au point de départ
        $route[] = $startStop;

        // Ajouter la route à la liste des itinéraires
        $allRoutes[] = $route;
    }

    // Distribuer les itinéraires entre les agents
    $itineraries = [];
    for ($i = 0; $i < $numAgents; $i++) {
        $itineraries[$i] = [];
    }

    $routeIndex = 0;
    foreach ($allRoutes as $route) {
        $agentIndex = $routeIndex % $numAgents;
        $itineraries[$agentIndex][] = $route;
        $routeIndex++;
    }

    // Retourner les itinéraires calculés
    return $itineraries;
}

// Obtenir le nombre d'agents depuis les paramètres GET ou utiliser 1 par défaut
$numAgents = isset($_GET['numAgents']) ? intval($_GET['numAgents']) : 1;

// Vérification des paramètres pour éviter des erreurs
if ($numAgents < 1) {
    $numAgents = 1;
}

// Appeler la fonction pour calculer les itinéraires
$itineraries = calculerItineraires($stops, 4, $numAgents, 'Porte d\'Ivry');

// Retourner les données sous forme de réponse JSON
header('Content-Type: application/json');
echo json_encode([
    'stops' => $stops,
    'itineraries' => $itineraries
]);
?>
