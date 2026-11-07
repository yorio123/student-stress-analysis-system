<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login_page.php");
    exit;
}

include "koneksi/koneksi.php";

/* =========================================================
   PAGINATION
========================================================= */

$batas = 50;

$halaman = isset($_GET['halaman'])
    ? (int)$_GET['halaman']
    : 1;

if ($halaman < 1) {
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;


/* =========================================================
   PENCARIAN
========================================================= */

$keyword = "";

if(isset($_GET['keyword'])){
    $keyword = trim(mysqli_real_escape_string($conn,$_GET['keyword']));
}


/* =========================================================
   QUERY UNTUK PERHITUNGAN EVALUASI
========================================================= */

$queryEvaluasi = mysqli_query($conn, "

SELECT

d.data_id,
d.nim,
d.nama,

hp.stress_score AS manual_score,
hp.stress_category AS manual_category,

hf.stress_score AS fuzzy_score,
hf.stress_category AS fuzzy_category

FROM hasil_perhitungan hp

INNER JOIN hasil_fuzzy hf
ON hp.data_id = hf.data_id

INNER JOIN data d
ON d.data_id = hp.data_id

INNER JOIN (
    SELECT data_id
    FROM data
    ORDER BY data_id ASC
    LIMIT 200
) AS limited_data
ON limited_data.data_id = d.data_id

ORDER BY d.data_id ASC

");


/* =========================================================
   INISIALISASI
========================================================= */

$totalData = 0;

$correct = 0;

$totalAbsError = 0;

$totalSquaredError = 0;


/* =========================================================
   CONFUSION MATRIX
========================================================= */

$matrix = [

"Rendah"=>[
"Rendah"=>0,
"Sedang"=>0,
"Tinggi"=>0
],

"Sedang"=>[
"Rendah"=>0,
"Sedang"=>0,
"Tinggi"=>0
],

"Tinggi"=>[
"Rendah"=>0,
"Sedang"=>0,
"Tinggi"=>0
]

];


/* =========================================================
   HITUNG EVALUASI
========================================================= */

while($row = mysqli_fetch_assoc($queryEvaluasi)){

$manualScore=(float)$row['manual_score'];

$fuzzyScore=(float)$row['fuzzy_score'];

$manualCategory=trim($row['manual_category']);

$fuzzyCategory=trim($row['fuzzy_category']);


/* Accuracy */

if($manualCategory==$fuzzyCategory){

$correct++;

}


/* MAE */

$absError=abs($manualScore-$fuzzyScore);

$totalAbsError+=$absError;


/* RMSE */

$totalSquaredError+=pow(($manualScore-$fuzzyScore),2);


/* Matrix */

$matrix[$manualCategory][$fuzzyCategory]++;

$totalData++;

}


/* =========================================================
   HASIL EVALUASI
========================================================= */

$accuracy = 0;
$mae = 0;
$rmse = 0;

if($totalData>0){

$accuracy=($correct/$totalData)*100;

$mae=$totalAbsError/$totalData;

$rmse=sqrt($totalSquaredError/$totalData);

}


/* =========================================================
   QUERY TABEL
========================================================= */

$sql="

SELECT

d.data_id,
d.nim,
d.nama,

hp.stress_score AS manual_score,
hp.stress_category AS manual_category,

hf.stress_score AS fuzzy_score,
hf.stress_category AS fuzzy_category

FROM hasil_perhitungan hp

INNER JOIN hasil_fuzzy hf
ON hp.data_id=hf.data_id

INNER JOIN data d
ON d.data_id=hp.data_id

WHERE

d.nama LIKE '%$keyword%'

OR

d.nim LIKE '%$keyword%'

ORDER BY d.data_id ASC

LIMIT $mulai,$batas

";

$query=mysqli_query($conn,$sql);


/* =========================================================
   TOTAL DATA TABEL
========================================================= */

$totalQuery=mysqli_query($conn,"

SELECT COUNT(*) total

FROM data

");

$totalMahasiswa=mysqli_fetch_assoc($totalQuery);

$totalHalaman=ceil($totalMahasiswa['total']/$batas);

?>
<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Evaluasi Sistem</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

/* ==========================
SIDEBAR
========================== */

.sidebar{
width:250px;
height:100vh;
background:linear-gradient(180deg,#667eea,#764ba2);
position:fixed;
left:0;
top:0;
padding:30px 20px;
color:white;
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
text-decoration:none;
color:white;
transition:.3s;
}

.sidebar ul li a:hover{
background:rgba(255,255,255,.2);
}

/* ==========================
MAIN
========================== */

.main{
margin-left:250px;
width:calc(100% - 250px);
padding:30px;
}

/* ==========================
NAVBAR
========================== */

.navbar{
background:white;
padding:20px;
border-radius:15px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:25px;
}

/* ==========================
CARD
========================== */

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:25px;
}

.card{
background:white;
padding:25px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
}

.card h4{
font-size:14px;
color:#777;
}

.card h2{
margin-top:10px;
font-size:28px;
color:#333;
}

/* ==========================
SEARCH
========================== */

.search-box{
background:white;
padding:20px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
margin-bottom:20px;
}

.search-box form{
display:flex;
gap:10px;
flex-wrap:wrap;
}

.search-box input{
flex:1;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
}

.search-box button{

padding:12px 20px;
border:none;
background:#667eea;
color:white;
border-radius:10px;
cursor:pointer;

}

.search-box a{

padding:12px 20px;
background:#999;
color:white;
text-decoration:none;
border-radius:10px;

}

/* ==========================
TABLE
========================== */

.table-box{

background:white;
padding:20px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
margin-top:20px;
overflow-x:auto;

}

table{

width:100%;
border-collapse:collapse;

}

table th{

background:#667eea;
color:white;
padding:14px;
text-align:center;

}

table td{

padding:14px;
border-bottom:1px solid #eee;
text-align:center;

}

table tr:hover{

background:#f9f9f9;

}

/* ==========================
BADGE
========================== */

.badge{

padding:6px 12px;
border-radius:30px;
color:white;
font-size:13px;

}

.badge-red{

background:#e74c3c;

}

.badge-orange{

background:#f39c12;

}

.badge-green{

background:#2ecc71;

}

/* ==========================
GRAFIK
========================== */

.chart-box{

background:white;
padding:25px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
margin-top:25px;

}

/* ==========================
PAGINATION
========================== */

.pagination{

display:flex;
justify-content:center;
gap:10px;
margin-top:25px;
flex-wrap:wrap;

}

.page{

width:40px;
height:40px;
background:#667eea;
color:white;
display:flex;
justify-content:center;
align-items:center;
border-radius:8px;
font-weight:bold;
transition:.3s;

}

.page:hover{

background:#5569d8;

}

.active-page{

background:#f39c12;

}

.pagination a{

text-decoration:none;

}

/* ==========================
RESPONSIVE
========================== */

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

.cards{

grid-template-columns:1fr;

}

.search-box form{

flex-direction:column;

}

}

</style>

</head>

<body>

<div class="sidebar">

<h2>

<i class="fas fa-brain"></i>

<br>

FuzzyStress

</h2>

<ul>

<li><a href="dashboard.php">
<i class="fas fa-chart-line"></i>
Dashboard
</a></li>

<li><a href="data.php">
<i class="fas fa-users"></i>
Data Mahasiswa
</a></li>

<li><a href="hasil.php">
<i class="fas fa-chart-pie"></i>
Hasil Fuzzy
</a></li>

<li>
<a href="evaluasi.php"
style="background:rgba(255,255,255,.2);">

<i class="fas fa-file"></></i>

Evaluasi

</a>
</li>

<li><a href="process/logout.php">
<i class="fas fa-sign-out-alt"></i>
Logout
</a></li>

</ul>

</div>

<div class="main">

<div class="navbar">

<h2>Evaluasi Sistem Fuzzy Mamdani</h2>

<div>

<i class="fas fa-user-circle fa-2x"></i>

<?= $_SESSION['nama']; ?>

</div>

</div>

<div class="cards">

<div class="card">

<h4>Total Data</h4>

<h2><?= $totalData ?></h2>

</div>

<div class="card">

<h4>Accuracy</h4>

<h2><?= round($accuracy,2) ?>%</h2>

</div>

<div class="card">

<h4>MAE</h4>

<h2><?= round($mae,2) ?></h2>

</div>

<div class="card">

<h4>RMSE</h4>

<h2><?= round($rmse,2) ?></h2>

</div>

</div>
<!-- =========================================================
SEARCH
========================================================= -->

<div class="search-box">

<form method="GET">

<input
type="text"
name="keyword"
placeholder="Cari berdasarkan NIM atau Nama..."
value="<?= htmlspecialchars($keyword); ?>">

<button type="submit">

<i class="fas fa-search"></i>

Cari

</button>

<a href="evaluasi.php">

Reset

</a>

</form>

</div>


<!-- =========================================================
TABEL EVALUASI
========================================================= -->

<div class="table-box">

<p style="margin-bottom:15px;">

<?php

$jumlahDataHalaman = mysqli_num_rows($query);

$awal = ($totalData == 0) ? 0 : $mulai + 1;

$akhir = min($mulai + $jumlahDataHalaman,$totalMahasiswa['total']);

?>

Menampilkan

<b><?= $awal ?></b>

-

<b><?= $akhir ?></b>

dari

<b><?= $totalMahasiswa['total'] ?></b>

data.

</p>


<table>

<thead>

<tr>

<th>No</th>

<th>NIM</th>

<th>Nama</th>

<th>Manual Score</th>

<th>Fuzzy Score</th>

<th>Manual</th>

<th>Fuzzy</th>

<th>Error</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($query)>0){

$no = $mulai + 1;

while($row=mysqli_fetch_assoc($query)){

$error = abs(

$row['manual_score']

-

$row['fuzzy_score']

);

?>

<tr>

<td><?= $no++ ?></td>

<td><?= htmlspecialchars($row['nim']) ?></td>

<td><?= htmlspecialchars($row['nama']) ?></td>

<td>

<?= number_format($row['manual_score'],2) ?>

</td>

<td>

<?= number_format($row['fuzzy_score'],2) ?>

</td>

<td>

<?php

if($row['manual_category']=="Tinggi"){

echo "<span class='badge badge-red'>Tinggi</span>";

}elseif($row['manual_category']=="Sedang"){

echo "<span class='badge badge-orange'>Sedang</span>";

}else{

echo "<span class='badge badge-green'>Rendah</span>";

}

?>

</td>

<td>

<?php

if($row['fuzzy_category']=="Tinggi"){

echo "<span class='badge badge-red'>Tinggi</span>";

}elseif($row['fuzzy_category']=="Sedang"){

echo "<span class='badge badge-orange'>Sedang</span>";

}else{

echo "<span class='badge badge-green'>Rendah</span>";

}

?>

</td>

<td>

<b>

<?= number_format($error,2) ?>

</b>

</td>

<td>

<?php

if($row['manual_category']==$row['fuzzy_category']){

?>

<span
style="color:#27ae60;font-weight:bold;">

<i class="fas fa-check-circle"></i>

Sesuai

</span>

<?php

}else{

?>

<span
style="color:#e74c3c;font-weight:bold;">

<i class="fas fa-times-circle"></i>

Berbeda

</span>

<?php

}

?>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="9">

<div style="padding:40px">

<i class="fas fa-folder-open fa-3x"></i>

<br><br>

Tidak ada data.

</div>

</td>

</tr>

<?php

}

?>

</tbody>

</table>
<!-- =========================================================
PAGINATION
========================================================= -->

<div class="pagination">

<?php

if($halaman>1){

?>

<a href="?keyword=<?= urlencode($keyword); ?>&halaman=<?= $halaman-1; ?>">

<div class="page">

<i class="fas fa-angle-left"></i>

</div>

</a>

<?php

}

?>


<?php

for($i=1;$i<=$totalHalaman;$i++){

?>

<a href="?keyword=<?= urlencode($keyword); ?>&halaman=<?= $i; ?>">

<div class="page <?= ($i==$halaman)?'active-page':''; ?>">

<?= $i; ?>

</div>

</a>

<?php

}

?>


<?php

if($halaman<$totalHalaman){

?>

<a href="?keyword=<?= urlencode($keyword); ?>&halaman=<?= $halaman+1; ?>">

<div class="page">

<i class="fas fa-angle-right"></i>

</div>

</a>

<?php

}

?>

</div>

</div>


<!-- =========================================================
CONFUSION MATRIX
========================================================= -->

<div class="table-box" style="margin-top:30px;">

<h3 style="margin-bottom:20px;">

Confusion Matrix

</h3>

<table>

<thead>

<tr>

<th>Manual \\ Fuzzy</th>

<th>Rendah</th>

<th>Sedang</th>

<th>Tinggi</th>

</tr>

</thead>

<tbody>

<tr>

<th>Rendah</th>

<td><?= $matrix['Rendah']['Rendah']; ?></td>

<td><?= $matrix['Rendah']['Sedang']; ?></td>

<td><?= $matrix['Rendah']['Tinggi']; ?></td>

</tr>

<tr>

<th>Sedang</th>

<td><?= $matrix['Sedang']['Rendah']; ?></td>

<td><?= $matrix['Sedang']['Sedang']; ?></td>

<td><?= $matrix['Sedang']['Tinggi']; ?></td>

</tr>

<tr>

<th>Tinggi</th>

<td><?= $matrix['Tinggi']['Rendah']; ?></td>

<td><?= $matrix['Tinggi']['Sedang']; ?></td>

<td><?= $matrix['Tinggi']['Tinggi']; ?></td>

</tr>

</tbody>

</table>

</div>


