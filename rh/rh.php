<?php
session_start();

// Vérification de la connexion et du rôle RH
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {  // 2 = ID du rôle RH
    header('Location: ../login.php'); // Redirection vers la page de connexion générale
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
$sqlTotalUtilisateurs = "SELECT COUNT(*) as total FROM utilisateurs";
$sqlTotalCyclistes = "SELECT COUNT(*) as total FROM utilisateurs WHERE role_id = 3";
$sqlTotalMalade = "SELECT COUNT(*) as total FROM utilisateurs WHERE disponibilite = 'malade'"; // Utilisation de la colonne 'disponibilite'

$resultTotalUtilisateurs = $conn->query($sqlTotalUtilisateurs);
$resultTotalCyclistes = $conn->query($sqlTotalCyclistes);
$resultTotalMalade = $conn->query($sqlTotalMalade);

$totalUtilisateurs = $resultTotalUtilisateurs->fetch_assoc()['total'];
$totalCyclistes = $resultTotalCyclistes->fetch_assoc()['total'];
$totalMalade = $resultTotalMalade->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Interface RH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Interface RH</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="rh.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_utilisateurs_rh.php">Gestion des utilisateurs</a>
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
                                <h5 class="card-title">Total Utilisateurs</h5>
                                <p class="card-text"><?php echo $totalUtilisateurs; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Cyclistes</h5>
                                <p class="card-text"><?php echo $totalCyclistes; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Employés Malades</h5>
                                <p class="card-text"><?php echo $totalMalade; ?></p>
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
