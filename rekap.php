<?php include "connect.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekap Absen</title>
  <link rel="stylesheet" href="css/style.css">

  <style>
    .rekap-container {
      max-width: 900px;
      margin: 30px auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .btn-date, .btn-delete {
      padding: 8px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      color: #fff;
    }
    .btn-date { background: #0066cc; }
    .btn-date:hover { background: #004a99; }
    .btn-delete { background: #d00000; }
    .btn-delete:hover { background: #900000; }

    .date-item {
      display: flex;
      gap: 10px;
      margin-bottom: 8px;
    }

    .rekap-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .rekap-table th {
      background: #0066cc;
      color: white;
      padding: 10px;
    }
    .rekap-table td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }
  </style>
</head>
<body>

<header class="dashboard-header">
    <div class="header-left">
        <img src="assets/absensi.png" class="logo">
        <h1>Absensi Kelas</h1>
    </div>
</header>

<nav class="dashboard-nav">
    <ul>
      <li><a href="dashboard.html" class="nav-link">Dashboard</a></li>
      <li><a href="absen.php" class="nav-link">Absensi Harian</a></li>
      <li><a href="rekap.php" class="nav-link active">Rekap Absen</a></li>
    </ul>
</nav>

<div class="rekap-container">
  <h2>Rekap Absensi</h2>

  <div id="dateList">
    <?php
      $q = $conn->query("SELECT DISTINCT tanggal FROM absensi ORDER BY tanggal DESC");

      if ($q->num_rows === 0) {
        echo "<p>Belum ada data absensi.</p>";
      } else {
        while ($row = $q->fetch_assoc()) {
          $tgl = $row['tanggal'];
          echo "
            <div class='date-item'>
              <button class='btn-date' onclick=\"loadRekap('$tgl')\">$tgl</button>
              <button class='btn-delete' onclick=\"hapusTanggal('$tgl')\">Hapus</button>
            </div>
          ";
        }
      }
    ?>
  </div>

  <div id="rekapDetail" style="display:none;">
    <h3 id="judulTanggal"></h3>

    <table class="rekap-table">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Kelas</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="rekapBody"></tbody>
    </table>

    <div id="summaryStats" style="margin-top:20px;"></div>
  </div>
</div>

<script>
function loadRekap(tanggal) {
  fetch("rekap_data.php?tanggal=" + tanggal)
    .then(r => r.json())
    .then(data => {
      document.getElementById("rekapDetail").style.display = "block";
      document.getElementById("judulTanggal").innerHTML =
        "Rekap Absensi Tanggal: <b>" + tanggal + "</b>";

      let tbody = document.getElementById("rekapBody");
      tbody.innerHTML = "";

      let hadir=0, izin=0, sakit=0, alpa=0;

      data.forEach((d, i) => {
        tbody.innerHTML += `
          <tr>
            <td>${i+1}</td>
            <td>${d.nama}</td>
            <td>${d.kelas}</td>
            <td>${d.status}</td>
          </tr>
        `;

        if (d.status === "Hadir") hadir++;
        else if (d.status === "Izin") izin++;
        else if (d.status === "Sakit") sakit++;
        else if (d.status === "Alpa") alpa++;
      });

      document.getElementById("summaryStats").innerHTML = `
        <b>Hadir:</b> ${hadir}<br>
        <b>Izin:</b> ${izin}<br>
        <b>Sakit:</b> ${sakit}<br>
        <b>Alpa:</b> ${alpa}
      `;
    });
}

function hapusTanggal(tanggal) {
  if (!confirm("Yakin ingin hapus data tanggal " + tanggal + "?")) return;

  fetch("rekap_hapus.php?tanggal=" + tanggal)
    .then(() => location.reload());
}
</script>

</body>
</html>
