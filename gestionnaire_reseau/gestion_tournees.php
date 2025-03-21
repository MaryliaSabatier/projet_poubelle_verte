<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Vérification de la connexion et du rôle (administrateur ou gestionnaire de réseau)
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 4)) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username_db = "root";
$password_db = "root"; // Assurez-vous que cette variable contient le bon mot de passe pour l'utilisateur root
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Traitement pour créer une nouvelle tournée unique avec des cyclistes disponibles et des vélos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_tournee'])) {
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'ete'; // Valeur par défaut = ete

    $stmt = $conn->prepare("INSERT INTO tournees (date, heure_debut, etat, mode) VALUES (NOW(), NOW(), 'planifiee', ?)");
    $stmt->bind_param("s", $mode); 

    if ($stmt->execute()) {
        $tourneeId = $conn->insert_id; 
        echo "<div class='alert alert-success'>Tournée créée en mode : $mode</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la création de la tournée.</div>";
    }

    $result = $conn->query("SELECT mode FROM tournees WHERE id = $tourneeId");
    $row = $result->fetch_assoc();
    echo "Mode enregistré en BDD : " . $row['mode'];
    




    // Récupérer les cyclistes disponibles
    $sqlCyclistesDisponibles = "SELECT id FROM utilisateurs WHERE role_id = 3 AND disponibilite = 'disponible'";
    $resultCyclistesDisponibles = $conn->query($sqlCyclistesDisponibles);

    // Vérification si des cyclistes sont disponibles
    if (!$resultCyclistesDisponibles || $resultCyclistesDisponibles->num_rows == 0) {
        echo "<div class='alert alert-warning'>Attention : Aucun cycliste disponible. Veuillez vérifier la table `utilisateurs`.</div>";
        exit();
    }



    // Récupérer les vélos disponibles
    $sqlVelosDisponibles = "SELECT id FROM velos WHERE etat = 'operationnel'";
    $resultVelosDisponibles = $conn->query($sqlVelosDisponibles);

    if ($resultCyclistesDisponibles->num_rows > $resultVelosDisponibles->num_rows) {
        echo "<div class='alert alert-warning'>Impossible de créer la tournée : Le nombre de vélos disponibles est insuffisant pour couvrir tous les cyclistes.</div>";
        exit();
    }

    // Vérification du nombre de vélos par rapport aux cyclistes disponibles
    if ($resultCyclistesDisponibles->num_rows > $resultVelosDisponibles->num_rows) {
        echo "<div class='alert alert-warning'>Impossible de créer la tournée : Le nombre de vélos disponibles est insuffisant pour couvrir tous les cyclistes.</div>";
        exit();
    }

    // Associer les cyclistes disponibles aux vélos
    while ($cycliste = $resultCyclistesDisponibles->fetch_assoc()) {
        $cyclisteId = $cycliste['id'];

        // Récupérer un vélo disponible
        if ($velo = $resultVelosDisponibles->fetch_assoc()) {
            $veloId = $velo['id'];

            // Associer le cycliste et le vélo à la tournée
            $stmt = $conn->prepare("INSERT INTO tournees_cyclistes (tournee_id, cycliste_id, velo_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $tourneeId, $cyclisteId, $veloId);
            if (!$stmt->execute()) {
                echo "<div class='alert alert-danger'>Erreur : Échec de l'association du cycliste (ID : $cyclisteId) avec un vélo.</div>";
                exit();
            }

            // Mettre à jour la disponibilité du cycliste
            $updateCycliste = $conn->prepare("UPDATE utilisateurs SET disponibilite = 'en tournée' WHERE id = ?");
            $updateCycliste->bind_param("i", $cyclisteId);
            if (!$updateCycliste->execute()) {
                echo "<div class='alert alert-danger'>Erreur : Impossible de mettre à jour le statut du cycliste (ID : $cyclisteId).</div>";
                exit();
            }

            // Mettre à jour l'état du vélo
            $updateVelo = $conn->prepare("UPDATE velos SET etat = 'en_cours_utilisation' WHERE id = ?");
            $updateVelo->bind_param("i", $veloId);
            if (!$updateVelo->execute()) {
                echo "<div class='alert alert-danger'>Erreur : Impossible de mettre à jour l'état du vélo (ID : $veloId).</div>";
                exit();
            }
        }
    }

    echo "<div class='alert alert-success'>Tournée créée avec succès ! Tous les cyclistes disponibles et les vélos ont été assignés.</div>";
}

// Traitement pour lancer la tournée
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['launch_tournee'])) {
    $tourneeId = $_POST['tournee_id'];

    // Mettre à jour l'état de la tournée à 'en cours'
    $stmt = $conn->prepare("UPDATE tournees SET etat = 'en cours', heure_debut = NOW() WHERE id = ?");
    $stmt->bind_param("i", $tourneeId);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>La tournée (ID : $tourneeId) a été lancée avec succès !</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur SQL : " . $stmt->error . "</div>";
    }


    // Rediriger vers la carte pour visualiser la tournée
    header("Location: index.html?tournee_id=$tourneeId");
    exit();
}

// Traitement pour supprimer une tournée et remettre les cyclistes et les vélos disponibles
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_tournee'])) {
    $tourneeId = $_POST['tournee_id'];

    // Supprimer les incidents liés à la tournée
    $stmtDeleteIncidents = $conn->prepare("DELETE FROM incidents WHERE tournee_id = ?");
    $stmtDeleteIncidents->bind_param("i", $tourneeId);
    $stmtDeleteIncidents->execute();

    // Récupérer les cyclistes et vélos associés à la tournée
    $stmt = $conn->prepare("SELECT cycliste_id, velo_id FROM tournees_cyclistes WHERE tournee_id = ?");
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Réinitialiser la disponibilité des cyclistes et des vélos
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

    // Supprimer les enregistrements associés
    $stmt = $conn->prepare("DELETE FROM tournees_cyclistes WHERE tournee_id = ?");
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM tournees WHERE id = ?");
    $stmt->bind_param("i", $tourneeId);
    $stmt->execute();

    echo "<div class='alert alert-success'>La tournée (ID : $tourneeId) a été supprimée avec succès. Les cyclistes et vélos sont de nouveau disponibles.</div>";
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

    // Supprimer les incidents liés à la tournée
    $stmtDeleteIncidents = $conn->prepare("DELETE FROM incidents WHERE tournee_id = ?");
    $stmtDeleteIncidents->bind_param("i", $tourneeId);
    $stmtDeleteIncidents->execute();

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


// Récupération des tournées
$sqlTournees = "SELECT t.id AS tournee_id, t.date, t.heure_debut, t.etat, t.mode,
                COUNT(tc.cycliste_id) AS nombre_cyclistes, 
                (SELECT COUNT(v.id) FROM tournees_cyclistes tc2 
                 LEFT JOIN velos v ON tc2.velo_id = v.id 
                 WHERE tc2.tournee_id = t.id) AS nombre_velos
                FROM tournees t
                LEFT JOIN tournees_cyclistes tc ON t.id = tc.tournee_id
                GROUP BY t.id
                ORDER BY t.date DESC";
$resultTournees = $conn->query($sqlTournees);

// Traitement pour mettre à jour l'état de la tournée
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['etat']) && isset($_POST['tournee_id'])) {
    $tourneeId = intval($_POST['tournee_id']);
    $nouvelEtat = $_POST['etat'];

    // Valider l'état
    $etatsValides = ['planifiee', 'en_cours', 'terminee', 'interrompue'];
    if (in_array($nouvelEtat, $etatsValides)) {
        $stmt = $conn->prepare("UPDATE tournees SET etat = ? WHERE id = ?");
        $stmt->bind_param("si", $nouvelEtat, $tourneeId);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>État mis à jour avec succès pour la tournée ID: $tourneeId.</div>";
        } else {
            echo "<div class='alert alert-danger'>Erreur : Impossible de mettre à jour l'état. " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Erreur : État non valide.</div>";
    }
}

$modeMessage = ''; // Initialisation du message

// Vérification du mode sélectionné
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mode'])) {
    $mode = $_POST['mode'];
    if ($mode === 'ete') {
        $modeMessage = "Les vélos ont une autonomie de 50 km.";
    } elseif ($mode === 'hiver') {
        $modeMessage = "Les vélos ont perdu une autonomie de -10%.";
    }
}
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
                <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom']); ?>!</h2>

                <!-- Affichage des messages -->
                <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <!-- Formulaire pour créer une nouvelle tournée -->
                <form method="POST">
                    <input type="hidden" name="create_tournee" value="1">
                    <button type="submit" class="btn btn-success mb-4">Créer une tournée</button>
                </form>
                <!-- Sélection du mode (Été / Hiver) -->
                <form method="POST">
                    <input type="hidden" id="selectedMode" name="mode" value="">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary mode-btn" data-mode="ete">Mode Été</button>
                        <button type="button" class="btn btn-secondary mode-btn" data-mode="hiver">Mode Hiver</button>
                    </div>
                    <button type="submit" name="create_tournee" class="btn btn-success mt-3">Créer une tournée</button>
                </form>

                <script>
                    // Ajouter un événement pour sélectionner le mode
                    document.querySelectorAll(".mode-btn").forEach(button => {
                        button.addEventListener("click", function() {
                            document.getElementById("selectedMode").value = this.getAttribute("data-mode");
                            alert("Mode sélectionné : " + this.getAttribute("data-mode")); // Debug
                        });
                    });
                </script>


                <!-- Affichage du message basé sur le mode -->
                <?php if (!empty($modeMessage)): ?>
                    <div class="alert alert-info">
                        <?php echo $modeMessage; ?>
                    </div>
                <?php endif; ?>

                <!-- Affichage du mode sélectionné -->
                <?php
                $mode = isset($_POST['mode']) ? $_POST['mode'] : 'non défini';

                if ($mode === 'ete') {
                    echo "<div class='alert alert-info'>Vous avez sélectionné le mode : <strong>Été</strong></div>";
                } elseif ($mode === 'hiver') {
                    echo "<div class='alert alert-info'>Vous avez sélectionné le mode : <strong>Hiver</strong></div>";
                } else {
                    echo "<div class='alert alert-warning'>Mode non sélectionné. Choisissez entre <strong>Été</strong> ou <strong>Hiver</strong>.</div>";
                }
                ?>
                <h3>Liste des tournées</h3>

                <?php if ($resultTournees->num_rows > 0): ?>
                    <?php while ($row = $resultTournees->fetch_assoc()): ?>
                        <div class='tournee'>
                            <h4>Tournée ID: <?php echo $row['tournee_id']; ?> - Date: <?php echo $row['date']; ?> - Cyclistes: <?php echo $row['nombre_cyclistes']; ?> - Vélos: <?php echo $row['nombre_velos']; ?></h4>
                            <div class='d-flex justify-content-between mt-3'>
                                <a href="trajet.php?tournee_id=<?php echo $row['tournee_id']; ?>" class="btn btn-info btn-sm">Visualisation Trajet</a>


                                <!-- Si la tournée est en cours, afficher le bouton "Terminer la tournée" -->
                                <?php if ($row['etat'] == 'en cours'): ?>
                                    <form method='POST' class='d-inline'>
                                        <input type='hidden' name='tournee_id' value='<?php echo $row['tournee_id']; ?>'>
                                        <button type='submit' class='btn btn-success btn-sm' name='end_tournee' onclick="return confirm('Êtes-vous sûr de vouloir terminer cette tournée ?')">Terminer la tournée</button>
                                    </form>
                                <?php endif; ?>

                                <!-- Si la tournée n'est pas encore lancée, afficher le bouton pour la lancer -->
                                <?php if ($row['etat'] == 'prévue'): ?>
                                    <form method='POST' class='d-inline'>
                                        <input type='hidden' name='tournee_id' value='<?php echo $row['tournee_id']; ?>'>
                                        <button type='submit' class='btn btn-primary btn-sm' name='launch_tournee'>Lancer la tournée</button>
                                    </form>
                                <?php endif; ?>

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

                                <!-- Formulaire pour mettre à jour l'état de la tournée -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="tournee_id" value="<?php echo $row['tournee_id']; ?>">
                                    <select name="etat" class="form-select form-select-sm d-inline" onchange="this.form.submit()">
                                        <option value="planifiee" <?php if ($row['etat'] == 'planifiee') echo 'selected'; ?>>Planifiée</option>
                                        <option value="en_cours" <?php if ($row['etat'] == 'en_cours') echo 'selected'; ?>>En cours</option>
                                        <option value="terminee" <?php if ($row['etat'] == 'terminee') echo 'selected'; ?>>Terminée</option>
                                        <option value="interrompue" <?php if ($row['etat'] == 'interrompue') echo 'selected'; ?>>Interrompue</option>
                                    </select>
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