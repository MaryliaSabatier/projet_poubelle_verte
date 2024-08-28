console.log("Script.js is loaded");

const nodes = [];
const links = [];
const nodeById = {};
const completedTours = []; // Historique des tournées terminées
const unvisitedStops = new Set(); // Ensemble des arrêts non visités

const padding = 50;
const width = window.innerWidth;
const height = window.innerHeight;
const streetSpacing = 50;

const depotIvry = { id: "Porte d'Ivry", type: "depot" };
nodes.push(depotIvry);
nodeById["Porte d'Ivry"] = depotIvry;

console.log("Initial nodes:", nodes);

let y = padding;
for (const rue in window.ruesEtArrets) { // Utiliser window.ruesEtArrets
    let x = padding;
    const arrets = window.ruesEtArrets[rue].stops;
    const xIncrement = (width - 2 * padding) / (arrets.length - 1);

    for (let i = 0; i < arrets.length; i++) {
        const arret = arrets[i];

        let existingNode = nodeById[arret.name];
        if (!existingNode) {
            existingNode = { id: arret.name, type: "stop", x, y };

            const ruesConnectees = Object.values(window.ruesEtArrets).filter(rue => rue.stops.some(s => s.name === arret.name));
            if (ruesConnectees.length === 1) {
                existingNode.isImpasse = true;
            }

            nodes.push(existingNode);
            nodeById[arret.name] = existingNode;
            unvisitedStops.add(arret.name); // Ajouter l'arrêt à l'ensemble des arrêts non visités
        } else {
            existingNode.x = (existingNode.x + x) / 2;
            existingNode.y = (existingNode.y + y) / 2;
            existingNode.type = "intersection";
        }

        if (i > 0) {
            links.push({
                source: nodeById[arrets[i - 1].name],
                target: existingNode,
                distance: window.ruesEtArrets[rue].distances[i - 1],
                trafficLights: window.ruesEtArrets[rue].trafficLights[i - 1],
                street: rue.replace(/\s+/g, '-') // Ajouter le nom de la rue comme classe
            });
        }

        x += xIncrement;
    }

    y += streetSpacing;
}

console.log("Nodes after processing:", nodes);
console.log("Links after processing:", links);

const svg = d3.select("#map").append("svg")
    .attr("width", width)
    .attr("height", height)
    .attr("viewBox", `0 0 ${width} ${height}`);

const zoom = d3.zoom()
    .scaleExtent([0.5, 5])
    .on("zoom", (event) => {
        g.attr("transform", event.transform);
    });

svg.call(zoom);

const g = svg.append("g")
    .attr("class", "everything");

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

// Initialisation des vélos avec des données par défaut
window.velos = [
    { id: 1, numero: 'Velo 001', etat: 'operationnel', autonomie_km: 45, date_derniere_revision: '2024-01-01', position: "Porte d'Ivry", charge: 0, distanceParcourue: 0, feuxRencontres: 0, saison: "été", tournee: [] },
    { id: 2, numero: 'Velo 002', etat: 'operationnel', autonomie_km: 50, date_derniere_revision: '2024-01-10', position: "Porte d'Ivry", charge: 0, distanceParcourue: 0, feuxRencontres: 0, saison: "été", tournee: [] }
    // Ajoutez d'autres vélos ici
];

function initializeMapWithVelos() {
    const velo = g.append("g")
        .attr("class", "velos")
        .selectAll("circle")
        .data(window.velos) // Utiliser window.velos
        .join("circle")
        .attr("r", 7)
        .attr("class", "velo")
        .attr("cx", d => nodeById[d.position]?.x || 0)
        .attr("cy", d => nodeById[d.position]?.y || 0);

    const simulation = d3.forceSimulation(nodes)
        .force("link", d3.forceLink(links).id(d => d.id).distance(80).strength(0.1))
        .force("charge", d3.forceManyBody().strength(-50))
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

        velo
            .attr("cx", d => nodeById[d.position]?.x || 0)
            .attr("cy", d => nodeById[d.position]?.y || 0);
    }
}

// A* Algorithm to find the optimal path
function aStarSearch(start, goal) {
    const openSet = [start];
    const cameFrom = {};
    const gScore = {};
    const fScore = {};

    nodes.forEach(node => {
        gScore[node.id] = Infinity;
        fScore[node.id] = Infinity;
    });

    gScore[start] = 0;
    fScore[start] = heuristicCostEstimate(start, goal);

    while (openSet.length > 0) {
        openSet.sort((a, b) => fScore[a] - fScore[b]);
        const current = openSet.shift();

        if (current === goal) {
            return reconstructPath(cameFrom, current);
        }

        const neighbors = links.filter(link => link.source.id === current || link.target.id === current)
            .map(link => link.source.id === current ? link.target.id : link.source.id);

        neighbors.forEach(neighbor => {
            const tentativeGScore = gScore[current] + getDistance(current, neighbor);
            if (tentativeGScore < gScore[neighbor]) {
                cameFrom[neighbor] = current;
                gScore[neighbor] = tentativeGScore;
                fScore[neighbor] = gScore[neighbor] + heuristicCostEstimate(neighbor, goal);
                if (!openSet.includes(neighbor)) {
                    openSet.push(neighbor);
                }
            }
        });
    }

    return []; // No path found
}

function heuristicCostEstimate(start, goal) {
    const dx = nodeById[start].x - nodeById[goal].x;
    const dy = nodeById[start].y - nodeById[goal].y;
    return Math.sqrt(dx * dx + dy * dy);
}

function getDistance(start, goal) {
    const link = links.find(link => (link.source.id === start && link.target.id === goal) || (link.source.id === goal && link.target.id === start));
    if (link) {
        return link.distance + (link.trafficLights / 20); // 1 km every 20 traffic lights
    }
    return Infinity;
}

function reconstructPath(cameFrom, current) {
    const totalPath = [current];
    while (current in cameFrom) {
        current = cameFrom[current];
        totalPath.unshift(current);
    }
    return totalPath;
}

function simulateIncidents() {
    const incidentTypes = ['arret_bloque', 'velo_en_panne'];
    const incidentType = incidentTypes[Math.floor(Math.random() * incidentTypes.length)];

    if (incidentType === 'arret_bloque') {
        const randomNode = nodes[Math.floor(Math.random() * nodes.length)];
        randomNode.isBlocked = true;
        console.log(`Incident: arrêt bloqué à ${randomNode.id}`);
    } else if (incidentType === 'velo_en_panne') {
        const randomVelo = window.velos[Math.floor(Math.random() * window.velos.length)]; // Utiliser window.velos
        randomVelo.isBroken = true;
        console.log(`Incident: ${randomVelo.numero} est en panne`);
    }

    updateMap();
    updateVeloInfo();
}

function moveVelos() {
    for (const velo of window.velos) { // Utiliser window.velos
        if (velo.isBroken) {
            continue; // Skip broken bikes
        }

        let stopsCount = 0;

        while (velo.autonomie_km > 0 && velo.charge < 200 && stopsCount < 4) {
            const nextStop = findNextStop(velo.position);

            if (!nextStop) {
                break;
            }

            const path = aStarSearch(velo.position, nextStop);
            for (let i = 1; i < path.length; i++) {
                const currentStop = path[i - 1];
                const nextStop = path[i];

                const distance = getDistance(currentStop, nextStop);
                const feux = links.find(link => (link.source.id === currentStop && link.target.id === nextStop) || (link.source.id === nextStop && link.target.id === currentStop)).trafficLights;

                if (nodeById[nextStop].isBlocked) {
                    console.log(`Arrêt ${nextStop} est bloqué, recherche d'un autre itinéraire.`);
                    break;
                }

                velo.distanceParcourue += distance;
                velo.autonomie_km -= distance;
                velo.feuxRencontres += feux;
                velo.charge += 50; // Suppose 50kg per stop
                stopsCount++;

                velo.position = nextStop;
                velo.tournee.push(velo.position);

                if (velo.autonomie_km <= 0 || velo.charge >= 200) {
                    break;
                }
            }
        }

        if (velo.autonomie_km <= 0 || velo.charge >= 200 || stopsCount === 4) {
            console.log(`${velo.numero} doit retourner à la base pour recharger ou vider la charge.`);
            // Enregistrer la tournée terminée
            completedTours.push({
                id: velo.id,
                tournee: [...velo.tournee], // Clone de l'itinéraire
                distanceParcourue: velo.distanceParcourue,
                feuxRencontres: velo.feuxRencontres,
                charge: velo.charge
            });

            velo.position = "Porte d'Ivry";
            velo.autonomie_km = 50; // Recharge
            velo.distanceParcourue = 0;
            velo.feuxRencontres = 0;
            velo.charge = 0;
            velo.tournee = [];
        }
    }

    updateMap();
    updateVeloInfo();
}

function findNextStop(currentPosition) {
    const unvisitedStopsArray = Array.from(unvisitedStops);

    if (unvisitedStopsArray.length === 0) {
        return null;
    }

    const closestStop = unvisitedStopsArray.reduce((prev, curr) => {
        const prevDist = heuristicCostEstimate(currentPosition, prev);
        const currDist = heuristicCostEstimate(currentPosition, curr);
        return (currDist < prevDist) ? curr : prev;
    });

    unvisitedStops.delete(closestStop);
    return closestStop;
}

function updateMap() {
    const velo = d3.selectAll(".velo");
    velo.attr("cx", d => nodeById[d.position]?.x || 0)
        .attr("cy", d => nodeById[d.position]?.y || 0);
}

function updateVeloInfo() {
    const infoContainer = d3.select("#velo-info-container");
    infoContainer.selectAll(".velo-info")
        .data(window.velos) // Utiliser window.velos
        .join("div")
        .attr("class", "velo-info")
        .html(d => {
            // Vérifier que les propriétés existent et sont des nombres
            const autonomie_km = d.autonomie_km !== undefined && !isNaN(d.autonomie_km) ? d.autonomie_km.toFixed(2) : "N/A";
            const charge = d.charge !== undefined && !isNaN(d.charge) ? d.charge : "N/A";
            const distanceParcourue = d.distanceParcourue !== undefined && !isNaN(d.distanceParcourue) ? d.distanceParcourue.toFixed(2) : "N/A";

            return `
                <h4>${d.numero}</h4>
                <p>Position actuelle: ${d.position}</p>
                <p>Autonomie restante: ${autonomie_km} km</p>
                <p>Charge actuelle: ${charge} kg</p>
                <p>Distance parcourue: ${distanceParcourue} km</p>
                <p>Feux rencontrés: ${d.feuxRencontres !== undefined ? d.feuxRencontres : "N/A"}</p>
            `;
        });

    const completedToursContainer = d3.select("#completed-tours-container");
    completedToursContainer.selectAll(".completed-tour-info")
        .data(completedTours)
        .join("div")
        .attr("class", "completed-tour-info")
        .html(d => `
            <h4>${d.id} - Tournée terminée</h4>
            <p>Distance parcourue: ${d.distanceParcourue !== undefined && !isNaN(d.distanceParcourue) ? d.distanceParcourue.toFixed(2) : "N/A"} km</p>
            <p>Charge: ${d.charge !== undefined && !isNaN(d.charge) ? d.charge : "N/A"} kg</p>
            <p>Feux rencontrés: ${d.feuxRencontres !== undefined ? d.feuxRencontres : "N/A"}</p>
            <p>Itinéraire: ${d.tournee.join(" -> ")}</p>
        `);
}

// Initialiser la carte avec les vélos chargés
initializeMapWithVelos();

// Appelez updateVeloInfo pour mettre à jour les informations des vélos
updateVeloInfo();

// Intervalles pour mettre à jour les informations des vélos et simuler les mouvements et incidents
setInterval(updateVeloInfo, 2000); // Mettez à jour les infos des vélos toutes les 2 secondes
setInterval(moveVelos, 2000);
setInterval(simulateIncidents, 10000); // Simulate incidents every 10 seconds
