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
                        <h1>Dashboard</h1>
                    </div>
                    <div class="real-time-clock">
                        <h3 id="clock"></h3>
                    </div>
                </nav>
            </div>
            <div class="content-body">
                <!-- row -->
                <div class="container-fluid">
                    <div class="row invoice-card-row">
                        <div class="col-xl-3 col-xxl-7 col-sm-6" >
                            <div class="card bg-gradient-1 invoice-card" style="cursor: pointer;">
                                <div class="card-body d-flex">
                                    <div class="icon me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" style="fill: #A7B49E;"><path d="M13 6c2.507.423 4.577 2.493 5 5h4c-.471-4.717-4.283-8.529-9-9v4z"></path><path d="M18 13c-.478 2.833-2.982 4.949-5.949 4.949-3.309 0-6-2.691-6-6C6.051 8.982 8.167 6.478 11 6V2c-5.046.504-8.949 4.773-8.949 9.949 0 5.514 4.486 10 10 10 5.176 0 9.445-3.903 9.949-8.949h-4z"></path></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-white invoice-num">1.214</h2>
                                        <span class="text-white fs-18">Jumlah Siswa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-xxl-5 col-sm-6">
                            <div class="card bg-gradient-2 invoice-card">
                                <div class="card-body d-flex">
                                    <div class="icon me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" style="fill: #A7B49E;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"></path><path d="M9.999 13.587 7.7 11.292l-1.412 1.416 3.713 3.705 6.706-6.706-1.414-1.414z"></path></svg>
                                    </div>
                                    <div>
                                        <h2 class="text-white invoice-num"><?=$tidakHadir?></h2>
                                        <span class="text-white fs-18">Jumlah Murid yang belum Absen Hari ini</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-xxl-6 col-sm-6">
                            <div class="card bg-gradient-3 invoice-card" data-bs-toggle="modal" data-bs-target="#badMoodStudentsModal" style="cursor: pointer;">
                                <div class="card-body d-flex">
                                    <div class="icon me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="34" viewBox="0 0 24 24" style="fill: #E52020;transform: msFilter;"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-5 8.5a1.5 1.5 0 1 1 3.001.001A1.5 1.5 0 0 1 7 10.5zM8 17s1-3 4-3 4 3 4 3H8zm7.493-5.014a1.494 1.494 0 1 1 .001-2.987 1.494 1.494 0 0 1-.001 2.987z"></path></svg>
                                        
                                    </div>
                                    <div>
                                        <h2 class="text-white invoice-num"><?=$totalBadMood;?></h2>
                                        <span class="text-white fs-18">Jumlah Murid Bad Mood</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-xxl-6 col-sm-6">
                            <div class="card bg-gradient-4 invoice-card view-late-students" data-bs-toggle="modal" data-bs-target="#lateStudentsModal"style="cursor: pointer;">
                                <div class="card-body d-flex">
                                    <div class="icon me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="34" viewBox="0 0 24 24" style="fill: #E52020;transform: msFilter;"><path d="M5 3H3v18h18v-2H5z"></path><path d="M13 12.586 8.707 8.293 7.293 9.707 13 15.414l3-3 4.293 4.293 1.414-1.414L16 9.586z"></path></svg>
                                    
                                    </div>
                                    <div>
                                        <h2 class="text-white invoice-num"><?=$totalLate;?></h2>
                                        <span class="text-white fs-18">Jumlah Murid Terlambat</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row info-graphic">
                        <!-- Data Absensi Siswa Hari Ini -->
                        <div class="col-lg-5 col-md-6 col-12 mb-4">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="me-auto">
                                        <h4 class="card-title mb-2">Data Absensi Siswa Hari Ini</h4>
                                        <span class="fs-12">Berikut Data Absensi Siswa yang terlampir pada hari ini</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="card-list mt-4">
                                        <li><span class="bg-blue circle"></span> Belum Absen <span id="belumAbsen">0%</span></li>
                                        <li><span class="bg-success circle"></span> Hadir <span id="hadir">0%</span></li>
                                        <li><span class="bg-warning circle"></span> Terlambat <span id="terlambat">0%</span></li>
                                    </ul>
                                    <div class="col-12">
                                        <div class="chart-container">
                                            <canvas id="chartPie"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Absensi Siswa Mingguan -->
                        <div class="col-lg-7 col-md-6 col-12 mb-4">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="me-auto">
                                        <h4 class="card-title mb-2">Data Absensi Siswa</h4>
                                        <span class="fs-12">Berikut Data Absensi Siswa per-Minggu</span>
                                    </div>
                                </div>
                                <div class="card-body pb-2">
                                    <!-- Date Range Selectors -->
                                    <div class="d-flex flex-wrap gap-2 mt-3 mb-3">
                                        <label for="startDate" class="form-label">Start Date:</label>
                                        <input type="date" id="startDate" name="startDate" class="form-control w-auto">
                                        
                                        <label for="endDate" class="form-label">End Date:</label>
                                        <input type="date" id="endDate" name="endDate" class="form-control w-auto">
                                        
                                        <button id="updateButton" class="btn btn-primary">Update Chart</button>
                                    </div>

                                    <div class="col-12">
                                        <div class="chart-container">
                                            <canvas id="chartBar"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="lateStudentsModal" tabindex="-1" aria-labelledby="lateStudentsModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="lateStudentsModalLabel">Siswa Terlambat</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead class="table-primary">
								<tr>
									<th>No</th>
									<th>Nama</th>
									<th>Kelas</th>
									<th>Jurusan</th>
									<th>Waktu Terlambat</th>
								</tr>
							</thead>
							<tbody id="lateStudentsTable">
								<!-- Data siswa akan dimasukkan di sini oleh JavaScript -->
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="prevPage">Previous</button>
					<button type="button" class="btn btn-primary" id="nextPage">Next</button>
				</div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="badMoodStudentsModal" tabindex="-1" aria-labelledby="badMoodStudentsModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="badMoodStudentsModalLabel">Siswa Terlambat</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead class="table-primary">
								<tr>
									<th>No</th>
									<th>Nama</th>
									<th>Kelas</th>
									<th>Jurusan</th>
									<th>    </th>
								</tr>
							</thead>
							<tbody id="badMoodStudentsTable">
								<!-- Data siswa akan dimasukkan di sini oleh JavaScript -->
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="prevPageBad">Previous</button>
					<button type="button" class="btn btn-primary" id="nextPageBad">Next</button>
				</div>
			</div>
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
     <script>
		$(document).ready(function () {
			var currentPage = 1;
			var itemsPerPage = 10;

			function loadLateStudents(page) {
				$.ajax({
					url: "get_late_students.php",
					type: "GET",
					data: { page: page, itemsPerPage: itemsPerPage },
					success: function (response) {
						$("#lateStudentsTable").html(response);
					}
				});
			}

			// Load data pertama kali saat modal dibuka
			$('#lateStudentsModal').on('show.bs.modal', function () {
				loadLateStudents(currentPage);
			});

			// Tombol Next
			$("#nextPage").click(function () {
				currentPage++;
				loadLateStudents(currentPage);
			});

			// Tombol Previous
			$("#prevPage").click(function () {
				if (currentPage > 1) {
					currentPage--;
					loadLateStudents(currentPage);
				}
			});
		});
	</script>

    <script>
		$(document).ready(function () {
			var currentPage = 1;
			var itemsPerPage = 10;

			function loadBadmoodStudents(page) {
				$.ajax({
					url: "get_badmood_students.php",
					type: "GET",
					data: { page: page, itemsPerPage: itemsPerPage },
					success: function (response) {
						$("#badMoodStudentsTable").html(response);
					}
				});
			}

			// Load data pertama kali saat modal dibuka
			$('#badMoodStudentsModal').on('show.bs.modal', function () {
				loadBadmoodStudents(currentPage);
			});

			// Tombol Next
			$("#nextPageBad").click(function () {
				currentPage++;
				loadBadmoodStudents(currentPage);
			});

			// Tombol Previous
			$("#prevPageBad").click(function () {
				if (currentPage > 1) {
					currentPage--;
					loadBadmoodStudents(currentPage);
				}
			});
		});
	</script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let pieChart, barChart;

            // Fungsi untuk mendapatkan tanggal awal dan akhir minggu ini
            function getCurrentWeek() {
                let today = new Date();
                let dayOfWeek = today.getDay(); // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu

                let startDate = new Date(today); // Clone today
                let endDate = new Date(today); // Clone today

                // Jika hari ini bukan Senin (1), geser ke Senin awal minggu ini
                startDate.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));
                endDate.setDate(startDate.getDate() + 6); // Tambahkan 6 hari untuk mendapatkan Minggu

                return {
                    start: startDate.toISOString().split("T")[0], // Format YYYY-MM-DD
                    end: endDate.toISOString().split("T")[0]
                };
            }

            // Fungsi untuk mengambil data hari ini
            function fetchDailyData() {
                fetch("data_hari_ini.php")
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById("hadir").textContent = data.persentase.hadir + "%";
                        document.getElementById("terlambat").textContent = data.persentase.terlambat + "%";
                        document.getElementById("belumAbsen").textContent = data.persentase.belumAbsen + "%";

                        updatePieChart(data.hadir, data.terlambat, data.belumAbsen);
                    })
                    .catch(error => console.error("Error loading daily data:", error));
            }

            // Fungsi untuk memperbarui Pie Chart
            function updatePieChart(hadir, terlambat, belumAbsen) {
                let ctxPie = document.getElementById("chartPie").getContext("2d");

                if (pieChart) pieChart.destroy();

                pieChart = new Chart(ctxPie, {
                    type: "pie",
                    data: {
                        labels: ["Hadir", "Terlambat", "Belum Absen"],
                        datasets: [{
                            data: [hadir, terlambat, belumAbsen],
                            backgroundColor: ["#597445", "#EF4B4B", "#295F98"]
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            // Fungsi untuk mengambil dan memperbarui Bar Chart
            function loadBarChart(startDate, endDate) {
                let url = `data_per_minggu.php?start_date=${startDate}&end_date=${endDate}`;

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        let ctxBar = document.getElementById("chartBar").getContext("2d");
                        let barChartLabels = Object.keys(data.barChart);
                        let hadirData = barChartLabels.map(day => data.barChart[day]["Hadir"]);
                        let terlambatData = barChartLabels.map(day => data.barChart[day]["Terlambat"]);

                        if (barChart) barChart.destroy();

                        barChart = new Chart(ctxBar, {
                            type: "bar",
                            data: {
                                labels: barChartLabels,
                                datasets: [
                                    { label: "Hadir", data: hadirData, backgroundColor: "#597445" },
                                    { label: "Terlambat", data: terlambatData, backgroundColor: "#EF4B4B" }
                                ]
                            },
                            options: { responsive: true, maintainAspectRatio: false }
                        });
                    })
                    .catch(error => console.error("Error loading weekly data:", error));
            }

            // Set default tanggal ke minggu ini
            let { start, end } = getCurrentWeek();
            document.getElementById("startDate").value = start;
            document.getElementById("endDate").value = end;

            // Ambil data pertama kali saat halaman dimuat
            fetchDailyData();
            loadBarChart(start, end);

            // Event listener untuk update Bar Chart berdasarkan tanggal yang dipilih
            document.getElementById("updateButton").addEventListener("click", function () {
                let startDate = document.getElementById("startDate").value;
                let endDate = document.getElementById("endDate").value;
                loadBarChart(startDate, endDate);
            });
        });
    </script>

</body>

</html>