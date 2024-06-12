<?php
session_start();

// Vérification de la connexion et du rôle d'administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
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

// Récupération des utilisateurs et de leurs rôles
$sqlUtilisateurs = "SELECT utilisateurs.id, utilisateurs.nom, utilisateurs.prenom, utilisateurs.email, roles.nom AS role 
                    FROM utilisateurs 
                    INNER JOIN roles ON utilisateurs.role_id = roles.id";
$resultUtilisateurs = $conn->query($sqlUtilisateurs);

// Traitement des actions (suppression d'utilisateur)
if (isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Suppression de l'utilisateur
    $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        header('Location: gestion_utilisateurs.php'); // Redirection après suppression réussie
        exit();
    } else {
        echo "Erreur lors de la suppression de l'utilisateur.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Gestion des utilisateurs</h2>

        <?php if ($resultUtilisateurs->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
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
                            <td>
                                <a href="modifier_utilisateur.php?id=<?php echo $row["id"]; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                <a href="gestion_utilisateurs.php?action=supprimer&id=<?php echo $row["id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun utilisateur trouvé.</p>
        <?php endif; ?>

        <a href="ajouter_utilisateur.php" class="btn btn-success">Ajouter un utilisateur</a>
    </div>
</body>
</html>
