<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login_page.php");
    exit;
}

include "koneksi/koneksi.php";

/* =========================================================
   AMBIL ID
========================================================= */

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan.");
}

$id = (int)$_GET['id'];

/* =========================================================
   QUERY DETAIL DATA
========================================================= */

$query = mysqli_query($conn, "

SELECT

d.*,

p.*,

tm.*,

pcv.*,

f.*,

s.*,

hp.score_paw,
hp.score_tm,
hp.score_pss,
hp.score_fs,
hp.score_sq,
hp.stress_score AS manual_score,
hp.stress_category AS manual_category,

hf.norm_paw,
hf.norm_tm,
hf.norm_pss,
hf.norm_fs,
hf.norm_sq,
hf.stress_score AS fuzzy_score,
hf.stress_category AS fuzzy_category

FROM data d

INNER JOIN perceived p
ON d.data_id = p.data_id

INNER JOIN time_management tm
ON d.data_id = tm.data_id

INNER JOIN pcv
ON d.data_id = pcv.data_id

INNER JOIN financial f
ON d.data_id = f.data_id

INNER JOIN sleep s
ON d.data_id = s.data_id

INNER JOIN hasil_perhitungan hp
ON d.data_id = hp.data_id

INNER JOIN hasil_fuzzy hf
ON d.data_id = hf.data_id

WHERE d.data_id='$id'

");

if(mysqli_num_rows($query)==0){

die("Data tidak ditemukan.");

}

$data = mysqli_fetch_assoc($query);

$error = abs(
$data['manual_score'] -
$data['fuzzy_score']
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Detail Hasil</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

/* CSS akan diberikan pada Bagian 4 *//* =========================================================
RESET
========================================================= */

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

/* =========================================================
SIDEBAR
========================================================= */

.sidebar{

width:250px;
height:100vh;
background:linear-gradient(180deg,#667eea,#764ba2);
position:fixed;
left:0;
top:0;
padding:30px 20px;
color:white;
overflow-y:auto;

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
text-decoration:none;
color:white;
border-radius:10px;
transition:.3s;

}

.sidebar ul li a:hover{

background:rgba(255,255,255,.2);

}

/* =========================================================
MAIN
========================================================= */

.main{

margin-left:250px;
width:calc(100% - 250px);
padding:30px;

}

/* =========================================================
NAVBAR
========================================================= */

.navbar{

background:white;
padding:20px;
border-radius:15px;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:30px;

}

/* =========================================================
CARD
========================================================= */

.card{

background:white;
padding:25px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
margin-bottom:25px;

}

.card h3{

margin-bottom:20px;
color:#333;

}

.card h3 i{

color:#667eea;
margin-right:8px;

}

/* =========================================================
DETAIL TABLE
========================================================= */

.detail-table{

width:100%;
border-collapse:collapse;

}

.detail-table tr{

border-bottom:1px solid #eee;

}

.detail-table td{

padding:14px;

}

.detail-table tr:last-child{

border-bottom:none;

}

.detail-table tr:hover{

background:#fafafa;

}

/* =========================================================
BADGE
========================================================= */

.badge{

display:inline-block;
padding:6px 14px;
border-radius:30px;
font-size:13px;
font-weight:600;
color:white;

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

/* =========================================================
BUTTON
========================================================= */

.btn{

padding:10px 18px;
border:none;
border-radius:8px;
cursor:pointer;
color:white;
font-size:14px;
transition:.3s;

}

.btn:hover{

opacity:.9;

}

.detail{

background:#3498db;

}

.edit{

background:#f39c12;

}

.delete{

background:#e74c3c;

}

/* =========================================================
RESPONSIVE
========================================================= */

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
align-items:flex-start;

}

.card{

padding:18px;

}

.detail-table td{

padding:10px;
font-size:14px;

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

<li>

<a href="dashboard.php">

<i class="fas fa-chart-line"></i>

Dashboard

</a>

</li>

<li>

<a href="data.php">

<i class="fas fa-users"></i>

Data Mahasiswa

</a>

</li>

<li>

<a href="hasil.php"
style="background:rgba(255,255,255,.2);">

<i class="fas fa-chart-pie"></i>

Hasil Fuzzy

</a>

</li>

<li>

<a href="evaluasi.php">

<i class="fas fa-chart-bar"></i>

Evaluasi

</a>

</li>

<li>

<a href="process/logout.php">

<i class="fas fa-sign-out-alt"></i>

Logout

</a>

</li>

</ul>

</div>

<div class="main">

<div class="navbar">

<h2>

Detail Hasil Perhitungan

</h2>

<div>

<i class="fas fa-user-circle fa-2x"></i>

<?= $_SESSION['nama']; ?>

</div>

</div>

<!-- =========================================================
IDENTITAS MAHASISWA
========================================================= -->

<div class="card">

<h3>

<i class="fas fa-user"></i>

Data Mahasiswa

</h3>

<table class="detail-table">

<tr>

<td width="220">

<b>NIM</b>

</td>

<td>

<?= htmlspecialchars($data['nim']); ?>

</td>

</tr>

<tr>

<td>

<b>Nama</b>

</td>

<td>

<?= htmlspecialchars($data['nama']); ?>

</td>

</tr>

<tr>

<td>

<b>Jurusan</b>

</td>

<td>

<?= htmlspecialchars($data['jurusan']); ?>

</td>

</tr>

<tr>

<td>

<b>Semester</b>

</td>

<td>

<?= htmlspecialchars($data['semester']); ?>

</td>

</tr>

</table>

</div>
<!-- =========================================================
PERCEIVED ACADEMIC WORKLOAD
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-book"></i>
Perceived Academic Workload
</h3>

<table class="detail-table">

<tr>
<td width="300"><b>Status Tugas Akhir</b></td>
<td><?= $data['status_tga']; ?></td>
</tr>

<tr>
<td><b>Proses Tugas Akhir</b></td>
<td><?= $data['proses_tga']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 1</b></td>
<td><?= $data['q1_tga']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 2</b></td>
<td><?= $data['q2_tga']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 3</b></td>
<td><?= $data['q3_tga']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 4</b></td>
<td><?= $data['q4_tga']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 5</b></td>
<td><?= $data['q5_tga']; ?></td>
</tr>

<tr style="background:#f5f5f5;font-weight:bold;">
<td>Total Skor PAW</td>
<td><?= $data['score_paw']; ?></td>
</tr>

</table>

</div>


<!-- =========================================================
TIME MANAGEMENT
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-clock"></i>
Time Management
</h3>

<table class="detail-table">

<tr>
<td width="300"><b>Sleep Time</b></td>
<td><?= $data['sleep_time']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 1</b></td>
<td><?= $data['q1_time']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 2</b></td>
<td><?= $data['q2_time']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 3</b></td>
<td><?= $data['q3_time']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 4</b></td>
<td><?= $data['q4_time']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 5</b></td>
<td><?= $data['q5_time']; ?></td>
</tr>

<tr style="background:#f5f5f5;font-weight:bold;">
<td>Total Skor Time Management</td>
<td><?= $data['score_tm']; ?></td>
</tr>

</table>

</div>
<!-- =========================================================
PERCEIVED SOCIAL SUPPORT
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-handshake"></i>
Perceived Social Support
</h3>

<table class="detail-table">

<tr>
<td width="300"><b>Pertanyaan 1</b></td>
<td><?= $data['q1_pcv']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 2</b></td>
<td><?= $data['q2_pcv']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 3</b></td>
<td><?= $data['q3_pcv']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 4</b></td>
<td><?= $data['q4_pcv']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 5</b></td>
<td><?= $data['q5_pcv']; ?></td>
</tr>

<tr style="background:#f5f5f5;font-weight:bold;">
<td>Total Skor Perceived Social Support</td>
<td><?= $data['score_pss']; ?></td>
</tr>

</table>

</div>


<!-- =========================================================
FINANCIAL STRESS
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-wallet"></i>
Financial Stress
</h3>

<table class="detail-table">

<tr>
<td width="300"><b>Pertanyaan 1</b></td>
<td><?= $data['q1_fnc']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 2</b></td>
<td><?= $data['q2_fnc']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 3</b></td>
<td><?= $data['q3_fnc']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 4</b></td>
<td><?= $data['q4_fnc']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 5</b></td>
<td><?= $data['q5_fnc']; ?></td>
</tr>

<tr style="background:#f5f5f5;font-weight:bold;">
<td>Total Skor Financial Stress</td>
<td><?= $data['score_fs']; ?></td>
</tr>

</table>

</div>


<!-- =========================================================
SLEEP QUALITY
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-bed"></i>
Sleep Quality
</h3>

<table class="detail-table">

<tr>
<td width="300"><b>Pertanyaan 1</b></td>
<td><?= $data['q1_sleep']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 2</b></td>
<td><?= $data['q2_sleep']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 3</b></td>
<td><?= $data['q3_sleep']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 4</b></td>
<td><?= $data['q4_sleep']; ?></td>
</tr>

<tr>
<td><b>Pertanyaan 5</b></td>
<td><?= $data['q5_sleep']; ?></td>
</tr>

<tr style="background:#f5f5f5;font-weight:bold;">
<td>Total Skor Sleep Quality</td>
<td><?= $data['score_sq']; ?></td>
</tr>

</table>

</div>
<!-- =========================================================
HASIL PERHITUNGAN
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-calculator"></i>
Hasil Perhitungan Manual
</h3>

<table class="detail-table">

<tr>
<td width="300"><b>Skor Stres</b></td>
<td><?= number_format($data['manual_score'],2); ?></td>
</tr>

<tr>
<td><b>Kategori</b></td>
<td>

<?php

if($data['manual_category']=="Tinggi"){

    echo "<span class='badge badge-red'>Tinggi</span>";

}elseif($data['manual_category']=="Sedang"){

    echo "<span class='badge badge-orange'>Sedang</span>";

}else{

    echo "<span class='badge badge-green'>Rendah</span>";

}

?>

</td>

</tr>

</table>

</div>


<!-- =========================================================
HASIL FUZZY
========================================================= -->

<div class="card">

<h3>
<i class="fas fa-brain"></i>
Hasil Fuzzy Mamdani
</h3>

<table class="detail-table">

<tr>

<td width="300"><b>Skor Fuzzy</b></td>

<td><?= number_format($data['fuzzy_score'],2); ?></td>

</tr>

<tr>

<td><b>Kategori</b></td>

<td>

<?php

if($data['fuzzy_category']=="Tinggi"){

    echo "<span class='badge badge-red'>Tinggi</span>";

}elseif($data['fuzzy_category']=="Sedang"){

    echo "<span class='badge badge-orange'>Sedang</span>";

}else{

    echo "<span class='badge badge-green'>Rendah</span>";

}

?>

</td>

</tr>

</table>

</div>


<!-- =========================================================
NORMALISASI
========================================================= -->

<div class="card">

<h3>

<i class="fas fa-chart-line"></i>

Nilai Normalisasi

</h3>

<table class="detail-table">

<tr>

<td width="300"><b>Perceived Academic Workload</b></td>

<td><?= number_format($data['norm_paw'],2); ?></td>

</tr>

<tr>

<td><b>Time Management</b></td>

<td><?= number_format($data['norm_tm'],2); ?></td>

</tr>

<tr>

<td><b>Perceived Social Support</b></td>

<td><?= number_format($data['norm_pss'],2); ?></td>

</tr>

<tr>

<td><b>Financial Stress</b></td>

<td><?= number_format($data['norm_fs'],2); ?></td>

</tr>

<tr>

<td><b>Sleep Quality</b></td>

<td><?= number_format($data['norm_sq'],2); ?></td>

</tr>

</table>

</div>


<!-- =========================================================
PERBANDINGAN
========================================================= -->

<div class="card">

<h3>

<i class="fas fa-balance-scale"></i>

Perbandingan Manual dan Fuzzy

</h3>

<table class="detail-table">

<tr>

<td width="300"><b>Skor Manual</b></td>

<td><?= number_format($data['manual_score'],2); ?></td>

</tr>

<tr>

<td><b>Skor Fuzzy</b></td>

<td><?= number_format($data['fuzzy_score'],2); ?></td>

</tr>

<tr>

<td><b>Selisih (Error)</b></td>

<td><?= number_format($error,2); ?></td>

</tr>

<tr>

<td><b>Tingkat Akurasi</b></td>

<td>

<?php

if($error<=5){

echo "<span style='color:green;font-weight:bold;'>Sangat Akurat</span>";

}elseif($error<=10){

echo "<span style='color:orange;font-weight:bold;'>Cukup Akurat</span>";

}else{

echo "<span style='color:red;font-weight:bold;'>Kurang Akurat</span>";

}

?>

</td>

</tr>

</table>

</div>


<!-- =========================================================
TOMBOL
========================================================= -->

<div style="margin-top:30px;">

<a href="hasil.php">

<button class="btn detail">

<i class="fas fa-arrow-left"></i>

Kembali

</button>

</a>

</div>

</div>

</body>

</html>