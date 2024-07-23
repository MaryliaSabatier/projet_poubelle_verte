<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
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

// Requêtes pour récupérer les données du tableau de bord
$sqlTotalVelos = "SELECT COUNT(*) as total FROM velos";
$sqlVelosOperationnels = "SELECT COUNT(*) as total FROM velos WHERE etat = 'operationnel'";
$sqlVelosMaintenance = "SELECT COUNT(*) as total FROM velos WHERE etat = 'en_maintenance'";
$sqlTourneesPlanifiees = "SELECT COUNT(*) as total FROM tournees WHERE etat = 'planifiee'";
$sqlTourneesEnCours = "SELECT COUNT(*) as total FROM tournees WHERE etat = 'en_cours'";
$sqlTotalIncidents = "SELECT COUNT(*) as total FROM incidents";

// Exécution des requêtes
$resultTotalVelos = $conn->query($sqlTotalVelos);
$resultVelosOperationnels = $conn->query($sqlVelosOperationnels);
$resultVelosMaintenance = $conn->query($sqlVelosMaintenance);
$resultTourneesPlanifiees = $conn->query($sqlTourneesPlanifiees);
$resultTourneesEnCours = $conn->query($sqlTourneesEnCours);
$resultTotalIncidents = $conn->query($sqlTotalIncidents);

// Récupération des résultats
$totalVelos = $resultTotalVelos->fetch_assoc()['total'];
$velosOperationnels = $resultVelosOperationnels->fetch_assoc()['total'];
$velosMaintenance = $resultVelosMaintenance->fetch_assoc()['total'];
$tourneesPlanifiees = $resultTourneesPlanifiees->fetch_assoc()['total'];
$tourneesEnCours = $resultTourneesEnCours->fetch_assoc()['total'];
$totalIncidents = $resultTotalIncidents->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Interface Gestionnaire de Réseau</title>
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
        <h1 class="text-center mb-4">Interface Gestionnaire de Réseau</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>
        <a href="../map/index.html" class="btn btn-primary mb-3">Visualiser la carte</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="gestionnaire_reseau.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_velos.php">Gestion des vélos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_tournees.php">Gestion des tournées</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ajouter_incident.php">Ajouter un incident</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="visualisation_rues.php">Visualisation des rues</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="visualisation_points_passage.php">Visualisation des points de passage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="utilisateurs_velos.php">Attribution des vélos et tournées</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <h3>Tableau de bord</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Vélos</h5>
                                <p class="card-text"><?php echo $totalVelos; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Vélos Opérationnels</h5>
                                <p class="card-text"><?php echo $velosOperationnels; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Vélos en Maintenance</h5>
                                <p class="card-text"><?php echo $velosMaintenance; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Tournées Planifiées</h5>
                                <p class="card-text"><?php echo $tourneesPlanifiees; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Tournées en Cours</h5>
                                <p class="card-text"><?php echo $tourneesEnCours; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Total Incidents</h5>
                                <p class="card-text"><?php echo $totalIncidents; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
