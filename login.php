<?php
session_start();

// Vérification si l'utilisateur est déjà connecté
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role_id'] == 1) {
        header('Location: admin/admin.php');
    } elseif ($_SESSION['role_id'] == 2) {
        header('Location: rh/rh.php');
    }
    exit();
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connexion à la base de données (à adapter avec vos informations)
    $servername = "localhost";
    $username_db = "root"; // Ou votre nom d'utilisateur
    $password_db = "root";   // Ou votre mot de passe
    $dbname = "poubelle_verte";

    $conn = new mysqli($servername, $username_db, $password_db, $dbname);
    if ($conn->connect_error) {
        die("La connexion a échoué : " . $conn->connect_error);
    }

    // Préparation de la requête pour éviter les injections SQL
    $stmt = $conn->prepare("SELECT id, role_id, mot_de_passe, prenom FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if ($password == $row['mot_de_passe']) {
            // Vérification du rôle (1 pour admin, 2 pour RH)
            if ($row['role_id'] == 1) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role_id'] = $row['role_id'];
                $_SESSION['prenom'] = $row['prenom'];
                header('Location: admin/admin.php'); // Redirection vers la page admin
                exit();
            } elseif ($row['role_id'] == 2) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role_id'] = $row['role_id'];
                $_SESSION['prenom'] = $row['prenom'];
                header('Location: rh/rh.php'); // Redirection vers la page RH
                exit();
            } elseif ($row['role_id'] == 4) { // Gestionnaire de réseau
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role_id'] = $row['role_id'];
                $_SESSION['prenom'] = $row['prenom'];
                header('Location: gestionnaire_reseau/gestionnaire_reseau.php'); // Redirection vers la page du gestionnaire de réseau
                exit();
            } elseif ($row['role_id'] == 3) { // Cycliste
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role_id'] = $row['role_id'];
                $_SESSION['prenom'] = $row['prenom'];
                header('Location: cycliste/trajet_cycliste.php'); // Redirection vers la page cycliste
                exit();
            } else {
                $error = "Vous n'avez pas les droits d'accès.";
            }
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Email ou mot de passe incorrect.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mb-4">Page de connexion </h2>
                <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
