<?php
    header('Content-Type: application/json');
    date_default_timezone_set('Asia/Jakarta');

    // Konfigurasi database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bacs5153_recode";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["error" => "Koneksi gagal: " . $conn->connect_error]));
    }

    $tanggalHariIni = date('Y-m-d');

    // Ambil data absensi hari ini
    $sqlHadir = "SELECT COUNT(*) AS total_hadir FROM Absensi WHERE Kehadiran = 'Hadir' AND DATE(Waktu) = '$tanggalHariIni'";
    $sqlTerlambat = "SELECT COUNT(*) AS total_terlambat FROM Absensi WHERE Kehadiran = 'Terlambat' AND DATE(Waktu) = '$tanggalHariIni'";

    $totalSiswa = 1214;
    $totalHadir = $conn->query($sqlHadir)->fetch_assoc()['total_hadir'] ?? 0;
    $totalTerlambat = $conn->query($sqlTerlambat)->fetch_assoc()['total_terlambat'] ?? 0;
    $totalBelumAbsen = $totalSiswa - ($totalHadir + $totalTerlambat);

    // Hitung persentase
    $persentaseHadir = round(($totalHadir / $totalSiswa) * 100, 2);
    $persentaseTerlambat = round(($totalTerlambat / $totalSiswa) * 100, 2);
    $persentaseBelumAbsen = round(($totalBelumAbsen / $totalSiswa) * 100, 2);

    // Kirim data JSON
    $data = [
        "hadir" => $totalHadir,
        "terlambat" => $totalTerlambat,
        "belumAbsen" => $totalBelumAbsen,
        "persentase" => [
            "hadir" => $persentaseHadir,
            "terlambat" => $persentaseTerlambat,
            "belumAbsen" => $persentaseBelumAbsen
        ]
    ];

    $conn->close();
    echo json_encode($data);
?>
