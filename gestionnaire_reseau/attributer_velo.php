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

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$cyclistes = $conn->query("SELECT id, nom, prenom FROM utilisateurs WHERE role_id = 3");
$velos = $conn->query("SELECT id, numero FROM velos WHERE etat = 'operationnel'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cycliste_id = $_POST['cycliste_id'];
    $velo_id = $_POST['velo_id'];

    $sql = "UPDATE utilisateurs SET velo_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $velo_id, $cycliste_id);

    if ($stmt->execute()) {
        header('Location: gestionnaire_reseau.php');
        exit();
    } else {
        $error = "Erreur lors de l'attribution du vélo : " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attribuer un vélo à un cycliste</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Attribuer un vélo à un cycliste</h2>
        <a href="../logout.php" class="btn btn-danger">Déconnexion</a>
        <a href="gestionnaire_reseau.php" class="btn btn-secondary">Retour au dashboard</a>
        
        <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="cycliste_id" class="form-label">Cycliste</label>
                <select class="form-select" id="cycliste_id" name="cycliste_id" required>
                    <?php while ($cycliste = $cyclistes->fetch_assoc()): ?>
                        <option value="<?= $cycliste['id'] ?>"><?= $cycliste['prenom'] . " " . $cycliste['nom'] ?></option>
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
            <button type="submit" class="btn btn-primary">Attribuer</button>
        </form>
    </div>
</body>
</html>
