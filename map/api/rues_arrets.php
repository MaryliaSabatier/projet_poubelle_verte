<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier de connexion à la base de données
include 'C:\xampp\htdocs\projet_poubelle_verte\config.php'; // Vérifiez que le chemin vers config.php est correct

// Définir le type de contenu en JSON
header('Content-Type: application/json');

try {
    // Préparer la requête SQL
    $query = "
        SELECT 
            r.libelle AS rue, 
            a.id AS arret_id, 
            a.latitude, 
            a.longitude, 
            a.libelle AS arret_libelle,
            ra.ordre AS ordre
        FROM arret_rues ra
        JOIN arrets a ON a.id = ra.arret_id
        JOIN rues r ON r.id = ra.rue_id
        ORDER BY r.libelle, ra.ordre
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Récupérer les résultats
    $result = $stmt->get_result();
    $ruesArrets = [];
    while ($row = $result->fetch_assoc()) {
        $ruesArrets[] = [
            'rue' => ['libelle' => $row['rue']],
            'arret' => [
                'id' => $row['arret_id'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'libelle' => $row['arret_libelle'],
                'ordre' => $row['ordre']
            ]
        ];
    }

    // Retourner les données au format JSON
    echo json_encode([
        'status' => 'success',
        'data' => $ruesArrets
    ]);
} catch (Exception $e) {
    // Gestion des erreurs
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    http_response_code(500); // Retourner un code HTTP 500 en cas d'erreur
    die();
}
?>
