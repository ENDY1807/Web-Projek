<?php
include 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// AMBIL DATA siswaX
$absenData = [];
$qX = $conn->query("SELECT * FROM siswaX ORDER BY id ASC");
while ($row = $qX->fetch_assoc()) {
  $absenData[] = [
    "nama" => $row["Nama"],
    "nis" => $row["NIS"],
    "kelas" => "X",
    "status" => isset($row["status"]) ? $row["status"] : "",
    "alasan" => isset($row["alasan"]) ? $row["alasan"] : ""
  ];
}

// AMBIL DATA siswaXI
$qXI = $conn->query("SELECT * FROM siswaXI ORDER BY id ASC");
while ($row = $qXI->fetch_assoc()) {
  $absenData[] = [
    "nama" => $row["Nama"],
    "nis" => $row["NIS"],
    "kelas" => "XI",
    "status" => isset($row["status"]) ? $row["status"] : "",
    "alasan" => isset($row["alasan"]) ? $row["alasan"] : ""
  ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Histori Absensi</title>
  <style>
    body {
      font-family: Arial;
      background: #f5f5f5;
      margin: 0;
    }

    .dashboard-header {
      background: #2b6cb0;
      padding: 15px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .container {
      padding: 20px;
    }

    .btn {
      padding: 10px 13px;
      border: none;
      border-radius: 6px;
      background: white;
      cursor: pointer;
      font-weight: bold;
    }

    .filter-box {
      background: white;
      padding: 15px;
      border-radius: 8px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 15px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }

    th {
      background: #2b6cb0;
      color: white;
      padding: 12px;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    tr:hover {
      background: #eef4ff;
    }

    .status {
      padding: 5px 8px;
      border-radius: 5px;
      font-weight: bold;
      color: white;
      display: inline-block;
    }

    .Hadir {
      background: #38a169;
    }

    .Izin {
      background: #3182ce;
    }

    .Sakit {
      background: #dd6b20;
    }

    .Alpa {
      background: #e53e3e;
    }
  </style>
</head>

<body>

  <header class="dashboard-header">
    <h2>Histori Absensi</h2>
    <a href="absen.php"><button class="btn">Kembali</button></a>
  </header>

  <div class="container">

    <div class="filter-box">
      <select id="pilihKelas">
        <option value="">Pilih Kelas</option>

        <?php
        $kelas = $conn->query("SELECT DISTINCT kelas FROM siswaX ORDER BY kelas ASC");
        while ($k = $kelas->fetch_assoc())
          echo "<option value='{$k['kelas']}'>{$k['kelas']}</option>";

        $kelas = $conn->query("SELECT DISTINCT kelas FROM siswaXI ORDER BY kelas ASC");
        while ($k = $kelas->fetch_assoc())
          echo "<option value='{$k['kelas']}'>{$k['kelas']}</option>";
        ?>
      </select>

      <select id="filterStatus">
        <option value="">Semua Status</option>
        <option value="Hadir">Hadir</option>
        <option value="Izin">Izin</option>
        <option value="Sakit">Sakit</option>
        <option value="Alpa">Alpa</option>
      </select>

      <input type="text" id="filterNama" placeholder="Cari nama...">
      <button class="btn" onclick="applyFilters()">Filter</button>
      <button class="btn" onclick="resetFilters()">Reset</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>NIS</th>
          <th>Kelas</th>
          <th>Status</th>
          <th>Alasan</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>

  </div>

  <script>
    const data = <?= json_encode($absenData); ?>;

    function loadTable(list) {
      const tbody = document.getElementById("tbody");
      tbody.innerHTML = "";
      list.forEach(r => {
        let statusClass = r.status ? r.status : "";
        let alasanText = r.alasan ? r.alasan : "-";
        tbody.innerHTML += `
        <tr>
            <td>${r.nama}</td>
            <td>${r.nis}</td>
            <td>${r.kelas}</td>
            <td>${r.status ? `<span class="status ${statusClass}">${r.status}</span>` : ""}</td>
            <td>${alasanText}</td>
        </tr>
        `;
      });
    }

    function applyFilters() {
      let kelas = document.getElementById("pilihKelas").value; // pakai id pilihKelas
      let status = document.getElementById("filterStatus").value;
      let nama = document.getElementById("filterNama").value.toLowerCase();

      const hasil = data.filter(r =>
        (kelas === "" || r.kelas === kelas) &&
        (status === "" || r.status === status) &&
        (nama === "" || r.nama.toLowerCase().includes(nama))
      );

      loadTable(hasil);
    }

    function resetFilters() {
      document.getElementById("pilihKelas").value = "";
      document.getElementById("filterStatus").value = "";
      document.getElementById("filterNama").value = "";
      loadTable(data);
    }

    loadTable(data);
  </script>

</body>

</html>
