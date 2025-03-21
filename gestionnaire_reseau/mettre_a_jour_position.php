<?php
session_start();

// Vérification de la connexion et du rôle de cycliste
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas les droits d\'accès.']);
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";
$password_db = "root";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
    exit();
}

// Récupération des données de position depuis la requête AJAX
if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Mise à jour de la position du cycliste dans la base de données
    // Vous devez avoir une table (par exemple, `positions_cyclistes`) pour stocker ces données
    $stmt = $conn->prepare("INSERT INTO positions_cyclistes (cycliste_id, latitude, longitude, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("idd", $_SESSION['user_id'], $latitude, $longitude);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Position mise à jour avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la position.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Données de position manquantes.']);
}

$conn->close();
