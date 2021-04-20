const L = require("leaflet/dist/leaflet");

function createMap(mapId = "mapid", mapUrl = "https://b.tile.openstreetmap.org/{z}/{x}/{y}.png",
                   initLat = 43.2953, initLon = 5.3691, initZoom = 15, minZoom = 13, maxZoom = 18)
{
    let mymap = L.map(mapId).setView([initLat, initLon], initZoom);
    L.tileLayer(mapUrl, {
        attribution: '© données par <a href="https://www.openstreetmap.org/copyright">les contributeurs & contributrices d’OpenStreetMap</a> sous licence libre ODbL,' +
            '<br>Fond de carte par <a href="https://www.hotosm.org/updates/2013-09-29_a_new_window_on_openstreetmap_data">Yohan Boniface & Humanitarian ' +
            'OpenStreetMap Team</a> sous licence domaine public CC0 hébergé par <a href="https://www.openstreetmap.fr/mentions-legales/">OSM France</a>',
        minZoom: minZoom,
        maxZoom: maxZoom
    }).addTo(mymap);

    return mymap;
}

function getLeafletMarkerIcon()
{
    return L.divIcon({
        className: 'map-marker-icon map-marker-icon-display',
        html: "<div class='marker-pin'></div><span class='icon-vision'></span>",
        iconSize: [30, 42],
        iconAnchor: [15, 42],
        popupAnchor:  [0, -35]
    })
}

function getLeafletIcon(el)
{
    return L.divIcon({
        className: 'map-marker-icon map-marker-icon-' + el.id,
        html: "<div class='marker-pin'></div><span class='icon-"+el.icon+"'></span>",
        iconSize: [30, 42],
        iconAnchor: [15, 42],
        popupAnchor:  [0, -35]
    })
}

module.exports = {
    createMap,
    getLeafletMarkerIcon,
    getLeafletIcon
}