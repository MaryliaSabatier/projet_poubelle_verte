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
$password_db = "root";    // Ou votre mot de passe
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
        <a href="../map/index.html" class="btn btn-primary mb-3">Visualiser la carte 1</a>
        <a href="../map/maps.php" class="btn btn-primary mb-3">Visualiser la carte 2</a>
        <a href="../map/index2.html" class="btn btn-primary mb-3">Visualiser la carte 3</a>


        <div class="row mt-4">
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
                        <a class="nav-link" href="gestion_incidents.php">Gérer les incidents</a>
                    </li>

                </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <h3 class="mt-4 mb-3">Tableau de bord</h3>
                <div class="row g-4">
                    <!-- Carte : Total Vélos -->
                    <div class="col-md-6">
                        <div class="card text-bg-primary mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Vélos</h5>
                                <p class="card-text display-6"><?php echo $totalVelos; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte : Vélos Opérationnels -->
                    <div class="col-md-6">
                        <div class="card text-bg-success mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Vélos Opérationnels</h5>
                                <p class="card-text display-6"><?php echo $velosOperationnels; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte : Vélos en Maintenance -->
                    <div class="col-md-6">
                        <div class="card text-bg-warning mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Vélos en Maintenance</h5>
                                <p class="card-text display-6"><?php echo $velosMaintenance; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte : Tournées Planifiées -->
                    <div class="col-md-6">
                        <div class="card text-bg-info mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Tournées Planifiées</h5>
                                <p class="card-text display-6"><?php echo $tourneesPlanifiees; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte : Tournées en Cours -->
                    <div class="col-md-6">
                        <div class="card text-bg-secondary mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Tournées en Cours</h5>
                                <p class="card-text display-6"><?php echo $tourneesEnCours; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Carte : Total Incidents -->
                    <div class="col-md-6">
                        <div class="card text-bg-danger mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Incidents</h5>
                                <p class="card-text display-6"><?php echo $totalIncidents; ?></p>
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