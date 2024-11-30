class Arret {
    /**
     * Crée un arrêt avec des propriétés spécifiques
     * @param {number} id - Identifiant unique de l'arrêt
     * @param {number} lat - Latitude de l'arrêt
     * @param {number} lon - Longitude de l'arrêt
     * @param {Array} adjacents - Liste des arrêts adjacents
     */
    constructor(id, lat, lon, adjacents = []) {
        this.id = id;
        this.lat = lat;
        this.lon = lon;
        this.adjacents = adjacents; // Liste des arrêts adjacents
    }

    /**
     * Ajoute un arrêt adjacent
     * @param {Arret} arret - L'arrêt adjacent à ajouter
     */
    addAdjacent(arret) {
        this.adjacents.push(arret);
    }

    /**
     * Vérifie si un arrêt est adjacent
     * @param {number} arretId - L'identifiant de l'arrêt à vérifier
     * @returns {boolean} - True si l'arrêt est adjacent
     */
    isAdjacent(arretId) {
        return this.adjacents.some(adjacent => adjacent.id === arretId);
    }
}

async function fetchArrets() {
    const response = await fetch('http://localhost/projet_poubelle_verte/map/api/arrets.php');
    if (!response.ok) {
        throw new Error('Erreur lors de la récupération des arrêts');
    }
    const data = await response.json();
    console.log("Données brutes reçues de l'API :", data); // Log pour analyser les données
    return data;
}


async function initializeArrets() {
    const rawArrets = await fetchArrets();
    if (!rawArrets || !rawArrets.data) {
        throw new Error('Les données reçues de l\'API ne contiennent pas de clé "data".');
    }
    const arrets = rawArrets.data.map(arret => {
        if (!Array.isArray(arret.adjacents)) {
            console.warn(`L'arrêt ${arret.id} n'a pas de liste d'adjacents valide.`, arret);
            arret.adjacents = [];
        }
        return new Arret(arret.id, arret.lat, arret.lon, arret.adjacents);
    });
    return arrets;
}


// Définition de la classe CheminPossibleDto
class CheminPossibleDto {
    constructor(arrets = []) {
        this.arrets = arrets;
    }

    /**
     * Ajoute un arrêt au chemin
     * @param {Object} arret - L'arrêt à ajouter
     */
    addArret(arret) {
        this.arrets.push(arret);
    }

    /**
     * Supprime le dernier arrêt du chemin
     */
    removeArret() {
        this.arrets.pop();
    }

    /**
     * Retourne le dernier arrêt du chemin
     * @returns {Object} Dernier arrêt
     */
    dernierArret() {
        return this.arrets[this.arrets.length - 1];
    }

    /**
     * Calcule la distance totale du chemin
     * @returns {number} Distance totale en km
     */
    calculDistance() {
        let distance = 0;
        for (let i = 1; i < this.arrets.length; i++) {
            const prev = this.arrets[i - 1];
            const curr = this.arrets[i];
            distance += ItineraireUtils.distanceEntreArrets(prev, curr);
        }
        return distance;
    }

    addArret(arret) {
        if (!arret || typeof arret !== 'object') {
            throw new Error('L\'arrêt ajouté doit être un objet valide.');
        }
        this.arrets.push(arret);
    }
}
// Définition de l'objet ramassageCyclisteVelo
const ramassageCyclisteVelo = {
    velo: {
        autonomie: 50, // en km
        charge: 0,
        chargeMax: 100,
        chargeMaxAtteint() {
            return this.charge >= this.chargeMax;
        }
    }
};


class ItineraireUtils {
    /**
     * Recherche des chemins possibles à partir d'un arrêt
     * @param {Object} arret - Arrêt courant
     * @param {number} numeroChemin - Indice du chemin actuel
     * @param {Array} cheminPossibleDtos - Liste des chemins possibles
     */
    static chercheChemin(arret, numeroChemin, cheminPossibleDtos) {
        if (arret.adjacents.length > 1) { // Remplacez "arret.arretAdjacents" par "arret.adjacents"
            // Cas d'un arrêt de liaison
            if (arret.adjacents.length === 2) { // Remplacez "arret.arretAdjacents" par "arret.adjacents"
                arret.adjacents.forEach((arretAdjacent) => { // Remplacez "arret.arretAdjacents" par "arret.adjacents"
                    if (!cheminPossibleDtos[numeroChemin].arrets.includes(arretAdjacent)) {
                        cheminPossibleDtos[numeroChemin].arrets.push(arretAdjacent);
                        this.chercheChemin(arretAdjacent, numeroChemin, cheminPossibleDtos);
                    }
                });
            } else {
                const arrets = new Set();
                arret.adjacents.forEach((arretAdjacent) => { // Remplacez "arret.arretAdjacents" par "arret.adjacents"
                    if (!cheminPossibleDtos[numeroChemin].arrets.includes(arretAdjacent)) {
                        arrets.add(arretAdjacent);
                    }
                });

                if (arrets.size > 0) {
                    const sauvegarde = JSON.parse(JSON.stringify(cheminPossibleDtos[numeroChemin]));
                    cheminPossibleDtos.splice(numeroChemin, 1);

                    Array.from(arrets).forEach((arret1) => {
                        const nouveauChemin = JSON.parse(JSON.stringify(sauvegarde));
                        nouveauChemin.arrets.push(arret1);
                        cheminPossibleDtos.push(nouveauChemin);
                        this.chercheChemin(arret1, cheminPossibleDtos.indexOf(nouveauChemin), cheminPossibleDtos);
                    });
                }
            }
        }
    }

    /**
     * Trie les chemins pour obtenir le chemin le plus court par arrêt
     * @param {Array} cheminPossibleDtos - Liste des chemins possibles
     * @returns {Array} Liste des chemins les plus courts
     */
    static cheminPlusCourtParArret(cheminPossibleDtos) {
        const arretCheminPossibleMap = new Map();

        cheminPossibleDtos.forEach((cheminPossibleDto) => {
            const dernierArret = cheminPossibleDto.arrets[cheminPossibleDto.arrets.length - 1];
            const distance = this.calculDistance(cheminPossibleDto.arrets);

            if (
                !arretCheminPossibleMap.has(dernierArret) ||
                arretCheminPossibleMap.get(dernierArret).distance > distance
            ) {
                arretCheminPossibleMap.set(dernierArret, cheminPossibleDto);
            }
        });

        return Array.from(arretCheminPossibleMap.values());
    }

    /**
     * Recherche le chemin le plus court
     * @param {Array} cheminPossibleDtos - Liste des chemins possibles
     * @returns {Object} Chemin le plus court
     */
    static chercheCheminPlusCourt(cheminPossibleDtos) {
        return cheminPossibleDtos.reduce((plusCourt, chemin) =>
            !plusCourt || this.calculDistance(chemin.arrets) < this.calculDistance(plusCourt.arrets) ? chemin : plusCourt
            , null);
    }

    /**
     * Gère la charge et le ramassage des arrêts
     * @param {Object} ramassageCyclisteVelo - Objet contenant les informations du vélo et du cycliste
     * @param {Array} allCheminsPossibles - Liste de tous les chemins possibles
     * @param {Set} arretsRamasse - Liste des arrêts ramassés
     * @param {Map} itineraireMap - Map des itinéraires
     * @returns {Array} Liste des arrêts parcourus
     */
    static ramasseCharge(ramassageCyclisteVelo, allCheminsPossibles, arretsRamasse, itineraireMap) {
        if (allCheminsPossibles.length > 0) {
            const cheminPlusCourtParArret = this.cheminPlusCourtParArret(allCheminsPossibles);
            const cheminPossibleLePlusCourt = this.chercheCheminPlusCourt(cheminPlusCourtParArret);

            const arrets = [...cheminPossibleLePlusCourt.arrets];

            cheminPossibleLePlusCourt.arrets.forEach((arret) => {
                const itineraireArrets = itineraireMap.get(ramassageCyclisteVelo).itineraireArrets;

                if (
                    itineraireArrets.length === 0 ||
                    arret !== itineraireArrets[itineraireArrets.length - 1].arret
                ) {
                    itineraireArrets.push({
                        itineraire: itineraireMap.get(ramassageCyclisteVelo),
                        arret,
                        ordrePassage: itineraireArrets.length + 1
                    });
                }
            });

            if (ramassageCyclisteVelo.velo.autonomie >= this.calculDistance(cheminPossibleLePlusCourt.arrets)) {
                ramassageCyclisteVelo.velo.autonomie -= this.calculDistance(cheminPossibleLePlusCourt.arrets);

                arrets.pop();

                for (let i = cheminPossibleLePlusCourt.arrets.length - 1; i >= 0; i--) {
                    const arret = cheminPossibleLePlusCourt.arrets[i];
                    arrets.push(arret);

                    const itineraireArrets = itineraireMap.get(ramassageCyclisteVelo).itineraireArrets;
                    itineraireArrets.push({
                        itineraire: itineraireMap.get(ramassageCyclisteVelo),
                        arret,
                        ordrePassage: itineraireArrets.length + 1
                    });

                    if (
                        !ramassageCyclisteVelo.velo.chargeMaxAtteint &&
                        !arret.ramasse
                    ) {
                        arretsRamasse.add(arret);
                    }
                }
            }
            return arrets;
        }
        return null;
    }

    /**
  * Supprime un arrêt des chemins possibles
  * @param {Array} cheminPossibleDtos - Liste des chemins possibles
  * @param {Object} arret - L'arrêt à supprimer
  */
    static removeArrets(cheminPossibleDtos, arret) {
        const toRemove = [];
        cheminPossibleDtos.forEach((chemin) => {
            if (chemin.dernierArret() === arret) {
                chemin.removeArret();
                if (chemin.arrets.length === 0) {
                    toRemove.push(chemin);
                }
            }
        });
        cheminPossibleDtos = cheminPossibleDtos.filter(chemin => !toRemove.includes(chemin));
    }

    /**
     * Calcule la distance totale pour un ensemble d'arrêts
     * @param {Array} arrets - Liste des arrêts
     * @returns {number} Distance totale en km
     */
    static calculDistance(arrets) {
        let distance = 0;
        for (let i = 1; i < arrets.length; i++) {
            const prev = arrets[i - 1];
            const curr = arrets[i];
            distance += this.distanceEntreArrets(prev, curr);
        }
        return distance;
    }

    /**
     * Calcule la distance entre deux arrêts
     * @param {Object} arret1 - Premier arrêt
     * @param {Object} arret2 - Deuxième arrêt
     * @returns {number} Distance en km
     */
    static distanceEntreArrets(arret1, arret2) {
        const R = 6371; // Rayon de la Terre en km
        const dLat = this.toRad(arret2.lat - arret1.lat);
        const dLon = this.toRad(arret2.lon - arret1.lon);
        const lat1 = this.toRad(arret1.lat);
        const lat2 = this.toRad(arret2.lat);

        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1) * Math.cos(lat2) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c; // Distance en km
    }

    /**
 * Obtient le dernier ordre de passage dans l'itinéraire
 * @param {Array} itineraireArrets - Liste des arrêts de l'itinéraire
 * @returns {number} Dernier ordre de passage
 */
    static getDernierOrdrePassage(itineraireArrets) {
        if (itineraireArrets.length === 0) return 0;
        return Math.max(...itineraireArrets.map(it => it.ordrePassage));
    }

    /**
  * Obtient le dernier ordre de ramassage dans l'itinéraire
  * @param {Array} itineraireArrets - Liste des arrêts de l'itinéraire
  * @returns {number} Dernier ordre de ramassage
  */
    static getDernierOrdreRamassage(itineraireArrets) {
        if (itineraireArrets.length === 0) return 0;
        return Math.max(...itineraireArrets.map(it => it.ordreRamassage || 0));
    }

    /**
     * Convertit les degrés en radians
     * @param {number} deg - Degrés
     * @returns {number} Radians
     */
    static toRad(deg) {
        return deg * (Math.PI / 180);
    }
}

initializeArrets()
    .then(arrets => {
        console.log('Liste des arrêts initialisés :', arrets);

        // Exemple : Trouver un arrêt par ID et vérifier ses adjacents
        const arret1 = arrets.find(arret => arret.id === 1);
        if (arret1) {
            console.log('Arret 1 est adjacent à 2 ?', arret1.isAdjacent(2));
        }

        // Exemple avec ItineraireUtils
        const cheminPossibleDtos = [new CheminPossibleDto([arret1])];
        ItineraireUtils.chercheChemin(arret1, 0, cheminPossibleDtos);
        console.log('Chemins possibles à partir de l\'arrêt 1 :', cheminPossibleDtos);
    })
    .catch(error => {
        console.error('Erreur lors de l\'initialisation des arrêts :', error);
    });


export { CheminPossibleDto, ramassageCyclisteVelo, ItineraireUtils };
export default ItineraireUtils;
