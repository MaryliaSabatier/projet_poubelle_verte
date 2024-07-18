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
$sqlTotalMalade = "SELECT COUNT(*) as total FROM utilisateurs WHERE disponibilite = 'malade'";

$resultTotalUtilisateurs = $conn->query($sqlTotalUtilisateurs);
$resultTotalCyclistes = $conn->query($sqlTotalCyclistes);
$resultTotalMalade = $conn->query($sqlTotalMalade);

$totalUtilisateurs = $resultTotalUtilisateurs->fetch_assoc()['total'];
$totalCyclistes = $resultTotalCyclistes->fetch_assoc()['total'];
$totalMalade = $resultTotalMalade->fetch_assoc()['total'];

// Récupération des utilisateurs et de leurs rôles
$sqlUtilisateurs = "SELECT utilisateurs.id, utilisateurs.nom, utilisateurs.prenom, utilisateurs.email, roles.nom AS role, utilisateurs.disponibilite
                    FROM utilisateurs 
                    INNER JOIN roles ON utilisateurs.role_id = roles.id";
$resultUtilisateurs = $conn->query($sqlUtilisateurs);

// Traitement des actions (suppression d'utilisateur, mise à jour du statut "malade")
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $userId = $_GET['id'];

    if ($action == 'supprimer') {
        // Suppression de l'utilisateur
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header('Location: gestion_utilisateurs_rh.php'); // Redirection après suppression réussie
            exit();
        } else {
            echo "Erreur lors de la suppression de l'utilisateur.";
        }
    } elseif ($action == 'marquer_malade') {
        // Marquer l'utilisateur comme malade
        $stmt = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'malade' WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    } elseif ($action == 'marquer_disponible') {
        // Marquer l'utilisateur comme disponible
        $stmt = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'disponible' WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }

    // Redirection après la mise à jour (pour les deux actions)
    header('Location: gestion_utilisateurs_rh.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs (RH)</title>
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
        <h1 class="text-center mb-4">Interface RH</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="rh.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion_utilisateurs_rh.php">Gestion des utilisateurs</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <h3>Gestion des utilisateurs</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultUtilisateurs->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["nom"]; ?></td>
                                <td><?php echo $row["prenom"]; ?></td>
                                <td><?php echo $row["email"]; ?></td>
                                <td><?php echo $row["role"]; ?></td>
                                <td><?php echo ($row["disponibilite"] == 'malade') ? 'Malade' : 'Disponible'; ?></td>
                                <td>
                                    <a href="modifier_utilisateur_rh.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                    <a href="gestion_utilisateurs_rh.php?action=supprimer&id=<?php echo $row["id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    <?php if ($row["disponibilite"] == 'malade'): ?>
                                        <a href="gestion_utilisateurs_rh.php?action=marquer_disponible&id=<?php echo $row["id"]; ?>" class="btn btn-success btn-sm">Marquer disponible</a>
                                    <?php else: ?>
                                        <a href="gestion_utilisateurs_rh.php?action=marquer_malade&id=<?php echo $row["id"]; ?>" class="btn btn-warning btn-sm">Marquer malade</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <a href="ajouter_utilisateur_rh.php" class="btn btn-success">Ajouter un utilisateur</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
