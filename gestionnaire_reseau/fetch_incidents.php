<?php
header('Content-Type: application/json; charset=utf-8');

// Connexion à la base de données
$dsn = "mysql:host=localhost;dbname=poubelle_verte;charset=utf8";
$username = "root";
$password = "";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur de connexion à la base de données : " . $e->getMessage()]);
    exit;
}

// Récupérer les arrêts et rues bloqués
$sqlBlockedStops = "
    SELECT 
        a.libelle AS arret_libelle,
        r.libelle AS rue_libelle
    FROM incidents i
    LEFT JOIN arrets a ON i.arret_id = a.id
    LEFT JOIN rues r ON i.rue_id = r.id
    WHERE i.resolved_at IS NULL
";

try {
    $blockedStops = $pdo->query($sqlBlockedStops)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur lors de la récupération des arrêts/rues bloqués : " . $e->getMessage()]);
    exit;
}

// Vérifier si des arrêts/rues sont bloqués
if (empty($blockedStops)) {
    echo json_encode(["blockedStops" => [], "message" => "Aucun arrêt ou rue bloqué actuellement."]);
} else {
    echo json_encode(["blockedStops" => $blockedStops]);
}
