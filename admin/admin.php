<?php
session_start();

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
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

// Récupération des données pour le tableau de bord
//$sqlVelos = "SELECT COUNT(*) as nombre_velos FROM velos";
//$resultVelos = $conn->query($sqlVelos);
//$velosData = $resultVelos->fetch_assoc();
//
//$sqlCyclistes = "SELECT COUNT(*) as nombre_cyclistes FROM utilisateurs WHERE role_id = 3"; // 3 = ID du rôle cycliste
//$resultCyclistes = $conn->query($sqlCyclistes);
//$cyclistesData = $resultCyclistes->fetch_assoc();
//
//$sqlTournees = "SELECT COUNT(*) as nombre_tournees FROM tournees";
//$resultTournees = $conn->query($sqlTournees);
//$tourneesData = $resultTournees->fetch_assoc();
//
//$sqlIncidents = "SELECT COUNT(*) as nombre_incidents FROM incidents WHERE resolu = 0"; // Incidents non résolus
//$resultIncidents = $conn->query($sqlIncidents);
//$incidentsData = $resultIncidents->fetch_assoc();

// ... (Autres requêtes pour récupérer les données nécessaires, par exemple, la liste des vélos, des cyclistes, des tournées en cours, etc.)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Interface Administrateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Style pour positionner le bouton de déconnexion */
        .logout-btn {
            position: absolute;
            top: 10px; /* Ajustez la distance du haut si nécessaire */
            right: 10px; /* Ajustez la distance de la droite si nécessaire */
        }    
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Interface Administrateur</h1>
        <a href="logout.php" class="btn btn-danger logout-btn">Déconnexion</a>
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
                        <a class="nav-link" href="gestion_velos.php">Gestion des vélos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_tournees.php">Gestion des tournées</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_incidents.php">Gestion des incidents</a>
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
                                <h5 class="card-title">Vélos</h5>
                                <p class="card-text"><?php echo $velosData['nombre_velos']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Cyclistes</h5>
                                <p class="card-text"><?php echo $cyclistesData['nombre_cyclistes']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Tournées</h5>
                                <p class="card-text"><?php echo $tourneesData['nombre_tournees']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Incidents</h5>
                                <p class="card-text"><?php echo $incidentsData['nombre_incidents']; ?></p>
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
