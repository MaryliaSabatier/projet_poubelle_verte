<?php
session_start();

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
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



// Requête pour récupérer tous les vélos
$sqlVelos = "SELECT id, numero, etat, autonomie_km, date_derniere_revision FROM velos";
$resultVelos = $conn->query($sqlVelos);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des vélos</title>
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
        <h2>Gestion des vélos</h2>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary mb-3">Retour au dashboard</a>
        <a href="gestion_tournees.php" class="btn btn-info mb-3">Gestion des tournées</a>
        <a href="ajouter_velo.php" class="btn btn-primary mb-3">Ajouter un vélo</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>État</th>
                    <th>Autonomie (km)</th>
                    <th>Date de dernière révision</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultVelos->num_rows > 0) {
                    while($velo = $resultVelos->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $velo['numero'] . "</td>";
                        echo "<td>" . $velo['etat'] . "</td>";
                        echo "<td>" . $velo['autonomie_km'] . "</td>";
                        echo "<td>" . $velo['date_derniere_revision'] . "</td>";
                        echo "<td>
                                <a href='modifier_velo.php?id=" . $velo['id'] . "' class='btn btn-warning btn-sm'>Modifier</a>
                                <a href='supprimer_velo.php?id=" . $velo['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce vélo ?\");'>Supprimer</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Aucun vélo trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
