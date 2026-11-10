<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "studikasus";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id         = intval($_POST['id']);
    $nama_fasil = $_POST['nama_fasil'];
    $kalurahan  = $_POST['kalurahan'];
    $kapanewon  = $_POST['kapanewon'];
    $x          = $_POST['x'];
    $y          = $_POST['y'];
    $status     = $_POST['status'];
    $alamat     = $_POST['alamat'];

    $sql = "UPDATE data_persampahan SET 
            nama_fasil='$nama_fasil', 
            kalurahan='$kalurahan', 
            kapanewon='$kapanewon', 
            x='$x', 
            y='$y', 
            status='$status', 
            alamat='$alamat' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// 2. AMBIL DATA LAMA (Berdasarkan ID di URL via GET)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM data_persampahan WHERE id = $id");
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan!";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Persampahan</title>
</head>
<body>

    <h2>Form Edit</h2>

    <form action="" method="post">
        <!-- Hidden input untuk menyimpan ID data yang diedit -->
        <input type="hidden" name="id" value="<?= $data['id']; ?>">

        <label>Nama Fasilitas:</label><br>
        <input type="text" name="nama_fasil" value="<?= htmlspecialchars($data['nama_fasil']); ?>" required><br><br>

        <label>Kalurahan:</label><br>
        <input type="text" name="kalurahan" value="<?= htmlspecialchars($data['kalurahan']); ?>" required><br><br>

        <label>Kapanewon:</label><br>
        <input type="text" name="kapanewon" value="<?= htmlspecialchars($data['kapanewon']); ?>" required><br><br>

        <label>Longitude (X):</label><br>
        <input type="text" name="x" value="<?= $data['x']; ?>" required><br><br>

        <label>Latitude (Y):</label><br>
        <input type="text" name="y" value="<?= $data['y']; ?>" required><br><br>

        <label>Status:</label><br>
        <input type="text" name="status" value="<?= htmlspecialchars($data['status']); ?>" required><br><br>

        <label>Alamat:</label><br>
        <input type="text" name="alamat" value="<?= htmlspecialchars($data['alamat']); ?>" required><br><br>

        <input type="submit" value="Submit">
    </form>

</body>
</html>