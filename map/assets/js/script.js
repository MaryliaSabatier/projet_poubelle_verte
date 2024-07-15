console.log("Script.js is loaded");

const nodes = [];
const links = [];
const nodeById = {};

const padding = 50;
const width = 1500;
const height = 800;
const streetSpacing = 50;

const depotIvry = { id: "Porte d'Ivry", type: "depot" };
nodes.push(depotIvry);
nodeById["Porte d'Ivry"] = depotIvry;

console.log("Initial nodes:", nodes);

let y = padding;
for (const rue in ruesEtArrets) {
    let x = padding;
    const arrets = ruesEtArrets[rue].stops;
    const xIncrement = (width - 2 * padding) / (arrets.length - 1);

    for (let i = 0; i < arrets.length; i++) {
        const arret = arrets[i];

        let existingNode = nodeById[arret];
        if (!existingNode) {
            existingNode = { id: arret, type: "stop", x, y };

            const ruesConnectees = Object.values(ruesEtArrets).filter(rue => rue.stops.includes(arret));
            if (ruesConnectees.length === 1) {
                existingNode.isImpasse = true;
            }

            nodes.push(existingNode);
            nodeById[arret] = existingNode;
        } else {
            existingNode.x = (existingNode.x + x) / 2;
            existingNode.y = (existingNode.y + y) / 2;
            existingNode.type = "intersection";
        }

        if (i > 0) {
            links.push({
                source: nodeById[arrets[i - 1]],
                target: existingNode,
                distance: ruesEtArrets[rue].distances[i - 1],
                trafficLights: ruesEtArrets[rue].trafficLights[i - 1]
            });
        }

        x += xIncrement;
    }

    y += streetSpacing;
}

console.log("Nodes after processing:", nodes);
console.log("Links after processing:", links);

const svg = d3.select("#map").append("svg")
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
    .attr("class", d => `link ${d.source.id.replace(/\s+/g, '-')}`);

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

const velo = g.append("g")
    .attr("class", "velos")
    .selectAll("circle")
    .data(velos)
    .join("circle")
    .attr("r", 7)
    .attr("class", "velo")
    .attr("cx", d => nodeById[d.position].x)
    .attr("cy", d => nodeById[d.position].y);

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
        .attr("cx", d => nodeById[d.position].x)
        .attr("cy", d => nodeById[d.position].y);
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

function moveVelos() {
    for (const velo of velos) {
        if (velo.autonomie <= 0 || velo.charge >= velo.capacite) {
            alert(`${velo.id} doit retourner à la base pour recharger ou vider la charge.`);
            velo.position = "Porte d'Ivry";
            velo.autonomie = 50; // Recharge
            velo.distanceParcourue = 0;
            velo.feuxRencontres = 0;
            velo.charge = 0; // Déchargement
            velo.tournee = ["Porte d'Ivry"];
        } else {
            const rueActuelle = Object.keys(ruesEtArrets).find(rue => ruesEtArrets[rue].stops.includes(velo.position));
            const indexArretActuel = ruesEtArrets[rueActuelle].stops.indexOf(velo.position);
            if (indexArretActuel < 0) continue;
            const prochainIndexArret = (indexArretActuel + 1) % ruesEtArrets[rueActuelle].stops.length;
            const prochainArret = ruesEtArrets[rueActuelle].stops[prochainIndexArret];

            const optimalPath = aStarSearch(velo.position, prochainArret);
            if (optimalPath.length > 0) {
                velo.position = optimalPath[1]; // Move to the next position in the optimal path
                const distance = getDistance(optimalPath[0], optimalPath[1]);
                const feux = links.find(link => (link.source.id === optimalPath[0] && link.target.id === optimalPath[1]) || (link.source.id === optimalPath[1] && link.target.id === optimalPath[0])).trafficLights;
                velo.distanceParcourue += distance;
                velo.autonomie -= distance;
                velo.feuxRencontres += feux;
                velo.charge += 50; // 50 kg de déchets par arrêt
                velo.tournee.push(velo.position);
            }
        }
    }

    updateMap();
    updateVeloInfo();
}

function updateMap() {
    velo.attr("cx", d => nodeById[d.position].x)
        .attr("cy", d => nodeById[d.position].y);

    const tourneePath = g.selectAll(".tournee")
        .data(velos)
        .join("path")
        .attr("class", "tournee")
        .attr("d", d => d3.line()
            .x(stop => nodeById[stop].x)
            .y(stop => nodeById[stop].y)
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
}

setInterval(moveVelos, 2000);
