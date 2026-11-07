<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pengisian Data - Analisis Stres Mahasiswa Akhir</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
        }

        /* Header - IDENTIK dengan landing page */
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        /* Hero Form Section */
        .hero-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 140px 0 80px;
            text-align: center;
            min-height: 60vh;
            display: flex;
            align-items: center;
        }

        .hero-form-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero-form h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-form .subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            font-weight: 300;
        }

        .progress-bar {
            background: rgba(255,255,255,0.2);
            height: 8px;
            border-radius: 10px;
            margin: 2rem 0;
            overflow: hidden;
        }

        .progress-fill {
            background: #ff6b6b;
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
            width: 25%;
        }

        /* Form Container */
        .form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .form-section {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 3px solid #f0f2ff;
        }

        .section-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: #333;
        }

        /* Form Groups */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #555;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 15px 20px;
            border: 2px solid #e8ecff;
            border-radius: 15px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
            background: #fafbff;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }

        /* Likert Scale */
        .likert-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .likert-scale {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .likert-option {
            appearance: none;
            width: 100%;
            height: 50px;
            border: 2px solid #e8ecff;
            border-radius: 12px;
            background: #fafbff;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
        }

        .likert-option:checked {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-color: #667eea;
            transform: scale(1.05);
        }

        .likert-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #888;
            margin-top: 0.5rem;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid #f0f2ff;
        }

        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            box-shadow: 0 10px 30px rgba(255,107,107,0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255,107,107,0.6);
        }

        .btn-secondary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        /* ===========================
   TABLET
=========================== */
@media (max-width: 992px){

    nav{
        padding:0 1.5rem;
    }

    .hero-form{
        padding:120px 0 70px;
    }

    .hero-form h1{
        font-size:2.5rem;
    }

    .hero-form .subtitle{
        font-size:1.15rem;
    }

    .form-container{
        padding:0 1.5rem;
    }

    .form-section{
        padding:2.5rem;
    }

}

/* ===========================
   MOBILE
=========================== */
@media (max-width:768px){

    body{
        font-size:15px;
    }

    header{
        padding:.8rem 0;
    }

    nav{
        padding:0 1rem;
    }

    .logo{
        font-size:1.4rem;
    }

    .logo i{
        font-size:1.5rem;
    }

    .nav-links{
        display:none;
    }

    .hero-form{
        min-height:auto;
        padding:100px 0 50px;
    }

    .hero-form-content{
        padding:0 1rem;
    }

    .hero-form h1{
        font-size:2rem;
        line-height:1.3;
    }

    .hero-form .subtitle{
        font-size:1rem;
        margin-bottom:1.5rem;
    }

    .progress-bar{
        margin:1.5rem 0;
    }

    .form-container{
        padding:0 15px;
    }

    .form-section{
        padding:1.5rem;
        margin:20px 0;
        border-radius:20px;
    }

    .section-header{
        gap:12px;
        margin-bottom:1.5rem;
        padding-bottom:1rem;
    }

    .section-icon{
        width:50px;
        height:50px;
        font-size:1.2rem;
    }

    .section-title{
        font-size:1.3rem;
    }

    .form-row{
        grid-template-columns:1fr;
        gap:1rem;
    }

    .form-group label{
        font-size:.9rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea{
        font-size:16px;
        padding:14px 16px;
    }

    .likert-group{
        flex-direction:column;
        align-items:stretch;
        gap:10px;
    }

    .likert-scale{
        gap:6px;
    }

    .likert-option{
        height:45px;
        font-size:14px;
        border-radius:10px;
    }

    .likert-labels{
        font-size:12px;
    }

    .form-actions{
        flex-direction:column;
        gap:15px;
    }

    .btn{
        width:100%;
        padding:14px;
        font-size:1rem;
    }

}

/* ===========================
   SMALL MOBILE
=========================== */
@media (max-width:480px){

    .hero-form{
        padding:90px 0 40px;
    }

    .hero-form h1{
        font-size:1.7rem;
    }

    .hero-form .subtitle{
        font-size:.95rem;
    }

    .form-section{
        padding:1.2rem;
        border-radius:18px;
    }

    .section-icon{
        width:45px;
        height:45px;
        font-size:1rem;
    }

    .section-title{
        font-size:1.15rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea{
        padding:12px 14px;
    }

    .likert-option{
        height:42px;
        font-size:13px;
    }

    .btn{
        font-size:.95rem;
        padding:13px;
    }

}

        /* ===========================
        VERY SMALL DEVICE
        =========================== */
        @media (max-width:360px){

            .hero-form h1{
                font-size:1.5rem;
            }

            .hero-form .subtitle{
                font-size:.9rem;
            }

            .logo{
                font-size:1.2rem;
            }

            .section-title{
                font-size:1rem;
            }

            .likert-option{
                height:38px;
                font-size:12px;
            }

        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-up {
            animation: slideInUp 0.6s ease forwards;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <div class="logo">
                <i class="fas fa-brain"></i>
                FuzzyStress Analysis
            </div>
            <ul class="nav-links">
                <li><a href="index.php">← Kembali ke Home</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Form -->
    <section class="hero-form">
        <div class="hero-form-content">
            <h1>📊 Form Pengisian Data</h1>
            <p class="subtitle">Isi data stres akademik Anda untuk analisis menggunakan Logika Fuzzy</p>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <p style="font-size: 1rem; opacity: 0.9;">Langkah <span id="currentStep">1</span> dari 6</p>
        </div>
    </section>

    <!-- Form Container -->
    <div class="form-container">
        <!-- Step 1: Data Diri -->
        <div class="form-section slide-in-up" id="step1">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2 class="section-title">1. Data Diri Mahasiswa</h2>
                    <p style="color: #666; margin: 0;">Informasi dasar responden</p>
                </div>
            </div>
            <form action="upload.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>NIM *</label>
                    <input type="text" id="nim" name="nim" maxlength="15" required>
                </div>
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Jurusan *</label>
                    <select id="jurusan" name="jurusan" required>
                        <option value="">Pilih Jurusan</option>
                        <option value="Teknik Sipil">Teknik Sipil</option>
                        <option value="Teknik Mesin">Teknik Mesin</option>
                        <option value="Teknik Kimia">Teknik Kimia</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Industri">Teknik Industri</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Teknik Material">Teknik Material</option>
                        <option value="Teknik Logistik">Teknik Logistik</option>
                        <option value="Teknik Arsitektur">Teknik Arstitektur</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                        <option value="Administrasi Bisnis">Administrasi Bisnis</option>
                        <option value="Ilmu Politik">Ilmu Politik</option>
                        <option value="Ilmu Komunikasi">Ilmu Komunikasi</option>
                        <option value="Sosiologi">Sosiologi</option>
                        <option value="Antropologi">Antropologi</option>
                        <option value="Magister Administrasi Publik">Magister Administrasi Publik</option>
                        <option value="Agroekoteknologi">Agroekoteknologi</option>
                        <option value="Agribisnis">Agribisnis</option>
                        <option value="Akuakultur">Akuakultur</option>
                        <option value="Ilmu Kelautan">ilmu Kelautan</option>
                        <option value="Bioteknologi">Bioteknologi</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Ekonomi Pembangunan">Ekonomi Pembangunan</option>
                        <option value="Ekonomi Syariah">Ekonomi Syariah</option>
                        <option value="Kewirausahaan">Kewirausahaan</option>
                        <option value="Kesekretariatan">Kesekretariatan</option>
                        <option value="Kedokteran">Kedokteran</option>
                        <option value="Kedokteran">Profesi Kedokteran</option>
                        <option value="Psikologi">Psikologi</option>
                        <option value="Keperawatan">Keperawatan</option>
                        <option value="Ilmu Hukum">Ilmu Hukum</option>
                        <option value="Pendidikan Matematika">Pendidikan Matematika</option>
                        <option value="Pendidikan Kimia">Pendidikan Kimia</option>
                        <option value="Pendidikan Fisika">Pendidikan Fisika</option>
                        <option value="Pendidikan Bahasa Indonesia">Pendidikan Bahasa Indonesia</option>
                        <option value="Vokasional">Vokasional</option>
                        <option value="Lainnya">Fakultas Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Semester Saat Ini *</label>
                    <select id="semester" name="semester"  required>
                        <option value="">Pilih Semester</option>
                        <option value="7">Semester 7</option>
                        <option value="8">Semester 8</option>
                        <option value="9">Semester 9</option>
                        <option value="10+">Semester 10+</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <div></div>
                <button type="button" class="btn btn-primary" onclick="nextStep(2)">Lanjut ke Langkah 2 →</button>
            </div>
        </div>

        <!-- Step 2: Beban Tugas Akhir -->
        <div class="form-section" id="step2" style="display: none;">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <h2 class="section-title">2. Perceived Academic Workload</h2>
                    <p style="color: #666; margin: 0;">Nilai pernyataan berikut (1 = Sangat Tidak Setuju, 5 = Sangat Setuju)</p>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Status Proposal TA</label>
                    <select id="status_skripsi" name="status_tga">
                        <option value="">Pilih Status</option>
                        <option value="5">Belum diajukan</option>
                        <option value="3">Sedang proses</option>
                        <option value="1">Sudah lolos</option>
                        <option value="4">Revisi berat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Persentase Progress TA</label>
                    <select id="status_tga" name="proses_tga">
                        <option value="">Pilih Progress</option>
                        <option value="5">0% (Belum mulai)</option>
                        <option value="4">25%</option>
                        <option value="3">50%</option>
                        <option value="2">75%</option>
                        <option value="1">100% (Selesai)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>1. Saya merasa cemas memikirkan tugas akhir</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q1tga" value="1" id="q1_1" class="likert-option">
                        <input type="radio" name="q1tga" value="2" id="q1_2" class="likert-option">
                        <input type="radio" name="q1tga" value="3" id="q1_3" class="likert-option">
                        <input type="radio" name="q1tga" value="4" id="q1_4" class="likert-option">
                        <input type="radio" name="q1tga" value="5" id="q1_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>2. Saya merasa kesulitan memenuhi target penyelesaian skripsi sesuai waktu yang ditentukan.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q2tga" value="1" id="q2_1" class="likert-option">
                        <input type="radio" name="q2tga" value="2" id="q2_2" class="likert-option">
                        <input type="radio" name="q2tga" value="3" id="q2_3" class="likert-option">
                        <input type="radio" name="q2tga" value="4" id="q2_4" class="likert-option">
                        <input type="radio" name="q2tga" value="5" id="q2_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
        <div class="form-group">
                <label>3. Beban pengerjaan skripsi terasa melebihi kemampuan saya.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q3tga" value="1" id="q3_1" class="likert-option">
                        <input type="radio" name="q3tga" value="2" id="q3_2" class="likert-option">
                        <input type="radio" name="q3tga" value="3" id="q3_3" class="likert-option">
                        <input type="radio" name="q3tga" value="4" id="q3_4" class="likert-option">
                        <input type="radio" name="q3tga" value="5" id="q3_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
        <div class="form-group">
                <label>4. Saya merasa tuntutan dalam pengerjaan skripsi sangat tinggi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q4tga" value="1" id="q4_1" class="likert-option">
                        <input type="radio" name="q4tga" value="2" id="q4_2" class="likert-option">
                        <input type="radio" name="q4tga" value="3" id="q4_3" class="likert-option">
                        <input type="radio" name="q4tga" value="4" id="q4_4" class="likert-option">
                        <input type="radio" name="q4tga" value="5" id="q4_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>5. Saya merasa tertekan dengan berbagai tuntutan dalam proses penyusunan skripsi</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q5tga" value="1" id="q5_1" class="likert-option">
                        <input type="radio" name="q5tga" value="2" id="q5_2" class="likert-option">
                        <input type="radio" name="q5tga" value="3" id="q5_3" class="likert-option">
                        <input type="radio" name="q5tga" value="4" id="q5_4" class="likert-option">
                        <input type="radio" name="q5tga" value="5" id="q5_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="prevStep(1)">← Kembali</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(3)">Lanjut ke Langkah 3 →</button>
            </div>
        </div>
        <!-- Step 3: Time Management -->
        <div class="form-section" id="step3" style="display: none;">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <div>
                    <h2 class="section-title">3. Time Management</h2>
                    <p style="color: #666; margin: 0;">Nilai pernyataan berikut (1 = Sangat Tidak Setuju, 5 = Sangat Setuju)</p>
                </div>
            </div>
            <div class="form-group">
                <label>Rata-rata jam tidur per hari (minggu ini) *</label>
                <select id="sleep_time" name="sleep_time" required>
                    <option value="">Pilih jam tidur</option>
                    <option value="5">1 jam</option>
                    <option value="5">2 jam</option>
                    <option value="5">3 jam</option>
                    <option value="5">4 jam</option>
                    <option value="4">5 jam</option>
                    <option value="3">6 jam</option>
                    <option value="2">7 jam</option>
                    <option value="1">8+ jam</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>1. Saya mampu mengatur waktu secara efektif dalam mengerjakan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q1time" value="1" id="q1_1" class="likert-option">
                        <input type="radio" name="q1time" value="2" id="q1_2" class="likert-option">
                        <input type="radio" name="q1time" value="3" id="q1_3" class="likert-option">
                        <input type="radio" name="q1time" value="4" id="q1_4" class="likert-option">
                        <input type="radio" name="q1time" value="5" id="q1_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>2. Saya sering menunda pekerjaan yang berkaitan dengan skripsi.</label>
                    <div class="likert-scale">
                        <input type="radio" name="q2time" value="1" id="q2_1" class="likert-option">
                        <input type="radio" name="q2time" value="2" id="q2_2" class="likert-option">
                        <input type="radio" name="q2time" value="3" id="q2_3" class="likert-option">
                        <input type="radio" name="q2time" value="4" id="q2_4" class="likert-option">
                        <input type="radio" name="q2time" value="5" id="q2_5" class="likert-option">
                    </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
        <div class="form-group">
                <label>3. Saya dapat membagi waktu antara pengerjaan skripsi dan aktivitas lain dengan baik.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q3time" value="1" id="q3_1" class="likert-option">
                        <input type="radio" name="q3time" value="2" id="q3_2" class="likert-option">
                        <input type="radio" name="q3time" value="3" id="q3_3" class="likert-option">
                        <input type="radio" name="q3time" value="4" id="q3_4" class="likert-option">
                        <input type="radio" name="q3time" value="5" id="q3_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
        <div class="form-group">
                <label>4. Saya memiliki jadwal yang teratur untuk mengerjakan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q4time" value="1" id="q4_1" class="likert-option">
                        <input type="radio" name="q4time" value="2" id="q4_2" class="likert-option">
                        <input type="radio" name="q4time" value="3" id="q4_3" class="likert-option">
                        <input type="radio" name="q4time" value="4" id="q4_4" class="likert-option">
                        <input type="radio" name="q4time" value="5" id="q4_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>5. Saya mampu menyelesaikan target pengerjaan skripsi sesuai rencana waktu yang telah ditetapkan.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q5time" value="1" id="q5_1" class="likert-option">
                        <input type="radio" name="q5time" value="2" id="q5_2" class="likert-option">
                        <input type="radio" name="q5time" value="3" id="q5_3" class="likert-option">
                        <input type="radio" name="q5time" value="4" id="q5_4" class="likert-option">
                        <input type="radio" name="q5time" value="5" id="q5_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="prevStep(2)">← Kembali</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(4)">Lanjut ke Langkah 4 →</button>
            </div>
        </div>

        <!-- Step 4: Stres Assessment (Likert Scale) -->
        <div class="form-section" id="step4" style="display: none;">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="section-title">4. Perceived Social Support</h2>
                    <p style="color: #666; margin: 0;">Nilai pernyataan berikut (1 = Sangat Tidak Setuju, 5 = Sangat Setuju)</p>
                </div>
            </div>

            <div class="form-group">
                <label>1. Saya merasa mendapatkan dukungan dari dosen pembimbing dalam pengerjaan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q1pcv" value="1" id="q1_1" class="likert-option">
                        <input type="radio" name="q1pcv" value="2" id="q1_2" class="likert-option">
                        <input type="radio" name="q1pcv" value="3" id="q1_3" class="likert-option">
                        <input type="radio" name="q1pcv" value="4" id="q1_4" class="likert-option">
                        <input type="radio" name="q1pcv" value="5" id="q1_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-group">
                <label>2. Saya merasa dosen pembimbing memberikan arahan yang jelas terkait skripsi saya.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q2pcv" value="1" id="q2_1" class="likert-option">
                        <input type="radio" name="q2pcv" value="2" id="q2_2" class="likert-option">
                        <input type="radio" name="q2pcv" value="3" id="q2_3" class="likert-option">
                        <input type="radio" name="q2pcv" value="4" id="q2_4" class="likert-option">
                        <input type="radio" name="q2pcv" value="5" id="q2_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-group">
                <label>3. Saya merasa mudah mendapatkan bantuan ketika mengalami kesulitan dalam skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q3pcv" value="1" id="q3_1" class="likert-option">
                        <input type="radio" name="q3pcv" value="2" id="q3_2" class="likert-option">
                        <input type="radio" name="q3pcv" value="3" id="q3_3" class="likert-option">
                        <input type="radio" name="q3pcv" value="4" id="q3_4" class="likert-option">
                        <input type="radio" name="q3pcv" value="5" id="q3_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>4. Saya merasa didukung oleh lingkungan sekitar dalam menyelesaikan skripsi. </label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q4pcv" value="1" id="q4_1" class="likert-option">
                        <input type="radio" name="q4pcv" value="2" id="q4_2" class="likert-option">
                        <input type="radio" name="q4pcv" value="3" id="q4_3" class="likert-option">
                        <input type="radio" name="q4pcv" value="4" id="q4_4" class="likert-option">
                        <input type="radio" name="q4pcv" value="5" id="q4_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>5. Saya merasa tidak sendirian dalam menghadapi proses pengerjaan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q5pcv" value="1" id="q5_1" class="likert-option">
                        <input type="radio" name="q5pcv" value="2" id="q5_2" class="likert-option">
                        <input type="radio" name="q5pcv" value="3" id="q5_3" class="likert-option">
                        <input type="radio" name="q5pcv" value="4" id="q5_4" class="likert-option">
                        <input type="radio" name="q5pcv" value="5" id="q5_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="prevStep(3)">← Kembali</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(5)">Lanjut Ke Langkah 5</button>
            </div>
        </div>
    <div class="form-section" id="step5" style="display: none;">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="section-title">5. Financial Stress</h2>
                    <p style="color: #666; margin: 0;">Nilai pernyataan berikut (1 = Sangat Tidak Setuju, 5 = Sangat Setuju)</p>
                </div>
            </div>

            <div class="form-group">
                <label>1. Saya merasa kondisi keuangan saya mempengaruhi proses pengerjaan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q1fnc" value="1" id="q1_1" class="likert-option">
                        <input type="radio" name="q1fnc" value="2" id="q1_2" class="likert-option">
                        <input type="radio" name="q1fnc" value="3" id="q1_3" class="likert-option">
                        <input type="radio" name="q1fnc" value="4" id="q1_4" class="likert-option">
                        <input type="radio" name="q1fnc" value="5" id="q1_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-group">
                <label>2. Saya merasa khawatir terhadap biaya yang diperlukan untuk menyelesaikan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q2fnc" value="1" id="q2_1" class="likert-option">
                        <input type="radio" name="q2fnc" value="2" id="q2_2" class="likert-option">
                        <input type="radio" name="q2fnc" value="3" id="q2_3" class="likert-option">
                        <input type="radio" name="q2fnc" value="4" id="q2_4" class="likert-option">
                        <input type="radio" name="q2fnc" value="5" id="q2_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-group">
                <label>3. Keterbatasan finansial menjadi beban dalam penyelesaian skripsi saya.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q3fnc" value="1" id="q3_1" class="likert-option">
                        <input type="radio" name="q3fnc" value="2" id="q3_2" class="likert-option">
                        <input type="radio" name="q3fnc" value="3" id="q3_3" class="likert-option">
                        <input type="radio" name="q3fnc" value="4" id="q3_4" class="likert-option">
                        <input type="radio" name="q3fnc" value="5" id="q3_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>4. Saya merasa tekanan keuangan mengganggu fokus saya dalam mengerjakan skripsi. </label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q4fnc" value="1" id="q4_1" class="likert-option">
                        <input type="radio" name="q4fnc" value="2" id="q4_2" class="likert-option">
                        <input type="radio" name="q4fnc" value="3" id="q4_3" class="likert-option">
                        <input type="radio" name="q4fnc" value="4" id="q4_4" class="likert-option">
                        <input type="radio" name="q4fnc" value="5" id="q4_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>5. Saya mengalami kesulitan memenuhi kebutuhan yang berkaitan dengan skripsi karena kondisi keuangan.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q5fnc" value="1" id="q5_1" class="likert-option">
                        <input type="radio" name="q5fnc" value="2" id="q5_2" class="likert-option">
                        <input type="radio" name="q5fnc" value="3" id="q5_3" class="likert-option">
                        <input type="radio" name="q5fnc" value="4" id="q5_4" class="likert-option">
                        <input type="radio" name="q5fnc" value="5" id="q5_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="prevStep(4)">← Kembali</button>
                <button type="button" class="btn btn-primary" onclick="nextStep(6)">🚀 Lanjut Ke Langkah 6</button>
            </div>
        </div>
    <div class="form-section" id="step6" style="display: none;">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <h2 class="section-title">6. Sleep Quality</h2>
                    <p style="color: #666; margin: 0;">Nilai pernyataan berikut (1 = Sangat Tidak Setuju, 5 = Sangat Setuju)</p>
                </div>
            </div>

            <div class="form-group">
                <label>1. Saya merasa kualitas tidur saya terganggu karena pengerjaan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q1sleep" value="1" id="q1_1" class="likert-option">
                        <input type="radio" name="q1sleep" value="2" id="q1_2" class="likert-option">
                        <input type="radio" name="q1sleep" value="3" id="q1_3" class="likert-option">
                        <input type="radio" name="q1sleep" value="4" id="q1_4" class="likert-option">
                        <input type="radio" name="q1sleep" value="5" id="q1_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-group">
                <label>2. Saya mengalami kesulitan tidur akibat memikirkan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q2sleep" value="1" id="q2_1" class="likert-option">
                        <input type="radio" name="q2sleep" value="2" id="q2_2" class="likert-option">
                        <input type="radio" name="q2sleep" value="3" id="q2_3" class="likert-option">
                        <input type="radio" name="q2sleep" value="4" id="q2_4" class="likert-option">
                        <input type="radio" name="q2sleep" value="5" id="q2_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-group">
                <label>3. Saya merasa tidur saya cukup meskipun sedang mengerjakan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q3sleep" value="1" id="q3_1" class="likert-option">
                        <input type="radio" name="q3sleep" value="2" id="q3_2" class="likert-option">
                        <input type="radio" name="q3sleep" value="3" id="q3_3" class="likert-option">
                        <input type="radio" name="q3sleep" value="4" id="q3_4" class="likert-option">
                        <input type="radio" name="q3sleep" value="5" id="q3_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>4. Saya merasa kurang segar saat bangun karena aktivitas pengerjaan skripsi. </label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q4sleep" value="1" id="q4_1" class="likert-option">
                        <input type="radio" name="q4sleep" value="2" id="q4_2" class="likert-option">
                        <input type="radio" name="q4sleep" value="3" id="q4_3" class="likert-option">
                        <input type="radio" name="q4sleep" value="4" id="q4_4" class="likert-option">
                        <input type="radio" name="q4sleep" value="5" id="q4_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>
            <div class="form-group">
                <label>5. Pola tidur saya menjadi tidak teratur sejak mengerjakan skripsi.</label>
                <div class="likert-group">
                    <div class="likert-scale">
                        <input type="radio" name="q5sleep" value="1" id="q5_1" class="likert-option">
                        <input type="radio" name="q5sleep" value="2" id="q5_2" class="likert-option">
                        <input type="radio" name="q5sleep" value="3" id="q5_3" class="likert-option">
                        <input type="radio" name="q5sleep" value="4" id="q5_4" class="likert-option">
                        <input type="radio" name="q5sleep" value="5" id="q5_5" class="likert-option">
                    </div>
                </div>
                <div class="likert-labels">
                    <span>Sangat Tidak Setuju</span>
                    <span></span>
                    <span>Netral</span>
                    <span></span>
                    <span>Sangat Setuju</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="prevStep(5)">← Kembali</button>
                <button type="submit" class="btn btn-primary">🚀 Submit Data</button>
            </div>
        </div>
    </div>
</form>

    <script>
        let currentStep = 1;
        const totalSteps = 6;

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
            document.getElementById('currentStep').textContent = currentStep;
        }

        function nextStep(step) {
            if (validateCurrentStep()) {
                document.getElementById('step' + currentStep).style.display = 'none';
                document.getElementById('step' + step).style.display = 'block';
                document.getElementById('step' + step).classList.add('slide-in-up');
                currentStep = step;
                updateProgress();
            }
        }

        function prevStep(step) {
            document.getElementById('step' + currentStep).style.display = 'none';
            document.getElementById('step' + step).style.display = 'block';
            currentStep = step;
            updateProgress();
        }

        function validateCurrentStep() {
            const requiredFields = document.querySelectorAll(`#step${currentStep} [required]`);
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    alert(`Mohon lengkapi field ${field.previousElementSibling.textContent}`);
                    return false;
                }
            }
            return true;
        }

        function submitForm() {
            if (validateCurrentStep()) {
                // Kumpulkan semua data
                const formData = {
                    nim: document.getElementById('nim').value,
                    nama: document.getElementById('nama').value,
                    jurusan: document.getElementById('jurusan').value,
                    semester: document.getElementById('semester').value,
                    status_skripsi: document.getElementById('status_skripsi').value,
                    status_tga: document.getElementById('status_tga').value,
                    sleep_time: document.getElementById('sleep_time').value,
                    q1tga: document.querySelector('input[name="q1tga"]:checked')?.value || '',
                    q2tga: document.querySelector('input[name="q2tga"]:checked')?.value || '',
                    q3tga: document.querySelector('input[name="q3tga"]:checked')?.value || '',
                    q4tga: document.querySelector('input[name="q4tga"]:checked')?.value || '',
                    q5tga: document.querySelector('input[name="q5tga"]:checked')?.value || '',
                    q1time: document.querySelector('input[name="q1time"]:checked')?.value || '',
                    q2time: document.querySelector('input[name="q2time"]:checked')?.value || '',
                    q3time: document.querySelector('input[name="q3time"]:checked')?.value || '',
                    q4time: document.querySelector('input[name="q4time"]:checked')?.value || '',
                    q5time: document.querySelector('input[name="q5time"]:checked')?.value || '',
                    q1pcv: document.querySelector('input[name="q1pcv"]:checked')?.value || '',
                    q2pcv: document.querySelector('input[name="q2pcv"]:checked')?.value || '',
                    q3pcv: document.querySelector('input[name="q3pcv"]:checked')?.value || '',
                    q4pcv: document.querySelector('input[name="q4pcv"]:checked')?.value || '',
                    q5pcv: document.querySelector('input[name="q5pcv"]:checked')?.value || '',
                    q1fnc: document.querySelector('input[name="q1fnc"]:checked')?.value || '',
                    q2fnc: document.querySelector('input[name="q2fnc"]:checked')?.value || '',
                    q3fnc: document.querySelector('input[name="q3fnc"]:checked')?.value || '',
                    q4fnc: document.querySelector('input[name="q4fnc"]:checked')?.value || '',
                    q5fnc: document.querySelector('input[name="q5fnc"]:checked')?.value || '',
                    q1sleep: document.querySelector('input[name="q1sleep"]:checked')?.value || '',
                    q2sleep: document.querySelector('input[name="q2sleep"]:checked')?.value || '',
                    q3sleep: document.querySelector('input[name="q3sleep"]:checked')?.value || '',
                    q4sleep: document.querySelector('input[name="q4sleep"]:checked')?.value || '',
                    q5sleep: document.querySelector('input[name="q5sleep"]:checked')?.value || ''
                    
                };

                // Simulasi pengiriman data
                console.log('Data yang dikirim:', formData);
                
                // Tampilkan hasil simulasi fuzzy (bisa diintegrasikan dengan backend)
                showFuzzyResult(formData);
            }
        }

        function showFuzzyResult(data) {
            // Simulasi hasil analisis fuzzy
            const stressLevel = Math.floor(Math.random() * 100);
            const stressStatus = stressLevel > 70 ? 'TINGGI' : stressLevel > 40 ? 'SEDANG' : 'RENDAH';
            
            alert(`
🎯 TERIMA KASIH TELAH MENGISI FORM!

📊 HASIL ANALISIS FUZZY STRES:
- Tingkat Stres: ${stressLevel}%
- Status: ${stressStatus}

✅ Data berhasil tersimpan untuk penelitian.
📧 Kami akan kirim hasil lengkap ke email kampus.

Data lengkap: NIM ${data.nim}
            `);

            // Reset form
            location.reload();
        }

        // Inisialisasi
        updateProgress();

        // Label untuk likert scale
        document.querySelectorAll('.likert-option').forEach((option, index) => {
            option.addEventListener('change', function() {
                this.parentNode.querySelectorAll('.likert-option').forEach(opt => {
                    opt.style.transform = opt.checked ? 'scale(1.05)' : 'scale(1)';
                });
            });
        });
    
    </script>
    
</body>
</html>