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

// Parcourir les données dynamiques de rues et arrêts
for (const rue in window.ruesEtArrets) {
    const arrets = window.ruesEtArrets[rue].stops;

    for (let i = 0; i < arrets.length; i++) {
        const arret = arrets[i];

        if (!arret || !arret.name) {
            console.error(`Nom d'arrêt invalide pour la rue ${rue} à l'indice ${i}.`);
            continue;
        }

        let existingNode = nodeById[arret.name];
        if (!existingNode) {
            const [x, y] = projection([arret.lon, arret.lat]);

            existingNode = { id: arret.name, type: "stop", x: x, y: y };

            const ruesConnectees = Object.values(window.ruesEtArrets).filter(r => r.stops.some(s => s.name === arret.name));
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
                distance: window.ruesEtArrets[rue].distances ? window.ruesEtArrets[rue].distances[i - 1] : 0,
                trafficLights: window.ruesEtArrets[rue].trafficLights ? window.ruesEtArrets[rue].trafficLights[i - 1] : 0,
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
    .scaleExtent([0.2, 5])
    .on("zoom", (event) => {
        g.attr("transform", event.transform);
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
    .attr("id", d => d.id);

velo.append("circle")
    .attr("r", 20)
    .attr("cx", d => nodeById["Porte d'Ivry"].x)
    .attr("cy", d => nodeById["Porte d'Ivry"].y);

velo.append("text")
    .attr("x", d => (nodeById[d.position]?.x || 0) + 12)
    .attr("y", d => nodeById[d.position]?.y || 0)
    .attr("dy", ".35em")
    .text(d => d.id);

// Fonctions Dijkstra, gestion de capacité, incidents et mise à jour des vélos
// Reprennent le même schéma de votre code initial, s'assurant de bien utiliser les nouvelles structures `window.ruesEtArrets` et `nodeById`

function calculateRoutesForVelos() {
    for (const velo of velos) {
        if (velo.isBroken || velo.assignedStops.size === 0) continue;
        const nextStop = getNextStopFromTournee(velo);
        if (!nextStop) continue;

        const { distances, previousNodes } = dijkstra({ nodes: nodeById, links: links }, velo.position);
        const path = reconstructPath(previousNodes, nextStop);

        if (path.length > 1) {
            const nextStopOnPath = path[1];
            moveVelo(velo, nodeById[nextStopOnPath]);
        }
    }
}

function moveVelo(velo, targetNode) {
    const currentPosition = nodeById[velo.position];
    if (!currentPosition || !targetNode) return;

    const distance = Math.sqrt((targetNode.x - currentPosition.x) ** 2 + (targetNode.y - currentPosition.y) ** 2);
    const duration = distance * 100;

    velo.isMoving = true;

    d3.select(`#${velo.id} circle`)
        .transition()
        .duration(duration)
        .attr("cx", targetNode.x)
        .attr("cy", targetNode.y)
        .on("end", () => {
            velo.position = targetNode.id;
            velo.distanceParcourue += distance;
            velo.autonomie -= distance;
            velo.isMoving = false;

            if (velo.position !== "Porte d'Ivry") {
                velo.charge += 50;
                velo.assignedStops.delete(targetNode.id);
            }

            if (velo.charge >= velo.capacite || velo.autonomie <= 0) {
                moveVelo(velo, nodeById["Porte d'Ivry"]);
                velo.charge = 0;
                velo.autonomie = WINTER_MODE ? 45 : 50;
            } else {
                calculateRoutesForVelos();
            }
        });

        function updateVeloInfo() {
            const infoContainer = d3.select("#velo-info-container");
            infoContainer.selectAll(".velo-info")
                .data(velos)
                .join("div")
                .attr("class", "velo-info")
                .html(d => `
                    <h4>${d.id}</h4>
                    <p>Position actuelle: ${d.position || 'En route'}</p>
                    <p>Autonomie restante: ${d.autonomie.toFixed(2)} km</p>
                    <p>Charge actuelle: ${d.charge} kg</p>
                    <p>Distance parcourue: ${d.distanceParcourue.toFixed(2)} km</p>
                    <p>Feux rencontrés: ${d.feuxRencontres}</p>
                `);
        }
        
}
// Appel des fonctions pour mise à jour
setInterval(updateVeloInfo, 2000);
setInterval(calculateRoutesForVelos, 50);
setInterval(simulateIncidents, 10000);
setInterval(updateTraiteesInfo, 2000);
