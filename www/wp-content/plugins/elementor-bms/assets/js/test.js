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

let pointUrl = document.getElementById("map").attributes["data-points"].nodeValue;

let polyline = L.polyline([], { color: "gray" });
let locationIcon = L.divIcon({
    html: '<i class="fa-solid fa-location-dot"></i><div class="marker-shadow"></div>',
    iconAnchor: [13, 36],
    popupAnchor: [0, -30],
    className: "marker"
});

async function loadJSON(url) {
    let points = await fetch(url);
    return points.json();
}

function createPopup({ title, text, image, link }) {
    let imgDiv = "";
    if (image !== undefined) {
        imgDiv = `<img src="${image.link}" alt="${title}" class="map-popup-image">`;
    }
    let titleDiv = `<a href="${link}" class="map-popup-title">${title}</a>`;
    // let textDiv = `<p class="map-popup-text">${text}</p>`;
    let popup = `<div class="map-popup-container">
        ${imgDiv}
        ${titleDiv}
        ${text}
        </div>`;
    return popup;
}

async function markersInit() {
    let markers = L.layerGroup();
    let points = await loadJSON(pointUrl);

    points.forEach(point => {
        polyline.addLatLng([point.latitude, point.longitude]);
        L.marker([point.latitude, point.longitude], {
            icon: locationIcon,
            title: point.name
        })
            .addTo(markers)
            .bindPopup(
                createPopup({
                    title: point.name,
                    text: point.description,
                    image: point.medias[0],
                    link: "#"
                })
            );
    });

    return markers;
}

async function mapInit() {
    let markers = await markersInit();
    let map = L.map(mapID, {
        center: [44.135, 3.8389],
        zoom: 9,
        layers: [PlanIGN]
    });

    var conditionalLayer = L.conditionalMarkers(markers.getLayers(), { maxMarkers: 2 }).addTo(map);

    let baseMaps = {
        Map: PlanIGN,
        Photos: OrthoIGN
    };
    let overlayMaps = {
        '<i class="fa-solid fa-house"></i> Points': conditionalLayer
    };
    L.control.layers(baseMaps, overlayMaps).addTo(map);

    return map;
}

async function addMapControl() {
    let map = await mapInit();

    polyline.addTo(map);
    L.control.scale().addTo(map);

    function onMapClick(e) {
        console.log(e);
    }

    map.on("click", onMapClick);
}

addMapControl();
