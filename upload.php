<?php

include 'koneksi/koneksi.php';
include 'fuzzy.php';

// Aktifkan error MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Validasi form
if (!isset($_POST['q1tga'])) {
    die('Form tidak terkirim dengan benar');
}

mysqli_begin_transaction($conn);

try {

    /* =========================================================
       1. AMBIL DATA FORM
       ========================================================= */

    // Data diri
    $nim = trim($_POST['nim'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $semester = (int) ($_POST['semester'] ?? 0);


    /* =========================================================
       PERCEIVED ACADEMIC WORKLOAD
       ========================================================= */

    $status_tga = (int) ($_POST['status_tga'] ?? 0);
    $proses_tga = (int) ($_POST['proses_tga'] ?? 0);

    $q1_tga = (int) ($_POST['q1tga'] ?? 0);
    $q2_tga = (int) ($_POST['q2tga'] ?? 0);
    $q3_tga = (int) ($_POST['q3tga'] ?? 0);
    $q4_tga = (int) ($_POST['q4tga'] ?? 0);
    $q5_tga = (int) ($_POST['q5tga'] ?? 0);


    /* =========================================================
       TIME MANAGEMENT
       ========================================================= */

    $sleep_time = (int) ($_POST['sleep_time'] ?? 0);

    $q1_time = (int) ($_POST['q1time'] ?? 0);
    $q2_time = (int) ($_POST['q2time'] ?? 0);
    $q3_time = (int) ($_POST['q3time'] ?? 0);
    $q4_time = (int) ($_POST['q4time'] ?? 0);
    $q5_time = (int) ($_POST['q5time'] ?? 0);

    // Reverse scoring item positif
    $q1_time = 6 - $q1_time;

    // q2_time adalah item negatif sehingga tidak dibalik

    $q3_time = 6 - $q3_time;
    $q4_time = 6 - $q4_time;
    $q5_time = 6 - $q5_time;


    /* =========================================================
       PERCEIVED SOCIAL SUPPORT
       ========================================================= */

    $q1_pcv = 6 - (int) ($_POST['q1pcv'] ?? 0);
    $q2_pcv = 6 - (int) ($_POST['q2pcv'] ?? 0);
    $q3_pcv = 6 - (int) ($_POST['q3pcv'] ?? 0);
    $q4_pcv = 6 - (int) ($_POST['q4pcv'] ?? 0);
    $q5_pcv = 6 - (int) ($_POST['q5pcv'] ?? 0);


    /* =========================================================
       FINANCIAL STRESS
       ========================================================= */

    $q1_fnc = (int) ($_POST['q1fnc'] ?? 0);
    $q2_fnc = (int) ($_POST['q2fnc'] ?? 0);
    $q3_fnc = (int) ($_POST['q3fnc'] ?? 0);
    $q4_fnc = (int) ($_POST['q4fnc'] ?? 0);
    $q5_fnc = (int) ($_POST['q5fnc'] ?? 0);


    /* =========================================================
       SLEEP QUALITY
       ========================================================= */

    $q1_sleep = (int) ($_POST['q1sleep'] ?? 0);
    $q2_sleep = (int) ($_POST['q2sleep'] ?? 0);

    // "Saya merasa tidur saya cukup" merupakan item positif
    $q3_sleep = 6 - (int) ($_POST['q3sleep'] ?? 0);

    $q4_sleep = (int) ($_POST['q4sleep'] ?? 0);
    $q5_sleep = (int) ($_POST['q5sleep'] ?? 0);


    /* =========================================================
       2. HITUNG SKOR TOTAL MASING-MASING VARIABEL
       ========================================================= */

    // Perceived Academic Workload
    $score_paw =
        $status_tga +
        $proses_tga +
        $q1_tga +
        $q2_tga +
        $q3_tga +
        $q4_tga +
        $q5_tga;


    // Time Management
    $score_tm =
        $sleep_time +
        $q1_time +
        $q2_time +
        $q3_time +
        $q4_time +
        $q5_time;


    // Perceived Social Support
    $score_pss =
        $q1_pcv +
        $q2_pcv +
        $q3_pcv +
        $q4_pcv +
        $q5_pcv;


    // Financial Stress
    $score_fs =
        $q1_fnc +
        $q2_fnc +
        $q3_fnc +
        $q4_fnc +
        $q5_fnc;


    // Sleep Quality
    $score_sq =
        $q1_sleep +
        $q2_sleep +
        $q3_sleep +
        $q4_sleep +
        $q5_sleep;


    /* =========================================================
       3. NORMALISASI MASING-MASING VARIABEL
       ========================================================= */

    /*
       Setiap variabel memiliki rentang skor yang berbeda.
       Oleh karena itu setiap skor diubah terlebih dahulu
       ke skala 0–100.
    */

    // PAW : 7–35
    $norm_paw = normalisasi(
        $score_paw,
        7,
        35
    );

    // Time Management : 6–30
    $norm_tm = normalisasi(
        $score_tm,
        6,
        30
    );

    // Perceived Social Support : 5–25
    $norm_pss = normalisasi(
        $score_pss,
        5,
        25
    );

    // Financial Stress : 5–25
    $norm_fs = normalisasi(
        $score_fs,
        5,
        25
    );

    // Sleep Quality : 5–25
    $norm_sq = normalisasi(
        $score_sq,
        5,
        25
    );


    /* =========================================================
       4. PERHITUNGAN SKOR MANUAL
       ========================================================= */

    /*
       Skor manual dihitung dari rata-rata lima skor
       yang telah dinormalisasi ke skala 0–100.
    */

    $manual_stress_score =
        (
            $norm_paw +
            $norm_tm +
            $norm_pss +
            $norm_fs +
            $norm_sq
        ) / 5;


    // Pastikan skor berada pada rentang 0–100
    $manual_stress_score = max(
        0,
        min(100, $manual_stress_score)
    );


    // Tentukan kategori stres
    $manual_stress_category = kategoriStress(
        $manual_stress_score
    );


    /* =========================================================
       5. PERHITUNGAN FUZZY MAMDANI
       ========================================================= */

    $fuzzy_stress_score = hitungStress(
        $score_paw,
        $score_tm,
        $score_pss,
        $score_fs,
        $score_sq
    );


    // Pastikan skor fuzzy berada pada rentang 0–100
    $fuzzy_stress_score = max(
        0,
        min(100, $fuzzy_stress_score)
    );


    // Tentukan kategori fuzzy
    $fuzzy_stress_category = kategoriStress(
        $fuzzy_stress_score
    );


    /* =========================================================
       6. SIMPAN DATA UTAMA
       ========================================================= */

    mysqli_query($conn, "INSERT INTO data
        (
            nim,
            nama,
            jurusan,
            semester
        )
        VALUES
        (
            '$nim',
            '$nama',
            '$jurusan',
            '$semester'
        )");

    $data_id = mysqli_insert_id($conn);


    /* =========================================================
       7. SIMPAN PERCEIVED ACADEMIC WORKLOAD
       ========================================================= */

    mysqli_query($conn, "INSERT INTO perceived
        (
            data_id,
            status_tga,
            proses_tga,
            q1_tga,
            q2_tga,
            q3_tga,
            q4_tga,
            q5_tga
        )
        VALUES
        (
            '$data_id',
            '$status_tga',
            '$proses_tga',
            '$q1_tga',
            '$q2_tga',
            '$q3_tga',
            '$q4_tga',
            '$q5_tga'
        )");


    /* =========================================================
       8. SIMPAN TIME MANAGEMENT
       ========================================================= */

    mysqli_query($conn, "INSERT INTO time_management
        (
            data_id,
            sleep_time,
            q1_time,
            q2_time,
            q3_time,
            q4_time,
            q5_time
        )
        VALUES
        (
            '$data_id',
            '$sleep_time',
            '$q1_time',
            '$q2_time',
            '$q3_time',
            '$q4_time',
            '$q5_time'
        )");


    /* =========================================================
       9. SIMPAN PERCEIVED SOCIAL SUPPORT
       ========================================================= */

    mysqli_query($conn, "INSERT INTO pcv
        (
            data_id,
            q1_pcv,
            q2_pcv,
            q3_pcv,
            q4_pcv,
            q5_pcv
        )
        VALUES
        (
            '$data_id',
            '$q1_pcv',
            '$q2_pcv',
            '$q3_pcv',
            '$q4_pcv',
            '$q5_pcv'
        )");


    /* =========================================================
       10. SIMPAN FINANCIAL STRESS
       ========================================================= */

    mysqli_query($conn, "INSERT INTO financial
        (
            data_id,
            q1_fnc,
            q2_fnc,
            q3_fnc,
            q4_fnc,
            q5_fnc
        )
        VALUES
        (
            '$data_id',
            '$q1_fnc',
            '$q2_fnc',
            '$q3_fnc',
            '$q4_fnc',
            '$q5_fnc'
        )");


    /* =========================================================
       11. SIMPAN SLEEP QUALITY
       ========================================================= */

    mysqli_query($conn, "INSERT INTO sleep
        (
            data_id,
            q1_sleep,
            q2_sleep,
            q3_sleep,
            q4_sleep,
            q5_sleep
        )
        VALUES
        (
            '$data_id',
            '$q1_sleep',
            '$q2_sleep',
            '$q3_sleep',
            '$q4_sleep',
            '$q5_sleep'
        )");


    /* =========================================================
       12. SIMPAN HASIL MANUAL
       ========================================================= */

    mysqli_query($conn, "INSERT INTO hasil_perhitungan
        (
            data_id,
            score_paw,
            score_tm,
            score_pss,
            score_fs,
            score_sq,
            stress_score,
            stress_category
        )
        VALUES
        (
            '$data_id',
            '$score_paw',
            '$score_tm',
            '$score_pss',
            '$score_fs',
            '$score_sq',
            '$manual_stress_score',
            '$manual_stress_category'
        )");


    /* =========================================================
       13. SIMPAN HASIL FUZZY
       ========================================================= */

    mysqli_query($conn, "INSERT INTO hasil_fuzzy
        (
            data_id,
            score_paw,
            score_tm,
            score_pss,
            score_fs,
            score_sq,
            norm_paw,
            norm_tm,
            norm_pss,
            norm_fs,
            norm_sq,
            stress_score,
            stress_category
        )
        VALUES
        (
            '$data_id',
            '$score_paw',
            '$score_tm',
            '$score_pss',
            '$score_fs',
            '$score_sq',
            '$norm_paw',
            '$norm_tm',
            '$norm_pss',
            '$norm_fs',
            '$norm_sq',
            '$fuzzy_stress_score',
            '$fuzzy_stress_category'
        )");


    /* =========================================================
       14. COMMIT TRANSACTION
       ========================================================= */

    mysqli_commit($conn);


    /* =========================================================
       15. REDIRECT KE HALAMAN HASIL
       ========================================================= */

    header("Location: hasil.php?id=" . $data_id);
    exit;


} catch (Exception $e) {

    mysqli_rollback($conn);

    echo '<h2>Gagal menyimpan data</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>