async function fetchData(url) {
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error(`Erreur HTTP! statut: ${response.status}`);
    }
    return await response.json();
}

// Fonction pour initialiser la carte avec Leaflet.js
async function initLeafletMap() {
    console.log("Initialisation de la carte Leaflet");

    try {
        const ruesResponse = await fetchData('api/arrets.php');
        console.log("Données des arrêts brutes :", ruesResponse);
        const rues = ruesResponse.data; // Extraire le tableau réel
        console.log("Données des arrêts (rues):", rues);

        const ruesArretsResponse = await fetchData('api/rues_arrets.php');
        console.log("Données brutes rues/arrets :", ruesArretsResponse);
        const ruesArrets = ruesArretsResponse.data; // Extraire le tableau réel
        console.log("Données rues/arrets :", ruesArrets);

        // Vérification des données
        if (!Array.isArray(rues)) {
            console.error("Les données des arrêts (rues) ne sont pas au format attendu.");
            return;
        }

        if (!Array.isArray(ruesArrets)) {
            console.error("Les données rues/arrets ne sont pas au format attendu.");
            return;
        }

        // Initialisation de la carte Leaflet
        const map = L.map('map').setView([48.8566, 2.3522], 13); // Centre sur Paris avec un zoom de 13

        // Ajouter le fond de carte OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Affichage des rues et arrêts sur la carte
        afficherCarte(map, rues, ruesArrets);
    } catch (error) {
        console.error("Erreur lors de l'initialisation de la carte :", error);
    }
}


function afficherCarte(map, rues, ruesArrets) {
    const arretsCoords = {};
    const groupedPoints = {}; // Regrouper les arrêts par rue
    const ligneColors = {}; // Couleurs pour les différentes rues

    // Construire un dictionnaire des coordonnées des arrêts
    rues.forEach(arret => {
        if (!arret.latitude || !arret.longitude) {
            console.warn(`Arrêt ${arret.id} sans coordonnées valides.`);
        } else {
            arretsCoords[arret.id] = {
                lat: parseFloat(arret.latitude),
                lon: parseFloat(arret.longitude),
                libelle: arret.libelle
            };
        }
    });

    // Regrouper les arrêts par rue
    ruesArrets.forEach(rueArret => {
        const rue = rueArret.rue.libelle; // Nom de la rue
        const arret = rueArret.arret;
        const coords = arretsCoords[arret.id];
    
        if (!coords) {
            console.warn(`Coordonnées non trouvées pour l'arrêt : ${arret.id}`);
            return;
        }
    
        if (!groupedPoints[rue]) {
            groupedPoints[rue] = [];
        }
        groupedPoints[rue].push([coords.lat, coords.lon]);
    
        console.log(`Régroupement : Rue = ${rue}, Arrêt = ${arret.id}, Coordonnées = `, coords);
    });
    
    // Définir une palette de couleurs pour les rues
    const colorPalette = [
        '#FF0000', '#00FF00', '#0000FF', '#FFFF00', '#FF00FF', '#00FFFF',
        '#800000', '#808000', '#008000', '#800080', '#008080', '#000080',
        '#FFA500', '#A52A2A', '#8A2BE2', '#5F9EA0', '#7FFF00', '#D2691E',
        '#FF7F50', '#6495ED', '#DC143C', '#00FA9A', '#8B0000', '#00CED1'
    ];
    let colorIndex = 0;

    // Parcourir les rues regroupées et afficher sur la carte
    for (const rue in groupedPoints) {
        const polylinePoints = groupedPoints[rue];
        const color = ligneColors[rue] || colorPalette[colorIndex++ % colorPalette.length];

        // Ajouter une polyligne pour représenter la rue
        const polyline = L.polyline(polylinePoints, { color: color, weight: 4, opacity: 0.8 })
            .addTo(map)
            .bindPopup(`<strong>${rue}</strong>`);

        // Ajouter des marqueurs pour chaque arrêt de la rue
        polylinePoints.forEach(point => {
            const marker = L.circleMarker(point, {
                radius: 6,
                fillColor: color,
                color: '#000',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            // Ajouter une popup avec les détails de l'arrêt
            const arretId = Object.keys(arretsCoords).find(id =>
                arretsCoords[id].lat === point[0] && arretsCoords[id].lon === point[1]);

            const arretLibelle = arretsCoords[arretId]?.libelle || 'Nom inconnu';
            marker.bindPopup(`<strong>Arrêt :</strong> ${arretLibelle}<br><strong>Rue :</strong> ${rue}`);
        });
    }
}


// Initialisation de la carte après le chargement du DOM
document.addEventListener('DOMContentLoaded', initLeafletMap);
