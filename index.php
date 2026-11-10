<?php
// Koneksi Database
$conn = new mysqli("localhost", "root", "", "studikasus");
if ($conn->connect_error) { die("Koneksi gagal: " . $conn->connect_error); }

// Proses Hapus Data (Langsung tanpa notif)
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    $conn->query("DELETE FROM data_persampahan WHERE id = $id_hapus");
    header("Location: index.php");
    exit();
}

// Ambil Seluruh Data
$query = $conn->query("SELECT * FROM data_persampahan");
$data_persampahan = array();
while ($row = $query->fetch_assoc()) {
    $data_persampahan[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Persebaran Persampahan</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css">

    <style>
        * { box-sizing: border-box; }
        html, body { 
            margin: 0; 
            padding: 0; 
            width: 100%;
            font-family: Arial, sans-serif; 
            background: #ffffff; 
        }
        h2 { 
            margin: 0; 
            padding: 12px 0;
            text-align: center; 
            color: #222; 
            background: #fff;
            border-bottom: 1px solid #ddd;
        }
        
        .main-container {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        
        /* Panel Peta Full Width (Atas) */
        #map {
            width: 100%;
            height: 555px;
            border-bottom: 1px solid #ccc;
            background: #fff;
        }
        
        /* Panel Tabel Full Width (Bawah) */
        #tabel-container {
            width: 100%;
            background: #fff;
            padding: 15px;
        }

        .table-header-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .left-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .btn-tambah {
            display: inline-block;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 3px;
            font-size: 13px;
        }
        .btn-tambah:hover { background-color: #218838; }

        .link-referensi {
            font-size: 12px;
            color: #007bff;
            text-decoration: underline;
        }
        .link-referensi:hover { color: #0056b3; }

        .info-count {
            font-size: 13px;
            color: #555;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px 10px; text-align: left; }
        th { background-color: #e9ecef; color: #333; }
        tr:hover { background-color: #f1f3f5; cursor: pointer; }
        
        .btn-aksi {
            display: inline-block;
            padding: 3px 8px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 11px;
        }
        .btn-hapus { background-color: #dc3545; color: white; }
        .btn-edit { background-color: #007bff; color: white; }
    </style>
</head>
<body>

    <h2>Peta Persebaran Fasilitas Pengelolaan Sampah</h2>

    <div class="main-container">
        <!-- PETA (ATAS) -->
        <div id="map"></div>
        
        <!-- TABEL DATA (BAWAH) -->
        <div id="tabel-container">
            <div class="table-header-action">
                <div class="left-actions">
                    <a href="input.php" class="btn-tambah">+ Input Data Baru</a>
                    <a href="https://geoportal.slemankab.go.id/catalogue/#/dataset/1596" target="_blank" class="link-referensi">Sumber Data (Geoportal Sleman)</a>
                </div>
                <span class="info-count">Total Fasilitas: <?= count($data_persampahan); ?> Data</span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Nama Fasilitas</th>
                        <th>Kalurahan</th>
                        <th>Kapanewon</th>
                        <th>Longitude (X)</th>
                        <th>Latitude (Y)</th>
                        <th>Status</th>
                        <th>Alamat</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($data_persampahan as $item): 
                    ?>
                    <tr onclick="focusMarker(<?= $item['id']; ?>)">
                        <td><?= $no++; ?></td>
                        <td><b><?= htmlspecialchars($item['nama_fasil']); ?></b></td>
                        <td><?= htmlspecialchars($item['kalurahan']); ?></td>
                        <td><?= htmlspecialchars($item['kapanewon']); ?></td>
                        <td><?= $item['x']; ?></td>
                        <td><?= $item['y']; ?></td>
                        <td><?= htmlspecialchars($item['status']); ?></td>
                        <td><?= htmlspecialchars($item['alamat']); ?></td>
                        <td onclick="event.stopPropagation();" style="white-space: nowrap;">
                            <a href="index.php?hapus=<?= $item['id']; ?>" class="btn-aksi btn-hapus">hapus</a>
                            <a href="edit.php?id=<?= $item['id']; ?>" class="btn-aksi btn-edit">edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.9.0/proj4.js"></script>

    <script>
        var utm49s = "+proj=utm +zone=49 +south +datum=WGS84 +units=m +no_defs";
        var wgs84 = "+proj=longlat +datum=WGS84 +no_defs";

        var map = L.map('map').setView([-7.74, 110.37], 11);

        // Basemaps
        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        var esri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });

        // Layer Group untuk Titik Sampah
        var sampalLayer = L.layerGroup().addTo(map);

        var baseMaps = { 
            "OpenStreetMap": osm, 
            "Esri Satellite": esri 
        };

        var overlayMaps = { 
            "Fasilitas Persampahan": sampalLayer 
        };

        // Inisialisasi Control Layer
        var layerControl = L.control.layers(baseMaps, overlayMaps, { collapsed: true }).addTo(map);

        // Hapus event hover bawaan Leaflet & ganti dengan Klik Manual
        var container = layerControl.getContainer();
        container.onmouseover = null;
        container.onmouseout = null;
        L.DomEvent.off(container, 'mouseover', layerControl._expand, layerControl);
        L.DomEvent.off(container, 'mouseout', layerControl._collapse, layerControl);

        L.DomEvent.on(container, 'click', function (e) {
            L.DomEvent.stopPropagation(e);
            if (L.DomUtil.hasClass(container, 'leaflet-control-layers-expanded')) {
                layerControl._collapse();
            } else {
                layerControl._expand();
            }
        });

        // Scale Control (KIRI BAWAH)
        L.control.scale({ position: 'bottomleft' }).addTo(map);

        // Load Markers
        var locations = <?= json_encode($data_persampahan); ?>;
        var markers = {};

        locations.forEach(function(item) {
            var coord = proj4(utm49s, wgs84, [parseFloat(item.x), parseFloat(item.y)]);
            var lat = coord[1];
            var lng = coord[0];

            var marker = L.marker([lat, lng])
                .bindPopup("<b>" + item.nama_fasil + "</b><br>Status: " + item.status);

            sampalLayer.addLayer(marker);

            markers[item.id] = { marker: marker, lat: lat, lng: lng };
        });

        function focusMarker(id) {
            if (markers[id]) {
                map.setView([markers[id].lat, markers[id].lng], 16);
                markers[id].marker.openPopup();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>