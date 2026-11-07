<?php

include 'koneksi/koneksi.php';

/*
|--------------------------------------------------------------------------
| AMBIL DATA MANUAL VS FUZZY
|--------------------------------------------------------------------------
*/

$query = mysqli_query($conn, "
    SELECT 
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
");

/*
|--------------------------------------------------------------------------
| INISIALISASI
|--------------------------------------------------------------------------
*/

$total_data = 0;
$correct = 0;

$total_abs_error = 0;
$total_squared_error = 0;

/*
|--------------------------------------------------------------------------
| CONFUSION MATRIX
|
| BARIS    = KATEGORI MANUAL / REFERENSI
| KOLOM    = KATEGORI FUZZY / PREDIKSI
|--------------------------------------------------------------------------
*/

$categories = ['Rendah', 'Sedang', 'Tinggi'];

$matrix = [];

foreach ($categories as $actual) {
    foreach ($categories as $predicted) {
        $matrix[$actual][$predicted] = 0;
    }
}

/*
|--------------------------------------------------------------------------
| DISTRIBUSI KATEGORI
|--------------------------------------------------------------------------
*/

$manual_distribution = [
    'Rendah' => 0,
    'Sedang' => 0,
    'Tinggi' => 0
];

$fuzzy_distribution = [
    'Rendah' => 0,
    'Sedang' => 0,
    'Tinggi' => 0
];

/*
|--------------------------------------------------------------------------
| SIMPAN DATA UNTUK TABEL
|--------------------------------------------------------------------------
*/

$data_evaluasi = [];

/*
|--------------------------------------------------------------------------
| PROSES DATA
|--------------------------------------------------------------------------
*/

while ($row = mysqli_fetch_assoc($query)) {

    $manual_score = (float) $row['manual_score'];
    $fuzzy_score  = (float) $row['fuzzy_score'];

    $manual_category = trim($row['manual_category']);
    $fuzzy_category  = trim($row['fuzzy_category']);

    /*
    |--------------------------------------------------------------------------
    | ACCURACY
    |--------------------------------------------------------------------------
    */

    if ($manual_category === $fuzzy_category) {
        $correct++;
    }

    /*
    |--------------------------------------------------------------------------
    | MAE
    |--------------------------------------------------------------------------
    */

    $abs_error = abs($manual_score - $fuzzy_score);

    $total_abs_error += $abs_error;

    /*
    |--------------------------------------------------------------------------
    | RMSE
    |--------------------------------------------------------------------------
    */

    $squared_error = pow(($manual_score - $fuzzy_score), 2);

    $total_squared_error += $squared_error;

    /*
    |--------------------------------------------------------------------------
    | CONFUSION MATRIX
    |--------------------------------------------------------------------------
    */

    if (
        isset($matrix[$manual_category]) &&
        isset($matrix[$manual_category][$fuzzy_category])
    ) {
        $matrix[$manual_category][$fuzzy_category]++;
    }

    /*
    |--------------------------------------------------------------------------
    | DISTRIBUSI KATEGORI
    |--------------------------------------------------------------------------
    */

    if (isset($manual_distribution[$manual_category])) {
        $manual_distribution[$manual_category]++;
    }

    if (isset($fuzzy_distribution[$fuzzy_category])) {
        $fuzzy_distribution[$fuzzy_category]++;
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA
    |--------------------------------------------------------------------------
    */

    $data_evaluasi[] = [
        'nama' => $row['nama'],
        'manual_score' => $manual_score,
        'fuzzy_score' => $fuzzy_score,
        'manual_category' => $manual_category,
        'fuzzy_category' => $fuzzy_category,
        'error' => $abs_error
    ];

    $total_data++;
}

/*
|--------------------------------------------------------------------------
| PERHITUNGAN METRIK
|--------------------------------------------------------------------------
*/

if ($total_data > 0) {

    $accuracy = ($correct / $total_data) * 100;

    $mae = $total_abs_error / $total_data;

    $rmse = sqrt($total_squared_error / $total_data);

} else {

    $accuracy = 0;
    $mae = 0;
    $rmse = 0;
}

/*
|--------------------------------------------------------------------------
| PRECISION, RECALL, F1-SCORE
|--------------------------------------------------------------------------
*/

$metrics = [];

$total_tp_all = 0;

foreach ($categories as $category) {

    $tp = $matrix[$category][$category];

    $fp = 0;
    $fn = 0;

    /*
    |--------------------------------------------------------------------------
    | FALSE POSITIVE
    |--------------------------------------------------------------------------
    */

    foreach ($categories as $actual) {

        if ($actual !== $category) {
            $fp += $matrix[$actual][$category];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FALSE NEGATIVE
    |--------------------------------------------------------------------------
    */

    foreach ($categories as $predicted) {

        if ($predicted !== $category) {
            $fn += $matrix[$category][$predicted];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PRECISION
    |--------------------------------------------------------------------------
    */

    if (($tp + $fp) > 0) {
        $precision = $tp / ($tp + $fp);
    } else {
        $precision = 0;
    }

    /*
    |--------------------------------------------------------------------------
    | RECALL
    |--------------------------------------------------------------------------
    */

    if (($tp + $fn) > 0) {
        $recall = $tp / ($tp + $fn);
    } else {
        $recall = 0;
    }

    /*
    |--------------------------------------------------------------------------
    | F1 SCORE
    |--------------------------------------------------------------------------
    */

    if (($precision + $recall) > 0) {
        $f1 = 2 * ($precision * $recall) / ($precision + $recall);
    } else {
        $f1 = 0;
    }

    $metrics[$category] = [
        'tp' => $tp,
        'fp' => $fp,
        'fn' => $fn,
        'precision' => $precision,
        'recall' => $recall,
        'f1' => $f1
    ];

    $total_tp_all += $tp;
}

/*
|--------------------------------------------------------------------------
| MACRO AVERAGE
|--------------------------------------------------------------------------
*/

$macro_precision = 0;
$macro_recall = 0;
$macro_f1 = 0;

foreach ($categories as $category) {

    $macro_precision += $metrics[$category]['precision'];
    $macro_recall += $metrics[$category]['recall'];
    $macro_f1 += $metrics[$category]['f1'];
}

$macro_precision /= count($categories);
$macro_recall /= count($categories);
$macro_f1 /= count($categories);

/*
|--------------------------------------------------------------------------
| WEIGHTED AVERAGE
|--------------------------------------------------------------------------
*/

$weighted_precision = 0;
$weighted_recall = 0;
$weighted_f1 = 0;

foreach ($categories as $category) {

    $support = $manual_distribution[$category];

    $weighted_precision +=
        $metrics[$category]['precision'] * $support;

    $weighted_recall +=
        $metrics[$category]['recall'] * $support;

    $weighted_f1 +=
        $metrics[$category]['f1'] * $support;
}

if ($total_data > 0) {

    $weighted_precision /= $total_data;
    $weighted_recall /= $total_data;
    $weighted_f1 /= $total_data;

} else {

    $weighted_precision = 0;
    $weighted_recall = 0;
    $weighted_f1 = 0;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Evaluasi Sistem Fuzzy Mamdani</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #333;
        }

        h2 {
            margin-top: 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        .metric-box {
            display: inline-block;
            padding: 20px;
            margin: 10px;
            min-width: 150px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            margin-top: 8px;
        }

    </style>

</head>

<body>

<h1>Evaluasi Sistem Fuzzy Mamdani</h1>

<!-- =========================================================
     RINGKASAN
========================================================= -->

<h2>1. Ringkasan Hasil Evaluasi</h2>

<div class="metric-box">

    <div>Total Data</div>

    <div class="metric-value">
        <?= $total_data ?>
    </div>

</div>

<div class="metric-box">

    <div>Accuracy</div>

    <div class="metric-value">
        <?= round($accuracy, 2) ?>%
    </div>

</div>

<div class="metric-box">

    <div>MAE</div>

    <div class="metric-value">
        <?= round($mae, 2) ?>
    </div>

</div>

<div class="metric-box">

    <div>RMSE</div>

    <div class="metric-value">
        <?= round($rmse, 2) ?>
    </div>

</div>


<!-- =========================================================
     DETAIL DATA
========================================================= -->

<h2>2. Perbandingan Hasil Manual dan Fuzzy</h2>

<table>

    <tr>

        <th>No</th>
        <th>Nama</th>

        <th>Manual Score</th>
        <th>Fuzzy Score</th>

        <th>Manual Category</th>
        <th>Fuzzy Category</th>

        <th>Error</th>

    </tr>

    <?php foreach ($data_evaluasi as $index => $data): ?>

    <tr>

        <td>
            <?= $index + 1 ?>
        </td>

        <td>
            <?= htmlspecialchars($data['nama']) ?>
        </td>

        <td>
            <?= round($data['manual_score'], 2) ?>
        </td>

        <td>
            <?= round($data['fuzzy_score'], 2) ?>
        </td>

        <td>
            <?= htmlspecialchars($data['manual_category']) ?>
        </td>

        <td>
            <?= htmlspecialchars($data['fuzzy_category']) ?>
        </td>

        <td>
            <?= round($data['error'], 2) ?>
        </td>

    </tr>

    <?php endforeach; ?>

</table>


<!-- =========================================================
     CONFUSION MATRIX
========================================================= -->

<h2>3. Confusion Matrix</h2>

<table>

    <tr>

        <th>Manual \ Fuzzy</th>

        <th>Rendah</th>
        <th>Sedang</th>
        <th>Tinggi</th>

    </tr>

    <?php foreach ($categories as $actual): ?>

    <tr>

        <th>
            <?= $actual ?>
        </th>

        <?php foreach ($categories as $predicted): ?>

        <td>
            <?= $matrix[$actual][$predicted] ?>
        </td>

        <?php endforeach; ?>

    </tr>

    <?php endforeach; ?>

</table>


<!-- =========================================================
     PRECISION RECALL F1
========================================================= -->

<h2>4. Precision, Recall, dan F1-Score</h2>

<table>

    <tr>

        <th>Kategori</th>
        <th>TP</th>
        <th>FP</th>
        <th>FN</th>

        <th>Precision</th>
        <th>Recall</th>
        <th>F1-Score</th>

    </tr>

    <?php foreach ($categories as $category): ?>

    <tr>

        <td>
            <?= $category ?>
        </td>

        <td>
            <?= $metrics[$category]['tp'] ?>
        </td>

        <td>
            <?= $metrics[$category]['fp'] ?>
        </td>

        <td>
            <?= $metrics[$category]['fn'] ?>
        </td>

        <td>
            <?= round($metrics[$category]['precision'] * 100, 2) ?>%
        </td>

        <td>
            <?= round($metrics[$category]['recall'] * 100, 2) ?>%
        </td>

        <td>
            <?= round($metrics[$category]['f1'] * 100, 2) ?>%
        </td>

    </tr>

    <?php endforeach; ?>

    <tr>

        <th>Macro Average</th>

        <td colspan="3">-</td>

        <td>
            <?= round($macro_precision * 100, 2) ?>%
        </td>

        <td>
            <?= round($macro_recall * 100, 2) ?>%
        </td>

        <td>
            <?= round($macro_f1 * 100, 2) ?>%
        </td>

    </tr>

    <tr>

        <th>Weighted Average</th>

        <td colspan="3">-</td>

        <td>
            <?= round($weighted_precision * 100, 2) ?>%
        </td>

        <td>
            <?= round($weighted_recall * 100, 2) ?>%
        </td>

        <td>
            <?= round($weighted_f1 * 100, 2) ?>%
        </td>

    </tr>

</table>


<!-- =========================================================
     DISTRIBUSI
========================================================= -->

<h2>5. Distribusi Kategori</h2>

<table>

    <tr>

        <th>Kategori</th>

        <th>Manual</th>
        <th>Fuzzy</th>

    </tr>

    <?php foreach ($categories as $category): ?>

    <tr>

        <td>
            <?= $category ?>
        </td>

        <td>
            <?= $manual_distribution[$category] ?>

            (<?= $total_data > 0
                ? round(($manual_distribution[$category] / $total_data) * 100, 2)
                : 0 ?>%)
        </td>

        <td>
            <?= $fuzzy_distribution[$category] ?>

            (<?= $total_data > 0
                ? round(($fuzzy_distribution[$category] / $total_data) * 100, 2)
                : 0 ?>%)
        </td>

    </tr>

    <?php endforeach; ?>

</table>

</body>

</html>