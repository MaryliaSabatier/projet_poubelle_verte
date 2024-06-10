<?php
session_start();
// ... (vérification de connexion et de rôle admin)

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root"; // Ou votre nom d'utilisateur
$password_db = "";   // Ou votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des utilisateurs et de leurs rôles
$sql = "SELECT utilisateurs.id, utilisateurs.nom, utilisateurs.email, roles.nom AS role 
        FROM utilisateurs 
        INNER JOIN roles ON utilisateurs.role_id = roles.id";
$result = $conn->query($sql);
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

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nom"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["role"] . "</td>";
                        echo "<td>";
                        echo "<a href='modifier_utilisateur.php?id=" . $row["id"] . "' class='btn btn-primary btn-sm'>Modifier</a> ";
                        echo "<a href='supprimer_utilisateur.php?id=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur ?\")'>Supprimer</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>

        <a href="ajouter_utilisateur.php" class="btn btn-success">Ajouter un utilisateur</a>
    </div>
</body>
</html>
