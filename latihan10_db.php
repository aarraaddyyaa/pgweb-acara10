<?php
// Koneksi ke Database studikasus
$conn = new mysqli("localhost", "root", "", "studikasus");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari tabel data_persampahan
$query =$conn->query("SELECT * FROM data_persampahan");
$data = array();
while ($row =$query->fetch_assoc()) {
    $data[] =$row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Web GIS - Data Persampahan</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">
    <style>
        html, body, #map { height: 100%; width: 100%; margin: 0; }
    </style>
</head>
<body>
    <div id="map"></div>

    <!-- Leaflet JS & Proj4js (untuk konversi UTM ke LatLong) -->
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>

    <script>
        // Deklarasi UTM Zone 49S (Sleman/Yogyakarta) & WGS84
        var utm49s = "+proj=utm +zone=49 +south +datum=WGS84 +units=m +no_defs";
        var wgs84 = "+proj=longlat +datum=WGS84 +no_defs";

        // Inisialisasi Peta (Center Yogyakarta)
        var map = L.map('map').setView([-7.677567, 110.406899], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Ambil data dari PHP
        var locations = <?= json_encode($data); ?>;

        locations.forEach(function(item) {
            // Konversi dari UTM (X, Y) ke Lat/Lng
            var coord = proj4(utm49s, wgs84, [parseFloat(item.x), parseFloat(item.y)]);
            var lng = coord[0];
            var lat = coord[1];

            // Tambahkan Marker
            L.marker([lat, lng]).addTo(map)
                .bindPopup("<b>" + item.nama_fasil + "</b><br>" + 
                           "Kalurahan: " + item.kalurahan + "<br>" +
                           "Kapanewon: " + item.kapanewon + "<br>" +
                           "Status: " + item.status);
        });
    </script>
</body>
</html>