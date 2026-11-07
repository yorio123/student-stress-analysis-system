<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbandingan Analisis Skor Manual vs     Skor Fuzzy Logic</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .comparison-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }

        .manual-card::before {
            background: linear-gradient(90deg, #ff6b6b, #ffa726);
        }

        .fuzzy-card::before {
            background: linear-gradient(90deg, #4ecdc4, #45b7d1);
        }

        .card-title {
            font-size: 1.8em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .manual-card .card-title { color: #ff6b6b; }
        .fuzzy-card .card-title { color: #4ecdc4; }

        .result-display {
            background: linear-gradient(145deg, #f0f0f0, #e6e6e6);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 2.5em;
            font-weight: bold;
            position: relative;
        }

        .manual-card .result-display { 
            background: linear-gradient(145deg, #fff3f3, #ffe6e6);
            color: #ff6b6b;
        }

        .fuzzy-card .result-display { 
            background: linear-gradient(145deg, #e6fffe, #d4f8f7);
            color: #4ecdc4;
        }

        .steps {
            list-style: none;
        }

        .steps li {
            padding: 12px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .steps li:last-child { border-bottom: none; }

        .step-label {
            font-weight: 500;
            color: #555;
        }

        .step-value {
            font-weight: bold;
            color: #333;
            background: rgba(102, 126, 234, 0.1);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.95em;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 20px;
        }

        .difference-card {
            grid-column: 1 / -1;
            background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .difference-value {
            font-size: 3.5em;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .difference-label {
            font-size: 1.3em;
            color: #666;
            margin-bottom: 20px;
        }

        .btn-calculate {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 20px auto;
            display: block;
        }

        .btn-calculate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 768px) {
            .comparison-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .result-display {
                font-size: 2em;
            }
        }

        .accuracy-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .manual-card .accuracy-badge { background: #ff9800; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-calculator"></i> Perbandingan Perhitungan</h1>
            <p>Manual vs Fuzzy Logic - Visualisasi Hasil Akurat</p>
        </div>

        <div style="text-align: center; margin-bottom: 30px;">
            <button class="btn-calculate">
                <i class="fas fa-play"></i> 
                <a href="index.php">Hitung Ulang</a>
            </button>
        </div>

        <?php
            include 'koneksi/koneksi.php';

            // Ambil data_id dari URL
            $data_id = (int) ($_GET['id'] ?? 0);

            // Query ke tabel hasil_perhitungan
            $queryManual = mysqli_query($conn, "
                SELECT *
                FROM hasil_perhitungan
                WHERE data_id = '$data_id'
            ");

            // Ubah hasil query menjadi array associative
            $manual = mysqli_fetch_assoc($queryManual);

            // Jika data tidak ditemukan
            if (!$manual) {
                die('Data hasil perhitungan manual tidak ditemukan.');
            }

            // Query ke tabel hasil_perhitungan
            $queryfuzzy = mysqli_query($conn, "
                SELECT *
                FROM hasil_fuzzy
                WHERE data_id = '$data_id'
            ");

            // Ubah hasil query menjadi array associative
            $fuzzy = mysqli_fetch_assoc($queryfuzzy);

            // Jika data tidak ditemukan
            if (!$fuzzy) {
                die('Data hasil perhitungan manual tidak ditemukan.');
            }

            ?>
        <div class="comparison-container">
            <div class="card manual-card">
                <div class="card-title">
                    <i class="fas fa-hand-pointer"></i>
                    Skor Manual
                </div>
                <div class="result-display" id="manualResult">0.00</div>
                <ul class="steps" id="manualSteps">
                    <li><span class="step-label">Perceived Academic Workload</span><span class="step-value"><?= htmlspecialchars($manual['score_paw']); ?></span></li>
                    <li><span class="step-label">Time Management</span><span class="step-value"><?= htmlspecialchars($manual['score_tm']); ?></span></li>
                    <li><span class="step-label">Perceived Social Support</span><span class="step-value"><?= htmlspecialchars($manual['score_pss']); ?></span></li>
                    <li><span class="step-label">Financial Stress</span><span class="step-value"><?= htmlspecialchars($manual['score_fs']); ?></span></li>
                    <li><span class="step-label">Sleep Quality</span><span class="step-value"><?= htmlspecialchars($manual['score_sq']); ?></span></li>
                    <li><span class="step-label">Hasil Akhir (Nilai AKhir/Nilai Maksimum * 100)</span><span class="step-value"><?= htmlspecialchars($manual['stress_score']); ?></span></li>
                </ul>
                <div class="chart-container">
                    <canvas id="manualChart"></canvas>
                </div>
            </div>

            <div class="card fuzzy-card">
                <div class="card-title">
                    <i class="fas fa-brain"></i>
                    Skor Fuzzy Logic
                </div>
                
                <div class="result-display" id="fuzzyResult">0.00</div>
                <ul class="steps" id="fuzzySteps">
                    <li><span class="step-label">Normalisasi Preceived Academic Workload</span><span class="step-value"><?= htmlspecialchars($fuzzy['norm_paw']); ?></span></li>
                    <li><span class="step-label">Normalisasi Time Management</span><span class="step-value"><?= htmlspecialchars($fuzzy['norm_tm']); ?></span></li>
                    <li><span class="step-label">Normalisasi Perceived Social Support</span><span class="step-value"><?= htmlspecialchars($fuzzy['norm_pss']); ?></span></li>
                    <li><span class="step-label">Normalisasi Financial Stress</span><span class="step-value"><?= htmlspecialchars($fuzzy['norm_fs']); ?></span></li>
                    <li><span class="step-label">Normalisasi Sleep Quality</span><span class="step-value"><?= htmlspecialchars($fuzzy['norm_sq']); ?></span></li>
                    <li><span class="step-label">Hasil Akhir</span><span class="step-value"><?= htmlspecialchars($fuzzy['stress_score']); ?></span></li>
                </ul>
                <div class="chart-container">
                    <canvas id="fuzzyChart"></canvas>
                </div>
            </div>

            <div class="card difference-card">
                <div class="difference-label">
                    <i class="fas fa-chart-line"></i>
                    Selisih Akurasi
                </div>
                <canvas id="comparisonChart" style="max-height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <script>
<?php
include 'koneksi/koneksi.php';

// Ambil data_id dari URL
$data_id = (int) ($_GET['id'] ?? 0);

// Query ke tabel hasil_perhitungan
$queryManual = mysqli_query($conn, "
    SELECT *
    FROM hasil_perhitungan
    WHERE data_id = '$data_id'
");

// Ubah hasil query menjadi array associative
$manual = mysqli_fetch_assoc($queryManual);

// Jika data tidak ditemukan
if (!$manual) {
    die('Data hasil perhitungan manual tidak ditemukan.');
}

// Query ke tabel hasil_perhitungan
$queryfuzzy = mysqli_query($conn, "
    SELECT *
    FROM hasil_fuzzy
    WHERE data_id = '$data_id'
");

// Ubah hasil query menjadi array associative
$fuzzy = mysqli_fetch_assoc($queryfuzzy);

// Jika data tidak ditemukan
if (!$fuzzy) {
    die('Data hasil perhitungan manual tidak ditemukan.');
}

?>

const manualData = [
    <?= (float)$manual['score_paw']; ?>,
    <?= (float)$manual['score_tm']; ?>,
    <?= (float)$manual['score_pss']; ?>,
    <?= (float)$manual['score_fs']; ?>,
    <?= (float)$manual['score_sq']; ?>
];

const fuzzyData = [
    <?= (float)$fuzzy['norm_paw']; ?>,
    <?= (float)$fuzzy['norm_tm']; ?>,
    <?= (float)$fuzzy['norm_pss']; ?>,
    <?= (float)$fuzzy['norm_fs']; ?>,
    <?= (float)$fuzzy['norm_sq']; ?>
];

const manualScore = <?= (float)$manual['stress_score']; ?>;
const fuzzyScore  = <?= (float)$fuzzy['stress_score']; ?>;

const manualCategory = "<?= $manual['stress_category']; ?>";
const fuzzyCategory  = "<?= $fuzzy['stress_category']; ?>";

let manualChart;
let fuzzyChart;
let comparisonChart;

/* =========================================================
   INISIALISASI CHART
   ========================================================= */
function initCharts() {

    // =====================================================
    // 1. CHART MANUAL
    // =====================================================
    const manualCtx = document.getElementById('manualChart').getContext('2d');

    manualChart = new Chart(manualCtx, {
        type: 'radar',
        data: {
            labels: [
                'PAW',
                'TM',
                'PSS',
                'FS',
                'SQ'
            ],
            datasets: [{
                label: 'Skor Manual',
                data: manualData,
                borderColor: '#ff6b6b',
                backgroundColor: 'rgba(255, 107, 107, 0.2)',
                pointBackgroundColor: '#ff6b6b',
                pointRadius: 4,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true
                }
            }
        }
    });


    // =====================================================
    // 2. CHART FUZZY (NORMALISASI)
    // =====================================================
    const fuzzyCtx = document.getElementById('fuzzyChart').getContext('2d');

    fuzzyChart = new Chart(fuzzyCtx, {
        type: 'radar',
        data: {
            labels: [
                'PAW',
                'TM',
                'PSS',
                'FS',
                'SQ'
            ],
            datasets: [{
                label: 'Normalisasi Fuzzy',
                data: fuzzyData,
                borderColor: '#4ecdc4',
                backgroundColor: 'rgba(78, 205, 196, 0.2)',
                pointBackgroundColor: '#4ecdc4',
                pointRadius: 4,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });


    // =====================================================
    // 3. PERBANDINGAN MANUAL VS FUZZY
    // =====================================================
    const comparisonCtx = document
        .getElementById('comparisonChart')
        .getContext('2d');

    comparisonChart = new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: ['Manual', 'Fuzzy Mamdani'],
            datasets: [{
                label: 'Skor Stres',
                data: [manualScore, fuzzyScore],
                backgroundColor: [
                    '#ff6b6b',
                    '#4ecdc4'
                ],
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    updateDisplay();
}


/* =========================================================
   UPDATE HASIL DI HALAMAN
   ========================================================= */
function updateDisplay() {

    // Nilai hasil utama
    document.getElementById('manualResult').textContent =
        manualScore.toFixed(2);

    document.getElementById('fuzzyResult').textContent =
        fuzzyScore.toFixed(2);

    // Kategori
    document.getElementById('manualCategory').textContent =
        manualCategory;

    document.getElementById('fuzzyCategory').textContent =
        fuzzyCategory;

    // Selisih
    const difference = Math.abs(fuzzyScore - manualScore);

    document.getElementById('differenceValue').textContent =
        difference.toFixed(2) + '%';

    // Langkah Manual
    document.getElementById('manualSteps').innerHTML = `
        <li>
            <span class="step-label">PAW</span>
            <span class="step-value">${manualData[0]}</span>
        </li>
        <li>
            <span class="step-label">TM</span>
            <span class="step-value">${manualData[1]}</span>
        </li>
        <li>
            <span class="step-label">PSS</span>
            <span class="step-value">${manualData[2]}</span>
        </li>
        <li>
            <span class="step-label">FS</span>
            <span class="step-value">${manualData[3]}</span>
        </li>
        <li>
            <span class="step-label">SQ</span>
            <span class="step-value">${manualData[4]}</span>
        </li>
    `;

    // Langkah Fuzzy
    document.getElementById('fuzzySteps').innerHTML = `
        <li>
            <span class="step-label">Norm PAW</span>
            <span class="step-value">${fuzzyData[0].toFixed(2)}</span>
        </li>
        <li>
            <span class="step-label">Norm TM</span>
            <span class="step-value">${fuzzyData[1].toFixed(2)}</span>
        </li>
        <li>
            <span class="step-label">Norm PSS</span>
            <span class="step-value">${fuzzyData[2].toFixed(2)}</span>
        </li>
        <li>
            <span class="step-label">Norm FS</span>
            <span class="step-value">${fuzzyData[3].toFixed(2)}</span>
        </li>
        <li>
            <span class="step-label">Norm SQ</span>
            <span class="step-value">${fuzzyData[4].toFixed(2)}</span>
        </li>
        <li>
            <span class="step-label">Defuzzifikasi</span>
            <span class="step-value">${fuzzyScore.toFixed(2)}</span>
        </li>
    `;
}


/* =========================================================
   LOAD
   ========================================================= */
window.addEventListener('load', function () {
    initCharts();
});
</script>
</body>
</html>