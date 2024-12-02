class Arret {
    constructor(id, lat, lon, adjacents = []) {
        this.id = id;
        this.lat = lat;
        this.lon = lon;
        this.adjacents = adjacents;
    }

    addAdjacent(arret) {
        this.adjacents.push(arret);
    }

    isAdjacent(arretId) {
        return this.adjacents.some(adjacent => adjacent.id === arretId);
    }
}

export default Arret;
