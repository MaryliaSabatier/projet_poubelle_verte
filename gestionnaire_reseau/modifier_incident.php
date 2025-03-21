<?php
session_start();

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";  // Votre utilisateur
$password_db = "root";      // Votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Initialisation des variables
$error = "";

// Vérification que l'ID de l'incident est passé en paramètre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID d'incident manquant.");
}

$incidentId = intval($_GET['id']);

// Récupération des données de l'incident à modifier
$sqlIncident = "SELECT * FROM incidents WHERE id = ?";
$stmtIncident = $conn->prepare($sqlIncident);
$stmtIncident->bind_param("i", $incidentId);
$stmtIncident->execute();
$resultIncident = $stmtIncident->get_result();

if ($resultIncident->num_rows === 0) {
    die("Incident introuvable.");
}

$incident = $resultIncident->fetch_assoc();

// Récupération des tournées pour le formulaire
$sqlTournees = "SELECT id FROM tournees";
$resultTournees = $conn->query($sqlTournees);

// Récupération des rues pour le formulaire
$sqlRues = "SELECT id, libelle FROM rues";
$resultRues = $conn->query($sqlRues);

// Récupération des arrêts pour le formulaire
$sqlArrets = "SELECT id, libelle FROM arrets";
$resultArrets = $conn->query($sqlArrets);

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des valeurs modifiées depuis le formulaire
    $tourneeId = $_POST['tournee_id'];
    $typeIncident = htmlspecialchars($_POST['type_incident']);
    $dateIncident = $_POST['date_incident'];
    $heureIncident = $_POST['heure_incident'];
    $description = htmlspecialchars($_POST['description']);
    $arretId = !empty($_POST['arret_id']) ? intval($_POST['arret_id']) : null;
    $rueId = !empty($_POST['rue_id']) ? intval($_POST['rue_id']) : null;

    // Validation des champs obligatoires
    if (empty($tourneeId) || empty($typeIncident) || empty($dateIncident) || empty($heureIncident)) {
        $error = "Tous les champs marqués sont obligatoires.";
    } elseif (!empty($arretId) && !empty($rueId)) {
        $error = "Vous ne pouvez pas sélectionner à la fois un arrêt et une rue.";
    }

    if (empty($error)) {
        // Requête pour mettre à jour l'incident
        $sqlUpdate = "
            UPDATE incidents 
            SET 
                tournee_id = ?, 
                type_incident = ?, 
                date = ?, 
                heure = ?, 
                description = ?, 
                arret_id = ?, 
                rue_id = ? 
            WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param(
            "issssiii",
            $tourneeId,
            $typeIncident,
            $dateIncident,
            $heureIncident,
            $description,
            $arretId,
            $rueId,
            $incidentId
        );

        if ($stmtUpdate->execute()) {
            header('Location: gestion_incidents.php'); // Redirection après succès
            exit();
        } else {
            $error = "Erreur lors de la mise à jour : " . $stmtUpdate->error;
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Modifier un incident</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Modifier un incident</h2>

        <?php if (!empty($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="tournee_id" class="form-label">Tournée</label>
                <select class="form-select" id="tournee_id" name="tournee_id" required>
                    <option value="">Sélectionnez une tournée</option>
                    <?php while ($tournee = $resultTournees->fetch_assoc()) { ?>
                        <option value="<?php echo $tournee['id']; ?>" <?php echo ($tournee['id'] == $incident['tournee_id']) ? 'selected' : ''; ?>>
                            <?php echo $tournee['id']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="type_incident" class="form-label">Type d'incident</label>
                <select class="form-select" id="type_incident" name="type_incident" required>
                    <option value="">Sélectionnez un type</option>
                    <option value="accident_corporel" <?php echo ($incident['type_incident'] == 'accident_corporel') ? 'selected' : ''; ?>>Accident corporel</option>
                    <option value="arret_supprime" <?php echo ($incident['type_incident'] == 'arret_supprime') ? 'selected' : ''; ?>>Arrêt supprimé</option>
                    <option value="casse_velo" <?php echo ($incident['type_incident'] == 'casse_velo') ? 'selected' : ''; ?>>Casse vélo</option>
                    <option value="rue_bloquee" <?php echo ($incident['type_incident'] == 'rue_bloquee') ? 'selected' : ''; ?>>Rue bloquée</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="arret_id" class="form-label">Arrêt impliqué (optionnel)</label>
                <select class="form-select" id="arret_id" name="arret_id">
                    <option value="">Sélectionnez un arrêt</option>
                    <?php while ($arret = $resultArrets->fetch_assoc()) { ?>
                        <option value="<?php echo $arret['id']; ?>" <?php echo ($incident['arret_id'] == $arret['id']) ? 'selected' : ''; ?>>
                            <?php echo $arret['libelle']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="rue_id" class="form-label">Rue impliquée (optionnel)</label>
                <select class="form-select" id="rue_id" name="rue_id">
                    <option value="">Sélectionnez une rue</option>
                    <?php while ($rue = $resultRues->fetch_assoc()) { ?>
                        <option value="<?php echo $rue['id']; ?>" <?php echo ($incident['rue_id'] == $rue['id']) ? 'selected' : ''; ?>>
                            <?php echo $rue['libelle']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date_incident" class="form-label">Date de l'incident</label>
                <input type="date" class="form-control" id="date_incident" name="date_incident" value="<?php echo $incident['date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="heure_incident" class="form-label">Heure de l'incident</label>
                <input type="time" class="form-control" id="heure_incident" name="heure_incident" value="<?php echo $incident['heure']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo $incident['description']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>
            <a href="gestion_incidents.php" class="btn btn-secondary">Annuler</a>
        </form>

    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const arretSelect = document.getElementById('arret_id');
    const rueSelect = document.getElementById('rue_id');

    arretSelect.addEventListener('change', function () {
        if (arretSelect.value) {
            rueSelect.disabled = true;
        } else {
            rueSelect.disabled = false;
        }
    });

    rueSelect.addEventListener('change', function () {
        if (rueSelect.value) {
            arretSelect.disabled = true;
        } else {
            arretSelect.disabled = false;
        }
    });
});

</script>

</html>