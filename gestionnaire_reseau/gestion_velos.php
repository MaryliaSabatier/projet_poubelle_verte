<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) { // 4 = ID du rôle gestionnaire de réseau
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";  // Ou votre nom d'utilisateur
$password_db = "";    // Ou votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Traitement du formulaire d'ajout de vélo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero = $_POST['numero'];
    $etat = $_POST['etat'];
    $autonomie_km = $_POST['autonomie_km'];
    $date_derniere_revision = $_POST['date_derniere_revision'];

    $stmt = $conn->prepare("INSERT INTO velos (numero, etat, autonomie_km, date_derniere_revision) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $numero, $etat, $autonomie_km, $date_derniere_revision);

    if ($stmt->execute()) {
        header('Location: gestion_velos.php'); // Redirection après ajout réussi
        exit();
    } else {
        echo "Erreur lors de l'ajout du vélo.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un vélo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Ajouter un vélo</h1>
        <a href="gestion_velos.php" class="btn btn-secondary mb-3">Retour à la gestion des vélos</a>

        <form method="post" action="ajouter_velo.php">
            <div class="mb-3">
                <label for="numero" class="form-label">Numéro</label>
                <input type="text" class="form-control" id="numero" name="numero" required>
            </div>
            <div class="mb-3">
                <label for="etat" class="form-label">État</label>
                <input type="text" class="form-control" id="etat" name="etat" required>
            </div>
            <div class="mb-3">
                <label for="autonomie_km" class="form-label">Autonomie (km)</label>
                <input type="number" class="form-control" id="autonomie_km" name="autonomie_km" required>
            </div>
            <div class="mb-3">
                <label for="date_derniere_revision" class="form-label">Date dernière révision</label>
                <input type="date" class="form-control" id="date_derniere_revision" name="date_derniere_revision" required>
            </div>
            <button type="submit" class="btn btn-success">Ajouter</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
