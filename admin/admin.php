<?php
session_start();

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username_db = "root";  // Remplacez par votre nom d'utilisateur
$password_db = "";      // Remplacez par votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Requêtes pour récupérer les statistiques utilisateurs
$sqlTotalUtilisateurs = "SELECT COUNT(*) as total FROM utilisateurs";
$sqlTotalCyclistes = "SELECT COUNT(*) as total FROM utilisateurs WHERE role_id = 3"; // Rôle cycliste
$sqlTotalMalade = "SELECT COUNT(*) as total FROM utilisateurs WHERE disponibilite = 'malade'";
$sqlTotalConge = "SELECT COUNT(*) as total FROM utilisateurs WHERE disponibilite = 'congé'";

// Requêtes pour les statistiques vélos, tournées, incidents
$sqlTotalVelos = "SELECT COUNT(*) as total FROM velos";
$sqlVelosOperationnels = "SELECT COUNT(*) as total FROM velos WHERE etat = 'operationnel'";
$sqlVelosMaintenance = "SELECT COUNT(*) as total FROM velos WHERE etat = 'en_maintenance'";
$sqlTourneesPlanifiees = "SELECT COUNT(*) as total FROM tournees WHERE etat = 'planifiee'";
$sqlTourneesEnCours = "SELECT COUNT(*) as total FROM tournees WHERE etat = 'en_cours'";
$sqlTotalIncidents = "SELECT COUNT(*) as total FROM incidents";

// Exécution des requêtes
$resultTotalUtilisateurs = $conn->query($sqlTotalUtilisateurs);
$resultTotalCyclistes = $conn->query($sqlTotalCyclistes);
$resultTotalMalade = $conn->query($sqlTotalMalade);
$resultTotalConge = $conn->query($sqlTotalConge);

$resultTotalVelos = $conn->query($sqlTotalVelos);
$resultVelosOperationnels = $conn->query($sqlVelosOperationnels);
$resultVelosMaintenance = $conn->query($sqlVelosMaintenance);
$resultTourneesPlanifiees = $conn->query($sqlTourneesPlanifiees);
$resultTourneesEnCours = $conn->query($sqlTourneesEnCours);
$resultTotalIncidents = $conn->query($sqlTotalIncidents);

// Récupération des résultats ou valeurs par défaut
$totalUtilisateurs = $resultTotalUtilisateurs->fetch_assoc()['total'] ?? 0;
$totalCyclistes = $resultTotalCyclistes->fetch_assoc()['total'] ?? 0;
$totalMalade = $resultTotalMalade->fetch_assoc()['total'] ?? 0;
$totalConge = $resultTotalConge->fetch_assoc()['total'] ?? 0;

$totalVelos = $resultTotalVelos->fetch_assoc()['total'] ?? 0;
$velosOperationnels = $resultVelosOperationnels->fetch_assoc()['total'] ?? 0;
$velosMaintenance = $resultVelosMaintenance->fetch_assoc()['total'] ?? 0;
$tourneesPlanifiees = $resultTourneesPlanifiees->fetch_assoc()['total'] ?? 0;
$tourneesEnCours = $resultTourneesEnCours->fetch_assoc()['total'] ?? 0;
$totalIncidents = $resultTotalIncidents->fetch_assoc()['total'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Administrateur</title>
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
        <h1 class="text-center mb-4">Interface Administrateur</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="admin.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_utilisateurs.php">Gestion des utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../gestionnaire_reseau/gestion_velos.php">Gestion des vélos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../gestionnaire_reseau/gestion_tournees.php">Gestion des tournées</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../gestionnaire_reseau/gestion_incidents.php">Gestion des incidents</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <h3>Tableau de bord</h3>
                <div class="row">
                    <!-- Total Utilisateurs -->
                    <div class="col-md-4">
                        <div class="card text-bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Utilisateurs</h5>
                                <p class="card-text display-6"><?php echo $totalUtilisateurs; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Cyclistes -->
                    <div class="col-md-4">
                        <div class="card text-bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Cyclistes</h5>
                                <p class="card-text display-6"><?php echo $totalCyclistes; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Employés Malades -->
                    <div class="col-md-4">
                        <div class="card text-bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Employés Malades</h5>
                                <p class="card-text display-6"><?php echo $totalMalade; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Employés en Congé -->
                    <div class="col-md-4">
                        <div class="card text-bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Employés en Congé</h5>
                                <p class="card-text display-6"><?php echo $totalConge; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Vélos -->
                    <div class="col-md-4">
                        <div class="card text-bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Vélos</h5>
                                <p class="card-text display-6"><?php echo $totalVelos; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Vélos Opérationnels -->
                    <div class="col-md-4">
                        <div class="card text-bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Vélos Opérationnels</h5>
                                <p class="card-text display-6"><?php echo $velosOperationnels; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Vélos en Maintenance -->
                    <div class="col-md-4">
                        <div class="card text-bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Vélos en Maintenance</h5>
                                <p class="card-text display-6"><?php echo $velosMaintenance; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tournées Planifiées -->
                    <div class="col-md-4">
                        <div class="card text-bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Tournées Planifiées</h5>
                                <p class="card-text display-6"><?php echo $tourneesPlanifiees; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tournées en Cours -->
                    <div class="col-md-4">
                        <div class="card text-bg-secondary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Tournées en Cours</h5>
                                <p class="card-text display-6"><?php echo $tourneesEnCours; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Incidents -->
                    <div class="col-md-4">
                        <div class="card text-bg-danger mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Incidents</h5>
                                <p class="card-text display-6"><?php echo $totalIncidents; ?></p>
                            </div>
                        </div>
                    </div>
                </div> <!-- Fin de la ligne -->
            </div> <!-- Fin de la colonne principale -->
        </div> <!-- Fin de la rangée principale -->
    </div> <!-- Fin du conteneur -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>