<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) { // 4 = ID du rôle gestionnaire de réseau
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root"; // Ou votre nom d'utilisateur
$password_db = "";     // Ou votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

// Récupération des tournées pour le formulaire
$sqlTournees = "SELECT id FROM tournees";
$resultTournees = $conn->query($sqlTournees);

// Initialisation des variables pour le formulaire
$tourneeId = $typeIncident = $dateIncident = $heureIncident = $description = $error = "";

// Traitement du formulaire d'ajout d'incident
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et validation des données du formulaire
    $tourneeId = intval($_POST['tournee_id']);
    $typeIncident = htmlspecialchars($_POST['type_incident']);
    $dateIncident = $_POST['date_incident'];
    $heureIncident = $_POST['heure_incident'];
    $description = htmlspecialchars($_POST['description']);

    // Validation des champs obligatoires
    if (empty($tourneeId) || empty($typeIncident) || empty($dateIncident) || empty($heureIncident)) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateIncident)) {
        $error = "Le format de la date est invalide.";
    } elseif (!preg_match('/^\d{2}:\d{2}$/', $heureIncident)) {
        $error = "Le format de l'heure est invalide.";
    }

    // Si aucune erreur, ajout de l'incident
    if (empty($error)) {
        // Préparation de la requête d'insertion
        $stmt = $conn->prepare("INSERT INTO incidents (tournee_id, type_incident, date, heure, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $tourneeId, $typeIncident, $dateIncident, $heureIncident, $description);

        if ($stmt->execute()) {
            // Redirection après succès
            header('Location: gestion_incidents.php');
            exit();
        } else {
            $error = "Erreur lors de l'ajout de l'incident : " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un incident</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter un incident</h2>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary mb-3">Retour au dashboard</a>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="tournee_id" class="form-label">Tournée</label>
                <select class="form-select" id="tournee_id" name="tournee_id" required>
                    <option value="">Sélectionnez une tournée</option>
                    <?php while ($tournee = $resultTournees->fetch_assoc()) { ?>
                        <option value="<?php echo $tournee['id']; ?>" <?php echo ($tournee['id'] == $tourneeId) ? 'selected' : ''; ?>>
                            Tournée <?php echo $tournee['id']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="type_incident" class="form-label">Type d'incident</label>
                <select class="form-select" id="type_incident" name="type_incident" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="accident_corporel" <?php echo ($typeIncident == 'accident_corporel') ? 'selected' : ''; ?>>Accident corporel</option>
                    <option value="arret_supprime" <?php echo ($typeIncident == 'arret_supprime') ? 'selected' : ''; ?>>Arrêt supprimé</option>
                    <option value="casse_velo" <?php echo ($typeIncident == 'casse_velo') ? 'selected' : ''; ?>>Casse vélo</option>
                    <option value="rue_bloquee" <?php echo ($typeIncident == 'rue_bloquee') ? 'selected' : ''; ?>>Rue bloquée</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="date_incident" class="form-label">Date de l'incident</label>
                <input type="date" class="form-control" id="date_incident" name="date_incident" value="<?php echo $dateIncident; ?>" required>
            </div>
            <div class="mb-3">
                <label for="heure_incident" class="form-label">Heure de l'incident</label>
                <input type="time" class="form-control" id="heure_incident" name="heure_incident" value="<?php echo $heureIncident; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $description; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
