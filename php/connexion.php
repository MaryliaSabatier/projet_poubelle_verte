<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Connexion à la base de données
    $connexion = new mysqli("localhost", "root", "", "poubelle_verte");

    if ($connexion->connect_error) {
        die("Connexion échouée : " . $connexion->connect_error);
    }

    // Préparer la requête SQL pour récupérer les informations de l'utilisateur
    $sql = $connexion->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $resultat = $sql->get_result();
    $utilisateur = $resultat->fetch_assoc();

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['email'] = $utilisateur['email'];
        header("Location: accueil.php");
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }

    $sql->close();
    $connexion->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/connexion.css">
</head>
<body>
    <h2>Connexion</h2>
    <?php if(isset($erreur)) { echo "<p>$erreur</p>"; } ?>
    <form action="connexion.php" method="post">
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
