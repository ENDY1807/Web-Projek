<?php
include 'connect.php';
$data = json_decode(file_get_contents("php://input"), true);
$tanggal = $data['tanggal'];
$kelas = $data['kelas'];
$absensi = $data['absensi'];

foreach($absensi as $a){
    $nis = $a['nis'];
    $status = $a['status'];

    // cek di siswa dulu
    $q = $conn->query("SELECT nis FROM siswa WHERE nis='$nis'");
    if($q->num_rows){
        $conn->query("UPDATE siswa SET status='$status', tanggal='$tanggal' WHERE nis='$nis'");
    } else {
        $conn->query("UPDATE siswaXI SET status='$status', tanggal='$tanggal' WHERE nis='$nis'");
    }
}
echo "success";
?>
