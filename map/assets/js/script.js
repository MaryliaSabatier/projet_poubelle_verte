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
                street: rue.replace(/\s+/g, '-')
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

/**
 * Dijkstra's Algorithm to find the shortest path in the map
 * @param {Object} graph - The graph containing nodes and links
 * @param {String} startNode - The starting point for the pathfinding
 * @returns {Object} - The shortest paths and distances from the startNode
 */
function dijkstra(graph, startNode) {
    const distances = {};
    const previousNodes = {};
    const visitedNodes = new Set();
    const unvisitedNodes = new Set(Object.keys(graph.nodes));
    
    // Initialize distances and previous nodes
    for (const nodeId of unvisitedNodes) {
        distances[nodeId] = Infinity;
        previousNodes[nodeId] = null;
    }
    distances[startNode] = 0;

    while (unvisitedNodes.size > 0) {
        // Get the closest unvisited node
        const currentNode = [...unvisitedNodes].reduce((closestNode, nodeId) => {
            return distances[nodeId] < distances[closestNode] ? nodeId : closestNode;
        }, [...unvisitedNodes][0]);

        // Remove the node from unvisited set and mark as visited
        unvisitedNodes.delete(currentNode);
        visitedNodes.add(currentNode);

        // Update distances to neighboring nodes
        for (const link of graph.links.filter(link => link.source.id === currentNode)) {
            const neighbor = link.target.id;
            if (!visitedNodes.has(neighbor)) {
                const newDist = distances[currentNode] + link.distance;
                if (newDist < distances[neighbor]) {
                    distances[neighbor] = newDist;
                    previousNodes[neighbor] = currentNode;
                }
            }
        }
    }

    return { distances, previousNodes };
}

/**
 * Find the shortest path using Dijkstra's algorithm from the depot to all stops.
 */
function calculateShortestPaths() {
    const graph = {
        nodes: nodeById,
        links: links
    };
    const depot = depotIvry.id;
    const { distances, previousNodes } = dijkstra(graph, depot);
    
    console.log('Shortest distances from depot:', distances);
    console.log('Previous nodes for path reconstruction:', previousNodes);
    
    // Example: Print shortest path to a specific stop (replace 'stopId' with an actual stop id)
    function reconstructPath(stopId) {
        const path = [];
        let currentNode = stopId;
        while (currentNode !== null) {
            path.unshift(currentNode);
            currentNode = previousNodes[currentNode];
        }
        return path;
    }
    
    // Calculate and display paths to all stops
    for (const stopId in distances) {
        if (stopId !== depot) {
            console.log(`Shortest path to ${stopId}:`, reconstructPath(stopId));
        }
    }
}

// Call this function after setting up the nodes and links
calculateShortestPaths();
