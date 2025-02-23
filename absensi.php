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
    $dateToday = date('Y-m-d');
    // Mendapatkan data dari database
    $sql = "SELECT * FROM Absensi WHERE DATE(Waktu) = '$dateToday'";
    $result = $conn->query($sql);

    // Menyimpan data dalam array untuk digunakan di JavaScript
    $dataAbsensi = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dataAbsensi[] = $row;
        }
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
    <title>Absensi</title>
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
                <div class="header-content">
                    <nav class="navbar navbar-expand">
                        <div class="collapse navbar-collapse justify-content-between">
                            <div class="header-left">
                                <div class="dashboard_bar">
                                    <h1>Absensi</h1> 
                                </div>
                            </div>
                            <div class="real-time-clock">
                                <h3 id="clock" class="m-0"></h3>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="container">
                <div class="card p-3 mb-3 mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label for="rowsPerPage">Tampilkan</label>
                            <select id="rowsPerPage" class="form-select" style="width: auto; display: inline-block;">
                                <option value="25">25</option>
                                <option value="40">40</option>
                                <option value="75">75</option>
                                <option value="100">100</option>
                            </select>
                            data per halaman
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control" placeholder="Cari Nama" id="filterNama">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control" placeholder="Cari NISN" id="filterNISN">
                        </div>
                        <div class="col-md-3 col-6">
                            <input type="text" class="form-control" placeholder="Cari Android ID" id="filterAndroidID">
                        </div>
                        <div class="col-md-3 col-6">
                            <select id="filterKelas" class="form-select">
                                <option value="">Semua Kelas</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <select id="filterJurusan" class="form-select">
                                <option value="">Semua Jurusan</option>
                                <option value="RPL 1">RPL 1</option>
                                <option value="RPL 2">RPL 2</option>
                                <option value="TBG 2">TBG 2</option>
                                <option value="TBG 3">TBG 3</option>
                                <option value="PH 1">PH 1</option>
                                <option value="PH 2">PH 2</option>
                                <option value="PH 3">PH 3</option>
                                <option value="TBS 1">TBS 1</option>
                                <option value="TBS 2">TBS 2</option>
                                <option value="TBS 3">TBS 3</option>
                                <option value="ULW">ULW</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <input id="filterTanggal" type="date" class="form-control">
                        </div>
                        <div class="col-md-3 col-6">
                            <select id="filterKehadiran" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Hadir">Hadir</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Terlambat">Terlambat</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <input id="filterCatatan" type="text" class="form-control" placeholder="Cari Catatan">
                        </div>
                        <div class="col-md-3 col-6">
                            <select id="filterMood" class="form-select">
                                <option value="">Semua Mood</option>
                                <option value="Baik">Baik</option>
                                <option value="Biasa Aja">Biasa Aja</option>
                                <option value="Buruk">Buruk</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-bordered text-center" id="absensiTable">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NISN</th>
                                <th class="d-none d-md-table-cell">Android ID</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Mood</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; ?>
                        <?php if (!empty($dataAbsensi)): ?>
                            <?php foreach ($dataAbsensi as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['Nama']); ?></td>
                                    <td><?= htmlspecialchars($row['NISN']); ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['AndroidID']); ?></td>
                                    <td><?= htmlspecialchars($row['Kelas']); ?></td>
                                    <td><?= htmlspecialchars($row['Jurusan']); ?></td>
                                    <td><?= htmlspecialchars($row['Waktu']); ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower(htmlspecialchars($row['Kehadiran'])); ?>">
                                            <?= htmlspecialchars($row['Kehadiran']); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['Catatan']); ?></td>
                                    <td><?= htmlspecialchars($row['Mood']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">Tidak ada data absensi.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-3">
                    <span id="pageInfo">Halaman 1 dari 1</span>
                    <div class="row">
                        <div class="col-6">
                            <button id="prevPage" class="btn btn-secondary btn-sm me-2" disabled>Sebelumnya</button>
                        </div>
                        <div class="col-6">
                            <button id="nextPage" class="btn btn-secondary btn-sm" disabled>Berikutnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

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
        document.addEventListener("DOMContentLoaded", function () {
            const table = document.getElementById("absensiTable");
            const tbody = table.querySelector("tbody");
            const filters = {
                nama: document.getElementById("filterNama"),
                nisn: document.getElementById("filterNISN"),
                androidID: document.getElementById("filterAndroidID"),
                kelas: document.getElementById("filterKelas"),
                jurusan: document.getElementById("filterJurusan"),
                tanggal: document.getElementById("filterTanggal"),
                status: document.getElementById("filterKehadiran"),
                catatan: document.getElementById("filterCatatan"),
                mood: document.getElementById("filterMood")
            };

            function filterTable() {
                const rows = tbody.querySelectorAll("tr");
                rows.forEach(row => {
                    const cells = row.getElementsByTagName("td");
                    let show = true;

                    if (filters.nama.value && !cells[1].textContent.toLowerCase().includes(filters.nama.value.toLowerCase())) {
                        show = false;
                    }
                    if (filters.nisn.value && !cells[2].textContent.includes(filters.nisn.value)) {
                        show = false;
                    }
                    if (filters.androidID.value && !cells[3].textContent.includes(filters.androidID.value)) {
                        show = false;
                    }
                    if (filters.kelas.value && filters.kelas.value !== "" && cells[4].textContent !== filters.kelas.value) {
                        show = false;
                    }
                    if (filters.jurusan.value && filters.jurusan.value !== "" && cells[5].textContent !== filters.jurusan.value) {
                        show = false;
                    }
                    if (filters.tanggal.value && cells[6].textContent !== filters.tanggal.value) {
                        show = false;
                    }
                    if (filters.status.value.trim() !== "" && cells[7].textContent.trim().toLowerCase() !== filters.status.value.trim().toLowerCase()) {
                        show = false;
                    }
                    if (filters.catatan.value && !cells[8].textContent.toLowerCase().includes(filters.catatan.value.toLowerCase())) {
                        show = false;
                    }
                    if (filters.mood.value && filters.mood.value !== "" && cells[9].textContent !== filters.mood.value) {
                        show = false;
                    }

                    row.style.display = show ? "" : "none";
                });
            }

            Object.values(filters).forEach(filter => {
                filter.addEventListener("input", filterTable);
            });

            // Responsive adjustments for table
            window.addEventListener("resize", function () {
                if (window.innerWidth < 768) {
                    document.querySelectorAll(".d-none.d-md-table-cell").forEach(el => el.classList.add("d-block"));
                } else {
                    document.querySelectorAll(".d-none.d-md-table-cell").forEach(el => el.classList.remove("d-block"));
                }
            });
        });

    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const table = document.getElementById("absensiTable").getElementsByTagName("tbody")[0];
            const rows = Array.from(table.getElementsByTagName("tr"));
            const rowsPerPageSelect = document.getElementById("rowsPerPage");
            const prevButton = document.getElementById("prevPage");
            const nextButton = document.getElementById("nextPage");
            const pageInfo = document.getElementById("pageInfo");

            let currentPage = 1;
            let rowsPerPage = parseInt(rowsPerPageSelect.value);

            function showPage(page) {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;

                rows.forEach((row, index) => {
                    row.style.display = (index >= start && index < end) ? "" : "none";
                });

                pageInfo.innerText = `Halaman ${page} dari ${Math.ceil(rows.length / rowsPerPage)}`;
                prevButton.disabled = page === 1;
                nextButton.disabled = page === Math.ceil(rows.length / rowsPerPage);
            }

            rowsPerPageSelect.addEventListener("change", function () {
                rowsPerPage = parseInt(this.value);
                currentPage = 1;
                showPage(currentPage);
            });

            prevButton.addEventListener("click", function () {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            nextButton.addEventListener("click", function () {
                if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            showPage(currentPage);
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