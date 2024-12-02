<?php
session_start();

// Vérification de la connexion et du rôle RH
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {  // 2 = ID du rôle RH
    header('Location: ../login.php'); // Redirection vers la page de connexion
    exit();
}

// Inclusion de la configuration pour la base de données
require '../config.php';

// Requêtes pour récupérer les données du tableau de bord
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

// Récupération des utilisateurs et de leurs rôles
$sqlUtilisateurs = "SELECT utilisateurs.id, utilisateurs.nom, utilisateurs.prenom, utilisateurs.email, roles.nom AS role, utilisateurs.disponibilite
                    FROM utilisateurs 
                    INNER JOIN roles ON utilisateurs.role_id = roles.id";
$resultUtilisateurs = $conn->query($sqlUtilisateurs);

// Traitement des actions (suppression, modification, mise à jour des statuts)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $userId = intval($_GET['id']); // Sécurisation de l'ID

    if ($action == 'supprimer') {
        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header('Location: gestion_utilisateurs_rh.php');
            exit();
        }
    } elseif ($action == 'marquer_malade') {
        $stmt = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'malade' WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header('Location: gestion_utilisateurs_rh.php');
            exit();
        }
    } elseif ($action == 'marquer_conge') {
        $stmt = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'congé' WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header('Location: gestion_utilisateurs_rh.php');
            exit();
        }
    } elseif ($action == 'marquer_disponible') {
        $stmt = $conn->prepare("UPDATE utilisateurs SET disponibilite = NULL WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header('Location: gestion_utilisateurs_rh.php');
            exit();
        }
    }
}


// Traitement de la modification d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_utilisateur'])) {
    $id = intval($_POST['id']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $roleId = intval($_POST['role_id']);
    $disponibilite = $_POST['disponibilite'];

    $stmt = $conn->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role_id = ?, disponibilite = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $nom, $prenom, $email, $roleId, $disponibilite, $id);

    if ($stmt->execute()) {
        header('Location: gestion_utilisateurs_rh.php');
        exit();
    } else {
        echo "Erreur lors de la modification : " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs (RH)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Interface RH - Gestion des utilisateurs</h1>
        <a href="../logout.php" class="btn btn-danger float-end">Déconnexion</a>

        <div class="row mt-5">
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
            <a href="ajouter_utilisateur_rh.php" class="btn btn-danger float-end">Ajouter un utilisateur</a>

            <div class="col-md-9">
                <h3>Gestion des utilisateurs</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Disponibilité</th>
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
                                <td><?php echo ucfirst($row["disponibilite"]); ?></td>
                                <td>
                                    <a href="gestion_utilisateurs_rh.php?action=supprimer&id=<?php echo $row["id"]; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    <a href="modifier_utilisateur_rh.php?id=<?php echo $row["id"]; ?>" 
                                       class="btn btn-primary btn-sm">Modifier</a>
                                    <?php if ($row["disponibilite"] == 'malade'): ?>
                                        <a href="gestion_utilisateurs_rh.php?action=marquer_disponible&id=<?php echo $row["id"]; ?>" 
                                           class="btn btn-success btn-sm">Marquer disponible</a>
                                    <?php elseif ($row["disponibilite"] == 'congé'): ?>
                                        <a href="gestion_utilisateurs_rh.php?action=marquer_disponible&id=<?php echo $row["id"]; ?>" 
                                           class="btn btn-success btn-sm">Marquer disponible</a>
                                    <?php else: ?>
                                        <a href="gestion_utilisateurs_rh.php?action=marquer_malade&id=<?php echo $row["id"]; ?>" 
                                           class="btn btn-warning btn-sm">Marquer malade</a>
                                        <a href="gestion_utilisateurs_rh.php?action=marquer_conge&id=<?php echo $row["id"]; ?>" 
                                           class="btn btn-info btn-sm">Marquer en congé</a>
                                    <?php endif; ?>
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
