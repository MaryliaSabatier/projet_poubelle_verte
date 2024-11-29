<?php
session_start();

// Vérification de la connexion et du rôle RH
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {  // 2 = ID du rôle RH
    header('Location: ../login.php'); // Redirection vers la page de connexion
    exit();
}

// Connexion à la base de données
require '../config.php';

$sqlTotalUtilisateurs = "SELECT COUNT(*) as total FROM utilisateurs";
$sqlTotalCyclistes = "SELECT COUNT(*) as total FROM utilisateurs WHERE role_id = 3";
$sqlTotalMalade = "SELECT COUNT(*) as total FROM utilisateurs WHERE disponibilite = 'malade'";
$sqlTotalConge = "SELECT COUNT(*) as total FROM utilisateurs WHERE disponibilite = 'congé'";

$resultTotalUtilisateurs = $conn->query($sqlTotalUtilisateurs);
$resultTotalCyclistes = $conn->query($sqlTotalCyclistes);
$resultTotalMalade = $conn->query($sqlTotalMalade);
$resultTotalConge = $conn->query($sqlTotalConge);

$totalUtilisateurs = $resultTotalUtilisateurs->fetch_assoc()['total'];
$totalCyclistes = $resultTotalCyclistes->fetch_assoc()['total'];
$totalMalade = $resultTotalMalade->fetch_assoc()['total'];
$totalConge = $resultTotalConge->fetch_assoc()['total'];


$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord RH</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Interface RH - Tableau de bord</h1>
        <a href="../logout.php" class="btn btn-danger">Déconnexion</a>

        <div class="row mt-4">
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
                <h3>Tableau de bord</h3>
                <div class="row">
                    <!-- Total Utilisateurs -->
                    <div class="col-md-6">
                        <div class="card text-bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Total Utilisateurs</h5>
                                <p class="card-text display-6"><?php echo $totalUtilisateurs; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Cyclistes -->
                    <div class="col-md-6">
                        <div class="card text-bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Cyclistes</h5>
                                <p class="card-text display-6"><?php echo $totalCyclistes; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Employés Malades -->
                    <div class="col-md-6">
                        <div class="card text-bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Employés Malades</h5>
                                <p class="card-text display-6"><?php echo $totalMalade; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Employés en Congé -->
                    <div class="col-md-6">
                        <div class="card text-bg-info mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Employés en Congé</h5>
                                <p class="card-text display-6"><?php echo $totalConge; ?></p>
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
