<?php
session_start();

// Vérification de la connexion et du rôle RH
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {  // 2 = ID du rôle RH
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";  // Ou votre nom d'utilisateur
$password_db = "root";    // Ou votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des données de l'utilisateur à modifier
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $utilisateur = $result->fetch_assoc();

    if (!$utilisateur) {
        die("Utilisateur non trouvé.");
    }
} else {
    die("ID d'utilisateur non spécifié.");
}

// Récupération des rôles autorisés pour les RH (cycliste et gestionnaire de réseau)
$sqlRoles = "SELECT * FROM roles WHERE id IN (3, 4)"; // 3 = cycliste, 4 = gestionnaire de réseau
$resultRoles = $conn->query($sqlRoles);

// Initialisation des variables pour le formulaire (avec les valeurs actuelles de l'utilisateur)
$nom = $utilisateur['nom'];
$prenom = $utilisateur['prenom'];
$email = $utilisateur['email'];
$roleId = $utilisateur['role_id'];
$dateEmbauche = $utilisateur['date_embauche'];
$disponibilite = $utilisateur['disponibilite'];
$error = "";
$success = ""; // Variable pour afficher un message de succès

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et validation des données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Mot de passe en clair (à modifier ultérieurement)
    $roleId = $_POST['role_id'];
    $dateEmbauche = $_POST['date_embauche']; // Optionnel, seulement pour les cyclistes
    $disponibilite = $_POST['disponibilite']; // Optionnel, seulement pour les cyclistes

    // Validations (email, mot de passe si modifié, unicité de l'email, champs obligatoires)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    }

    // Vérification de l'unicité de l'email (sauf si l'email n'a pas été modifié)
    if ($email != $utilisateur['email']) {
        $stmtCheckEmail = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmtCheckEmail->bind_param("s", $email);
        $stmtCheckEmail->execute();
        $resultCheckEmail = $stmtCheckEmail->get_result();
        if ($resultCheckEmail->num_rows > 0) {
            $error .= "<br>Cette adresse email est déjà utilisée.";
        }
    }

    // Vérification des champs obligatoires
    if (empty($nom) || empty($prenom) || empty($email) || empty($roleId)) {
        $error .= "<br>Tous les champs sont obligatoires.";
    }

    // Vérifications spécifiques au rôle de cycliste
    if ($roleId == 3) { // 3 = ID du rôle cycliste
        if (empty($dateEmbauche)) {
            $error .= "<br>La date d'embauche est obligatoire pour les cyclistes.";
        }
        // Vous pouvez ajouter d'autres validations pour la disponibilité si nécessaire
    }

    // Si aucune erreur, mise à jour de l'utilisateur
    if (empty($error)) {
        // Hachage du mot de passe si modifié (à implémenter pour la sécurité)
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 
        } else {
            // Si le mot de passe n'est pas modifié, on récupère le hash actuel
            $hashedPassword = $utilisateur['mot_de_passe'];
        }

        // Préparation de la requête de mise à jour
        $stmt = $conn->prepare("UPDATE utilisateurs SET nom=?, prenom=?, email=?, mot_de_passe=?, role_id=?, date_embauche=?, disponibilite=? WHERE id=?");
        $stmt->bind_param("sssisssi", $nom, $prenom, $email, $hashedPassword, $roleId, $dateEmbauche, $disponibilite, $userId);

        if ($stmt->execute()) {
            $success = "Utilisateur modifié avec succès !"; // Message de succès
        } else {
            $error = "Erreur lors de la modification de l'utilisateur : " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un utilisateur (RH)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Modifier un utilisateur (RH)</h2>

        <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (!empty($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $prenom; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-select" id="role" name="role_id" required>
                    <?php while ($role = $resultRoles->fetch_assoc()) { ?>
                        <option value="<?php echo $role['id']; ?>" <?php echo ($role['id'] == $roleId) ? 'selected' : ''; ?>>
                            <?php echo $role['nom']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div id="cyclisteFields" style="display: <?php echo ($roleId == 3) ? 'block' : 'none'; ?>;">
                <div class="mb-3">
                    <label for="date_embauche" class="form-label">Date d'embauche</label>
                    <input type="date" class="form-control" id="date_embauche" name="date_embauche" value="<?php echo $dateEmbauche; ?>">
                </div>
                <div class="mb-3">
                    <label for="disponibilite" class="form-label">Disponibilité</label>
                    <textarea class="form-control" id="disponibilite" name="disponibilite"><?php echo $disponibilite; ?></textarea>
                    <small class="form-text text-muted">Veuillez écrire "disponible" dans cette case.</small>
=                </div>
            </div>

            <button type="submit" class btn btn-primary>Modifier</button>
        </form>

        <a href="gestion_utilisateurs_rh.php" class="btn btn-secondary mt-3">Retour à la liste des utilisateurs</a> 
    </div>

    <script>
        // Afficher/masquer les champs spécifiques aux cyclistes en fonction du rôle sélectionné
        const roleSelect = document.getElementById('role');
        const cyclisteFields = document.getElementById('cyclisteFields');

        roleSelect.addEventListener('change', function() {
            if (this.value == 3) { // 3 = ID du rôle cycliste
                cyclisteFields.style.display = 'block';
            } else {
                cyclisteFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
