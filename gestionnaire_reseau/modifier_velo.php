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

// Récupérer l'ID du vélo à modifier
$veloId = $_GET['id'];

// Récupération des informations du vélo
$sqlVelo = "SELECT id, numero, etat, autonomie_km, date_derniere_revision FROM velos WHERE id = ?";
$stmt = $conn->prepare($sqlVelo);
$stmt->bind_param("i", $veloId);
$stmt->execute();
$resultVelo = $stmt->get_result();
$velo = $resultVelo->fetch_assoc();

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero = htmlspecialchars($_POST['numero']);
    $etat = $_POST['etat'];
    $autonomie = 50; // Autonomie fixe à 50 km
    $dateRevision = $_POST['date_derniere_revision'];

    // Mise à jour des informations du vélo
    $sqlUpdate = "UPDATE velos SET numero = ?, etat = ?, autonomie_km = ?, date_derniere_revision = ? WHERE id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("ssisi", $numero, $etat, $autonomie, $dateRevision, $veloId);

    if ($stmtUpdate->execute()) {
        header('Location: gestion_velos.php');
        exit();
    } else {
        $error = "Erreur lors de la modification du vélo : " . $stmtUpdate->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un vélo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier un vélo</h2>
        <a href="gestion_velos.php" class="btn btn-secondary mb-3">Retour à la gestion des vélos</a>

        <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="numero" class="form-label">Numéro de vélo</label>
                <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $velo['numero']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="etat" class="form-label">État</label>
                <select class="form-select" id="etat" name="etat" required>
                    <option value="operationnel" <?php if ($velo['etat'] == 'operationnel') echo 'selected'; ?>>Opérationnel</option>
                    <option value="en_maintenance" <?php if ($velo['etat'] == 'en_maintenance') echo 'selected'; ?>>En maintenance</option>
                </select>
            </div>
            <input type="hidden" name="autonomie_km" value="50">
            <div class="mb-3">
                <label for="date_derniere_revision" class="form-label">Date de dernière révision</label>
                <input type="date" class="form-control" id="date_derniere_revision" name="date_derniere_revision" value="<?php echo $velo['date_derniere_revision']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Modifier</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
