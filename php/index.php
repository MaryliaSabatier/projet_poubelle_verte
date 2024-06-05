<?php
session_start();

if (isset($_SESSION['email'])) {
    header("Location: accueil.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <h2>Bienvenue</h2>
    <a href="inscription.php">Inscription</a>
    <a href="connexion.php">Connexion</a>
</body>
</html>
