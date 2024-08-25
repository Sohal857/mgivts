@extends('layouts.app')

@section('content')
    <div id="map" style="height: 500px;"></div>
@endsection

@section('map_script')
<script>
    const vehicleData = @json($vehicleData);
    console.log('Vehicle Data:', vehicleData);

    function calculateBearing(startPoint, endPoint) {
        const startLat = startPoint.lat * Math.PI / 180;
        const startLng = startPoint.lng * Math.PI / 180;
        const endLat = endPoint.lat * Math.PI / 180;
        const endLng = endPoint.lng * Math.PI / 180;

        const dLng = endLng - startLng;
        const y = Math.sin(dLng) * Math.cos(endLat);
        const x = Math.cos(startLat) * Math.sin(endLat) - Math.sin(startLat) * Math.cos(endLat) * Math.cos(dLng);
        const brng = Math.atan2(y, x) * 180 / Math.PI;

        return (brng + 360) % 360; // Normalize to 0-360
    }

    function getIconUrl(bearing, engine_stat, on_halt_time, speed) {
        const roundedBearing = Math.round(bearing / 60) * 60;  // Round to the nearest 60 degrees
        let iconName = 'default_car';

        if (engine_stat === 'ON' && on_halt_time <= 15) {
            iconName = 'green_car';
        } else if (engine_stat === 'OFF') {
            iconName = 'red_car';
        } else if (engine_stat === 'ON' && on_halt_time > 15) {
            iconName = 'blue_car';
        } else if (speed > 60) {
            iconName = 'gray_car';
        }

        const iconUrl = `{{ asset('icons/${iconName}/car_') }}${roundedBearing}.png`;
        console.log(`Icon URL for bearing ${bearing}: ${iconUrl}`);
        return iconUrl;
    }

    function calculateMidpoint(point1, point2) {
        const lat = (point1.lat + point2.lat) / 2;
        const lng = (point1.lng + point2.lng) / 2;
        return { lat: lat, lng: lng };
    }

    function plotMarkersAndPolyline(map) {
        if (vehicleData.length === 0) {
            console.error('No vehicle data to plot markers.');
            return;
        }

        const coordinates = [];
        vehicleData.forEach(function(data, index) {
            const position = { lat: parseFloat(data.lat), lng: parseFloat(data.lng) };
            console.log(`Position at index ${index}:`, position);

            if (isNaN(position.lat) || isNaN(position.lng)) {
                console.error(`Invalid position at index ${index}:`, position);
                return;
            }

            coordinates.push(position);

            let bearing = 0;
            if (index < vehicleData.length - 1) {
                const nextPosition = { lat: parseFloat(vehicleData[index + 1].lat), lng: parseFloat(vehicleData[index + 1].lng) };
                bearing = calculateBearing(position, nextPosition);
            } else if (index > 0) {
                const prevPosition = { lat: parseFloat(vehicleData[index - 1].lat), lng: parseFloat(vehicleData[index - 1].lng) };
                bearing = calculateBearing(prevPosition, position);
            }

            const iconUrl = getIconUrl(bearing, data.engine_stat, data.on_halt_time, data.speed);

            const marker = new google.maps.Marker({
                position: position,
                map: map,
                icon: {
                    url: iconUrl,
                    scaledSize: new google.maps.Size(32, 32), // Adjust icon size if necessary
                    anchor: new google.maps.Point(16, 16),   // Adjust the anchor point to center the icon
                },
                title: data.carnum // Adding title for debugging purposes
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div>
                        <h3>${data.carnum}</h3>
                        <p><strong>Time:</strong> ${data.time}</p>
                        <p><strong>Engine:</strong> ${data.engine_stat}</p>
                        <p><strong>Speed:</strong> ${data.speed} km/h</p>
                        <p><strong>Halting (Engine On): </strong> ${data.on_halt_time}</p>
                        <p><strong>Halting (Engine Off): </strong> ${data.off_halt_time}</p>
                    </div>
                `,
            });

            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });

            // Adding mid-arrow
            if (index < vehicleData.length - 1) {
                const nextPosition = { lat: parseFloat(vehicleData[index + 1].lat), lng: parseFloat(vehicleData[index + 1].lng) };
                const midpoint = calculateMidpoint(position, nextPosition);
                const arrowBearing = calculateBearing(position, nextPosition);

                new google.maps.Marker({
                    position: midpoint,
                    map: map,
                    icon: {
                        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                        scale: 4,
                        rotation: arrowBearing,
                        fillColor: '#FF0000',
                        fillOpacity: 1,
                        strokeWeight: 1
                    },
                    title: 'Direction Arrow'
                });
            }
        });

        console.log('Coordinates:', coordinates);

        const polyline = new google.maps.Polyline({
            path: coordinates,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2,
            icons: [{
                icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW },
                offset: '50%'
            }]
        });

        polyline.setMap(map);
    }

    window.plotMarkersAndPolyline = plotMarkersAndPolyline;  // Ensure the function is globally accessible

    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 10,
            center: { lat: 23.6843, lng: 90.4815 },
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "geometry",
                    "stylers": [{ "visibility": "off" }]
                },
                {
                    "featureType": "poi",
                    "stylers": [{ "visibility": "off" }]
                },
                {
                    "featureType": "poi.park",
                    "elementType": "geometry",
                    "stylers": [{ "visibility": "off" }]
                },
                {
                    "featureType": "road",
                    "elementType": "labels",
                    "stylers": [{ "visibility": "off" }]
                },
                {
                    "featureType": "transit",
                    "elementType": "labels.icon",
                    "stylers": [{ "visibility": "off" }]
                },
                {
                    "featureType": "water",
                    "elementType": "labels",
                    "stylers": [{ "visibility": "off" }]
                }
            ]
        });

        plotMarkersAndPolyline(map);
    }

    window.initMap = initMap;
</script>
@endsection
