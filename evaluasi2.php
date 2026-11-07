<?php

include 'koneksi/koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
|--------------------------------------------------------------------------
| 1. AMBIL DATA HASIL MANUAL DAN HASIL FUZZY
|--------------------------------------------------------------------------
*/

$query = "
    SELECT
        hp.data_id,
        hp.stress_category AS kategori_manual,
        hf.stress_category AS kategori_fuzzy
    FROM hasil_perhitungan hp
    INNER JOIN hasil_fuzzy hf
        ON hp.data_id = hf.data_id
";

$result = mysqli_query($conn, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}


/*
|--------------------------------------------------------------------------
| 2. INISIALISASI CONFUSION MATRIX
|--------------------------------------------------------------------------
*/

$categories = [
    'Rendah',
    'Sedang',
    'Tinggi'
];

$confusion_matrix = [];

foreach ($categories as $actual) {

    foreach ($categories as $predicted) {

        $confusion_matrix[$actual][$predicted] = 0;

    }

}


/*
|--------------------------------------------------------------------------
| 3. MEMBENTUK CONFUSION MATRIX
|--------------------------------------------------------------------------
*/

foreach ($data as $row) {

    $actual = trim($row['kategori_manual']);
    $predicted = trim($row['kategori_fuzzy']);

    if (
        isset($confusion_matrix[$actual]) &&
        isset($confusion_matrix[$actual][$predicted])
    ) {

        $confusion_matrix[$actual][$predicted]++;

    }

}


/*
|--------------------------------------------------------------------------
| 4. HITUNG TOTAL DATA
|--------------------------------------------------------------------------
*/

$total_data = count($data);


/*
|--------------------------------------------------------------------------
| 5. HITUNG TRUE POSITIVE
|--------------------------------------------------------------------------
*/

$TP = [];

foreach ($categories as $category) {

    $TP[$category] =
        $confusion_matrix[$category][$category];

}


/*
|--------------------------------------------------------------------------
| 6. HITUNG ACCURACY
|--------------------------------------------------------------------------
*/

$total_correct = 0;

foreach ($categories as $category) {

    $total_correct += $TP[$category];

}

$accuracy = ($total_data > 0)
    ? $total_correct / $total_data
    : 0;


/*
|--------------------------------------------------------------------------
| 7. HITUNG PRECISION, RECALL, DAN F1-SCORE
|--------------------------------------------------------------------------
*/

$precision = [];
$recall = [];
$f1_score = [];

foreach ($categories as $category) {

    /*
    |--------------------------------------------------------------
    | True Positive
    |--------------------------------------------------------------
    */

    $tp = $confusion_matrix[$category][$category];


    /*
    |--------------------------------------------------------------
    | False Positive
    |
    | Prediksi kategori benar tetapi sebenarnya kategori lain
    |--------------------------------------------------------------
    */

    $fp = 0;

    foreach ($categories as $actual) {

        if ($actual !== $category) {

            $fp +=
                $confusion_matrix[$actual][$category];

        }

    }


    /*
    |--------------------------------------------------------------
    | False Negative
    |
    | Data sebenarnya kategori tersebut tetapi diprediksi
    | sebagai kategori lain
    |--------------------------------------------------------------
    */

    $fn = 0;

    foreach ($categories as $predicted) {

        if ($predicted !== $category) {

            $fn +=
                $confusion_matrix[$category][$predicted];

        }

    }


    /*
    |--------------------------------------------------------------
    | Precision
    |--------------------------------------------------------------
    */

    if (($tp + $fp) > 0) {

        $precision[$category] =
            $tp / ($tp + $fp);

    } else {

        $precision[$category] = 0;

    }


    /*
    |--------------------------------------------------------------
    | Recall
    |--------------------------------------------------------------
    */

    if (($tp + $fn) > 0) {

        $recall[$category] =
            $tp / ($tp + $fn);

    } else {

        $recall[$category] = 0;

    }


    /*
    |--------------------------------------------------------------
    | F1-Score
    |--------------------------------------------------------------
    */

    if (($precision[$category] + $recall[$category]) > 0) {

        $f1_score[$category] =
            2 *
            (
                $precision[$category] *
                $recall[$category]
            )
            /
            (
                $precision[$category] +
                $recall[$category]
            );

    } else {

        $f1_score[$category] = 0;

    }

}


/*
|--------------------------------------------------------------------------
| 8. MACRO AVERAGE
|--------------------------------------------------------------------------
*/

$precision_macro =
    array_sum($precision) / count($categories);

$recall_macro =
    array_sum($recall) / count($categories);

$f1_macro =
    array_sum($f1_score) / count($categories);


/*
|--------------------------------------------------------------------------
| 9. TAMPILKAN HASIL
|--------------------------------------------------------------------------
*/

echo "<h2>Evaluasi Fuzzy Mamdani</h2>";

echo "<p>
Jumlah data pengujian:
<strong>{$total_data}</strong>
</p>";


/*
|--------------------------------------------------------------------------
| CONFUSION MATRIX
|--------------------------------------------------------------------------
*/

echo "<h3>Confusion Matrix</h3>";

echo "<table border='1' cellpadding='10' cellspacing='0'>";

echo "<tr>";

echo "<th>Manual \\ Fuzzy</th>";

foreach ($categories as $category) {

    echo "<th>{$category}</th>";

}

echo "</tr>";


foreach ($categories as $actual) {

    echo "<tr>";

    echo "<th>{$actual}</th>";

    foreach ($categories as $predicted) {

        echo "<td align='center'>";

        echo $confusion_matrix[$actual][$predicted];

        echo "</td>";

    }

    echo "</tr>";

}

echo "</table>";


/*
|--------------------------------------------------------------------------
| HASIL METRIK
|--------------------------------------------------------------------------
*/

echo "<h3>Hasil Evaluasi</h3>";

echo "<table border='1' cellpadding='10' cellspacing='0'>";

echo "<tr>";

echo "<th>Kategori</th>";
echo "<th>Precision</th>";
echo "<th>Recall</th>";
echo "<th>F1-Score</th>";

echo "</tr>";


foreach ($categories as $category) {

    echo "<tr>";

    echo "<td>{$category}</td>";

    echo "<td>" .
        number_format(
            $precision[$category] * 100,
            2
        )
        . "%</td>";

    echo "<td>" .
        number_format(
            $recall[$category] * 100,
            2
        )
        . "%</td>";

    echo "<td>" .
        number_format(
            $f1_score[$category] * 100,
            2
        )
        . "%</td>";

    echo "</tr>";

}


/*
|--------------------------------------------------------------------------
| MACRO AVERAGE
|--------------------------------------------------------------------------
*/

echo "<tr>";

echo "<th>Macro Average</th>";

echo "<th>" .
    number_format(
        $precision_macro * 100,
        2
    )
    . "%</th>";

echo "<th>" .
    number_format(
        $recall_macro * 100,
        2
    )
    . "%</th>";

echo "<th>" .
    number_format(
        $f1_macro * 100,
        2
    )
    . "%</th>";

echo "</tr>";

echo "</table>";


/*
|--------------------------------------------------------------------------
| ACCURACY
|--------------------------------------------------------------------------
*/

echo "<h3>Accuracy</h3>";

echo "<p style='font-size:20px;'>
<strong>" .
    number_format(
        $accuracy * 100,
        2
    )
    . "%</strong>
</p>";

?>