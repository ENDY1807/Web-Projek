<?php
include "connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo "DATA KOSONG";
    exit;
}

$tanggal = $data["tanggal"];
$kelas   = $data["kelas"];
$absen   = $data["absensi"];

foreach ($absen as $a) {

    $nis = $a["nis"];
    $status = $a["status"];

    if ($status == "") continue;

    $q = $conn->query("
        SELECT Nama FROM siswaX WHERE NIS='$nis'
        UNION 
        SELECT Nama FROM siswaXI WHERE NIS='$nis'
    ");

    if($q->num_rows == 0) continue;

    $row = $q->fetch_assoc();
    $nama = $row["Nama"];

    $conn->query("
        INSERT INTO absensi_kelas (tanggal, nama, kelas, status, alasan)
        VALUES ('$tanggal', '$nama', '$kelas', '$status', '')
    ");
}

echo "SUKSES"; 
?>