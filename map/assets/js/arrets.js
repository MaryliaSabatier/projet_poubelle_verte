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
        return this.adjacents.includes(arretId);
    }
}

export default Arret;
