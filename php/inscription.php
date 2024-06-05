<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    // Connexion à la base de données
    $connexion = new mysqli("localhost", "root", "", "poubelle_verte");

    if ($connexion->connect_error) {
        die("Connexion échouée : " . $connexion->connect_error);
    }

    // Préparer la requête SQL pour insérer l'utilisateur
    $sql = $connexion->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss", $nom, $email, $mot_de_passe, $role);

    if ($sql->execute()) {
        $_SESSION['email'] = $email;
        header("Location: accueil.php");
        exit();
    } else {
        $erreur = "Erreur lors de l'inscription.";
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
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <?php if(isset($erreur)) { echo "<p>$erreur</p>"; } ?>
    <form action="inscription.php" method="post">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required>
        </div>
        <div>
            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="cycliste">Cycliste</option>
                <option value="gestionnaire">Gestionnaire</option>
                <option value="RH">RH</option>
                <option value="administrateur">Administrateur</option>
            </select>
        </div>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
