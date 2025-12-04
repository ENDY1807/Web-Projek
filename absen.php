<?php include "connect.php"; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Harian</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #0066cc;
            color: white;
        }

        .btn {
            padding: 10px 15px;
            background: #0066cc;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 7px;
        }

        .btn:hover {
            background: #004a99;
        }

        select, input {
            padding: 8px;
            border-radius: 7px;
        }

        .container {
            padding: 20px;
            margin: 20px;
            border-radius: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .Hadir {
            background-color: #459548FF;
            color: white;
        }

        .Izin {
            background-color: #D2BD00FF;
            color: black;
        }

        .Sakit {
            background-color: #008CCDFF;
            color: white;
        }

        .Alpa {
            background-color: #9C0A00FF;
            color: white;
        }
    </style>
</head>

<body>
<div class="container">
        <div class="header-left">
            <img src="assets/absensi.png" alt="Logo Absensi" class="logo">
            <h1>Absensi Kelas</h1>
        </div>
        <div class="rekap">
            <a href="rekap.php">
                <button>Rekap Absen</button>
            </a>
        </div>
    </header>
        <label>Pilih Tanggal:</label>
        <input type="date" id="tanggalAbsen">
        <br><br>
        <label>Pilih Kelas:</label>
        <select id="pilihKelas" onchange="loadSiswa()" style="padding:8px; border-radius:6px;">
            <option value="">Pilih Kelas</option>
            <?php
            $kelas = $conn->query("SELECT DISTINCT kelas FROM siswaXI ORDER BY kelas ASC");
            while ($k = $kelas->fetch_assoc()) {
                echo "<option value='$k[kelas]'>$k[kelas]</option>";
            }
            $kelas = $conn->query("SELECT DISTINCT kelas FROM siswaX ORDER BY kelas ASC");
            while ($k = $kelas->fetch_assoc()) {
                echo "<option value='$k[kelas]'>$k[kelas]</option>";
            }
            ?>
        </select>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="tbodySiswa"></tbody>
        </table>
        <button class="btn" onclick="simpanAbsensi()">Simpan Absensi</button>
    </div>
    <script>
        document.getElementById("pilihKelas").addEventListener("change", loadSiswa);
        function loadSiswa() {
            const kelas = document.getElementById("pilihKelas").value;
            if (!kelas) return;
            fetch("api_datasiswa.php?kelas=" + kelas)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById("tbodySiswa");
                    tbody.innerHTML = "";

                    data.forEach((siswa, i) => {
                        const tr = document.createElement("tr");

                        tr.innerHTML = `
                            <td>${i + 1}</td>
                            <td>${siswa.Nama}</td>
                            <td>
                                <select onchange="ubahWarna(this)" name="status_${siswa.NIS}">
                                    <option value="" class="">Status</option>
                                    <option value="Hadir" class="Hadir">Hadir</option>
                                    <option value="Izin" class="Izin">Izin</option>
                                    <option value="Sakit" class="Sakit">Sakit</option>
                                    <option value="Alpa" class="Alpa">Alpa</option>
                                </select>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        }

        function simpanAbsensi() {
            const tanggal = document.getElementById("tanggalAbsen").value;
            const kelas = document.getElementById("pilihKelas").value;

            if (!tanggal) return alert("Tanggal belum dipilih!");
            if (!kelas) return alert("Kelas belum dipilih!");

            const radios = document.querySelectorAll("tbody input[type=radio]");
            let hasilAbsen = {};

            radios.forEach(r => {
                if (r.checked) {
                    const nis = r.name.replace("status_", "");
                    hasilAbsen[nis] = r.value;
                }
            });

            const key = `absensi_${tanggal}_${kelas}`;
            localStorage.setItem(key, JSON.stringify(hasilAbsen));

            alert("Absensi tersimpan untuk kelas " + kelas);
        }

        function ubahWarna(select) {
            select.classList.remove("Hadir", "Izin", "Sakit", "Alpa");
            select.classList.add(select.value);
        }
    </script>
</body>
</html>
