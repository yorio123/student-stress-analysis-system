<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login_page.php");
    exit;
}

include "koneksi/koneksi.php";

/* =========================================================
   TOTAL RESPONDEN
========================================================= */

$queryTotal = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM data
");

$total = mysqli_fetch_assoc($queryTotal);


/* =========================================================
   TOTAL STRES TINGGI
========================================================= */

$queryTinggi = mysqli_query($conn, "
    SELECT COUNT(*) AS tinggi
    FROM hasil_fuzzy
    WHERE stress_category='Tinggi'
");

$tinggi = mysqli_fetch_assoc($queryTinggi);


/* =========================================================
   TOTAL STRES SEDANG
========================================================= */

$querySedang = mysqli_query($conn, "
    SELECT COUNT(*) AS sedang
    FROM hasil_fuzzy
    WHERE stress_category='Sedang'
");

$sedang = mysqli_fetch_assoc($querySedang);


/* =========================================================
   TOTAL STRES RENDAH
========================================================= */

$queryRendah = mysqli_query($conn, "
    SELECT COUNT(*) AS rendah
    FROM hasil_fuzzy
    WHERE stress_category='Rendah'
");

$rendah = mysqli_fetch_assoc($queryRendah);


/* =========================================================
   NILAI RATA-RATA STRES
========================================================= */

$queryRata = mysqli_query($conn, "
    SELECT ROUND(AVG(stress_score),2) AS rata
    FROM hasil_fuzzy
");

$rata = mysqli_fetch_assoc($queryRata);


/* =========================================================
   10 DATA RESPONDEN TERBARU
========================================================= */

$queryData = mysqli_query($conn, "
SELECT
    d.data_id,
    d.nim,
    d.nama,
    d.jurusan,
    d.semester,
    h.stress_score,
    h.stress_category

FROM data d

INNER JOIN hasil_fuzzy h
ON d.data_id = h.data_id

ORDER BY d.data_id DESC

LIMIT 10
");


/* =========================================================
   DATA GRAFIK
========================================================= */

$queryGrafik = mysqli_query($conn,"
SELECT
stress_category,
COUNT(*) jumlah

FROM hasil_fuzzy

GROUP BY stress_category
");

$grafik = [];

while($row = mysqli_fetch_assoc($queryGrafik)){
    $grafik[$row['stress_category']] = $row['jumlah'];
}

$jumlahTinggi = $grafik['Tinggi'] ?? 0;
$jumlahSedang = $grafik['Sedang'] ?? 0;
$jumlahRendah = $grafik['Rendah'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Admin</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
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

/* Sidebar */

.sidebar{
width:250px;
height:100vh;
background:linear-gradient(180deg,#667eea,#764ba2);
position:fixed;
left:0;
top:0;
color:white;
padding:30px 20px;
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
text-decoration:none;
color:white;
display:block;
padding:12px;
border-radius:10px;
transition:.3s;
}

.sidebar ul li a:hover{
background:rgba(255,255,255,.2);
}

/* Main */

.main{
margin-left:250px;
width:calc(100% - 250px);
padding:30px;
}

/* Navbar */

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

/* Card */

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:30px;
}

.card{
background:white;
padding:25px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
display:flex;
justify-content:space-between;
align-items:center;
}

.card h4{
color:#666;
font-size:15px;
}

.card h2{
margin-top:10px;
}

.icon{
width:60px;
height:60px;
border-radius:15px;
display:flex;
justify-content:center;
align-items:center;
font-size:24px;
color:white;
}

.blue{background:#667eea;}
.red{background:#ff6b6b;}
.orange{background:#f39c12;}
.green{background:#2ecc71;}

/* Grafik */

.chart-box{
background:white;
padding:20px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
margin-bottom:30px;
}

/* Table */

.table-box{
background:white;
padding:20px;
border-radius:20px;
box-shadow:0 10px 20px rgba(0,0,0,.08);
}

table{
width:100%;
border-collapse:collapse;
margin-top:15px;
}

table th{
background:#667eea;
color:white;
padding:12px;
}

table td{
padding:12px;
border-bottom:1px solid #eee;
text-align:center;
}

.btn{
padding:7px 12px;
border:none;
border-radius:8px;
color:white;
cursor:pointer;
}

.detail{background:#3498db;}
.edit{background:#f39c12;}
.delete{background:#e74c3c;}

</style>

</head>

<body>

<div class="sidebar">

<h2><i class="fas fa-brain"></i><br>FuzzyStress</h2>

<ul>

<li><a href="dashboard.php" style="background:rgba(255,255,255,.2);"><i class="fas fa-chart-line"></i> Dashboard</a></li>

<li><a href="data.php"><i class="fas fa-users"></i> Data Mahasiswa</a></li>

<li><a href="hasil.php"><i class="fas fa-chart-pie"></i> Hasil Fuzzy</a></li>

<li><a href="evaluasi.php"><i class="fas fa-file"></i> Evaluasi</a></li>

<li><a href="process/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

</ul>

</div>

<div class="main">

<div class="navbar">

<h2>Dashboard Admin</h2>

<div>

<i class="fas fa-user-circle fa-2x"></i>

<?= $_SESSION['nama']; ?>

</div>

</div>

<div class="cards">

<div class="card">

<div>

<h4>Total Responden</h4>

<h2><?= $total['total']; ?></h2>

</div>

<div class="icon blue">

<i class="fas fa-users"></i>

</div>

</div>

<div class="card">

<div>

<h4>Stres Tinggi</h4>

<h2><?= $tinggi['tinggi']; ?></h2>

</div>

<div class="icon red">

<i class="fas fa-face-frown"></i>

</div>

</div>

<div class="card">

<div>

<h4>Stres Sedang</h4>

<h2><?= $sedang['sedang']; ?></h2>

</div>

<div class="icon orange">

<i class="fas fa-face-meh"></i>

</div>

</div>

<div class="card">

<div>

<h4>Stres Rendah</h4>

<h2><?= $rendah['rendah']; ?></h2>

</div>

<div class="icon green">

<i class="fas fa-face-smile"></i>

</div>

</div>

</div>

<div class="chart-box">

<h3>Grafik Tingkat Stres Mahasiswa</h3>

<canvas id="chart"></canvas>

</div>

<div class="table-box">

<h3>Data Responden Terbaru</h3>

<table>

<thead>

<tr>

<th>No</th>

<th>NIM</th>

<th>Nama</th>

<th>Jurusan</th>

<th>Skor Fuzzy</th>

<th>Status</th>

<th>Aksi</th>

</tr>

</thead>

<<tbody>

<?php
$no = 1;

while($row = mysqli_fetch_assoc($queryData)){
?>

<tr>

<td><?= $no++; ?></td>
<td><?= $row['nim']; ?></td>
<td><?= $row['nama']; ?></td>
<td><?= $row['jurusan']; ?></td>
<td><?= number_format($row['stress_score'],2); ?></td>

<td>
<?php
if($row['stress_category']=="Tinggi"){
    echo "<span style='color:red;font-weight:bold;'>Tinggi</span>";
}elseif($row['stress_category']=="Sedang"){
    echo "<span style='color:orange;font-weight:bold;'>Sedang</span>";
}else{
    echo "<span style='color:green;font-weight:bold;'>Rendah</span>";
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
   onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
    <button class="btn delete">
        <i class="fas fa-trash"></i>
    </button>
</a>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>

<script>

const ctx = document.getElementById('chart');

new Chart(ctx,{

type:'bar',

data:{

labels:['Tinggi','Sedang','Rendah'],

datasets:[{

label:'Jumlah Mahasiswa',

data:[
<?= $jumlahTinggi ?>,
<?= $jumlahSedang ?>,
<?= $jumlahRendah ?>
],

backgroundColor:[
'#ff6b6b',
'#f39c12',
'#2ecc71'
]

}]

}

});
</script>

</body>
</html>