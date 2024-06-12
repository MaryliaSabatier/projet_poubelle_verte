<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) { // 4 = ID du rôle gestionnaire de réseau
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

// Traitement des actions (suppression de vélo)
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $veloId = $_GET['id'];

    // Suppression du vélo
    $stmt = $conn->prepare("DELETE FROM velos WHERE id = ?");
    $stmt->bind_param("i", $veloId);
    if ($stmt->execute()) {
        header('Location: gestion_velos.php'); // Redirection après suppression réussie
        exit();
    } else {
        echo "Erreur lors de la suppression du vélo.";
    }
}

// Récupération de la liste des vélos
$sqlVelos = "SELECT * FROM velos";
$resultVelos = $conn->query($sqlVelos);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des vélos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestion des vélos</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="gestionnaire_reseau.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion_velos.php">Gestion des vélos</a>
                    </li>
                    </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <h3>Liste des vélos</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Numéro</th>
                            <th>État</th>
                            <th>Autonomie (km)</th>
                            <th>Date dernière révision</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $resultVelos->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row["id"]; ?></td>
                                <td><?php echo $row["numero"]; ?></td>
                                <td><?php echo $row["etat"]; ?></td>
                                <td><?php echo $row["autonomie_km"]; ?></td>
                                <td><?php echo $row["date_derniere_revision"]; ?></td>
                                <td>
                                    <a href="modifier_velo.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                    <a href="gestion_velos.php?action=supprimer&id=<?php echo $row["id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce vélo ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <a href="ajouter_velo.php" class="btn btn-success">Ajouter un vélo</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
