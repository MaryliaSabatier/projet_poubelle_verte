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
$password_db = "";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des tournées
$sqlTournees = "SELECT t.*, u.nom AS nom_cycliste, v.numero AS numero_velo
                FROM tournees t
                LEFT JOIN utilisateurs u ON t.cycliste_id = u.id
                LEFT JOIN velos v ON t.velo_id = v.id";
$resultTournees = $conn->query($sqlTournees);

// Récupération des cyclistes disponibles et des vélos opérationnels
$sqlCyclistesDisponibles = "SELECT id, nom, prenom FROM utilisateurs WHERE role_id = 3 AND malade = 0";
$resultCyclistesDisponibles = $conn->query($sqlCyclistesDisponibles);
$sqlVelosOperationnels = "SELECT id, numero FROM velos WHERE etat = 'operationnel'";
$resultVelosOperationnels = $conn->query($sqlVelosOperationnels);

// Traitement de l'attribution d'un vélo à une tournée
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attribuer_velo'])) {
    $tourneeId = $_POST['tournee_id'];
    $veloId = $_POST['velo_id'];
    $cyclisteId = $_POST['cycliste_id'];

    // Mise à jour de la tournée
    $stmt = $conn->prepare("UPDATE tournees SET cycliste_id = ?, velo_id = ? WHERE id = ?");
    $stmt->bind_param("iii", $cyclisteId, $veloId, $tourneeId);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Vélo attribué avec succès à la tournée !</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de l'attribution du vélo.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des tournées</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestion des tournées</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="gestionnaire_reseau.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion_tournees.php">Gestion des tournées</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="utilisateurs_velos.php">Utilisateurs avec vélos et tournées</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <h3>Liste des tournées</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Cycliste</th>
                            <th>Vélo</th>
                            <th>Heure de début</th>
                            <th>Heure de fin</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultTournees->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["date"]; ?></td>
                                <td><?php echo $row["nom_cycliste"]; ?></td>
                                <td><?php echo $row["numero_velo"]; ?></td>
                                <td><?php echo $row["heure_debut"]; ?></td>
                                <td><?php echo $row["heure_fin"]; ?></td>
                                <td><?php echo $row["etat"]; ?></td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#attribuerVeloModal<?php echo $row["id"]; ?>">
                                        Attribuer vélo
                                    </button>

                                    <div class="modal fade" id="attribuerVeloModal<?php echo $row["id"]; ?>" tabindex="-1" aria-labelledby="attribuerVeloModalLabel<?php echo $row["id"]; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="attribuerVeloModalLabel<?php echo $row["id"]; ?>">Attribuer un vélo à la tournée <?php echo $row["id"]; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST">
                                                        <input type="hidden" name="tournee_id" value="<?php echo $row["id"]; ?>">
                                                        <div class="mb-3">
                                                            <label for="cycliste_id" class="form-label">Cycliste</label>
                                                            <select class="form-select" id="cycliste_id" name="cycliste_id">
                                                                <?php while ($cycliste = $resultCyclistesDisponibles->fetch_assoc()) { ?>
                                                                    <option value="<?php echo $cycliste['id']; ?>"><?php echo $cycliste['prenom'] . ' ' . $cycliste['nom']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="velo_id" class="form-label">Vélo</label>
                                                            <select class="form-select" id="velo_id" name="velo_id">
                                                                <?php while ($velo = $resultVelosOperationnels->fetch_assoc()) { ?>
                                                                    <option value="<?php echo $velo['id']; ?>"><?php echo $velo['numero']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary" name="attribuer_velo">Attribuer</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
