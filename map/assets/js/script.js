console.log("Script.js is loaded");

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
    .scaleExtent([0.5, 5]) // Définir les niveaux minimum et maximum de zoom
    .on("zoom", (event) => {
        g.attr("transform", event.transform); // Appliquer la transformation au groupe principal
    });

// Appliquer le zoom au SVG
svg.call(zoom);

// Fonction pour initialiser la carte
function initMap() {
    console.log("Initialisation de la carte");

    if (!window.ruesEtArrets) {
        console.error("Les données des rues et arrêts ne sont pas disponibles.");
        return;
    }

    const nodes = [];
    const links = [];
    const nodeById = {};

    // Préparer les données des arrêts et des liens
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
                existingNode = { id: arret.name, type: "stop" };
                nodes.push(existingNode);
                nodeById[arret.name] = existingNode;
            }

            // Relier uniquement les arrêts consécutifs sur chaque rue
            if (i > 0) {
                links.push({
                    source: nodeById[arrets[i - 1].name],
                    target: existingNode,
                    street: rue.replace(/\s+/g, '-')
                });
            }
        }
    }

    // Initialiser la simulation de forces pour organiser les liens
    const simulation = d3.forceSimulation(nodes)
        .force("link", d3.forceLink(links).id(d => d.id).distance(40).strength(0.1)) // Lien court avec flexibilité
        .force("charge", d3.forceManyBody().strength(-1200)) // Force de répulsion augmentée
        .force("collision", d3.forceCollide().radius(50)) // Collision augmentée pour espacer davantage
        .force("center", d3.forceCenter(width / 2, height / 2))
        .on("tick", ticked);

    // Calculer les positions finales sans animation
    for (let i = 0; i < 600; i++) simulation.tick();
    simulation.stop();

    // Afficher les liens entre les arrêts
    const link = g.append("g")
        .attr("class", "links")
        .selectAll("line")
        .data(links)
        .join("line")
        .attr("class", d => `link ${d.street}`)
        .attr("stroke", "grey")
        .attr("stroke-width", 1.2)
        .attr("opacity", 0.7);

    // Afficher les nœuds (stations)
    const node = g.append("g")
        .attr("class", "nodes")
        .selectAll("g")
        .data(nodes)
        .join("g");

    node.append("circle")
        .attr("r", 15) // Taille augmentée pour les arrêts
        .attr("class", "stop")
        .attr("fill", "blue");

    // Afficher les noms des arrêts
    node.append("text")
        .attr("class", "label")
        .text(d => d.id)
        .attr("x", 50) // Décaler un peu plus pour éviter les chevauchements
        .attr("y", 3)
        .style("font-size", "50px") // Ajuster la taille de la police pour plus de lisibilité
        .style("fill", "black");

    // Fonction pour fixer les positions calculées
    function ticked() {
        link
            .attr("x1", d => d.source.x)
            .attr("y1", d => d.source.y)
            .attr("x2", d => d.target.x)
            .attr("y2", d => d.target.y);

        node
            .attr("transform", d => `translate(${d.x},${d.y})`);
    }

    ticked(); // Appliquer les positions calculées
}

// Initialiser la carte après le chargement des données
document.addEventListener("DOMContentLoaded", function() {
    fetch("get_streets_data.php")
        .then(response => response.json())
        .then(data => {
            window.ruesEtArrets = data;
            initMap();
        })
        .catch(error => console.error("Erreur lors du chargement des données : ", error));
});
