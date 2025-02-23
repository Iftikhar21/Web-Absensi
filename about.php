<?php
    // Konfigurasi database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bacs5153_recode";

        date_default_timezone_set('Asia/Jakarta');

        // Membuat koneksi
        $conn = new mysqli($servername, $username, $password, $dbname);

    // Mengecek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Mendapatkan data dari database
    $sql = "SELECT * FROM Absensi";
    $result = $conn->query($sql);

    // Menyimpan data dalam array untuk digunakan di JavaScript
    $dataAbsensi = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataAbsensi[] = $row;
        }
    }

    $dateAbsenToday = date('Y-m-d');

    $sqlAbsen = "SELECT COUNT(*) AS total_absen 
            FROM Absensi 
            WHERE DATE(Waktu) = '$dateAbsenToday'";

    $resultAbsen = $conn->query($sqlAbsen);

    $totalAbsen = 0;

    if ($resultAbsen && $resultAbsen->num_rows > 0) {
        $rowAbsen = $resultAbsen->fetch_assoc();
        $totalAbsen = $rowAbsen['total_absen'];
    } else {
        echo $conn->error;
    }


    $dateToday = date('Y-m-d');
    $sqlToday = "SELECT COUNT(*) AS total_hadir 
            FROM Absensi 
            WHERE Kehadiran = 'Hadir' 
            AND DATE(Waktu) = '$dateToday'";

    $resultToday = $conn->query($sqlToday);

    $totalHadir = 0;

    if ($resultToday && $resultToday->num_rows > 0) {
        $rowToday = $resultToday->fetch_assoc();
        $totalHadir = $rowToday['total_hadir'];
    } else {
        echo $conn->error;
    }

    $lateToday = date('Y-m-d');
    $sqlLateToday = "SELECT COUNT(*) AS total_terlambat 
            FROM Absensi 
            WHERE Kehadiran = 'Terlambat' 
            AND DATE(Waktu) = '$lateToday'";

    $resultLateToday = $conn->query($sqlLateToday);

    $totalLate = 0;

    if ($resultLateToday && $resultLateToday->num_rows > 0) {
        $rowLateToday = $resultLateToday->fetch_assoc();
        $totalLate = $rowLateToday['total_terlambat'];
    } else {
        echo $conn->error;
    }

    $today = date('Y-m-d');  // Get today's date
    
    $sqlBadMoodToday = "SELECT COUNT(*) AS total_buruk 
                        FROM Absensi 
                        WHERE Mood = 'Buruk' 
                        AND DATE(Waktu) = '$today'";  // Adjusting Mood to 'buruk' for bad mood
    
    $resultBadMoodToday = $conn->query($sqlBadMoodToday);
    
    $totalBadMood = 0;  // Initialize count variable
    
    if ($resultBadMoodToday && $resultBadMoodToday->num_rows > 0) {
        $rowBadMoodToday = $resultBadMoodToday->fetch_assoc();
        $totalBadMood = $rowBadMoodToday['total_buruk'];  // Retrieve the count of students with a bad mood
    } else {
        echo $conn->error;  // Display error if query fails
    }



    $persentaseKehadiran = ($totalAbsen > 0) ? ($totalHadir / $totalAbsen) * 100 : 0;
    $persentaseTerlambat = ($totalAbsen > 0) ? ($totalLate / $totalAbsen) * 100 : 0;
    $persentaseAbsen = ($totalAbsen > 0) ? ($totalAbsen / 1214) * 100 : 0;
    
    $persentaseBadMood = ($totalAbsen > 0) ? ($totalBadMood / $totalAbsen) * 100: 0;
    
    $tidakHadir = 1214 - $totalAbsen;
    
    $persentaseTidakHadir = ($totalAbsen > 0) ? ($tidakHadir / 1214) * 100 : 0;

    
    $sqlLateStudents = "SELECT Nama, Kelas, Jurusan, Waktu FROM Absensi WHERE Kehadiran = 'Terlambat' AND DATE(Waktu) = '$lateToday'";
    $resultLateStudents = $conn->query($sqlLateStudents);
    
    $lateStudents = [];
    if ($resultLateStudents && $resultLateStudents->num_rows > 0) {
        while ($row = $resultLateStudents->fetch_assoc()) {
            $lateStudents[] = $row;
        }
    } else {
        echo $conn->error;
    }

    // Query to fetch students with a bad mood
    $sqlBadMoodStudents = "SELECT Nama, Kelas, Jurusan, Catatan FROM Absensi WHERE Mood = 'Buruk' AND DATE(Waktu) = '$today'";  // Make sure 'Mood' is checked for 'Buruk'
    
    // Execute the query
    $resultBadMoodStudents = $conn->query($sqlBadMoodStudents);
    
    // Initialize an array to hold the students with bad mood
    $badMoodStudents = [];
    
    if ($resultBadMoodStudents && $resultBadMoodStudents->num_rows > 0) {
        // Fetch each student data into the array
        while ($row = $resultBadMoodStudents->fetch_assoc()) {
            $badMoodStudents[] = $row;  // Add the student details to the array
        }
    } else {
        // If query failed, show error
        echo $conn->error;
    }
    // Menutup koneksi
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class='bx bx-qr' ></i>
                </button>
                <div class="sidebar-logo" style="color: #fff;">
                    <h1>Re-Code</h1>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="dashboard.php" class="sidebar-link">
                        <i class='bx bxs-dashboard'></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="absensi.php" class="sidebar-link">
                        <i class='bx bx-bar-chart-alt-2'></i>
                        <span>Absensi</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="about.php" class="sidebar-link">
                        <i class='bx bx-info-circle' ></i>
                        <span>About</span>
                    </a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <a href="#" class="sidebar-link">
                    <i class='bx bx-log-out'></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>
        <div class="main p-3">
            <div class="header">
                <nav class="navbar">
                    <div class="dashboard_bar">
                        <h1>About</h1>
                    </div>
                    <div class="real-time-clock">
                        <h3 id="clock"></h3>
                    </div>
                </nav>
            </div>
            <!-- About 1 - Bootstrap Brain Component -->
            <section class="py-3 py-md-5">
                <div class="container">
                    <div class="row gy-3 gy-md-4 gy-lg-0 align-items-lg-center">
                        <div class="col-12 col-lg-6 col-xl-4">
                            <img class="img-fluid rounded" loading="lazy" src="img/recode_logo.png" alt="recode">
                        </div>
                        <div class="col-12 col-lg-6 col-xl-7">
                            <div class="row justify-content-xl-center">
                                <div class="col-12 col-xl-11">
                                    <h2 class="mb-3">Apa Itu Re-Code?</h2>
                                    <p class="lead fs-4 text-secondary mb-3">Re-Code adalah solusi absensi berbasis QR Code yang aman dan efisien dengan teknologi geofencing, memastikan kehadiran hanya dapat dilakukan di lokasi yang ditentukan.</p>
                                    <p class="mb-5">Re-Code dibentuk pada tanggal 21 November 2024.
                                    Re-Code berfokus pada pengembangan teknologi cerdas yang progresif, inklusif, dan berkelanjutan.</p>
                                    <div class="row gy-4 gy-md-0 gx-xxl-5X">
                                        <div class="d-flex">
                                            <div class="me-4 text-dark mb-2">
                                                <h1>Features</h1>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex">
                                                <div class="me-4 text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-qr-code" viewBox="0 0 16 16">
                                                    <path d="M2 2h2v2H2z"/>
                                                    <path d="M6 0v6H0V0zM5 1H1v4h4zM4 12H2v2h2z"/>
                                                    <path d="M6 10v6H0v-6zm-5 1v4h4v-4zm11-9h2v2h-2z"/>
                                                    <path d="M10 0v6h6V0zm5 1v4h-4V1zM8 1V0h1v2H8v2H7V1zm0 5V4h1v2zM6 8V7h1V6h1v2h1V7h5v1h-4v1H7V8zm0 0v1H2V8H1v1H0V7h3v1zm10 1h-1V7h1zm-1 0h-1v2h2v-1h-1zm-4 0h2v1h-1v1h-1zm2 3v-1h-1v1h-1v1H9v1h3v-2zm0 0h3v1h-2v1h-1zm-4-1v1h1v-2H7v1z"/>
                                                    <path d="M7 12h1v3h4v1H7zm9 2v2h-3v-1h2v-1z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h2 class="h4 mb-3">Attendance Using <br>QR-Code</h2>
                                                    <p class="text-secondary mb-0">Mempermudah proses kehadiran dengan scan QR-Code.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="d-flex">
                                                <div class="me-4 text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h2 class="h4 mb-3">Geofencing</h2>
                                                    <p class="text-secondary mb-0">Membatasi area absensi agar hanya bisa dilakukan di lokasi yang sudah ditentukan.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mt-4">
                                            <div class="d-flex">
                                                <div class="me-4 text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                                                    <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h2 class="h4 mb-3">Keamanan Data</h2>
                                                    <p class="text-secondary mb-0">Semua data absensi tersimpan dengan enkripsi yang aman.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mt-4">
                                            <div class="d-flex">
                                                <div class="me-4 text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-kanban" viewBox="0 0 16 16">
                                                    <path d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                                    <path d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h2 class="h4 mb-3">Management Absensi</h2>
                                                    <p class="text-secondary mb-0">Menyediakan statistik kehadiran harian dan mingguan.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mt-4">
                                            <div class="d-flex">
                                                <div class="me-4 text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-journal-code" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M8.646 5.646a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L10.293 8 8.646 6.354a.5.5 0 0 1 0-.708m-1.292 0a.5.5 0 0 0-.708 0l-2 2a.5.5 0 0 0 0 .708l2 2a.5.5 0 0 0 .708-.708L5.707 8l1.647-1.646a.5.5 0 0 0 0-.708"/>
                                                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                                                    <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h2 class="h4 mb-3">Student Notes</h2>
                                                    <p class="text-secondary mb-0">Menyediakan fitur catatan untuk mencatat kebutuhan belajar siswa.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mt-4">
                                            <div class="d-flex">
                                                <div class="me-4 text-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-map-fill" viewBox="0 0 16 16">
                                                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.598-.49L10.5.99 5.598.01a.5.5 0 0 0-.196 0l-5 1A.5.5 0 0 0 0 1.5v14a.5.5 0 0 0 .598.49l4.902-.98 4.902.98a.5.5 0 0 0 .196 0l5-1A.5.5 0 0 0 16 14.5zM5 14.09V1.11l.5-.1.5.1v12.98l-.402-.08a.5.5 0 0 0-.196 0zm5 .8V1.91l.402.08a.5.5 0 0 0 .196 0L11 1.91v12.98l-.5.1z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h2 class="h4 mb-3">Schedule and Map</h2>
                                                    <p class="text-secondary mb-0">Siswa dapat melihat lokasi penting di sekolah melalui peta yang tersedia dan jadwal pelajaran dan kegiatan sekolah yang selalu diperbarui.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>

     <script>
        const hamBurger = document.querySelector(".toggle-btn");

        hamBurger.addEventListener("click", function () {
            const sidebar = document.querySelector("#sidebar");
            const header = document.querySelector(".header");

            sidebar.classList.toggle("expand");

            if (sidebar.classList.contains("expand")) {
                header.style.left = "260px"; 
                header.style.width = "calc(100% - 260px)"; 
            } else {
                header.style.left = "70px";  
                header.style.width = "calc(100% - 70px)";  
            }
        });

        function showRealTimeClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Jakarta' };
            
            const formattedDate = now.toLocaleDateString('id-ID', options);
            const formattedTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            document.getElementById("clock").innerHTML = `<strong>${formattedTime}</strong> | ${formattedDate}`;
        }

        setInterval(showRealTimeClock, 1000);
        showRealTimeClock();

     </script>
</body>

</html>