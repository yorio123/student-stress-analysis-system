<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login_page.php");
    exit;
}

include "koneksi/koneksi.php";

/* =========================================================
   PENCARIAN, FILTER DAN PAGINATION
========================================================= */

// Jumlah data setiap halaman
$batas = 50;

// Halaman aktif
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;

if ($halaman < 1) {
    $halaman = 1;
}

// Posisi awal data
$mulai = ($halaman - 1) * $batas;

// Keyword pencarian
$keyword = "";

if (isset($_GET['keyword'])) {
    $keyword = trim(mysqli_real_escape_string($conn, $_GET['keyword']));
}

// Filter Status
$status = "";

if (isset($_GET['status'])) {
    $status = trim(mysqli_real_escape_string($conn, $_GET['status']));
}


/* =========================================================
   QUERY DASAR
========================================================= */

$sql = "
SELECT
    d.data_id,
    d.nim,
    d.nama,
    d.jurusan,
    d.semester,
    h.stress_score,
    h.stress_category

FROM data d

LEFT JOIN hasil_fuzzy h
ON d.data_id = h.data_id

WHERE 1=1
";


/* =========================================================
   FILTER KEYWORD
========================================================= */

if ($keyword != "") {

    $sql .= "
    AND (
        d.nim LIKE '%$keyword%'
        OR d.nama LIKE '%$keyword%'
        OR d.jurusan LIKE '%$keyword%'
    )
    ";

}


/* =========================================================
   FILTER STATUS
========================================================= */

if ($status != "") {

    $sql .= "
    AND h.stress_category='$status'
    ";

}


/* =========================================================
   QUERY TOTAL DATA
========================================================= */

$sqlCount = "
SELECT COUNT(*) AS total

FROM data d

LEFT JOIN hasil_fuzzy h

ON d.data_id=h.data_id

WHERE 1=1
";


if ($keyword != "") {

    $sqlCount .= "
    AND (
        d.nim LIKE '%$keyword%'
        OR d.nama LIKE '%$keyword%'
        OR d.jurusan LIKE '%$keyword%'
    )
    ";

}


if ($status != "") {

    $sqlCount .= "
    AND h.stress_category='$status'
    ";

}


// Hitung jumlah data
$resultCount = mysqli_query($conn, $sqlCount);

$dataCount = mysqli_fetch_assoc($resultCount);

$totalData = $dataCount['total'];

$totalHalaman = ceil($totalData / $batas);


/* =========================================================
   QUERY DATA MAHASISWA
========================================================= */

$sql .= "
ORDER BY d.data_id ASC

LIMIT $mulai, $batas
";

$queryData = mysqli_query($conn, $sql);


/* =========================================================
   JUMLAH DATA PADA HALAMAN AKTIF
========================================================= */

$jumlahDataHalaman = mysqli_num_rows($queryData);

?>
<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Data Mahasiswa</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#f4f6fb;
display:flex;
}

/* ===========================
SIDEBAR
=========================== */

.sidebar{

width:250px;
height:100vh;
background:linear-gradient(180deg,#667eea,#764ba2);
position:fixed;
left:0;
top:0;
padding:30px 20px;
color:#fff;

}

.sidebar h2{

text-align:center;
margin-bottom:40px;

}

.sidebar ul{

list-style:none;

}

.sidebar ul li{

margin:15px 0;

}

.sidebar ul li a{

display:block;
padding:12px;
border-radius:10px;
color:white;
text-decoration:none;
transition:.3s;

}

.sidebar ul li a:hover{

background:rgba(255,255,255,.2);

}

/* ===========================
MAIN
=========================== */

.main{

margin-left:250px;
width:calc(100% - 250px);
padding:30px;

}

/* ===========================
NAVBAR
=========================== */

.navbar{

background:#fff;
padding:20px;
border-radius:15px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:25px;

}

/* ===========================
HEADER
=========================== */

.header{

display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;

}

.header h2{

color:#333;

}

.btnTambah{

background:#667eea;
color:white;
padding:10px 18px;
border-radius:10px;
text-decoration:none;
transition:.3s;

}

.btnTambah:hover{

background:#5568d8;

}

/* ===========================
SEARCH
=========================== */

.search-box{

background:white;
padding:20px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:20px;

}

.search-box form{

display:flex;
gap:10px;

}

.search-box input{

flex:1;
padding:10px;
border:1px solid #ddd;
border-radius:8px;

}

.search-box button{

background:#667eea;
color:white;
border:none;
padding:10px 18px;
border-radius:8px;
cursor:pointer;

}

/* ===========================
TABLE BOX
=========================== */

.table-box{

background:white;
padding:20px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);

}

/* ===========================
TABLE
=========================== */

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

table thead{

background:#667eea;
color:white;

}

table th{

padding:15px;
text-align:center;
font-size:14px;
font-weight:600;

}

table td{

padding:15px;
text-align:center;
border-bottom:1px solid #e9ecef;
font-size:14px;

}

table tbody tr{

transition:.3s;

}

table tbody tr:hover{

background:#f8f9ff;

}

/* ===========================
BUTTON
=========================== */

.btn{

width:38px;
height:38px;
border:none;
border-radius:8px;
cursor:pointer;
color:white;
transition:.3s;
margin:2px;

}

.btn:hover{

transform:scale(1.08);

}

.detail{

background:#3498db;

}

.detail:hover{

background:#2980b9;

}

.edit{

background:#f39c12;

}

.edit:hover{

background:#d68910;

}

.delete{

background:#e74c3c;

}

.delete:hover{

background:#c0392b;

}

/* ===========================
BADGE
=========================== */

.badge{

padding:6px 14px;
border-radius:20px;
color:white;
font-size:13px;
font-weight:500;

}

.badge-red{

background:#ff6b6b;

}

.badge-orange{

background:#f39c12;

}

.badge-green{

background:#2ecc71;

}

/* ===========================
SEARCH
=========================== */

.search-box input:focus{

outline:none;
border:1px solid #667eea;
box-shadow:0 0 10px rgba(102,126,234,.2);

}

.search-box button:hover{

background:#5568d8;

}

/* ===========================
SCROLL TABLE
=========================== */

.table-box{

overflow-x:auto;

}

/* ===========================
RESPONSIVE
=========================== */

@media(max-width:991px){

.sidebar{

width:220px;

}

.main{

margin-left:220px;
width:calc(100% - 220px);

}

}

@media(max-width:768px){

.sidebar{

position:relative;
width:100%;
height:auto;

}

.main{

margin-left:0;
width:100%;

}

.navbar{

flex-direction:column;
gap:15px;

}

.header{

flex-direction:column;
align-items:flex-start;
gap:15px;

}

.search-box form{

flex-direction:column;

}

table{

font-size:13px;

}

.btn{

width:34px;
height:34px;

}

}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<h2>

<i class="fas fa-brain"></i>

<br>

FuzzyStress

</h2>

<ul>

<li><a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>

<li><a href="data.php" style="background:rgba(255,255,255,.2);">
<i class="fas fa-users"></i> Data Mahasiswa</a></li>

<li><a href="hasil.php"><i class="fas fa-chart-pie"></i> Hasil Fuzzy</a></li>

<li><a href="evaluasi.php"><i class="fas fa-file"></i> Evaluasi</a></li>

<li><a href="process/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

</ul>

</div>

<!-- MAIN -->

<div class="main">

<div class="navbar">

<h2>Data Mahasiswa</h2>

<div>

<i class="fas fa-user-circle fa-2x"></i>

<?= $_SESSION['nama']; ?>

</div>

</div>

<div class="header">

</div>

<div class="search-box">

<form method="GET">

<input
type="text"
name="keyword"
placeholder="Cari NIM, Nama, atau Jurusan..."
value="<?= htmlspecialchars($keyword); ?>">

<select name="status">

<option value="">Semua Status</option>

<option value="Tinggi" <?= ($status=="Tinggi") ? "selected" : ""; ?>>
Tinggi
</option>

<option value="Sedang" <?= ($status=="Sedang") ? "selected" : ""; ?>>
Sedang
</option>

<option value="Rendah" <?= ($status=="Rendah") ? "selected" : ""; ?>>
Rendah
</option>

</select>

<button type="submit">
<i class="fas fa-search"></i>
Cari
</button>

<a href="data.php" class="btnTambah">
Reset
</a>

</form>

</div>

<div class="table-box">
<?php
$awal = ($totalData == 0) ? 0 : $mulai + 1;
$akhir = min($mulai + $jumlahDataHalaman, $totalData);
?>

<p>
Menampilkan <b><?= $awal ?></b>–<b><?= $akhir ?></b> dari <b><?= $totalData ?></b> data mahasiswa.
</p>
<table>

<thead>

<tr>

<th width="60">No</th>

<th>NIM</th>

<th>Nama Mahasiswa</th>

<th>Jurusan</th>

<th>Semester</th>

<th>Skor Fuzzy</th>

<th>Status</th>

<th width="170">Aksi</th>

</tr>

</thead>    

<tbody>

<?php

if(mysqli_num_rows($queryData) > 0){

$no = $mulai + 1;

while($row = mysqli_fetch_assoc($queryData)){

?>

<tr>

<td><?= $no++; ?></td>

<td><?= htmlspecialchars($row['nim']); ?></td>

<td><?= htmlspecialchars($row['nama']); ?></td>

<td><?= htmlspecialchars($row['jurusan']); ?></td>

<td><?= $row['semester']; ?></td>

<td>

<b><?= number_format($row['stress_score'],2); ?></b>

</td>

<td>

<?php

if($row['stress_category']=="Tinggi"){

?>

<span style="background:#ff6b6b;
padding:6px 12px;
border-radius:20px;
color:white;
font-size:13px;">

Tinggi

</span>

<?php

}elseif($row['stress_category']=="Sedang"){

?>

<span style="background:#f39c12;
padding:6px 12px;
border-radius:20px;
color:white;
font-size:13px;">

Sedang

</span>

<?php

}else{

?>

<span style="background:#2ecc71;
padding:6px 12px;
border-radius:20px;
color:white;
font-size:13px;">

Rendah

</span>

<?php

}

?>

</td>

<td>

<a href="detail_hasil.php?id=<?= $row['data_id']; ?>">

<button class="btn detail">

<i class="fas fa-eye"></i>

</button>

</a>

<a href="process/hapus.php?id=<?= $row['data_id']; ?>"

onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">

<button class="btn delete">

<i class="fas fa-trash"></i>

</button>

</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="8">

<div style="padding:30px;color:#888;">

<i class="fas fa-folder-open fa-3x"></i>

<br><br>

Data mahasiswa tidak ditemukan.

</div>

</td>

</tr>

<?php

}

?>

</tbody>

</table>
<div style="margin-top:20px;text-align:center;">

<?php if($halaman > 1){ ?>

<a href="?keyword=<?= urlencode($keyword) ?>&status=<?= urlencode($status) ?>&halaman=<?= $halaman-1; ?>">

<button class="btn detail">

<i class="fas fa-angle-left"></i>

</button>

</a>

<?php } ?>


<?php

for($i=1;$i<=$totalHalaman;$i++){

?>

<a href="?keyword=<?= urlencode($keyword) ?>&status=<?= urlencode($status) ?>&halaman=<?= $i; ?>">

<button class="btn <?= ($i==$halaman) ? 'edit' : 'detail'; ?>">

<?= $i ?>

</button>

</a>

<?php

}

?>


<?php if($halaman < $totalHalaman){ ?>

<a href="?keyword=<?= urlencode($keyword) ?>&status=<?= urlencode($status) ?>&halaman=<?= $halaman+1; ?>">

<button class="btn detail">

<i class="fas fa-angle-right"></i>

</button>

</a>

<?php } ?>

</div>

</div>

</div>

</body>

</html>