console.log("Script.js is loaded");

const WINTER_MODE = true; // Active ou désactive le mode hiver

// Définir les vélos (exemple de structure)
const velos = [
    { id: "Velo1", position: "Porte d'Ivry", autonomie: WINTER_MODE ? 45 : 50, charge: 0, capacite: 200, distanceParcourue: 0, feuxRencontres: 0, isBroken: false, tournee: [], currentLink: null, progress: 0, assignedStops: new Set() },
    { id: "Velo2", position: "Porte d'Ivry", autonomie: WINTER_MODE ? 45 : 50, charge: 0, capacite: 200, distanceParcourue: 0, feuxRencontres: 0, isBroken: false, tournee: [], currentLink: null, progress: 0, assignedStops: new Set() }
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
            existingNode = { id: arret.name, type: "stop", x: Math.random() * width, y: Math.random() * height };

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
    .scaleExtent([0.5, 5])
    .on("zoom", (event) => {
        g.attr("transform", event.transform);
    });

svg.call(zoom);

const link = g.append("g")
    .attr("class", "links")
    .selectAll("line")
    .data(links)
    .join("line")
    .attr("class", d => `link ${d.street}`);

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

// Création des éléments pour les vélos
const velo = g.append("g")
    .attr("class", "velos")
    .selectAll("g")
    .data(velos)
    .join("g")
    .attr("class", "velo");

// Ajout des cercles pour les vélos
velo.append("circle")
    .attr("r", 10) // Cercle plus grand pour représenter les vélos
    .attr("cx", d => nodeById[d.position]?.x || 0)
    .attr("cy", d => nodeById[d.position]?.y || 0);

// Ajout des textes pour les noms des vélos
velo.append("text")
    .attr("x", d => (nodeById[d.position]?.x || 0) + 12) // Décale le texte à droite du cercle
    .attr("y", d => nodeById[d.position]?.y || 0)
    .attr("dy", ".35em")
    .text(d => d.id);

function dijkstra(graph, startNode) {
    const distances = {};
    const previousNodes = {};
    const visitedNodes = new Set();
    const unvisitedNodes = new Set(Object.keys(graph.nodes));
    
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
        visitedNodes.add(currentNode);

        for (const link of graph.links.filter(link => link.source.id === currentNode)) {
            const neighbor = link.target.id;
            if (!visitedNodes.has(neighbor)) {
                const newDist = distances[currentNode] + link.distance + (link.trafficLights / 20); // Pénaliser les feux tricolores
                if (newDist < distances[neighbor]) {
                    distances[neighbor] = newDist;
                    previousNodes[neighbor] = currentNode;
                }
            }
        }
    }

    return { distances, previousNodes };
}

function divideStopsAmongVelos() {
    const stopArray = Array.from(unvisitedStops);
    const stopsPerVelo = Math.ceil(stopArray.length / velos.length);

    velos.forEach((velo, index) => {
        const start = index * stopsPerVelo;
        const end = start + stopsPerVelo;
        const assignedStops = stopArray.slice(start, end);
        
        // Assignez ces arrêts au vélo
        assignedStops.forEach(stop => velo.assignedStops.add(stop));
    });
}

divideStopsAmongVelos();

const initialTransform = d3.zoomIdentity
    .translate(width / 2, height / 2)
    .scale(0.8);  // Ajustez cette valeur pour zoomer plus ou moins sur la carte

svg.call(zoom.transform, initialTransform);

function calculateRoutesForVelos() {
    for (const velo of velos) {
        if (!velo.currentLink && velo.autonomie > 0 && velo.charge < velo.capacite && velo.assignedStops.size > 0) {
            const currentPositionId = typeof velo.position === 'string' ? velo.position : velo.position.id;
            const nextStop = findNextStopForVelo(currentPositionId, velo);

            if (!nextStop) {
                continue;
            }

            const graph = {
                nodes: nodeById,
                links: links
            };
            const { distances, previousNodes } = dijkstra(graph, currentPositionId);
            const path = reconstructPath(previousNodes, nextStop);

            if (path.length > 1) {
                const currentStop = path[0];
                const nextStop = path[1];

                const link = links.find(link =>
                    (link.source.id === currentStop && link.target.id === nextStop) ||
                    (link.source.id === nextStop && link.target.id === currentStop)
                );

                if (link) {
                    velo.currentLink = link;
                    velo.progress = 0;
                }
            }
        }

        if (velo.currentLink) {
            const link = velo.currentLink;
            velo.progress += 0.01; // Progression de l'animation (ajustez la valeur pour contrôler la vitesse)

            if (velo.progress >= 1) {
                // Le vélo atteint la fin de l'arête, mise à jour des propriétés du vélo
                velo.position = link.target;
                velo.distanceParcourue += link.distance;
                velo.autonomie -= link.distance + (link.trafficLights / 20);
                velo.feuxRencontres += link.trafficLights;
                velo.charge += 50;
                velo.tournee.push(velo.position.id);

                // Enregistrer l'heure de passage pour l'arrêt
                const currentTime = new Date().toLocaleTimeString();
                pointsDePassageTraites[velo.position.id] = currentTime;

                // Enregistrer l'heure de traitement pour la rue
                if (!ruesTraitees[link.street]) {
                    ruesTraitees[link.street] = currentTime;
                }

                // Vérifier si le vélo doit retourner à la base
                if (velo.autonomie <= 0 || velo.charge >= velo.capacite) {
                    console.log(`${velo.id} doit retourner à la base pour recharger ou vider la charge.`);
                    completedTours.push({
                        id: velo.id,
                        tournee: [...velo.tournee], // Cloner l'itinéraire
                        distanceParcourue: velo.distanceParcourue,
                        feuxRencontres: velo.feuxRencontres,
                        charge: velo.charge
                    });

                    velo.position = depotIvry;
                    velo.autonomie = WINTER_MODE ? 45 : 50; // Recharge
                    velo.distanceParcourue = 0;
                    velo.feuxRencontres = 0;
                    velo.charge = 0;
                    velo.tournee = [];
                }

                velo.currentLink = null; // Réinitialiser le lien actuel
            } else {
                // Calculer la position intermédiaire pendant que le vélo se déplace
                updateVeloPosition(velo);
            }
        }
    }

    updateMap();
    updateVeloInfo();
    updateTraiteesInfo();
}

function updateVeloPosition(velo) {
    if (!velo.currentLink) return;

    const x1 = velo.currentLink.source.x;
    const y1 = velo.currentLink.source.y;
    const x2 = velo.currentLink.target.x;
    const y2 = velo.currentLink.target.y;

    // Calculer la position intermédiaire sur la ligne entre les deux points
    const cx = x1 + (x2 - x1) * velo.progress;
    const cy = y1 + (y2 - y1) * velo.progress;

    // Mettre à jour la position du vélo sur la carte
    d3.select(`#${velo.id} circle`)
        .attr("cx", cx)
        .attr("cy", cy);

    d3.select(`#${velo.id} text`)
        .attr("x", cx + 12)
        .attr("y", cy);
}

function findNextStopForVelo(currentPosition, velo) {
    const unvisitedStopsArray = Array.from(velo.assignedStops);

    if (unvisitedStopsArray.length === 0 || !currentPosition) {
        return null;
    }

    const closestStop = unvisitedStopsArray.reduce((prev, curr) => {
        const prevDist = heuristicCostEstimate(currentPosition, prev);
        const currDist = heuristicCostEstimate(currentPosition, curr);
        return (currDist < prevDist) ? curr : prev;
    });

    velo.assignedStops.delete(closestStop);
    return closestStop;
}

function heuristicCostEstimate(start, goal) {
    // Assurez-vous que start et goal sont des identifiants de chaîne
    const startNodeId = typeof start === 'string' ? start : start.id;
    const goalNodeId = typeof goal === 'string' ? goal : goal.id;

    const startNode = nodeById[startNodeId];
    const goalNode = nodeById[goalNodeId];

    if (!startNode || !goalNode) {
        console.error(`Nœud manquant pour le calcul heuristique: start = ${startNodeId}, goal = ${goalNodeId}`);
        return Infinity;
    }

    const dx = startNode.x - goalNode.x;
    const dy = startNode.y - goalNode.y;
    return Math.sqrt(dx * dx + dy * dy);
}

function reconstructPath(cameFrom, current) {
    const totalPath = [];

    while (current) {
        totalPath.unshift(current);
        current = cameFrom[current];
    }

    return totalPath;
}

function simulateIncidents() {
    const incidentTypes = ['arret_bloque', 'velo_en_panne', 'accident_corporel', 'arret_supprime', 'casse_velo'];
    const incidentType = incidentTypes[Math.floor(Math.random() * incidentTypes.length)];

    if (incidentType === 'arret_bloque') {
        const randomNode = nodes[Math.floor(Math.random() * nodes.length)];
        randomNode.isBlocked = true;
        console.log(`Incident: arrêt bloqué à ${randomNode.id}`);
    } else if (incidentType === 'velo_en_panne') {
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isBroken = true;
        console.log(`Incident: ${randomVelo.id} est en panne`);
    } else if (incidentType === 'accident_corporel') {
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isAccident = true;
        console.log(`Incident: Accident corporel pour ${randomVelo.id}`);
    } else if (incidentType === 'arret_supprime') {
        const randomStop = Array.from(unvisitedStops)[Math.floor(Math.random() * unvisitedStops.size)];
        unvisitedStops.delete(randomStop);
        console.log(`Incident: Arrêt supprimé ${randomStop}`);
    } else if (incidentType === 'casse_velo') {
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isBroken = true;
        console.log(`Incident: ${randomVelo.id} est cassé`);
    }

    updateMap();
    updateVeloInfo();
    updateTraiteesInfo();
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

// Appel des fonctions pour mettre à jour les informations régulièrement
setInterval(updateVeloInfo, 2000);
setInterval(calculateRoutesForVelos, 50); // Réduire l'intervalle pour des animations plus fluides
setInterval(simulateIncidents, 10000);
setInterval(updateTraiteesInfo, 2000); // Mise à jour régulière des infos sur les rues et points de passage
