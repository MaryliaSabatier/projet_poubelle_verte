<?php
session_start();

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";
$password_db = "root";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupérer l'ID du vélo à supprimer
$veloId = $_GET['id'];

// Suppression du vélo
$sqlDelete = "DELETE FROM velos WHERE id = ?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("i", $veloId);

if ($stmtDelete->execute()) {
    header('Location: gestion_velos.php');
    exit();
} else {
    die("Erreur lors de la suppression du vélo : " . $stmtDelete->error);
}
?>
