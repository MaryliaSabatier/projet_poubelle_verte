<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../css/acceuil.css">

</head>
<body>
    <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h2>
    <p>Vous êtes maintenant connecté à l'application de suivi de ramassage de poubelles.</p>
    <a href="deconnexion.php">Déconnexion</a>
</body>
</html>
