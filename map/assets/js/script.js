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

const svg = d3.select("#graph-container").append("svg")
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

function moveVelos() {
    for (const velo of velos) {
        const rueActuelle = Object.keys(ruesEtArrets).find(rue => ruesEtArrets[rue].stops.includes(velo.position));
        const indexArretActuel = ruesEtArrets[rueActuelle].stops.indexOf(velo.position);
        
        if (indexArretActuel < 0) continue;

        const prochainIndexArret = (indexArretActuel + 1) % ruesEtArrets[rueActuelle].stops.length;
        const prochainArret = ruesEtArrets[rueActuelle].stops[prochainIndexArret];

        const distance = ruesEtArrets[rueActuelle].distances[indexArretActuel];
        const feux = ruesEtArrets[rueActuelle].trafficLights[indexArretActuel];

        // Mettre à jour la distance parcourue et l'autonomie du vélo
        velo.distanceParcourue += distance;
        velo.autonomie -= distance;
        velo.feuxRencontres += feux;

        if (velo.autonomie <= 0) {
            alert(`${velo.id} doit retourner à la base pour recharger.`);
            velo.position = "Porte d'Ivry";
            velo.autonomie = 50; // Recharge
            velo.distanceParcourue = 0;
            velo.feuxRencontres = 0;
        } else {
            velo.position = prochainArret;
        }
    }

    simulation.alpha(0.1).restart();
}

setInterval(moveVelos, 2000);
