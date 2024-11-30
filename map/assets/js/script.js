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
    const projection = d3.geoMercator()
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
    }

    const colorScale = d3.scaleOrdinal(d3.schemeCategory10);

    // Ajout des liens
    const link = g.append("g")
        .attr("class", "links")
        .selectAll("line")
        .data(links)
        .join("line")
        .attr("class", d => `link ${d.street}`)
        .attr("stroke", d => colorScale(d.street))
        .attr("stroke-width", 1.5)
        .attr("opacity", 0.7)
        .attr("x1", d => d.source.x)
        .attr("y1", d => d.source.y)
        .attr("x2", d => d.target.x)
        .attr("y2", d => d.target.y);

    // Ajout des nœuds
    const node = g.append("g")
        .attr("class", "nodes")
        .selectAll("g")
        .data(nodes)
        .join("g");

    node.append("circle")
        .attr("r", 15)
        .attr("class", "stop")
        .attr("fill", "#1f77b4")
        .attr("stroke", "#333")
        .attr("stroke-width", 1.5)
        .attr("cx", d => d.x)
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

    node.append("text")
        .attr("class", "label")
        .text(d => d.id)
        .attr("x", d => d.x + 15)
        .attr("y", d => d.y + 5)
        .style("font-size", "20px")
        .style("fill", "#333")
        .style("pointer-events", "none");
}

// Initialiser la carte après le chargement des données
document.addEventListener("DOMContentLoaded", function() {
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
