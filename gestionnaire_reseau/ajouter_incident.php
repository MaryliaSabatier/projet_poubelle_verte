<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) { // 4 = ID du rôle gestionnaire de réseau
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
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
    $tourneeId = !empty($_POST['tournee_id']) ? intval($_POST['tournee_id']) : null;
    $typeIncident = !empty($_POST['type_incident']) ? htmlspecialchars($_POST['type_incident']) : null;
    $dateIncident = !empty($_POST['date_incident']) ? $_POST['date_incident'] : null;
    $heureIncident = !empty($_POST['heure_incident']) ? $_POST['heure_incident'] : null;
    $description = !empty($_POST['description']) ? htmlspecialchars($_POST['description']) : null;
    $arretId = !empty($_POST['arret_id']) ? intval($_POST['arret_id']) : null;
    $rueId = !empty($_POST['rue_id']) ? intval($_POST['rue_id']) : null;

    // Validation des champs obligatoires
    if (empty($tourneeId) || empty($typeIncident) || empty($dateIncident) || empty($heureIncident)) {
        $error = "Tous les champs obligatoires doivent être remplis.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateIncident)) {
        $error = "Le format de la date est invalide. Utilisez AAAA-MM-JJ.";
    } elseif (!preg_match('/^\d{2}:\d{2}$/', $heureIncident)) {
        $error = "Le format de l'heure est invalide. Utilisez HH:MM.";
    } else {
        // Vérification si la tournée existe
        $checkTournee = $conn->prepare("SELECT id FROM tournees WHERE id = ?");
        $checkTournee->bind_param("i", $tourneeId);
        $checkTournee->execute();
        $result = $checkTournee->get_result();

        if ($result->num_rows == 0) {
            $error = "Erreur : La tournée spécifiée n'existe pas.";
        } else {
            // Préparation de l'insertion de l'incident
            $stmt = $conn->prepare("
                INSERT INTO incidents 
                (tournee_id, type_incident, date, heure, description, arret_id, rue_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issssii", $tourneeId, $typeIncident, $dateIncident, $heureIncident, $description, $arretId, $rueId);

            // Exécution de la requête
            if ($stmt->execute()) {
                // Redirection après succès
                header('Location: gestion_incidents.php');
                exit();
            } else {
                // Gestion des erreurs d'exécution SQL
                $error = "Erreur lors de l'ajout de l'incident : " . $stmt->error;
            }
        }
    }
}

// Récupérer les arrêts pour le formulaire
$sqlArrets = "SELECT id, libelle FROM arrets";
$resultArrets = $conn->query($sqlArrets);

// Récupérer les rues pour le formulaire
$sqlRues = "SELECT id, libelle FROM rues";
$resultRues = $conn->query($sqlRues);
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
                        <option value="<?php echo $tournee['id']; ?>">
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
                <label for="arret_id" class="form-label">Arrêt impacté (optionnel)</label>
                <select class="form-select" id="arret_id" name="arret_id">
                    <option value="">Sélectionnez un arrêt</option>
                    <?php while ($arret = $resultArrets->fetch_assoc()) { ?>
                        <option value="<?php echo $arret['id']; ?>"><?php echo $arret['libelle']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="rue_id" class="form-label">Rue impactée (optionnel)</label>
                <select class="form-select" id="rue_id" name="rue_id">
                    <option value="">Sélectionnez une rue</option>
                    <?php while ($rue = $resultRues->fetch_assoc()) { ?>
                        <option value="<?php echo $rue['id']; ?>"><?php echo $rue['libelle']; ?></option>
                    <?php } ?>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rueSelect = document.getElementById('rue_id');
        const arretSelect = document.getElementById('arret_id');

        // Quand une rue est sélectionnée
        rueSelect.addEventListener('change', function() {
            const rueId = this.value;

            if (rueId) {
                fetch(`get_related_data.php?type=arrets_by_rue&rue_id=${rueId}`)
                    .then(response => response.json())
                    .then(data => {
                        arretSelect.innerHTML = '<option value="">Sélectionnez un arrêt</option>';
                        if (data.error) {
                            console.error(data.error);
                        } else {
                            data.forEach(arret => {
                                arretSelect.innerHTML += `<option value="${arret.id}">${arret.libelle}</option>`;
                            });
                        }
                    })
                    .catch(error => console.error('Erreur :', error));
            } else {
                arretSelect.innerHTML = '<option value="">Sélectionnez un arrêt</option>';
            }
        });

        // Quand un arrêt est sélectionné
        arretSelect.addEventListener('change', function() {
            const arretId = this.value;

            if (arretId) {
                fetch(`get_related_data.php?type=rues_by_arret&arret_id=${arretId}`)
                    .then(response => response.json())
                    .then(data => {
                        rueSelect.innerHTML = '<option value="">Sélectionnez une rue</option>';
                        if (data.error) {
                            console.error(data.error);
                        } else {
                            data.forEach(rue => {
                                rueSelect.innerHTML += `<option value="${rue.id}">${rue.libelle}</option>`;
                            });
                        }
                    })
                    .catch(error => console.error('Erreur :', error));
            } else {
                rueSelect.innerHTML = '<option value="">Sélectionnez une rue</option>';
            }
        });
    });
</script>

</html>