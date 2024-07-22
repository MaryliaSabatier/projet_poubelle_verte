<?php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
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

// Récupération du nombre de cyclistes disponibles (ceux qui ne sont pas malades)
$sqlCyclistesDisponibles = "SELECT COUNT(*) as total FROM utilisateurs WHERE role_id = 3 AND disponibilite != 'malade'";
$resultCyclistesDisponibles = $conn->query($sqlCyclistesDisponibles);
$cyclistesDisponibles = $resultCyclistesDisponibles->fetch_assoc()['total'];

// Récupération du nombre de vélos existants
$sqlTotalVelos = "SELECT COUNT(*) as total FROM velos";
$resultTotalVelos = $conn->query($sqlTotalVelos);
$totalVelos = $resultTotalVelos->fetch_assoc()['total'];

// Vérification si l'ajout est autorisé
$ajoutAutorise = ($totalVelos < $cyclistesDisponibles);

// Initialisation des variables pour le formulaire
$numero = $autonomie = $dateRevision = $etat = $error = "";

// Traitement du formulaire d'ajout de vélo (seulement si l'ajout est autorisé)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $ajoutAutorise) {
    // Récupération et validation des données du formulaire
    $numero = htmlspecialchars($_POST['numero']);
    $autonomie = 50; // Autonomie fixe à 50 km
    $dateRevision = $_POST['date_derniere_revision'];
    $etat = $_POST['etat'];

    // Validations (champs obligatoires, format de la date, unicité du numéro, etc.)
    if (empty($numero) || empty($dateRevision) || empty($etat)) {
        $error = "Tous les champs sont obligatoires.";
    }

    // Vérification de l'unicité du numéro de vélo
    $stmtCheckNumero = $conn->prepare("SELECT id FROM velos WHERE numero = ?");
    $stmtCheckNumero->bind_param("s", $numero);
    $stmtCheckNumero->execute();
    $resultCheckNumero = $stmtCheckNumero->get_result();
    if ($resultCheckNumero->num_rows > 0) {
        $error .= "<br>Ce numéro de vélo est déjà utilisé.";
    }

    // Si aucune erreur, ajout du vélo
    if (empty($error)) {
        // Préparation de la requête d'insertion
        $stmt = $conn->prepare("INSERT INTO velos (numero, autonomie_km, date_derniere_revision, etat) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $numero, $autonomie, $dateRevision, $etat);

        if ($stmt->execute()) {
            header('Location: gestion_velos.php'); // Redirection après succès
            exit();
        } else {
            $error = "Erreur lors de l'ajout du vélo : " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un vélo</title>
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
        <h2>Ajouter un vélo</h2>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary mb-3">Retour au dashboard</a>

        <?php if (!$ajoutAutorise): ?>
            <div class="alert alert-warning">
                Vous ne pouvez pas ajouter plus de vélos que le nombre de cyclistes disponibles (<?php echo $cyclistesDisponibles; ?>).
            </div>
        <?php else: ?>
            <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

            <form method="POST">
                <div class="mb-3">
                    <label for="numero" class="form-label">Numéro de vélo</label>
                    <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $numero; ?>" required>
                </div>
                <input type="hidden" name="autonomie_km" value="50">
                <div class="mb-3">
                    <label for="etat" class="form-label">État</label>
                    <select class="form-select" id="etat" name="etat" required>
                        <option value="">Sélectionnez un état</option>
                        <option value="operationnel" <?php echo ($etat == 'operationnel') ? 'selected' : ''; ?>>Opérationnel</option>
                        <option value="en_maintenance" <?php echo ($etat == 'en_maintenance') ? 'selected' : ''; ?>>En maintenance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="date_derniere_revision" class="form-label">Date de dernière révision</label>
                    <input type="date" class="form-control" id="date_derniere_revision" name="date_derniere_revision" value="<?php echo $dateRevision; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
                <a href="gestionnaire_reseau.php" class="btn btn-secondary">Retour au dashboard</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
