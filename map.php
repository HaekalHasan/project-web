<!DOCTYPE html>
<html>
<head>
    <title>Map of Hearing Locations</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Map of Hearing Locations</h1>
    <div id="map" style="width: 100%; height: 500px;"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap" async defer></script>
    <script>
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: {lat: -34.397, lng: 150.644}
            });
            var marker = new google.maps.Marker({
                position: {lat: -34.397, lng: 150.644},
                map: map
            });
        }
    </script>
</body>
</html>
