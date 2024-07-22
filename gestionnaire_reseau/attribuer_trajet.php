<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    header('Location: ../login.php');
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $username_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$tournees = $conn->query("SELECT id, nom FROM tournees");
$velos = $conn->query("SELECT id, numero FROM velos WHERE etat = 'operationnel'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tourne_id = $_POST['tourne_id'];
    $velo_id = $_POST['velo_id'];
    $depart = $_POST['depart'];
    $destination = $_POST['destination'];
    $distance = $_POST['distance'];

    $sql = "INSERT INTO trajets (tourne_id, velo_id, depart, destination, distance) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissd", $tourne_id, $velo_id, $depart, $destination, $distance);

    if ($stmt->execute()) {
        header('Location: gestionnaire_reseau.php');
        exit();
    } else {
        $error = "Erreur lors de l'attribution du trajet : " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attribuer un trajet à un vélo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Attribuer un trajet à un vélo</h2>
        <a href="../logout.php" class="btn btn-danger">Déconnexion</a>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary">Retour au dashboard</a>

        <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="tourne_id" class="form-label">Tournée</label>
                <select class="form-select" id="tourne_id" name="tourne_id" required>
                    <?php while ($tourne = $tournees->fetch_assoc()): ?>
                        <option value="<?= $tourne['id'] ?>"><?= $tourne['nom'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="velo_id" class="form-label">Vélo</label>
                <select class="form-select" id="velo_id" name="velo_id" required>
                    <?php while ($velo = $velos->fetch_assoc()): ?>
                        <option value="<?= $velo['id'] ?>"><?= $velo['numero'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="depart" class="form-label">Départ</label>
                <input type="text" class="form-control" id="depart" name="depart" required>
            </div>
            <div class="mb-3">
                <label for="destination" class="form-label">Destination</label>
                <input type="text" class="form-control" id="destination" name="destination" required>
            </div>
            <div class="mb-3">
                <label for="distance" class="form-label">Distance (km)</label>
                <input type="number" step="0.1" class="form-control" id="distance" name="distance" required>
            </div>
            <button type="submit" class="btn btn-primary">Attribuer</button>
        </form>
    </div>
</body>
</html>
