// L'id du container, par exemple <div id="map"></div>
const mapID = "map";

let mapAttrib = 'Données cartographiques &copy; <a href="https://geoservices.ign.fr/">Géoservices</a>';
// Plan IGN avec une transparence de 50%
const PlanIGN = L.tileLayer(
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
const OrthoIGN = L.tileLayer(
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
let typesUrl = document.getElementById("map").attributes["data-types"].nodeValue;
let trailUrl = document.getElementById("map").attributes["data-trail"].nodeValue;

let polyline = L.polyline([], { color: "gray" });

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
    if (link == "#") {
        titleDiv = `<div class="map-popup-title">${title}</div>`;
    }
    let popup = `<div class="map-popup-container">
        ${imgDiv}
        ${titleDiv}
        ${text}
        </div>`;
    return popup;
}

const pointSort = (a, b) => {
    return map.distance(a.getLatLng(), b.getLatLng());
};

async function loadIconsType() {
    let icons = {};
    let iconsJSON = await loadJSON(typesUrl);

    iconsJSON.forEach(icon => {
        icons[icon.id] = {
            picture: icon.picture,
            name: icon.tag
        };
    });

    return icons;
}

const createIcon = (point, icons) => {
    let locationIcon = L.divIcon({
        html: '<i class="fa-solid fa-location-dot"></i><div class="marker-shadow"></div>',
        iconAnchor: [13, 36],
        popupAnchor: [0, -30],
        className: "marker"
    });

    if (point.types_id[0] === undefined) {
        return locationIcon;
    }

    let icon = icons[point.types_id[0]];

    return L.icon({
        iconUrl: icon.picture,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });
};

async function markersInit(icons) {
    let markers = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: 50,
        disableClusteringAtZoom: 14
    });
    let points = await loadJSON(pointUrl);

    let postsCollection = await wp.api.collections.Posts().fetch();

    points.forEach(point => {
        let link = "#";
        postsCollection.forEach(post => {
            if (point.name == post.title.rendered) {
                link = post.link;
            }
        });

        L.marker([point.latitude, point.longitude], {
            icon: createIcon(point, icons),
            title: point.name
        })
            .addTo(markers)
            .bindPopup(
                createPopup({
                    title: point.name,
                    text: point.description,
                    image: point.medias[0],
                    link: link
                })
            )
            .on("click", e => {
                e.target._map.panTo([e.latlng.lat + 0.01, e.latlng.lng]);
            });
    });

    return markers;
}

async function mapInit() {
    let icons = await loadIconsType();
    let markers = await markersInit(icons);
    let map = L.map(mapID, {
        center: [44.135, 3.8389],
        zoom: 9,
        layers: [PlanIGN, markers]
    });

    let baseMaps = {
        Map: PlanIGN,
        Photos: OrthoIGN
    };
    let overlayMaps = {
        '<i class="fa-solid fa-house"></i> Points d\'intérêt': markers
    };
    L.control.layers(baseMaps, overlayMaps).addTo(map);

    return map;
}

async function addMapControl() {
    let map = await mapInit();

    new L.GPX(trailUrl, {
        async: true,
        marker_options: {
            startIconUrl: "",
            endIconUrl: "",
            shadowUrl: ""
        },
        polyline_options: {
            color: "#1b3c70",
            opacity: 0.75,
            weight: 3,
            lineCap: "round"
        }
    })
        .on("loaded", function (e) {
            map.fitBounds(e.target.getBounds());
            // console.log(e.target.get_elevation_data());//get elevation data from gpx
        })
        .addTo(map);

    L.control.scale().addTo(map);

    // map.on("click", console.log);
}

addMapControl();
