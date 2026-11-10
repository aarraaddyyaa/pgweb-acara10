<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_fasil = $_POST['nama_fasil'];
    $kalurahan  = $_POST['kalurahan'];
    $kapanewon  = $_POST['kapanewon'];
    $x          = $_POST['x'];
    $y          = $_POST['y'];
    $status     = $_POST['status'];
    $alamat     = $_POST['alamat'];

    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "studikasus";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO data_persampahan (nama_fasil, kalurahan, kapanewon, x, y, status, alamat)
            VALUES ('$nama_fasil', '$kalurahan', '$kapanewon', '$x', '$y', '$status', '$alamat')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Input Data Persampahan</title>
</head>
<body>

    <h2>Form Input</h2>

    <form action="" method="post">
        <label>Nama Fasilitas:</label><br>
        <input type="text" name="nama_fasil" required><br><br>

        <label>Kalurahan:</label><br>
        <input type="text" name="kalurahan" required><br><br>

        <label>Kapanewon:</label><br>
        <input type="text" name="kapanewon" required><br><br>

        <label>Longitude (X):</label><br>
        <input type="text" name="x" required><br><br>

        <label>Latitude (Y):</label><br>
        <input type="text" name="y" required><br><br>

        <label>Status:</label><br>
        <input type="text" name="status" required><br><br>

        <label>Alamat:</label><br>
        <input type="text" name="alamat" required><br><br>

        <input type="submit" value="Submit">
    </form>

</body>
</html>