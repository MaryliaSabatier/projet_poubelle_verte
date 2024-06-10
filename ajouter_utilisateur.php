<?php
session_start();

// Vérification de la connexion et du rôle d'administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) { // 1 = ID du rôle administrateur
    header('Location: login.php');
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";  // Ou votre nom d'utilisateur
$password_db = "";    // Ou votre mot de passe
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des rôles pour le formulaire
$sqlRoles = "SELECT * FROM roles";
$resultRoles = $conn->query($sqlRoles);

// Initialisation des variables pour le formulaire
$nom = $email = $password = $roleId = $error = "";

// Traitement du formulaire d'ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération et validation des données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $roleId = $_POST['role_id'];

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'adresse email n'est pas valide.";
    }

    // Validation du mot de passe (au moins 8 caractères)
    if (strlen($password) < 8) {
        $error .= "<br>Le mot de passe doit contenir au moins 8 caractères.";
    }

    // Vérification de l'unicité de l'email
    $stmtCheckEmail = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();
    $resultCheckEmail = $stmtCheckEmail->get_result();
    if ($resultCheckEmail->num_rows > 0) {
        $error .= "<br>Cette adresse email est déjà utilisée.";
    }

    // Si aucune erreur, ajout de l'utilisateur
    if (empty($error)) {
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Préparation de la requête d'insertion
        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nom, $email, $hashedPassword, $roleId);

        if ($stmt->execute()) {
            header('Location: admin.php'); // Redirection vers la page admin après succès
            exit();
        } else {
            $error = "Erreur lors de l'ajout de l'utilisateur : " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter un utilisateur</h2>

        <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $nom; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
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
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
</body>
</html>
