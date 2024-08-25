<!DOCTYPE html>
<html>
<head>
    <title>Vehicle Tracker</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 600px; }
        .label {
            font-size: 12px;
            font-weight: bold;
            color: #000;
            background-color: white;
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            border-radius: 3px;
            width: 100px;
            position: absolute;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <h1>Latest Vehicle Locations</h1>
    <div id="map"></div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([23.684304, 90.481493], 8);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var vehicleData = @json($latestData);

        console.log(vehicleData); // Debugging: log the vehicle data

        vehicleData.forEach(function(vehicle) {
            var marker = L.marker([vehicle.lat, vehicle.lng])
                .bindPopup("<b>" + vehicle.carnum + "</b><br>Last updated: " + vehicle.time)
                .addTo(map);
            
            var label = L.divIcon({
                className: 'label',
                html: vehicle.carnum,
                iconSize: [100, 20],
                iconAnchor: [50, 0] // Center the label horizontally above the marker
            });

            L.marker([vehicle.lat, vehicle.lng], { icon: label }).addTo(map);
        });
    </script>
</body>
</html>