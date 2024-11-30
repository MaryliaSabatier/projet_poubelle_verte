// Initialiser la carte
var map = L.map('map').setView([48.8566, 2.3522], 12);

// Ajouter les tuiles OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
    maxZoom: 20,
    attribution: '&copy; OpenStreetMap France | Données &copy; contributeurs OpenStreetMap'
}).addTo(map);

// Définir des couleurs pour les itinéraires des différents agents
var colors = ['red', 'blue', 'green', 'orange', 'purple', 'brown', 'cyan', 'magenta'];

// Charger les arrêts et itinéraires depuis le backend
fetch('calcul_itineraires.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`Erreur HTTP : ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Les données des arrêts et des itinéraires
        var stops = data.stops;
        var itineraries = data.itineraries;

        // Ajouter les arrêts sur la carte
        for (var stopName in stops) {
            var stop = stops[stopName];
            L.circleMarker([stop.lat, stop.lng], {
                radius: 5,
                fillColor: '#0000FF',
                color: '#0000FF',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            })
            .bindPopup(`<b>${stopName}</b>`)
            .addTo(map);
        }

        // Ajouter les itinéraires sur la carte
        var agentIndex = 0; // Pour itérer les couleurs
        for (var agent in itineraries) {
            var routes = itineraries[agent];

            routes.forEach((route, routeIndex) => {
                var latlngs = [];
                route.forEach(stopName => {
                    if (stops[stopName]) {
                        latlngs.push([stops[stopName].lat, stops[stopName].lng]);
                    }
                });

                // Ajouter une polyline pour cet itinéraire
                var polyline = L.polyline(latlngs, {
                    color: colors[agentIndex % colors.length],
                    weight: 3,
                    opacity: 0.7
                }).addTo(map);

                // Ajouter une info-bulle à la polyline
                polyline.bindPopup(
                    `<b>Agent ${parseInt(agent) + 1} - Route ${routeIndex + 1}</b>`
                );
            });

            agentIndex++;
        }
    })
    .catch(error => console.error('Erreur lors du chargement des données :', error));
