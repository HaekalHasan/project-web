<!DOCTYPE html>
<html>
<head>
    <title>Map of Hearing Locations</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="container map">
        <h1>Map of Hearing Locations</h1>
        <script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 15,
                    center: { lat: -6.315745, lng: 106.791785 }
                });
                var marker = new google.maps.Marker({
                    position: { lat: -6.315745, lng: 106.791785 },
                    map: map
                });
            }
        </script>
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.6031647189043!2d106.79178547399098!3d-6.3157451936736395!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ee3e065d4f6b%3A0xe176f81a31564166!2sUniversitas%20Pembangunan%20Nasional%20%22Veteran%22%20Jakarta!5e0!3m2!1sid!2sid!4v1717478686171!5m2!1sid!2sid" 
            width="300" 
            height="300" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
        
        <a href="profile.php">Back to Profile</a>
    </div>
</body>
</html>
