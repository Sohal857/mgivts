<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Tracking</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        @yield('content')
    </div>
    @yield('map_script')
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: { lat: 23.6843, lng: 90.4815 }
            });

            // Call the function to plot markers and polyline
            plotMarkersAndPolyline(map);
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZxMlTX9nVXfWdQRoWOdbLLA8VykjxJ9A&callback=initMap" async defer></script>
</body>
</html>
