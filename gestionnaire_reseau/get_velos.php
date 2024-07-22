<?php
// get_velos.php
session_start();

// Vérification de la connexion et du rôle de gestionnaire de réseau
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Connexion à la base de données (à adapter avec vos informations)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "poubelle_verte";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des informations des vélos
$sql = "SELECT id, numero, etat, autonomie_km, date_derniere_revision FROM velos";
$result = $conn->query($sql);

$velos = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $velos[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($velos);
?>
