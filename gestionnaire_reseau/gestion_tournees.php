<?php
session_start();

// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username_db = "root";
$password_db = ""; // Assurez-vous que cette variable contient le bon mot de passe pour l'utilisateur root
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Traitement pour créer une nouvelle tournée unique avec des cyclistes disponibles et des vélos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tournee'])) {
    // Créer la tournée unique
    $stmt = $conn->prepare("INSERT INTO tournees (date, heure_debut, etat) VALUES (NOW(), NOW(), 'prévue')");
    $stmt->execute();
    $tourneeId = $conn->insert_id;

    // Récupérer les cyclistes disponibles
    $sqlCyclistesDisponibles = "SELECT id FROM utilisateurs WHERE role_id = 3 AND disponibilite = 'disponible'";
    $resultCyclistesDisponibles = $conn->query($sqlCyclistesDisponibles);

    // Récupérer les vélos disponibles
    $sqlVelosDisponibles = "SELECT id FROM velos WHERE etat = 'operationnel'";
    $resultVelosDisponibles = $conn->query($sqlVelosDisponibles);

    if ($resultCyclistesDisponibles && $resultCyclistesDisponibles->num_rows > 0 && $resultVelosDisponibles && $resultVelosDisponibles->num_rows >= $resultCyclistesDisponibles->num_rows) {
        while ($cycliste = $resultCyclistesDisponibles->fetch_assoc()) {
            $cyclisteId = $cycliste['id'];

            // Récupérer un vélo disponible pour ce cycliste
            $velo = $resultVelosDisponibles->fetch_assoc();
            $veloId = $velo['id'];

            // Associer le cycliste et le vélo à la tournée dans une table intermédiaire
            $stmt = $conn->prepare("INSERT INTO tournees_cyclistes (tournee_id, cycliste_id, velo_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $tourneeId, $cyclisteId, $veloId);
            $stmt->execute();

            // Mettre à jour la disponibilité du cycliste
            $updateCycliste = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'en tournée' WHERE id = ?");
            $updateCycliste->bind_param("i", $cyclisteId);
            $updateCycliste->execute();

            // Mettre à jour l'état du vélo
            $updateVelo = $conn->prepare("UPDATE velos SET etat = 'en_cours_utilisation' WHERE id = ?");
            $updateVelo->bind_param("i", $veloId);
            $updateVelo->execute();
        }
        echo "<div class='alert alert-success'>Tournée créée avec tous les cyclistes disponibles et les vélos assignés !</div>";
    } else {
        echo "<div class='alert alert-warning'>Pas assez de vélos disponibles pour créer la tournée.</div>";
    }
}

// Traitement pour lancer la tournée
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['launch_tournee'])) {
    $tourneeId = $_POST['tournee_id'];

    // Mettre à jour l'état de la tournée à 'en cours'
    $stmt = $conn->prepare("UPDATE tournees SET etat = 'en cours', heure_debut = NOW() WHERE id = ?");
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();

    // Rediriger vers index.html pour afficher la carte
    header("Location: index.html?tournee_id=$tourneeId");
    exit();
}

// Traitement pour supprimer une tournée et remettre les cyclistes et les vélos disponibles
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_tournee'])) {
    $tourneeId = $_POST['tournee_id'];

    // Récupérer les cyclistes et vélos associés à la tournée pour remettre leur disponibilité
    $sqlCyclistesTournee = "SELECT cycliste_id, velo_id FROM tournees_cyclistes WHERE tournee_id = ?";
    $stmt = $conn->prepare($sqlCyclistesTournee);
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Mettre à jour la disponibilité des cyclistes et des vélos
    while ($row = $result->fetch_assoc()) {
        $cyclisteId = $row['cycliste_id'];
        $veloId = $row['velo_id'];

        $updateCycliste = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'disponible' WHERE id = ?");
        $updateCycliste->bind_param("i", $cyclisteId);
        $updateCycliste->execute();

        $updateVelo = $conn->prepare("UPDATE velos SET etat = 'operationnel' WHERE id = ?");
        $updateVelo->bind_param("i", $veloId);
        $updateVelo->execute();
    }

    // Supprimer les enregistrements de la table intermédiaire tournees_cyclistes
    $stmtSuppressionTourneeCyclistes = $conn->prepare("DELETE FROM tournees_cyclistes WHERE tournee_id = ?");
    $stmtSuppressionTourneeCyclistes->bind_param("i", $tourneeId);
    $stmtSuppressionTourneeCyclistes->execute();

    // Supprimer la tournée
    $stmtSuppressionTournee = $conn->prepare("DELETE FROM tournees WHERE id = ?");
    $stmtSuppressionTournee->bind_param("i", $tourneeId);
    $stmtSuppressionTournee->execute();

    echo "<div class='alert alert-success'>Tournée supprimée avec succès, cyclistes et vélos disponibles.</div>";
}

// Traitement pour réassigner des vélos aux cyclistes d'une tournée existante
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reassigner_velos'])) {
    $tourneeId = $_POST['tournee_id'];

    // Récupérer les cyclistes de la tournée
    $sqlCyclistesTournee = "SELECT cycliste_id FROM tournees_cyclistes WHERE tournee_id = ?";
    $stmt = $conn->prepare($sqlCyclistesTournee);
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer les cyclistes de la tournée
    $sqlCyclistesTournee = "SELECT cycliste_id FROM tournees_cyclistes WHERE tournee_id = ?";
    $stmt = $conn->prepare($sqlCyclistesTournee);
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Récupérer les vélos disponibles
    $sqlVelosDisponibles = "SELECT id FROM velos WHERE etat = 'operationnel'";
    $resultVelosDisponibles = $conn->query($sqlVelosDisponibles);

    if ($result->num_rows > 0 && $resultVelosDisponibles->num_rows >= $result->num_rows) {
        // Supprimer les anciennes affectations de vélos
        $stmtSuppressionAncienneAffectation = $conn->prepare("DELETE FROM tournees_cyclistes WHERE tournee_id = ?");
        $stmtSuppressionAncienneAffectation->bind_param("i", $tourneeId);
        $stmtSuppressionAncienneAffectation->execute();

        while ($cycliste = $result->fetch_assoc()) {
            $cyclisteId = $cycliste['cycliste_id'];

            // Récupérer un vélo disponible pour ce cycliste
            $velo = $resultVelosDisponibles->fetch_assoc();
            $veloId = $velo['id'];

            // Associer le cycliste et le nouveau vélo à la tournée
            $stmt = $conn->prepare("INSERT INTO tournees_cyclistes (tournee_id, cycliste_id, velo_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $tourneeId, $cyclisteId, $veloId);
            $stmt->execute();

            // Mettre à jour l'état du vélo
            $updateVelo = $conn->prepare("UPDATE velos SET etat = 'en_cours_utilisation' WHERE id = ?");
            $updateVelo->bind_param("i", $veloId);
            $updateVelo->execute();
        }

        echo "<div class='alert alert-success'>Les vélos ont été réassignés avec succès !</div>";
    } else {
        echo "<div class='alert alert-warning'>Pas assez de vélos disponibles pour réassigner tous les cyclistes.</div>";
    }
}

// Récupération des tournées
$sqlTournees = "SELECT t.id AS tournee_id, t.date, t.heure_debut, t.etat, COUNT(tc.cycliste_id) AS nombre_cyclistes, 
                (SELECT COUNT(v.id) FROM tournees_cyclistes tc2 LEFT JOIN velos v ON tc2.velo_id = v.id WHERE tc2.tournee_id = t.id) AS nombre_velos
                FROM tournees t
                LEFT JOIN tournees_cyclistes tc ON t.id = tc.tournee_id
                GROUP BY t.id
                ORDER BY t.date DESC";
$resultTournees = $conn->query($sqlTournees);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des tournées</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestion des tournées</h1>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <div class="row">
            <div class="col-md-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="gestionnaire_reseau.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="gestion_tournees.php">Gestion des tournées</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestion_velos.php">Gestion des vélos</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>

                <!-- Formulaire pour créer une nouvelle tournée -->
                <form method="POST">
                    <input type="hidden" name="create_tournee" value="1">
                    <button type="submit" class="btn btn-success mb-4">Créer une tournée</button>
                </form>

                <h3>Liste des tournées</h3>

                <?php if ($resultTournees->num_rows > 0): ?>
                    <?php while ($row = $resultTournees->fetch_assoc()): ?>
                        <div class='tournee'>
                            <h4>Tournée ID: <?php echo $row['tournee_id']; ?> - Date: <?php echo $row['date']; ?> - Cyclistes: <?php echo $row['nombre_cyclistes']; ?> - Vélos: <?php echo $row['nombre_velos']; ?></h4>
                            <div class='d-flex justify-content-between mt-3'>
                                <!-- Formulaire pour lancer la tournée -->
                                <form method='POST' class='d-inline'>
                                    <input type='hidden' name='tournee_id' value='<?php echo $row['tournee_id']; ?>'>
                                    <button type='submit' class='btn btn-primary btn-sm' name='launch_tournee' 
                                    <?php if ($row['nombre_cyclistes'] == 0 || $row['nombre_velos'] < $row['nombre_cyclistes']) echo 'disabled'; ?>>
                                    Lancer la tournée
                                    </button>
                                </form>

                                <!-- Si pas assez de vélos, afficher le bouton pour aller à la gestion des vélos -->
                                <?php if ($row['nombre_velos'] < $row['nombre_cyclistes']): ?>
                                    <a href="gestion_velos.php" class="btn btn-warning btn-sm">Ajouter des vélos</a>
                                <?php endif; ?>

                                <!-- Nouveau bouton pour réassigner les vélos -->
                                <form method='POST' class='d-inline'>
                                    <input type='hidden' name='tournee_id' value='<?php echo $row['tournee_id']; ?>'>
                                    <button type='submit' class='btn btn-secondary btn-sm' name='reassigner_velos'>Réassigner les vélos</button>
                                </form>

                                <!-- Formulaire pour supprimer la tournée -->
                                <form method='POST' class='d-inline'>
                                    <input type='hidden' name='tournee_id' value='<?php echo $row['tournee_id']; ?>'>
                                    <button type='submit' class='btn btn-danger btn-sm' name='delete_tournee' onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tournée ?')">Supprimer</button>
                                </form>
                            </div>
                        </div>
                        <hr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Aucune tournée n'est disponible pour le moment.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>