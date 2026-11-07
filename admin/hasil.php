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
   FILTER
========================================================= */

$keyword = "";

if (isset($_GET['keyword'])) {
    $keyword = trim(
        mysqli_real_escape_string(
            $conn,
            $_GET['keyword']
        )
    );
}

$status = "";

if (isset($_GET['status'])) {
    $status = trim(
        mysqli_real_escape_string(
            $conn,
            $_GET['status']
        )
    );
}


/* =========================================================
   QUERY TOTAL DATA
========================================================= */

$sqlCount = "

SELECT COUNT(*) AS total

FROM data d

INNER JOIN hasil_perhitungan hp
ON d.data_id = hp.data_id

INNER JOIN hasil_fuzzy hf
ON d.data_id = hf.data_id

WHERE 1=1

";

if ($keyword != "") {

    $sqlCount .= "

    AND

    (
        d.nama LIKE '%$keyword%'
        OR
        d.nim LIKE '%$keyword%'
    )

    ";
}

if ($status != "") {

    $sqlCount .= "

    AND

    hf.stress_category='$status'

    ";
}

$resultCount = mysqli_query($conn, $sqlCount);

$totalData = mysqli_fetch_assoc(
    $resultCount
)['total'];

$totalHalaman = ceil(
    $totalData / $batas
);


/* =========================================================
   QUERY DATA
========================================================= */

$sql = "

SELECT

d.data_id,

d.nim,
d.nama,
d.jurusan,
d.semester,

hp.stress_score AS manual_score,
hp.stress_category AS manual_category,

hf.stress_score AS fuzzy_score,
hf.stress_category AS fuzzy_category

FROM data d

INNER JOIN hasil_perhitungan hp
ON d.data_id = hp.data_id

INNER JOIN hasil_fuzzy hf
ON d.data_id = hf.data_id

WHERE 1=1

";

if ($keyword != "") {

    $sql .= "

    AND

    (
        d.nama LIKE '%$keyword%'
        OR
        d.nim LIKE '%$keyword%'
    )

    ";
}

if ($status != "") {

    $sql .= "

    AND

    hf.stress_category='$status'

    ";
}

$sql .= "

ORDER BY d.data_id ASC

LIMIT $mulai,$batas

";

$query = mysqli_query($conn, $sql);


/* =========================================================
   CARD RINGKASAN
========================================================= */

$totalPerhitungan = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total FROM hasil_fuzzy"
    )
);

$totalTinggi = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total
         FROM hasil_fuzzy
         WHERE stress_category='Tinggi'"
    )
);

$totalSedang = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total
         FROM hasil_fuzzy
         WHERE stress_category='Sedang'"
    )
);

$totalRendah = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) total
         FROM hasil_fuzzy
         WHERE stress_category='Rendah'"
    )
);

?>
<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Hasil Fuzzy</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
rel="stylesheet">

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

.main{
margin-left:250px;
width:calc(100% - 250px);
padding:30px;
}

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

.cards{
display:grid;
grid-template-columns:
repeat(auto-fit,minmax(220px,1fr));
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
color:#666;
font-size:14px;
}

.card h2{
margin-top:10px;
}

.search-box{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:20px;
}
/* ==========================================
TABLE
========================================== */

.table-box{
background:white;
padding:20px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
overflow-x:auto;
}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

table th{
background:#667eea;
color:white;
padding:15px;
font-size:14px;
font-weight:600;
text-align:center;
}

table td{
padding:14px;
border-bottom:1px solid #eee;
text-align:center;
font-size:14px;
}

table tbody tr{
transition:.3s;
}

table tbody tr:hover{
background:#f7f9ff;
}

/* ==========================================
BADGE
========================================== */

.badge{
padding:6px 12px;
border-radius:20px;
color:white;
font-size:13px;
font-weight:600;
display:inline-block;
min-width:75px;
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

/* ==========================================
BUTTON
========================================== */

.btn{
width:38px;
height:38px;
border:none;
border-radius:8px;
cursor:pointer;
color:white;
transition:.3s;
}

.btn:hover{
transform:scale(1.05);
}

.detail{
background:#3498db;
}

.detail:hover{
background:#2980b9;
}

/* ==========================================
SEARCH
========================================== */

.search-box form{
display:flex;
gap:10px;
flex-wrap:wrap;
}

.search-box input,
.search-box select{

padding:10px 15px;
border:1px solid #ddd;
border-radius:8px;
font-size:14px;

}

.search-box input{

flex:1;

}

.search-box button{

padding:10px 18px;
border:none;
background:#667eea;
color:white;
border-radius:8px;
cursor:pointer;

}

.search-box button:hover{

background:#5569d8;

}

.search-box a{

padding:10px 18px;
background:#95a5a6;
color:white;
text-decoration:none;
border-radius:8px;

}

.search-box a:hover{

background:#7f8c8d;

}

/* ==========================================
PAGINATION
========================================== */

.pagination{

margin-top:25px;
display:flex;
justify-content:center;
gap:8px;
flex-wrap:wrap;

}

.pagination a{

text-decoration:none;

}

.page{

width:40px;
height:40px;
display:flex;
justify-content:center;
align-items:center;
border-radius:8px;
background:#667eea;
color:white;
font-weight:bold;
transition:.3s;

}

.page:hover{

background:#5569d8;

}

.active-page{

background:#f39c12;

}

/* ==========================================
RESPONSIVE
========================================== */

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
<i class="fas fa-file"></i>
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

<h2>Hasil Perhitungan Fuzzy</h2>

<div>

<i class="fas fa-user-circle fa-2x"></i>

<?= $_SESSION['nama']; ?>

</div>

</div>

<div class="cards">

<div class="card">
<h4>Total Perhitungan</h4>
<h2><?= $totalPerhitungan['total']; ?></h2>
</div>

<div class="card">
<h4>Stres Tinggi</h4>
<h2><?= $totalTinggi['total']; ?></h2>
</div>

<div class="card">
<h4>Stres Sedang</h4>
<h2><?= $totalSedang['total']; ?></h2>
</div>

<div class="card">
<h4>Stres Rendah</h4>
<h2><?= $totalRendah['total']; ?></h2>
</div>

</div>

<div class="search-box">

<form method="GET">

<input
type="text"
name="keyword"
placeholder="Cari NIM atau Nama"
value="<?= htmlspecialchars($keyword); ?>">

<select name="status">

<option value="">Semua Status</option>

<option value="Tinggi"
<?= ($status=="Tinggi") ? "selected" : ""; ?>>
Tinggi
</option>

<option value="Sedang"
<?= ($status=="Sedang") ? "selected" : ""; ?>>
Sedang
</option>

<option value="Rendah"
<?= ($status=="Rendah") ? "selected" : ""; ?>>
Rendah
</option>

</select>

<button type="submit">
Cari
</button>

<a href="hasil.php">
Reset
</a>

</form>

</div>

<div class="table-box">
<p style="margin-bottom:15px;">

<?php

$jumlahDataHalaman = mysqli_num_rows($query);

$awal = ($totalData == 0) ? 0 : $mulai + 1;
$akhir = min($mulai + $jumlahDataHalaman, $totalData);

?>

Menampilkan

<b><?= $awal; ?></b>

-

<b><?= $akhir; ?></b>

dari

<b><?= $totalData; ?></b>

hasil perhitungan.

</p>

<table>

<thead>

<tr>

<th>No</th>

<th>NIM</th>

<th>Nama</th>

<th>Jurusan</th>

<th>Manual</th>

<th>Kategori Manual</th>

<th>Fuzzy</th>

<th>Kategori Fuzzy</th>

<th>Selisih</th>

<th>Aksi</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($query)>0){

$no = $mulai + 1;

while($row = mysqli_fetch_assoc($query)){

$error = abs(
$row['manual_score'] -
$row['fuzzy_score']
);

?>

<tr>

<td><?= $no++; ?></td>

<td><?= htmlspecialchars($row['nim']); ?></td>

<td><?= htmlspecialchars($row['nama']); ?></td>

<td><?= htmlspecialchars($row['jurusan']); ?></td>

<td><?= number_format($row['manual_score'],2); ?></td>

<td>

<?php

if($row['manual_category']=="Tinggi"){

?>

<span class="badge badge-red">

Tinggi

</span>

<?php

}elseif($row['manual_category']=="Sedang"){

?>

<span class="badge badge-orange">

Sedang

</span>

<?php

}else{

?>

<span class="badge badge-green">

Rendah

</span>

<?php

}

?>

</td>

<td>

<?= number_format($row['fuzzy_score'],2); ?>

</td>

<td>

<?php

if($row['fuzzy_category']=="Tinggi"){

?>

<span class="badge badge-red">

Tinggi

</span>

<?php

}elseif($row['fuzzy_category']=="Sedang"){

?>

<span class="badge badge-orange">

Sedang

</span>

<?php

}else{

?>

<span class="badge badge-green">

Rendah

</span>

<?php

}

?>

</td>

<td>

<b>

<?= number_format($error,2); ?>

</b>

</td>

<td>

<a href="detail_hasil.php?id=<?= $row['data_id']; ?>">

<button class="btn detail">

<i class="fas fa-eye"></i>

</button>


</a>

</td>

</tr>

<?php

}

}else{

?>

<tr>

<td colspan="10">

<div style="padding:40px;">

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
<div class="pagination">

<?php if($halaman > 1){ ?>

<a href="?keyword=<?= urlencode($keyword); ?>&status=<?= urlencode($status); ?>&halaman=<?= $halaman-1; ?>">

<div class="page">

<i class="fas fa-angle-left"></i>

</div>

</a>

<?php } ?>


<?php

for($i=1;$i<=$totalHalaman;$i++){

?>

<a href="?keyword=<?= urlencode($keyword); ?>&status=<?= urlencode($status); ?>&halaman=<?= $i; ?>">

<div class="page <?= ($i==$halaman)?'active-page':''; ?>">

<?= $i; ?>

</div>

</a>

<?php

}

?>


<?php if($halaman < $totalHalaman){ ?>

<a href="?keyword=<?= urlencode($keyword); ?>&status=<?= urlencode($status); ?>&halaman=<?= $halaman+1; ?>">

<div class="page">

<i class="fas fa-angle-right"></i>

</div>

</a>

<?php } ?>

</div>

</div>