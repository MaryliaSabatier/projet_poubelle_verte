console.log("Script.js is loaded");

const WINTER_MODE = true; // Active ou désactive le mode hiver

// Définir les vélos (exemple de structure)
const velos = [
    { id: "Velo1", position: "Porte d'Ivry", autonomie: WINTER_MODE ? 45 : 50, charge: 0, capacite: 200, distanceParcourue: 0, feuxRencontres: 0, isBroken: false, tournee: [] },
    { id: "Velo2", position: "Porte d'Ivry", autonomie: WINTER_MODE ? 45 : 50, charge: 0, capacite: 200, distanceParcourue: 0, feuxRencontres: 0, isBroken: false, tournee: [] }
];

const nodes = [];
const links = [];
const nodeById = {};
const completedTours = []; // Historique des tournées terminées
const unvisitedStops = new Set(); // Ensemble des arrêts non visités

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

    g.selectAll(".velo")
        .attr("cx", d => nodeById[d.position]?.x || 0)
        .attr("cy", d => nodeById[d.position]?.y || 0);
}

const velo = g.append("g")
    .attr("class", "velos")
    .selectAll("circle")
    .data(velos)
    .join("circle")
    .attr("r", 7)
    .attr("class", "velo")
    .attr("cx", d => nodeById[d.position]?.x || 0)
    .attr("cy", d => nodeById[d.position]?.y || 0);

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

const initialTransform = d3.zoomIdentity
    .translate(width / 3, height / 3)
    .scale(0.8);

svg.call(zoom.transform, initialTransform);

function calculateRoutesForVelos() {
    for (const velo of velos) {
        while (velo.autonomie > 0 && velo.charge < velo.capacite && unvisitedStops.size > 0) {
            const nextStop = findNextStop(velo.position);

            if (!nextStop) {
                break;
            }

            const graph = {
                nodes: nodeById,
                links: links
            };
            const { distances, previousNodes } = dijkstra(graph, velo.position);
            const path = reconstructPath(previousNodes, nextStop);

            for (let i = 1; i < path.length; i++) {
                const currentStop = path[i - 1];
                const nextStop = path[i];

                const link = links.find(link => 
                    (link.source.id === currentStop && link.target.id === nextStop) || 
                    (link.source.id === nextStop && link.target.id === currentStop)
                );

                if (!link) {
                    console.error(`Aucun lien trouvé entre ${currentStop} et ${nextStop}.`);
                    continue;
                }

                const distance = link.distance;
                const feux = link.trafficLights || 0;

                if (nodeById[nextStop].isBlocked) {
                    console.log(`Arrêt ${nextStop} est bloqué, recherche d'un autre itinéraire.`);
                    break;
                }

                velo.distanceParcourue += distance;
                velo.autonomie -= distance + (feux / 20); // Diminuer l'autonomie en fonction des feux tricolores
                velo.feuxRencontres += feux;
                velo.charge += 50; // Suppose 50kg par arrêt
                velo.position = nextStop;
                velo.tournee.push(velo.position);

                if (velo.autonomie <= 0 || velo.charge >= velo.capacite) {
                    break;
                }
            }

            if (velo.autonomie <= 0 || velo.charge >= velo.capacite) {
                console.log(`${velo.id} doit retourner à la base pour recharger ou vider la charge.`);
                completedTours.push({
                    id: velo.id,
                    tournee: [...velo.tournee], // Cloner l'itinéraire
                    distanceParcourue: velo.distanceParcourue,
                    feuxRencontres: velo.feuxRencontres,
                    charge: velo.charge
                });

                velo.position = "Porte d'Ivry";
                velo.autonomie = WINTER_MODE ? 45 : 50; // Recharge
                velo.distanceParcourue = 0;
                velo.feuxRencontres = 0;
                velo.charge = 0;
                velo.tournee = [];
            }
        }
    }

    updateMap();
    updateVeloInfo();
}

function findNextStop(currentPosition) {
    const unvisitedStopsArray = Array.from(unvisitedStops);

    if (unvisitedStopsArray.length === 0 || !currentPosition) {
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

function heuristicCostEstimate(start, goal) {
    const dx = nodeById[start].x - nodeById[goal].x;
    const dy = nodeById[start].y - nodeById[goal].y;
    return Math.sqrt(dx * dx + dy * dy);
}

function getDistance(start, goal) {
    const link = links.find(link => (link.source.id === start && link.target.id === goal) || (link.source.id === goal && link.target.id === start));
    if (link) {
        return link.distance + (link.trafficLights / 20); // 1 km tous les 20 feux tricolores
    }
    return Infinity;
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
    const incidentTypes = ['arret_bloque', 'velo_en_panne'];
    const incidentType = incidentTypes[Math.floor(Math.random() * incidentTypes.length)];

    if (incidentType === 'arret_bloque') {
        const randomNode = nodes[Math.floor(Math.random() * nodes.length)];
        randomNode.isBlocked = true;
        console.log(`Incident: arrêt bloqué à ${randomNode.id}`);
    } else if (incidentType === 'velo_en_panne') {
        const randomVelo = velos[Math.floor(Math.random() * velos.length)];
        randomVelo.isBroken = true;
        console.log(`Incident: ${randomVelo.id} est en panne`);
    }

    updateMap();
    updateVeloInfo();
}

function updateMap() {
    velo.attr("cx", d => nodeById[d.position]?.x || 0)
        .attr("cy", d => nodeById[d.position]?.y || 0);

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
            <p>Position actuelle: ${d.position}</p>
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

setInterval(updateVeloInfo, 2000);
setInterval(calculateRoutesForVelos, 2000);
setInterval(simulateIncidents, 10000);
