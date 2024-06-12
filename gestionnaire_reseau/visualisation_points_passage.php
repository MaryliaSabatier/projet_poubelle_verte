<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
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

// Récupération des données pour la liste des points de passage
$sqlPointsPassage = "SELECT a.id, r.nom AS rue, a.type_dechets, ea.date_traitement, ea.heure_traitement
                    FROM arrets a
                    INNER JOIN rues r ON a.rue_id = r.id
                    LEFT JOIN etat_arrets ea ON a.id = ea.arret_id";
$resultPointsPassage = $conn->query($sqlPointsPassage);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Visualisation des points de passage</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Visualisation des points de passage</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="gestionnaire_reseau.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="visualisation_points_passage.php">Visualisation des points de passage</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <h3>Liste des points de passage</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Rue</th>
                            <th>Type de déchets</th>
                            <th>Date de traitement</th>
                            <th>Heure de traitement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultPointsPassage->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["rue"]; ?></td>
                                <td><?php echo $row["type_dechets"]; ?></td>
                                <td><?php echo $row["date_traitement"] ?? "Non trait\u00e9"; ?></td>
                                <td><?php echo $row["heure_traitement"] ?? ""; ?></td>
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
