<?php

include 'koneksi/koneksi.php';
include 'fuzzy.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
|--------------------------------------------------------------------------
| IMPORT CSV
|--------------------------------------------------------------------------
| FORMAT CSV:
|
| nim,nama,jurusan,semester,status_tga,proses_tga,q1tga,q2tga,...
|
| Disarankan export dari Excel -> CSV UTF-8
|--------------------------------------------------------------------------
*/

if (!isset($_FILES['file'])) {
    die("File belum dipilih");
}

$file = $_FILES['file']['tmp_name'];

if (($handle = fopen($file, "r")) === FALSE) {
    die("Gagal membuka file CSV");
}

mysqli_begin_transaction($conn);

try {

    /*
    |--------------------------------------------------------------------------
    | SKIP HEADER
    |--------------------------------------------------------------------------
    */

    $header = fgetcsv($handle, 1000, ",");

    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {

        /*
        |--------------------------------------------------------------------------
        | VALIDASI JUMLAH KOLOM
        |--------------------------------------------------------------------------
        */

        if (count($row) < 32) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | 1. DATA DIRI
        |--------------------------------------------------------------------------
        */

        $nim = trim($row[0]);
        $nama = trim($row[1]);
        $jurusan = trim($row[2]);
        $semester = (int)$row[3];

        /*
        |--------------------------------------------------------------------------
        | 2. PERCEIVED ACADEMIC WORKLOAD
        |--------------------------------------------------------------------------
        */

        $status_tga = (int)$row[4];
        $proses_tga = (int)$row[5];

        $q1_tga = (int)$row[6];
        $q2_tga = (int)$row[7];
        $q3_tga = (int)$row[8];
        $q4_tga = (int)$row[9];
        $q5_tga = (int)$row[10];

        /*
        |--------------------------------------------------------------------------
        | 3. TIME MANAGEMENT
        |--------------------------------------------------------------------------
        */

        $sleep_time = (int)$row[11];

        $q1_time = (int)$row[12];
        $q2_time = (int)$row[13];
        $q3_time = (int)$row[14];
        $q4_time = (int)$row[15];
        $q5_time = (int)$row[16];

        // Reverse scoring
        $q1_time = 6 - $q1_time;
        $q3_time = 6 - $q3_time;
        $q4_time = 6 - $q4_time;
        $q5_time = 6 - $q5_time;

        /*
        |--------------------------------------------------------------------------
        | 4. PERCEIVED SOCIAL SUPPORT
        |--------------------------------------------------------------------------
        */

        $q1_pcv = 6 - (int)$row[17];
        $q2_pcv = 6 - (int)$row[18];
        $q3_pcv = 6 - (int)$row[19];
        $q4_pcv = 6 - (int)$row[20];
        $q5_pcv = 6 - (int)$row[21];

        /*
        |--------------------------------------------------------------------------
        | 5. FINANCIAL STRESS
        |--------------------------------------------------------------------------
        */

        $q1_fnc = (int)$row[22];
        $q2_fnc = (int)$row[23];
        $q3_fnc = (int)$row[24];
        $q4_fnc = (int)$row[25];
        $q5_fnc = (int)$row[26];

        /*
        |--------------------------------------------------------------------------
        | 6. SLEEP QUALITY
        |--------------------------------------------------------------------------
        */

        $q1_sleep = (int)$row[27];
        $q2_sleep = (int)$row[28];
        $q3_sleep = 6 - (int)$row[29];
        $q4_sleep = (int)$row[30];
        $q5_sleep = (int)$row[31];

        /*
        |--------------------------------------------------------------------------
        | 7. HITUNG SKOR TOTAL
        |--------------------------------------------------------------------------
        */

        $score_paw =
            $status_tga +
            $proses_tga +
            $q1_tga +
            $q2_tga +
            $q3_tga +
            $q4_tga +
            $q5_tga;

        $score_tm =
            $sleep_time +
            $q1_time +
            $q2_time +
            $q3_time +
            $q4_time +
            $q5_time;

        $score_pss =
            $q1_pcv +
            $q2_pcv +
            $q3_pcv +
            $q4_pcv +
            $q5_pcv;

        $score_fs =
            $q1_fnc +
            $q2_fnc +
            $q3_fnc +
            $q4_fnc +
            $q5_fnc;

        $score_sq =
            $q1_sleep +
            $q2_sleep +
            $q3_sleep +
            $q4_sleep +
            $q5_sleep;

        /*
        |--------------------------------------------------------------------------
        | 8. PERHITUNGAN MANUAL
        |--------------------------------------------------------------------------
        */

        $total_score =
            $score_paw +
            $score_tm +
            $score_pss +
            $score_fs +
            $score_sq;

        $manual_stress_score = ($total_score / 140) * 100;

        $manual_stress_category =
            kategoriStress($manual_stress_score);

        /*
        |--------------------------------------------------------------------------
        | 9. PERHITUNGAN FUZZY
        |--------------------------------------------------------------------------
        */

        $fuzzy_stress_score = hitungStress(
            $score_paw,
            $score_tm,
            $score_pss,
            $score_fs,
            $score_sq
        );

        $fuzzy_stress_category =
            kategoriStress($fuzzy_stress_score);

        /*
        |--------------------------------------------------------------------------
        | 10. NORMALISASI
        |--------------------------------------------------------------------------
        */

        $norm_paw = normalisasi($score_paw, 7, 35);
        $norm_tm  = normalisasi($score_tm, 6, 30);
        $norm_pss = normalisasi($score_pss, 5, 25);
        $norm_fs  = normalisasi($score_fs, 5, 25);
        $norm_sq  = normalisasi($score_sq, 5, 25);

        /*
        |--------------------------------------------------------------------------
        | 11. SIMPAN KE TABEL data
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO data
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
            )
        ");

        $data_id = mysqli_insert_id($conn);

        /*
        |--------------------------------------------------------------------------
        | 12. SIMPAN perceived
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO perceived
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
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 13. SIMPAN time_management
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO time_management
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
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 14. SIMPAN pcv
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO pcv
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
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 15. SIMPAN financial
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO financial
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
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 16. SIMPAN sleep
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO sleep
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
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 17. SIMPAN HASIL MANUAL
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO hasil_perhitungan
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
            )
        ");

        /*
        |--------------------------------------------------------------------------
        | 18. SIMPAN HASIL FUZZY
        |--------------------------------------------------------------------------
        */

        mysqli_query($conn, "
            INSERT INTO hasil_fuzzy
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
            )
        ");
    }

    mysqli_commit($conn);

    echo "<h2>Import CSV berhasil!</h2>";

} catch (Exception $e) {

    mysqli_rollback($conn);

    echo "Gagal import: " . $e->getMessage();
}

fclose($handle);

?> 