<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bacs5153_recode";

    date_default_timezone_set('Asia/Jakarta');

    // Membuat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    $dateToday = date('Y-m-d');

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 10;
    $offset = ($page - 1) * $itemsPerPage;

    $query = "SELECT * FROM absensi WHERE Kehadiran = 'Terlambat' AND Date(Waktu) = '$dateToday' LIMIT $itemsPerPage OFFSET $offset";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $no = $offset + 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($row['Nama']) . "</td>
                    <td>" . htmlspecialchars($row['Kelas']) . "</td>
                    <td>" . htmlspecialchars($row['Jurusan']) . "</td>
                    <td>" . htmlspecialchars($row['Waktu']) . "</td>
                </tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='5'>Tidak ada siswa terlambat.</td></tr>";
    }

    $conn->close();
?>
