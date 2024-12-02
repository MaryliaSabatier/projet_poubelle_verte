<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérification de la connexion et du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header('Location: ../login.php');
    exit();
}

// Connexion à la base de données
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupération de la tournée en cours
$sqlTournee = "SELECT t.*, tc.velo_id, v.numero AS numero_velo
                FROM tournees t
                INNER JOIN tournees_cyclistes tc ON t.id = tc.tournee_id
                INNER JOIN velos v ON tc.velo_id = v.id
                WHERE tc.cycliste_id = ? AND t.etat = 'en_cours'";

$stmtTournee = $conn->prepare($sqlTournee);
$stmtTournee->bind_param("i", $_SESSION['user_id']);
$stmtTournee->execute();
$resultTournee = $stmtTournee->get_result();
$tournee = $resultTournee->fetch_assoc();

// Récupération des points de passage
if ($tournee) {
    $sqlPointsPassage = "SELECT pp.*, a.libelle  AS nom_arret, a.latitude, a.longitude
                         FROM points_passage pp
                         INNER JOIN arrets a ON pp.arret_id = a.id
                         WHERE pp.tournee_id = ?
                         ORDER BY pp.ordre_passage";
    $stmtPointsPassage = $conn->prepare($sqlPointsPassage);
    $stmtPointsPassage->bind_param("i", $tournee['id']);
    $stmtPointsPassage->execute();
    $pointsPassage = $stmtPointsPassage->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($pointsPassage)) {
        echo '<div class="alert alert-warning">Aucun point de passage disponible pour cette tournée.</div>';
    }
}

$sqlVerification = "SELECT tc.*, t.etat, t.date 
                    FROM tournees_cyclistes tc
                    INNER JOIN tournees t ON tc.tournee_id = t.id
                    WHERE tc.cycliste_id = ? AND t.etat = 'en_cours'";
$stmtVerification = $conn->prepare($sqlVerification);
$stmtVerification->bind_param("i", $_SESSION['user_id']);
$stmtVerification->execute();
$resultVerification = $stmtVerification->get_result();

if ($resultVerification->num_rows === 0) {
    echo '<div class="alert alert-warning">Aucune tournée active associée à votre compte.</div>';
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Cycliste</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        #map {
            height: 400px;
        }

        .next-stop {
            font-weight: bold;
            color: green;
        }

        .previous-stop {
            font-weight: bold;
            color: red;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-3">Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom']); ?>!</h2>
        <a href="../logout.php" class="btn btn-danger mb-3">Déconnexion</a>

        <?php if ($tournee): ?>
            <h3>Tournée en cours</h3>
            <p><strong>Vélo :</strong> <?php echo $tournee['numero_velo']; ?></p>

            <h4>Itinéraire :</h4>
            <ol>
                <?php foreach ($pointsPassage as $point): ?>
                    <li><?php echo htmlspecialchars($point['nom_arret']); ?></li>
                <?php endforeach; ?>
            </ol>

            <h4>Position Actuelle :</h4>
            <p id="current-location">Récupération de votre position...</p>

            <h4>Prochain arrêt :</h4>
            <p id="next-stop" class="next-stop">Calcul en cours...</p>

            <h4>Arrêt précédent :</h4>
            <p id="previous-stop" class="previous-stop">Calcul en cours...</p>

            <div id="map" class="mt-4 rounded border"></div>
        <?php else: ?>
            <?php if (!$tournee): ?>
                <div class="alert alert-info">Aucune tournée en cours. Veuillez attendre l'attribution d'une tournée.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Vérifier si le conteneur de la carte existe
        if (document.getElementById('map')) {
            // Initialisation de la carte
            var map = L.map('map').setView([48.8566, 2.3522], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Points de passage
            // Points de passage
            var pointsPassage = <?php echo json_encode($pointsPassage); ?>;

            // Tableau des coordonnées pour tracer la polyligne
            var latlngs = [];
            var markers = [];

            // Ajout des marqueurs pour chaque arrêt
            pointsPassage.forEach((point) => {
                latlngs.push([point.latitude, point.longitude]);

                var marker = L.marker([point.latitude, point.longitude]).addTo(map);
                marker.bindPopup(`<b>${point.nom_arret}</b>`);
            });

            if (latlngs.length > 0) {
                var polyline = L.polyline(latlngs, {
                    color: colors[agentIndex % colors.length],
                    weight: 3,
                    opacity: 0.7
                });
                polyline.bindPopup("<b>Agent " + (parseInt(agent) + 1) + " - Route " + (i + 1) + "</b>");
                agentLayerGroup.addLayer(polyline);
            } else {
                console.warn("Itinéraire vide pour cet agent.");
            }


            if (pointsPassage.length > 0) {
                pointsPassage.forEach((point) => {
                    // Ajout des coordonnées
                    latlngs.push([point.latitude, point.longitude]);

                    // Création des marqueurs
                    var marker = L.marker([point.latitude, point.longitude]).addTo(map);
                    marker.bindPopup(`<b>Arrêt :</b> ${point.nom_arret}`);
                    markers.push(marker);
                });

                // Vérifier si des coordonnées sont disponibles avant de tracer
                if (latlngs.length > 0) {
                    // Tracer la polyligne
                    var polyline = L.polyline(latlngs, {
                        color: 'blue',
                        weight: 5,
                        opacity: 0.7
                    }).addTo(map);

                    // Zoomer sur la polyligne
                    map.fitBounds(polyline.getBounds());
                } else {
                    console.warn("Aucun point de passage disponible pour tracer l'itinéraire.");
                    alert("Aucun itinéraire à afficher pour cette tournée.");
                }
            }

            function calculateNextAndPreviousStops(lat, lng) {
                let closestIndex = 0;
                let minDistance = Infinity;

                pointsPassage.forEach((point, index) => {
                    const distance = Math.sqrt(Math.pow(point.latitude - lat, 2) + Math.pow(point.longitude - lng, 2));
                    if (distance < minDistance) {
                        minDistance = distance;
                        closestIndex = index;
                    }
                });

                const previousStop = pointsPassage[closestIndex - 1] || null;
                const nextStop = pointsPassage[closestIndex + 1] || null;

                document.getElementById('next-stop').textContent = nextStop ? nextStop.nom_arret : "Aucun";
                document.getElementById('previous-stop').textContent = previousStop ? previousStop.nom_arret : "Aucun";

                // Mettre en surbrillance le prochain arrêt
                if (nextStop && markers[closestIndex + 1]) {
                    markers[closestIndex + 1].openPopup();
                }
            }

            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(updatePosition, (error) => {
                    if (error.code === 1) {
                        alert("La géolocalisation est désactivée. Activez-la pour suivre votre position en temps réel.");
                    } else {
                        console.error("Erreur de géolocalisation : ", error.message);
                    }
                }, {
                    enableHighAccuracy: true
                });
            } else {
                alert("La géolocalisation n'est pas prise en charge par votre navigateur.");
            }

        }
    </script>
</body>

</html>