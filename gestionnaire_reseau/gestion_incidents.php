<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root"; // Votre utilisateur MySQL
$password_db = "";     // Votre mot de passe MySQL
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'])) {
    $incidentId = intval($_POST['incident_id']);
    $resolution = isset($_POST['resolution']) && !empty(trim($_POST['resolution']))
        ? $conn->real_escape_string(trim($_POST['resolution']))
        : "résolu"; // Par défaut, la résolution sera "résolu"

    $stmt = $conn->prepare("UPDATE incidents SET resolution = ?, resolved_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $resolution, $incidentId);

    if ($stmt->execute()) {
        header('Location: gestion_incidents.php'); // Rafraîchir la page après mise à jour
        exit();
    } else {
        $error = "Erreur lors de la mise à jour de la résolution : " . $stmt->error;
    }
}



// Suppression d'un incident si demandé
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $incidentId = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM incidents WHERE id = ?");
    $stmt->bind_param("i", $incidentId);
    if ($stmt->execute()) {
        header('Location: gestion_incidents.php'); // Rafraîchir la page après suppression
        exit();
    } else {
        $error = "Erreur lors de la suppression : " . $stmt->error;
    }
}

// Récupération des incidents enregistrés
$sqlIncidents = "
    SELECT 
        i.id, 
        i.tournee_id, 
        i.type_incident, 
        i.date, 
        i.heure, 
        i.description, 
        i.resolution, 
        i.resolved_at, 
        t.date AS tournee_date, 
        t.heure_debut, 
        t.heure_fin,
        a.libelle AS arret_libelle, -- Nom de l'arrêt associé
        r.libelle AS rue_libelle   -- Nom de la rue associée
    FROM incidents i
    LEFT JOIN tournees t ON i.tournee_id = t.id
    LEFT JOIN arrets a ON i.arret_id = a.id
    LEFT JOIN rues r ON i.rue_id = r.id
    ORDER BY i.date DESC, i.heure DESC
";
$resultIncidents = $conn->query($sqlIncidents);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des incidents</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Gestion des incidents</h2>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary mb-3">Retour au tableau de bord</a>
        <a href="ajouter_incident.php" class="btn btn-primary mb-3">Ajouter un incident</a>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tournée</th>
                    <th>Type d'incident</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Arrêt impliqué</th>
                    <th>Rue impliquée</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date de clôture</th>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultIncidents->num_rows > 0) { ?>
                    <?php while ($incident = $resultIncidents->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $incident['id']; ?></td>
                            <td><?php echo $incident['tournee_id'] ?: 'Non assignée'; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $incident['type_incident'])); ?></td>
                            <td><?php echo $incident['date']; ?></td>
                            <td><?php echo $incident['heure']; ?></td>
                            <td><?php echo $incident['arret_libelle'] ?: 'Non spécifié'; ?></td>
                            <td><?php echo $incident['rue_libelle'] ?: 'Non spécifiée'; ?></td>
                            <td><?php echo htmlspecialchars($incident['description']); ?></td>
                            <td>
                                <?php if ($incident['resolved_at']) { ?>
                                    <span class="badge bg-success">Clôturé</span>
                                <?php } else { ?>
                                    <form method="post" action="gestion_incidents.php">
                                        <input type="hidden" name="incident_id" value="<?php echo $incident['id']; ?>">
                                        <input type="text" name="resolution" placeholder="Ajouter une résolution (par défaut : 'résolu')" class="form-control mb-2">
                                        <button type="submit" class="btn btn-success btn-sm">Clôturer</button>
                                    </form>

                                <?php } ?>
                            </td>
                            <td><?php echo $incident['resolved_at'] ?: 'Non clôturé'; ?></td>
                            <td>
                                <a href="modifier_incident.php?id=<?php echo $incident['id']; ?>"
                                    class="btn btn-warning btn-sm <?php echo $incident['resolved_at'] ? 'disabled' : ''; ?>">
                                    Modifier
                                </a>
                                <a href="gestion_incidents.php?action=supprimer&id=<?php echo $incident['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');">
                                    Supprimer
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="11" class="text-center">Aucun incident enregistré.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>