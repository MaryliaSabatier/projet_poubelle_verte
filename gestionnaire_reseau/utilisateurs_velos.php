<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 4 && $_SESSION['role_id'] != 1)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username_db = "root";
$password_db = "root";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des cyclistes, vélos et tournées
$sql = "SELECT u.id, u.nom, u.prenom, u.velo_id, t.id AS tournee_id, t.date AS tournee_date
        FROM utilisateurs u
        LEFT JOIN tournees t ON u.id = t.cycliste_id
        WHERE u.role_id = 3"; // 3 = cycliste

$result = $conn->query($sql);

$cyclistes = [];
while ($row = $result->fetch_assoc()) {
    if (!isset($cyclistes[$row['id']])) {
        $cyclistes[$row['id']] = [
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'velo_id' => $row['velo_id'],
            'tournees' => []
        ];
    }
    if (!is_null($row['tournee_date'])) {
        $cyclistes[$row['id']]['tournees'][] = [
            'id' => $row['tournee_id'],
            'date' => $row['tournee_date']
        ];
    }
}

// Récupération des cyclistes disponibles, des vélos opérationnels et des tournées disponibles
$sqlCyclistesDisponibles = "SELECT id, nom, prenom FROM utilisateurs WHERE role_id = 3";
$resultCyclistesDisponibles = $conn->query($sqlCyclistesDisponibles);

$sqlVelosOperationnels = "SELECT id, numero FROM velos WHERE etat = 'operationnel'";
$resultVelosOperationnels = $conn->query($sqlVelosOperationnels);

$sqlTourneesDisponibles = "SELECT id, date FROM tournees WHERE cycliste_id IS NULL";
$resultTourneesDisponibles = $conn->query($sqlTourneesDisponibles);

// Traitement des modifications
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['attribuer_velo'])) {
        $cyclisteId = $_POST['cycliste_id'];
        $veloId = $_POST['velo_id'];

        // Mise à jour de l'attribution du vélo
        $stmt = $conn->prepare("UPDATE utilisateurs SET velo_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $veloId, $cyclisteId);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Vélo attribué avec succès !</div>";
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de l'attribution du vélo.</div>";
        }
    }

    if (isset($_POST['attribuer_tournee'])) {
        $cyclisteId = $_POST['cycliste_id'];
        $tourneeDate = $_POST['tournee_date'];

        // Vérifier si la tournée pour la date choisie existe déjà
        $stmtCheck = $conn->prepare("SELECT id FROM tournees WHERE date = ?");
        $stmtCheck->bind_param("s", $tourneeDate);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $tourneeId = $resultCheck->fetch_assoc()['id'] ?? null; // Utilisation de l'opérateur de fusion null

        if ($tourneeId) {
            // Mise à jour de l'attribution de la tournée
            $stmt = $conn->prepare("UPDATE tournees SET cycliste_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $cyclisteId, $tourneeId);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Tournée attribuée avec succès !</div>";
            } else {
                echo "<div class='alert alert-danger'>Erreur lors de l'attribution de la tournée.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Aucune tournée disponible pour la date choisie.</div>";
        }
    }

    if (isset($_POST['supprimer_tournee'])) {
        $tourneeId = $_POST['tournee_id'];

        // Suppression de la tournée
        $stmt = $conn->prepare("DELETE FROM tournees WHERE id = ?");
        $stmt->bind_param("i", $tourneeId);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Tournée supprimée avec succès !</div>";
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de la suppression de la tournée.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs, vélos et tournées</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestion des utilisateurs, vélos et tournées</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary mb-3">Retour au dashboard</a>

        <h3>Liste des cyclistes</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Vélo attribué</th>
                    <th>Tournée attribuée</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cyclistes as $id => $cycliste): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cycliste['nom']); ?></td>
                        <td><?php echo htmlspecialchars($cycliste['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($cycliste['velo_id']); ?></td>
                        <td>
                            <?php if (!empty($cycliste['tournees'])): ?>
                                <?php foreach ($cycliste['tournees'] as $tournee): ?>
                                    <div>
                                        <?php echo htmlspecialchars($tournee['date']); ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="tournee_id" value="<?php echo htmlspecialchars($tournee['id']); ?>">
                                            <button type="submit" name="supprimer_tournee" class="btn btn-danger btn-sm">Supprimer</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Aucune tournée attribuée</p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#attribuerVeloModal<?php echo $id; ?>">
                                Attribuer vélo
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#attribuerTourneeModal<?php echo $id; ?>">
                                Attribuer tournée
                            </button>

                            <!-- Modal pour attribuer un vélo -->
                            <div class="modal fade" id="attribuerVeloModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="attribuerVeloModalLabel<?php echo $id; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="attribuerVeloModalLabel<?php echo $id; ?>">Attribuer un vélo à <?php echo htmlspecialchars($cycliste['prenom']) . ' ' . htmlspecialchars($cycliste['nom']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="cycliste_id" value="<?php echo htmlspecialchars($id); ?>">
                                                <div class="mb-3">
                                                    <label for="velo_id" class="form-label">Vélo</label>
                                                    <select class="form-select" id="velo_id" name="velo_id" required>
                                                        <?php while ($velo = $resultVelosOperationnels->fetch_assoc()): ?>
                                                            <option value="<?php echo htmlspecialchars($velo['id']); ?>"><?php echo htmlspecialchars($velo['numero']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="attribuer_velo">Attribuer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal pour attribuer une tournée -->
                            <div class="modal fade" id="attribuerTourneeModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="attribuerTourneeModalLabel<?php echo $id; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="attribuerTourneeModalLabel<?php echo $id; ?>">Attribuer une tournée à <?php echo htmlspecialchars($cycliste['prenom']) . ' ' . htmlspecialchars($cycliste['nom']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="cycliste_id" value="<?php echo htmlspecialchars($id); ?>">
                                                <div class="mb-3">
                                                    <label for="tournee_date" class="form-label">Date de la tournée</label>
                                                    <input type="date" class="form-control" id="tournee_date" name="tournee_date" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary" name="attribuer_tournee">Attribuer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
