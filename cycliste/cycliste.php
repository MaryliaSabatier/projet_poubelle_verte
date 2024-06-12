<?php
session_start();

// Vérification de la connexion et du rôle de cycliste
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) { // 3 = ID du rôle cycliste
    header('Location: ../login.php');
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

// Récupération de la tournée en cours du cycliste (s'il y en a une)
$sqlTournee = "SELECT t.*, v.numero AS numero_velo 
                FROM tournees t
                INNER JOIN velos v ON t.velo_id = v.id
                WHERE t.cycliste_id = ? AND t.etat = 'en_cours'";
$stmtTournee = $conn->prepare($sqlTournee);
$stmtTournee->bind_param("i", $_SESSION['user_id']);
$stmtTournee->execute();
$resultTournee = $stmtTournee->get_result();
$tournee = $resultTournee->fetch_assoc();

// Récupération des points de passage de la tournée (s'il y en a une)
if ($tournee) {
    $sqlPointsPassage = "SELECT pp.*, a.rue_id, r.nom AS nom_rue
                        FROM points_passage pp
                        INNER JOIN arrets a ON pp.arret_id = a.id
                        INNER JOIN rues r ON a.rue_id = r.id
                        WHERE pp.tournee_id = ?
                        ORDER BY pp.ordre_passage";
    $stmtPointsPassage = $conn->prepare($sqlPointsPassage);
    $stmtPointsPassage->bind_param("i", $tournee['id']);
    $stmtPointsPassage->execute();
    $resultPointsPassage = $stmtPointsPassage->get_result();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Interface Cycliste</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 400px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Bienvenue, <?php echo $_SESSION['prenom']; ?>!</h2>
        <a href="../logout.php" class="btn btn-danger logout-btn">Déconnexion</a>

        <?php if ($tournee): ?>
            <h3>Tournée en cours (Vélo <?php echo $tournee['numero_velo']; ?>)</h3>

            <h4>Itinéraire :</h4>
            <ol>
                <?php while ($point = $resultPointsPassage->fetch_assoc()): ?>
                    <li><?php echo $point['nom_rue']; ?></li>
                <?php endwhile; ?>
            </ol>

            <h4>Prochain arrêt :</h4>
            <?php 
            // Logique pour déterminer le prochain arrêt 
            // ...
            ?>

            <h4>Arrêt précédent :</h4>
            <?php 
            // Logique pour déterminer l'arrêt précédent 
            // ...
            ?>

            <div id="map"></div>

            <script>
                // Initialisation de la carte (à adapter avec vos coordonnées)
                var map = L.map('map').setView([48.8566, 2.3522], 13); 

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Ajout des marqueurs pour les points de passage et la position actuelle (à implémenter)
                // ...
            </script>

            <button id="startStopButton" class="btn btn-primary">Terminer la tournée</button>
            <script>
                // Gestion du bouton démarrer/arrêter (à implémenter)
                // ...
            </script>

        <?php else: ?>
            <p>Aucune tournée en cours.</p>
        <?php endif; ?>
    </div>
</body>
</html>
