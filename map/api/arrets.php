<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de connexion à la base de données
include 'C:\xampp\htdocs\projet_poubelle_verte\config.php'; // Assurez-vous que le chemin est correct

// Définir le type de contenu en JSON
header('Content-Type: application/json');

try {
    // Préparer la requête SQL pour récupérer les arrêts et leurs adjacents
    $query = "
        SELECT 
            a.id AS arret_id, 
            a.latitude, 
            a.longitude, 
            a.libelle,
            IFNULL(GROUP_CONCAT(DISTINCT ar2.arret_id), '') AS adjacents
        FROM arrets a
        LEFT JOIN arret_rues ar1 ON a.id = ar1.arret_id
        LEFT JOIN arret_rues ar2 ON ar1.rue_id = ar2.rue_id AND ar2.arret_id != a.id
        GROUP BY a.id
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupérer les résultats
    $result = $stmt->get_result();
    $arrets = [];
    while ($row = $result->fetch_assoc()) {
        // Convertir les adjacents en tableau
        $row['adjacents'] = $row['adjacents'] ? explode(',', $row['adjacents']) : [];
        $arrets[] = $row;
    }

    // Retourner les données au format JSON
    echo json_encode([
        'status' => 'success',
        'data' => $arrets
    ]);
} catch (Exception $e) {
    // Gestion des erreurs
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    http_response_code(500); // Retourne un code HTTP 500 en cas d'erreur
    die();
}
?>
