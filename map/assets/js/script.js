console.log("Script.js is loaded");

const WINTER_MODE = true; // Active ou désactive le mode hiver

let velos = [
    { id: "Velo1", position: "Porte d'Ivry", autonomie: WINTER_MODE ? 45 : 50, charge: 0, capacite: 200, distanceParcourue: 0, feuxRencontres: 0, isBroken: false, currentLink: null, tournee: [], assignedStops: new Set(), isMoving: false },
];

const nodes = [];
const links = [];
const nodeById = {};
const completedTours = []; // Historique des tournées terminées
const unvisitedStops = new Set(); // Ensemble des arrêts non visités

const ruesTraitees = {}; // Pour stocker les informations des rues traitées
const pointsDePassageTraites = {}; // Pour stocker les informations des points de passage traités

const width = window.innerWidth;
const height = window.innerHeight;

const depotIvry = { id: "Porte d'Ivry", type: "depot", x: width / 2, y: height / 2 };
nodes.push(depotIvry);
nodeById["Porte d'Ivry"] = depotIvry;

console.log("Initial nodes:", nodes);

// Définir la projection Mercator
const projection = d3.geoMercator()
    .center([2.3522, 48.8566]) // Centrer sur Paris
    .scale(150000) // Ajustez l'échelle selon vos besoins
    .translate([width / 2, height / 2]); // Centrer la projection

for (const rue in ruesEtArrets) {
    const arrets = ruesEtArrets[rue].stops;

    for (let i = 0; i < arrets.length; i++) {
        const arret = arrets[i];

        if (!arret || !arret.name) {
            console.error(`Nom d'arrêt invalide pour la rue ${rue} à l'indice ${i}.`);
            continue;
        }

        let existingNode = nodeById[arret.name];
        if (!existingNode) {
            // Utiliser la projection pour obtenir les coordonnées x et y
            const [x, y] = projection([arret.lon, arret.lat]);

            existingNode = { id: arret.name, type: "stop", x: x, y: y };

            const ruesConnectees = Object.values(ruesEtArrets).filter(rue => rue.stops.some(s => s.name === arret.name));
            if (ruesConnectees.length === 1) {
                existingNode.isImpasse = true;
            }

            nodes.push(existingNode);
            nodeById[arret.name] = existingNode;
            unvisitedStops.add(arret.name);
        }

        if (i > 0) {
            links.push({
                source: nodeById[arrets[i - 1].name],
                target: existingNode,
                distance: ruesEtArrets[rue].distances[i - 1],
                trafficLights: ruesEtArrets[rue].trafficLights[i - 1] || 0,
                street: rue.replace(/\s+/g, '-')
            });
        }
    }
}

console.log("Nodes after processing:", nodes);
console.log("Links after processing:", links);

const svg = d3.select("#map").append("svg")
    .attr("width", width)
    .attr("height", height)
    .attr("viewBox", `0 0 ${width} ${height}`);

const g = svg.append("g")
    .attr("class", "everything");

const zoom = d3.zoom()
    .scaleExtent([0.2, 5]) // Autoriser un zoom minimum et maximum
    .on("zoom", (event) => {
        g.attr("transform", event.transform); // Permettre le zoom et le déplacement
    });

const link = g.append("g")
    .attr("class", "links")
    .selectAll("line")
    .data(links)
    .join("line")
    .attr("class", d => `link ${d.street}`);
console.log("Liens entre arrêts :", links);

const node = g.append("g")
    .attr("class", "nodes")
    .selectAll("g")
    .data(nodes)
    .join("g");

node.append("circle")
    .attr("r", d => d.type === "depot" ? 10 : (d.isImpasse ? 3 : 5))
    .attr("class", d => d.type);

node.append("text")
    .attr("class", "label")
    .text(d => d.id)
    .attr("x", 6)
    .attr("y", 3);

const simulation = d3.forceSimulation(nodes)
    .force("link", d3.forceLink(links).id(d => d.id).distance(150))
    .force("charge", d3.forceManyBody().strength(-300))
    .force("center", d3.forceCenter(width / 2, height / 2))
    .on("tick", ticked);

function ticked() {
    link
        .attr("x1", d => d.source.x)
        .attr("y1", d => d.source.y)
        .attr("x2", d => d.target.x)
        .attr("y2", d => d.target.y);

    node
        .attr("transform", d => `translate(${d.x},${d.y})`);

    velo.select("circle")
        .attr("cx", d => nodeById[d.position]?.x || 0)
        .attr("cy", d => nodeById[d.position]?.y || 0);
}

const velo = g.append("g")
    .attr("class", "velos")
    .selectAll("g")
    .data(velos)
    .join("g")
    .attr("class", "velo")
    .attr("id", d => d.id); // Ajouter un identifiant unique basé sur l'id du vélo

// Création des éléments pour les vélos
velo.append("circle")
    .attr("r", 20) // Cercle plus grand pour représenter les vélos
    .attr("cx", d => nodeById["Porte d'Ivry"].x)  // Utiliser la position de Porte d'Ivry pour tous les vélos au départ
    .attr("cy", d => nodeById["Porte d'Ivry"].y);

velo.append("text")
    .attr("x", d => (nodeById[d.position]?.x || 0) + 12) // Décale le texte à droite du cercle
    .attr("y", d => nodeById[d.position]?.y || 0)
    .attr("dy", ".35em")
    .text(d => d.id);

// Fonction de calcul de Dijkstra pour trouver le chemin le plus court
function dijkstra(graph, startNode) {
    const distances = {};
    const previousNodes = {};
    const unvisitedNodes = new Set(Object.keys(graph.nodes));

    // Initialisation des distances
    for (const nodeId of unvisitedNodes) {
        distances[nodeId] = Infinity;
        previousNodes[nodeId] = null;
    }
    distances[startNode] = 0;

    while (unvisitedNodes.size > 0) {
        const currentNode = [...unvisitedNodes].reduce((closestNode, nodeId) => {
            return distances[nodeId] < distances[closestNode] ? nodeId : closestNode;
        }, [...unvisitedNodes][0]);

        unvisitedNodes.delete(currentNode);

        // Parcourir les voisins de l'actuel
        for (const link of graph.links.filter(link => link.source.id === currentNode)) {
            const neighbor = link.target.id;
            const newDist = distances[currentNode] + link.distance + (link.trafficLights / 20);
            if (newDist < distances[neighbor]) {
                distances[neighbor] = newDist;
                previousNodes[neighbor] = currentNode;
            }
        }
    }

    return { distances, previousNodes };
}

// Fonction pour diviser les arrêts entre les vélos
function divideStopsAmongVelos() {
    const stopArray = Array.from(unvisitedStops);
    if (!stopArray || stopArray.length === 0) {
        console.log("Aucun arrêt non visité.");
        return;
    }

    const stopsPerVelo = Math.ceil(stopArray.length / velos.length);

    velos.forEach((velo, index) => {
        const start = index * stopsPerVelo;
        const end = start + stopsPerVelo;
        const assignedStops = stopArray.slice(start, end);

        // Assurez-vous que velo.assignedStops est bien un Set
        if (!(velo.assignedStops instanceof Set)) {
            velo.assignedStops = new Set();
        }

        assignedStops.forEach(stop => {
            velo.assignedStops.add(stop);
        });
    });
}

// Fonction d'itinéraire du vélo
function calculateRoutesForVelos() {
    for (const velo of velos) {
        // Vérifier si le vélo est en panne ou n'a pas d'arrêts assignés
        if (velo.isBroken || !velo.assignedStops || velo.assignedStops.size === 0) {
            console.log(`${velo.id} ne peut pas se déplacer car il est en panne ou n'a pas d'arrêt assigné`);
            continue;
        }

        if (velo.autonomie > 0 && velo.charge < velo.capacite && !velo.isMoving) {
            const currentPositionId = velo.position;
            console.log(`${velo.id} est actuellement à ${currentPositionId}`);

            // Obtenir l'arrêt suivant dans la tournée assignée
            const nextStop = getNextStopFromTournee(velo);
            if (!nextStop) {
                console.log(`Pas d'arrêt suivant trouvé pour ${velo.id}`);
                continue;
            }

            console.log(`Arrêt suivant pour ${velo.id}: ${nextStop}`);

            // Trouver le chemin le plus court vers l'arrêt suivant
            const { distances, previousNodes } = dijkstra({ nodes: nodeById, links: links }, currentPositionId);
            const path = reconstructPath(previousNodes, nextStop);

            // Se déplacer vers le prochain arrêt si le chemin est valide
            if (path.length > 1) {
                const nextStopOnPath = path[1]; // Prochain arrêt réel
                moveVelo(velo, nodeById[nextStopOnPath]); // Déplacer le vélo
            }
        }
    }
}

// Fonction pour obtenir l'arrêt suivant
function getNextStopFromTournee(velo) {
    // Vérifiez d'abord si assignedStops existe et est itérable
    if (!velo.assignedStops || !(velo.assignedStops instanceof Set) || velo.assignedStops.size === 0) {
        console.log(`${velo.id} n'a pas d'arrêts assignés ou la collection n'est pas valide.`);
        return null;
    }
    
    const stopsArray = Array.from(velo.assignedStops);
    
    if (stopsArray.length === 0) {
        console.log(`${velo.id} n'a pas d'arrêts à visiter.`);
        return null;
    }
    
    return stopsArray[0]; // Prendre le premier arrêt dans la liste
}

divideStopsAmongVelos();

// Définir initialTransform pour dézoomer la carte au chargement
const initialTransform = d3.zoomIdentity
    .translate(width / 2, height / 2)
    .scale(0.5);  // Dézoome initialement

svg.call(zoom.transform, initialTransform);
svg.call(zoom);

function moveVelo(velo, targetNode) {
    const currentPosition = nodeById[velo.position]; // Position actuelle du vélo
    if (!currentPosition || !targetNode) {
        console.error(`Position ou cible manquante pour le vélo ${velo.id}`);
        return;
    }

    const x1 = currentPosition.x;
    const y1 = currentPosition.y;
    const x2 = targetNode.x;
    const y2 = targetNode.y;

    console.log(`Déplacement du vélo ${velo.id} de (${x1}, ${y1}) à (${x2}, ${y2})`);

    const distance = Math.sqrt((x2 - x1) ** 2 + (y2 - y1) ** 2); // Calculer la distance
    const duration = distance * 100; // Ajuster la durée en fonction de la distance

    velo.isMoving = true; // Le vélo est en train de se déplacer

    // Démarrer une transition D3 pour déplacer le vélo
    d3.select(`#${velo.id} circle`)
        .transition()
        .duration(duration)
        .attr("cx", x2)
        .attr("cy", y2)
        .on("start", () => console.log(`Déplacement du ${velo.id} vers ${targetNode.id}`))
        .on("end", () => {
            // Mettre à jour la position du vélo après déplacement
            velo.position = targetNode.id;
            velo.distanceParcourue += distance;
            velo.autonomie -= distance; // Réduire l'autonomie
            velo.isMoving = false; // Le vélo a terminé son déplacement

            // Gestion de la charge du vélo
            if (velo.position !== "Porte d'Ivry") {
                velo.charge += 50; // Ajouter 50 kg à chaque arrêt
                velo.assignedStops.delete(targetNode.id); // Supprimer l'arrêt visité
            }

            // Si le vélo a atteint sa capacité maximale ou n'a plus assez d'autonomie, retour au dépôt
            if (velo.charge >= velo.capacite || velo.autonomie <= 0) {
                console.log(`${velo.id} retourne au dépôt pour décharger.`);
                moveVelo(velo, nodeById["Porte d'Ivry"]); // Retour au dépôt
                velo.charge = 0; // Réinitialiser la charge après déchargement
                velo.autonomie = WINTER_MODE ? 45 : 50; // Réinitialiser l'autonomie
            } else {
                // Continuer vers le prochain arrêt
                calculateRoutesForVelos();
            }
        });
}


function calculateRoutesForVelos() {
    for (const velo of velos) {
        // Vérifier si le vélo est en panne ou n'a pas d'arrêts assignés
        if (velo.isBroken || velo.assignedStops.size === 0) {
            console.log(`${velo.id} ne peut pas se déplacer car il est en panne ou n'a pas d'arrêt assigné`);
            continue;
        }

        if (velo.autonomie > 0 && velo.charge < velo.capacite && !velo.isMoving) {
            const currentPositionId = velo.position;
            console.log(`${velo.id} est actuellement à ${currentPositionId}`);

            // Obtenir l'arrêt suivant dans la tournée assignée
            const nextStop = getNextStopFromTournee(velo);
            if (!nextStop) {
                console.log(`Pas d'arrêt suivant trouvé pour ${velo.id}`);
                continue;
            }

            console.log(`Arrêt suivant pour ${velo.id}: ${nextStop}`);

            // Trouver le chemin le plus court vers l'arrêt suivant
            const { distances, previousNodes } = dijkstra({ nodes: nodeById, links: links }, currentPositionId);
            const path = reconstructPath(previousNodes, nextStop);

            // Se déplacer vers le prochain arrêt si le chemin est valide
            if (path.length > 1) {
                const nextStopOnPath = path[1]; // Prochain arrêt réel
                moveVelo(velo, nodeById[nextStopOnPath]); // Déplacer le vélo
            }
        }
    }
}

function getNextStopFromTournee(velo) {
    if (!velo.assignedStops || velo.assignedStops.size === 0) {
        console.log(`${velo.id} n'a pas d'arrêts assignés.`);
        return null;
    }
    const stopsArray = Array.from(velo.assignedStops);
    if (stopsArray.length === 0) return null;
    return stopsArray[0]; // Prendre le premier arrêt dans la liste
}


function findNextStopForVelo(currentPosition, velo) {
    const unvisitedStopsArray = Array.from(velo.assignedStops);

    if (unvisitedStopsArray.length === 0 || !currentPosition) {
        return null;
    }

    // Trouver l'arrêt le plus proche
    const closestStop = unvisitedStopsArray.reduce((prev, curr) => {
        const prevDist = heuristicCostEstimate(currentPosition, prev);
        const currDist = heuristicCostEstimate(currentPosition, curr);
        return (currDist < prevDist) ? curr : prev;
    });

    velo.assignedStops.delete(closestStop); // Supprimer l'arrêt visité
    return closestStop;
}

function heuristicCostEstimate(start, goal) {
    const startNode = nodeById[typeof start === 'string' ? start : start.id];
    const goalNode = nodeById[typeof goal === 'string' ? goal : goal.id];

    if (!startNode || !goalNode) {
        console.error(`Nœud manquant pour le calcul heuristique: start = ${start}, goal = ${goal}`);
        return Infinity;
    }

    const dx = startNode.x - goalNode.x;
    const dy = startNode.y - goalNode.y;
    return Math.sqrt(dx * dx + dy * dy);
}


// Fonction d'estimation heuristique de la distance entre deux nœuds
function heuristicCostEstimate(start, goal) {
    const startNode = nodeById[typeof start === 'string' ? start : start.id];
    const goalNode = nodeById[typeof goal === 'string' ? goal : goal.id];

    if (!startNode || !goalNode) {
        console.error(`Nœud manquant pour le calcul heuristique: start = ${start}, goal = ${goal}`);
        return Infinity;
    }

    // Calculer la distance euclidienne entre deux nœuds
    const dx = startNode.x - goalNode.x;
    const dy = startNode.y - goalNode.y;
    return Math.sqrt(dx * dx + dy * dy);
}


function manageCapacity(velo) {
    if (velo.charge >= velo.capacite) {
        const retourLink = links.find(link => link.target.id === "Porte d'Ivry" || link.source.id === "Porte d'Ivry");
        moveVelo(velo, nodeById["Porte d'Ivry"]);
        velo.charge = 0; // Décharger le vélo
    }
}


// Reconstituer le chemin à partir de Dijkstra
function reconstructPath(previousNodes, target) {
    const path = [];
    let current = target;

    while (current) {
        path.unshift(current);
        current = previousNodes[current];
    }

    return path;
}
function simulateIncidents() {
    const incidentTypes = ['arret_bloque', 'velo_en_panne', 'accident_corporel', 'arret_supprime', 'casse_velo'];
    const incidentType = incidentTypes[Math.floor(Math.random() * incidentTypes.length)];

    if (incidentType === 'arret_bloque') {
        // Bloquer un arrêt aléatoire
        const randomNode = nodes[Math.floor(Math.random() * nodes.length)];
        randomNode.isBlocked = true;
        console.log(`Incident: arrêt bloqué à ${randomNode.id}`);
        
        // Recalculer les itinéraires pour éviter l'arrêt bloqué
        recalculateAllVeloRoutes();
        
    } else if (incidentType === 'velo_en_panne') {
        // Mettre un vélo en panne
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isBroken = true;
        console.log(`Incident: ${randomVelo.id} est en panne`);
        
        // Redistribuer les arrêts assignés à ce vélo
        redistributeStops(randomVelo);
        
    } else if (incidentType === 'accident_corporel') {
        // Signaler un accident pour un vélo
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isAccident = true;
        console.log(`Incident: Accident corporel pour ${randomVelo.id}`);
        
        // Marquer le vélo comme non opérationnel et redistribuer ses arrêts
        redistributeStops(randomVelo);
        
    } else if (incidentType === 'arret_supprime') {
        // Supprimer un arrêt aléatoire
        const randomStop = Array.from(unvisitedStops)[Math.floor(Math.random() * unvisitedStops.size)];
        unvisitedStops.delete(randomStop);
        console.log(`Incident: Arrêt supprimé ${randomStop}`);
        
        // Recalculer les itinéraires pour éviter cet arrêt
        recalculateAllVeloRoutes();
        
    } else if (incidentType === 'casse_velo') {
        // Casser un vélo
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isBroken = true;
        console.log(`Incident: ${randomVelo.id} est cassé`);
        
        // Redistribuer les arrêts assignés au vélo cassé
        redistributeStops(randomVelo);
    }

    // Mettre à jour l'interface
    updateMap();
    updateVeloInfo();
    updateTraiteesInfo();
}

// Fonction pour redistribuer les arrêts d'un vélo non opérationnel
function redistributeStops(velo) {
    console.log(`Redistribution des arrêts de ${velo.id} car il est non opérationnel.`);
    
    if (velo.assignedStops.size > 0) {
        const stopsArray = Array.from(velo.assignedStops);
        
        // Réattribuer ces arrêts à d'autres vélos actifs
        velos.forEach(v => {
            if (!v.isBroken && !v.isAccident) {
                const stopsToAssign = stopsArray.splice(0, Math.ceil(stopsArray.length / (velos.length - 1)));
                stopsToAssign.forEach(stop => v.assignedStops.add(stop));
            }
        });
        
        // Retirer tous les arrêts assignés à ce vélo
        velo.assignedStops.clear();
    }
}

// Fonction pour recalculer les itinéraires de tous les vélos après un incident
function recalculateAllVeloRoutes() {
    velos.forEach(velo => {
        if (!velo.isBroken && !velo.isAccident) {
            calculateRoutesForVelos();
        }
    });
}

function updateMap() {
    velo.select("circle")
        .attr("cx", d => nodeById[d.position]?.x || 0)
        .attr("cy", d => nodeById[d.position]?.y || 0);

    velo.select("text")
        .attr("x", d => (nodeById[d.position]?.x || 0) + 12)
        .attr("y", d => nodeById[d.position]?.y || 0);

    const tourneePath = g.selectAll(".tournee")
        .data(velos)
        .join("path")
        .attr("class", "tournee")
        .attr("d", d => d3.line()
            .x(stop => nodeById[stop]?.x || 0)
            .y(stop => nodeById[stop]?.y || 0)
            .curve(d3.curveBasis)(d.tournee)
        );

    const completedTourPath = g.selectAll(".completed-tour")
        .data(completedTours)
        .join("path")
        .attr("class", "completed-tour")
        .attr("d", d => d3.line()
            .x(stop => nodeById[stop]?.x || 0)
            .y(stop => nodeById[stop]?.y || 0)
            .curve(d3.curveBasis)(d.tournee)
        );
}

function updateVeloInfo() {
    const infoContainer = d3.select("#velo-info-container");
    infoContainer.selectAll(".velo-info")
        .data(velos)
        .join("div")
        .attr("class", "velo-info")
        .html(d => `
            <h4>${d.id}</h4>
            <p>Position actuelle: ${d.position.id || 'En route'}</p>
            <p>Autonomie restante: ${d.autonomie.toFixed(2)} km</p>
            <p>Charge actuelle: ${d.charge} kg</p>
            <p>Distance parcourue: ${d.distanceParcourue.toFixed(2)} km</p>
            <p>Feux rencontrés: ${d.feuxRencontres}</p>
        `);

    const completedToursContainer = d3.select("#completed-tours-container");
    completedToursContainer.selectAll(".completed-tour-info")
        .data(completedTours)
        .join("div")
        .attr("class", "completed-tour-info")
        .html(d => `
            <h4>${d.id} - Tournée terminée</h4>
            <p>Distance parcourue: ${d.distanceParcourue.toFixed(2)} km</p>
            <p>Charge: ${d.charge} kg</p>
            <p>Feux rencontrés: ${d.feuxRencontres}</p>
            <p>Itinéraire: ${d.tournee.join(" -> ")}</p>
        `);
}

function updateTraiteesInfo() {
    const ruesContainer = d3.select("#rues-traitees-container");
    const stopsContainer = d3.select("#stops-traitees-container");

    ruesContainer.selectAll(".rue-info")
        .data(Object.entries(ruesTraitees))
        .join("div")
        .attr("class", "rue-info")
        .html(([rue, time]) => `
            <p>Rue: ${rue} - Heure de traitement: ${time}</p>
        `);

    stopsContainer.selectAll(".stop-info")
        .data(Object.entries(pointsDePassageTraites))
        .join("div")
        .attr("class", "stop-info")
        .html(([stop, time]) => `
            <p>Point de passage: ${stop} - Heure de traitement: ${time}</p>
        `);
}
console.log(`Coordonnées du vélo ${velo.id}: `, nodeById[velo.position]?.x, nodeById[velo.position]?.y);

// Appel des fonctions pour mettre à jour les informations régulièrement
setInterval(updateVeloInfo, 2000);
setInterval(calculateRoutesForVelos, 50); // Réduire l'intervalle pour des animations plus fluides
setInterval(simulateIncidents, 10000);
setInterval(updateTraiteesInfo, 2000); // Mise à jour régulière des infos sur les rues et points de passage
