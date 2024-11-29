// Définition de variables globales
let projection;
let activeIncidents = []; // Initialiser activeIncidents comme un tableau vide
const stops = {
    Stop1: { lat: 48.8566, lon: 2.3522 }, // Paris
    Stop2: { lat: 48.8584, lon: 2.2945 }, // Tour Eiffel
    Stop3: { lat: 48.8738, lon: 2.295 }, // Arc de Triomphe
    Stop4: { lat: 48.8606, lon: 2.3376 } // Louvre
};

// Dimensions de la carte
const width = window.innerWidth;
const height = window.innerHeight;

// Initialisation de l'élément SVG pour la carte
const svg = d3.select("#map").append("svg")
    .attr("width", width)
    .attr("height", height)
    .attr("viewBox", `0 0 ${width} ${height}`);

// Groupe principal pour le contenu de la carte
const g = svg.append("g")
    .attr("class", "everything");

// Fonction de zoom et de dézoom
const zoom = d3.zoom()
    .scaleExtent([0.1, 5])
    .on("zoom", (event) => {
        g.attr("transform", event.transform);
    });

svg.call(zoom);

// Initialisation du tooltip
const tooltip = d3.select("body").append("div")
    .attr("class", "tooltip")
    .style("position", "absolute")
    .style("background-color", "white")
    .style("padding", "5px")
    .style("border", "1px solid #ddd")
    .style("border-radius", "3px")
    .style("display", "none");

// Fonction principale pour initialiser la carte
function initMap() {
    console.log("Initialisation de la carte");

    if (!window.ruesEtArrets) {
        console.error("Les données des rues et arrêts ne sont pas disponibles.");
        return;
    }

    // Initialisation de la projection Mercator pour Paris
    projection = d3.geoMercator()
        .center([2.3522, 48.8566]) // Centre de Paris
        .scale(1000000) // Ajustement de l'échelle pour le métro
        .translate([width / 2, height / 2]);

    const nodes = [];
    const links = [];
    const nodeById = {};
    const stopLines = {}; // Stocker les lignes auxquelles appartient chaque arrêt

    // Créer un dictionnaire pour stocker l'ordre des arrêts par rue
    const arretsParRue = {};

    for (const rue in window.ruesEtArrets) {
        const arrets = window.ruesEtArrets[rue].stops.sort((a, b) => a.order - b.order);
        arretsParRue[rue] = arrets;

        for (let i = 0; i < arrets.length; i++) {
            const arret = arrets[i];

            if (!arret || !arret.name || arret.lat == null || arret.lon == null) {
                console.error(`Nom ou coordonnées d'arrêt invalide pour la rue ${rue} à l'indice ${i}.`);
                continue;
            }

            // Créer un nœud pour chaque arrêt s'il n'existe pas déjà
            let existingNode = nodeById[arret.name];
            if (!existingNode) {
                const [x, y] = projection([arret.lon, arret.lat]);
                if (isNaN(x) || isNaN(y)) {
                    console.error(`Projection échouée pour l'arrêt ${arret.name} avec les coordonnées (${arret.lon}, ${arret.lat}).`);
                    continue;
                }

                existingNode = { id: arret.name, type: "stop", x: x, y: y, lat: arret.lat, lon: arret.lon };
                nodes.push(existingNode);
                nodeById[arret.name] = existingNode; // Stocker l'arrêt avec ses coordonnées GPS
            }

            if (!stopLines[arret.name]) {
                stopLines[arret.name] = new Set();
            }
            stopLines[arret.name].add(rue);

            if (i > 0) {
                const previousNode = nodeById[arrets[i - 1].name];
                if (previousNode && existingNode) {
                    links.push({
                        source: previousNode,
                        target: existingNode,
                        street: rue.replace(/\s+/g, '-')
                    });
                }
            }
        }
        drawTours(assignments, stops, projection); // Utilisez la projection définie ici

    }

    // Simulation statique : les nœuds restent fixes
    nodes.forEach(node => {
        // Définir directement la position projetée des nœuds à partir de leurs coordonnées
        const [x, y] = projection([node.lon, node.lat]);
        node.x = x;
        node.y = y;
    });

    const colorScale = d3.scaleOrdinal(d3.schemeCategory10);

    // Créer les liens sans simulation
    const link = g.append("g")
        .attr("class", "links")
        .selectAll("line")
        .data(links)
        .join("line")
        .attr("class", d => `link ${d.street}`)
        .attr("stroke", d => colorScale(d.street))
        .attr("stroke-width", 1.5)
        .attr("opacity", 0.7)
        .attr("x1", d => projection([d.source.lon, d.source.lat])[0])
        .attr("y1", d => projection([d.source.lon, d.source.lat])[1])
        .attr("x2", d => projection([d.target.lon, d.target.lat])[0])
        .attr("y2", d => projection([d.target.lon, d.target.lat])[1]);

    const node = g.append("g")
        .attr("class", "nodes")
        .selectAll("g")
        .data(nodes)
        .join("g");

    // Ajouter des cercles pour représenter les nœuds
    node.append("circle")
        .attr("r", 15)
        .attr("class", "stop")
        .attr("fill", "#1f77b4")
        .attr("stroke", "#333")
        .attr("stroke-width", 1.5)
        .attr("cx", d => d.x) // Utiliser les positions calculées
        .attr("cy", d => d.y)
        .on("mouseover", (event, d) => {
            d3.select(event.currentTarget).attr("fill", "#ff7f0e");
            tooltip.style("display", "block")
                .html(`ID: ${d.id}<br>Latitude: ${d.lat}<br>Longitude: ${d.lon}`);
        })
        .on("mousemove", (event) => {
            tooltip.style("top", `${event.pageY + 10}px`)
                .style("left", `${event.pageX + 10}px`);
        })
        .on("mouseout", (event) => {
            d3.select(event.currentTarget).attr("fill", "#1f77b4");
            tooltip.style("display", "none");
        });

    // Ajouter des étiquettes aux nœuds
    node.append("text")
        .attr("class", "label")
        .text(d => d.id)
        .attr("x", d => d.x + 20) // Décaler les étiquettes horizontalement
        .attr("y", d => d.y + 5) // Décaler les étiquettes verticalement
        .style("font-size", "12px")
        .style("fill", "#333")
        .style("pointer-events", "none");


    function ticked() {
        link.attr("x1", d => d.source.x)
            .attr("y1", d => d.source.y)
            .attr("x2", d => d.target.x)
            .attr("y2", d => d.target.y);

        node.attr("transform", d => `translate(${d.x},${d.y})`);
    }
}

console.log("Données envoyées :", JSON.stringify({ incidents: activeIncidents }));

function recalculateTours(activeIncidents) {
    fetch('http://localhost/projet_poubelle_verte/gestionnaire_reseau/api/recalculate_tours.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ incidents: activeIncidents }) // Envoyer les incidents
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                console.log("Tournées recalculées avec succès : ", data.newTours);

                // Transformez les arrêts en tableau si nécessaire
                const stopsArray = Object.entries(stops).map(([id, stop]) => ({
                    id,
                    ...stop
                }));

                // Recalculer les assignations avec les nouvelles données
                const assignments = assignStopsToCyclists(cyclists, stopsArray, graph, activeIncidents);
                console.log("Assignations recalculées :", JSON.stringify(assignments, null, 2));

                // Mettre à jour la carte avec les nouvelles tournées
                updateMap(assignments);
            } else {
                console.error('Erreur de recalcul des tournées : ', data.message);
            }
        })
        .catch(error => console.error("Erreur réseau lors du recalcul :", error));
}


function updateMap(newTours) {
    // Supprime les anciennes lignes de tournées
    g.selectAll(".tour-line").remove();

    // Redessine les tournées recalculées
    drawTours(newTours, stops, projection);
}


// Initialiser la carte après le chargement des données
document.addEventListener("DOMContentLoaded", function () {
    fetch("get_streets_data.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur réseau lors de la récupération des données.");
            }
            return response.json();
        })
        .then(data => {
            if (!data || Object.keys(data).length === 0) {
                throw new Error("Données des rues et arrêts vides ou incorrectes.");
            }
            window.ruesEtArrets = data;
            initMap();
        })
        .catch(error => console.error("Erreur lors du chargement des données : ", error));
});

// Calculer les distances entre les arrêts
function calculateDistances(stops) {
    const graph = {};
    const earthRadius = 6371; // Rayon de la Terre en kilomètres

    for (const from in stops) {
        graph[from] = {};
        for (const to in stops) {
            if (from !== to) {
                const latFrom = (stops[from].lat * Math.PI) / 180;
                const lonFrom = (stops[from].lon * Math.PI) / 180;
                const latTo = (stops[to].lat * Math.PI) / 180;
                const lonTo = (stops[to].lon * Math.PI) / 180;

                const latDelta = latTo - latFrom;
                const lonDelta = lonTo - lonFrom;

                const a =
                    Math.sin(latDelta / 2) ** 2 +
                    Math.cos(latFrom) *
                    Math.cos(latTo) *
                    Math.sin(lonDelta / 2) ** 2;
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                const distance = earthRadius * c;
                graph[from][to] = distance;
            }
        }
    }

    return graph;
}

// Implémentation de Dijkstra
function dijkstra(graph, start) {
    const distances = {};
    const previous = {};
    const queue = new Set(Object.keys(graph));

    // Initialiser les distances
    for (const node in graph) {
        distances[node] = Infinity;
        previous[node] = null;
    }
    distances[start] = 0;

    while (queue.size > 0) {
        // Trouver le nœud avec la distance minimale
        let current = null;
        for (const node of queue) {
            if (current === null || distances[node] < distances[current]) {
                current = node;
            }
        }

        queue.delete(current);

        for (const neighbor in graph[current]) {
            const alt = distances[current] + graph[current][neighbor];
            if (alt < distances[neighbor]) {
                distances[neighbor] = alt;
                previous[neighbor] = current;
            }
        }
    }

    return { distances, previous };
}

// Reconstruire le chemin optimal
function reconstructPath(previous, target) {
    const path = [];
    while (target) {
        path.unshift(target);
        target = previous[target];
    }
    return path;
}

function assignStopsToCyclists(cyclists, stops, graph, incidents) {
    // Si incidents n'est pas un tableau, l'initialiser vide
    if (!Array.isArray(incidents)) {
        console.error("Les incidents ne sont pas un tableau valide.");
        incidents = []; // Initialiser incidents comme tableau vide
    }

    const blockedStops = incidents.map(incident => incident.stop_id);
    const validStops = stops.filter(stop => !blockedStops.includes(stop.id));

    const assignments = {};
    cyclists.forEach(cyclist => {
        if (validStops.length > 0) {
            assignments[cyclist] = [];
            let currentStop = validStops.shift(); // Assigner le premier arrêt
            assignments[cyclist].push(currentStop);

            while (validStops.length > 0) {
                let nextStop = null;
                let minDistance = Infinity;

                validStops.forEach(stop => {
                    const distance = graph[currentStop.id]?.[stop.id] ?? Infinity;
                    if (distance < minDistance) {
                        minDistance = distance;
                        nextStop = stop;
                    }
                });

                if (nextStop) {
                    assignments[cyclist].push(nextStop);
                    validStops.splice(validStops.indexOf(nextStop), 1);
                    currentStop = nextStop;
                } else {
                    break;
                }
            }
        }
    });

    return assignments;
}

// Calculer le graphe des distances
const graph = calculateDistances(stops);

// Exécuter Dijkstra pour un point de départ spécifique
const start = "Stop1";
const end = "Stop3";
const { distances, previous } = dijkstra(graph, start);

// Chemin optimal pour aller du point de départ à une destination
const path = reconstructPath(previous, end);
console.log("Chemin optimal:", path);

// Assignation des arrêts aux cyclistes
const cyclists = ["Cycliste1", "Cycliste2"];

// Afficher les tournées sur la carte
function drawTours(tours, stops, projection) {
    if (!projection) {
        console.error("La projection n'est pas définie.");
        return;
    }

    const colorScale = d3.scaleOrdinal(d3.schemeCategory10);

    for (const [cyclist, tour] of Object.entries(tours)) {
        const pathData = tour.map(stop => {
            const { lon, lat } = stops[stop.id]; // Identifier correctement les arrêts
            return projection([lon, lat]);
        });

        g.append("path")
            .datum(pathData)
            .attr("d", d3.line())
            .attr("fill", "none")
            .attr("stroke", colorScale(cyclist))
            .attr("stroke-width", 2)
            .attr("class", "tour-line");

        d3.select(".legend")
            .append("div")
            .style("color", colorScale(cyclist))
            .text(`${cyclist}`);
    }
}

const stopsArray = Object.entries(stops).map(([id, stop]) => ({
    id,
    ...stop
}));

// Créer les assignations pour les cyclistes
const assignments = assignStopsToCyclists(cyclists, stopsArray, graph, []);
console.log("Assignations des arrêts :", JSON.stringify(assignments, null, 2));


function exportToursToJson(tours) {
    const blob = new Blob([JSON.stringify(tours, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "tours.json";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Bouton pour exporter
document.getElementById("export-tours").addEventListener("click", () => {
    exportToursToJson(assignments);
});

function exportToursToJson(tours) {
    const blob = new Blob([JSON.stringify(tours, null, 2)], { type: "application/json" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "tours.json";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Bouton pour exporter
document.getElementById("export-tours").addEventListener("click", () => {
    exportToursToJson(assignments);
});

document.getElementById("recalculate-button").addEventListener("click", () => {
    // Exemple d'incidents actifs (à remplacer par des données dynamiques si nécessaire)
    const activeIncidents = [
        { stop_id: "Stop2", type: "rue_bloquee" } // Exemple de données
    ];

    // Vérifier que les incidents sont correctement définis pour le débogage
    console.log("Incidents actifs envoyés :", activeIncidents);

    fetch('http://localhost/projet_poubelle_verte/gestionnaire_reseau/api/recalculate_tours.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ incidents: activeIncidents }) // Envoyer les incidents actifs
    })
        .then(response => {
            // Vérification de la réponse du serveur
            if (!response.ok) {
                throw new Error(`Erreur HTTP ! Statut : ${response.status}`);
            }
            return response.json(); // Parse la réponse JSON
        })
        .then(data => {
            // Vérification du statut dans les données reçues
            if (data.status === 'success') {
                console.log("Tournées recalculées :", data.newTours);
                updateMap(data.newTours); // Met à jour la carte avec les nouvelles tournées
            } else {
                console.error('Erreur dans la réponse JSON :', data.message);
            }
        })
        .catch(error => {
            // Gestion des erreurs réseau ou JSON
            console.error('Erreur réseau ou JSON :', error);
        });
});
