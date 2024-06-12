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
$password_db = "";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
    exit();
}

// Récupération de l'ID de la tournée depuis la requête AJAX
if (isset($_POST['tournee_id'])) {
    $tourneeId = $_POST['tournee_id'];

    // Vérification si le cycliste est bien assigné à la tournée et si elle est en cours
    $stmtCheck = $conn->prepare("SELECT id FROM tournees WHERE id = ? AND cycliste_id = ? AND etat = 'en_cours'");
    $stmtCheck->bind_param("ii", $tourneeId, $_SESSION['user_id']);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows == 1) {
        // Mise à jour de la tournée
        $stmt = $conn->prepare("UPDATE tournees SET etat = 'terminee', heure_fin = NOW() WHERE id = ?");
        $stmt->bind_param("i", $tourneeId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Tournée terminée avec succès.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la fin de la tournée.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas assigné à cette tournée ou elle n\'est pas en cours.']);
    }

    $stmtCheck->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID de tournée manquant.']);
}

$conn->close();
