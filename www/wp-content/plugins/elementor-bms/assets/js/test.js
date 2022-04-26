// L'id du container, par exemple <div id="map"></div>
var mapID = "map";

let mapAttrib = 'Données cartographiques &copy; <a href="https://geoservices.ign.fr/">Géoservices</a>';
// Plan IGN avec une transparence de 50%
var PlanIGN = L.tileLayer(
    "https://wxs.ign.fr/{ignApiKey}/geoportail/wmts?" +
        "&REQUEST=GetTile&SERVICE=WMTS&VERSION=1.0.0&TILEMATRIXSET=PM" +
        "&LAYER={ignLayer}&STYLE={style}&FORMAT={format}" +
        "&TILECOL={x}&TILEROW={y}&TILEMATRIX={z}",
    {
        ignApiKey: "decouverte",
        ignLayer: "GEOGRAPHICALGRIDSYSTEMS.PLANIGNV2",
        style: "normal",
        format: "image/png",
        service: "WMTS",
        attribution: mapAttrib
    }
);

// Photographies aériennes en-dessous de Plan IGN
var OrthoIGN = L.tileLayer(
    "https://wxs.ign.fr/{ignApiKey}/geoportail/wmts?" +
        "&REQUEST=GetTile&SERVICE=WMTS&VERSION=1.0.0&TILEMATRIXSET=PM" +
        "&LAYER={ignLayer}&STYLE={style}&FORMAT={format}" +
        "&TILECOL={x}&TILEROW={y}&TILEMATRIX={z}",
    {
        ignApiKey: "decouverte",
        ignLayer: "ORTHOIMAGERY.ORTHOPHOTOS",
        style: "normal",
        format: "image/jpeg",
        service: "WMTS",
        attribution: mapAttrib
    }
);

let markers = L.layerGroup();
let myIcon = L.divIcon({
    html: '<i class="fa-solid fa-location-dot"></i><div class="marker-shadow"></div>',
    iconAnchor: [13, 36],
    popupAnchor: [0, -30],
    className: "marker"
});

L.marker([44.4401, 3.1969], { icon: myIcon }).addTo(markers).bindPopup('<a href="#">Test popup</a>').openPopup();

let pointUrl = document.getElementById("map").attributes["data-points"].nodeValue;

let polyline = L.polyline([], { color: "gray" });

let test = fetch(pointUrl)
    .then(response => {
        return response.json();
    })
    .then(result => {
        result.forEach(point => {
            polyline.addLatLng([point.latitude, point.longitude]);
            L.marker([point.latitude, point.longitude], {
                icon: myIcon,
                title: point.name
            })
                .addTo(markers)
                .bindPopup(point.name);
        });
    });

// Ma carte
var map = L.map(mapID, {
    center: [44.135, 3.8389],
    zoom: 9,
    // layers: [OrthoIGN, PlanIGN]
    layers: [PlanIGN, markers]
});

polyline.addTo(map);

L.control.scale().addTo(map);

let baseMaps = {
    Map: PlanIGN,
    Photos: OrthoIGN
};

let overlayMaps = {
    '<i class="fa-solid fa-house"></i> Points': markers
};
let layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);

function onMapClick(e) {
    console.log(e);
}

map.on("click", onMapClick);
