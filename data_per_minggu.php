<?php
    header('Content-Type: application/json');

    // Konfigurasi database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bacs5153_recode";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
    }

    // Ambil parameter tanggal dari request
    $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('monday this week'));
    $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d', strtotime('friday this week'));

    // Query untuk mengambil jumlah hadir dan terlambat per hari
    $sqlBarChart = "
        SELECT DATE(Waktu) as tanggal, Kehadiran, COUNT(*) as jumlah 
        FROM Absensi 
        WHERE DATE(Waktu) BETWEEN '$startDate' AND '$endDate'
        GROUP BY DATE(Waktu), Kehadiran
        ORDER BY tanggal;
    ";

    $resultBarChart = $conn->query($sqlBarChart);
    $barChartData = [];

    // Inisialisasi array default untuk Senin-Jumat
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    foreach ($days as $day) {
        $barChartData[$day] = ["Hadir" => 0, "Terlambat" => 0];
    }

    // Masukkan data dari database ke dalam struktur array
    while ($row = $resultBarChart->fetch_assoc()) {
        $dayOfWeek = date('l', strtotime($row['tanggal']));
        if (in_array($dayOfWeek, $days)) {
            $barChartData[$dayOfWeek][$row['Kehadiran']] = (int)$row['jumlah'];
        }
    }

    // Kirim data JSON
    $data = ["barChart" => $barChartData];

    $conn->close();
    echo json_encode($data);
?>
