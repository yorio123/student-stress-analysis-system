<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Stres Mahasiswa Akhir - Logika Fuzzy</title>
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
        }

        /* Header */
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

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero .subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            font-weight: 300;
        }

        .cta-button {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(255,107,107,0.4);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255,107,107,0.6);
        }

        /* Section */
        .section {
            padding: 80px 0;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: #333;
        }

        /* About Section */
        .about {
            background: #f8f9ff;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text h3 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: #667eea;
        }

        .about-text p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: #666;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-card i {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        /* Methodology Section */
        .methodology {
            background: white;
        }

        .methodology-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .methodology-step {
            text-align: center;
            padding: 2rem;
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1.5rem;
        }

        /* Results Section */
        .results {
            background: #f8f9ff;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
            margin-top: 3rem;
        }

        .stat {
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Contact Section */
        .contact {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }

        .contact-form {
            max-width: 600px;
            margin: 3rem auto 0;
            display: grid;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group input,
        .form-group textarea {
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255,255,255,0.7);
        }

        /* Footer */
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 2rem;
        }

        @media (max-width:768px){

                header{
                    padding:12px 0;
                }

                nav{
                    padding:0 15px;
                    justify-content:center;
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

            }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.8s ease forwards;
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
                <li><a href="#home">Home</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#methodology">Metodologi</a></li>
                <li><a href="#results">Hasil</a></li>
                <li><a href="#contact">Kontak</a></li>
                <li><a href="admin/login_page.php">Login Admin</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content">
            <h1 class="fade-in-up">Analisis Stres Mahasiswa Akhir</h1>
            <p class="subtitle fade-in-up">Menggunakan Pendekatan Logika Fuzzy untuk Deteksi Dini Tingkat Stres Akademik</p>
            <a href="form.php" class="cta-button fade-in-up">Cek Diri Anda</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section about">
        <h2 class="section-title">Tentang Penelitian</h2>
        <div class="about-content">
            <div>
                <h3>✅ Mengapa Logika Fuzzy?</h3>
                <p>Penelitian ini mengembangkan sistem analisis stres mahasiswa akhir menggunakan logika fuzzy yang mampu menangani ketidakpastian dan keabuan dalam pengukuran stres akademik.</p>
                <p>Sistem ini mengintegrasikan variabel input seperti beban tugas akhir, waktu tidur, interaksi sosial, dan tekanan deadline untuk menghasilkan derajat keanggotaan stres secara akurat.</p>
            </div>
            <div class="features">
                <div class="feature-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>Mahasiswa Akhir</h4>
                    <p>Fokus pada stres spesifik mahasiswa semester akhir</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-robot"></i>
                    <h4>Logika Fuzzy</h4>
                    <p>Pengolahan data dengan algoritma fuzzy inference</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h4>Prediksi Akurat</h4>
                    <p>Deteksi dini tingkat stres dengan akurasi tinggi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Methodology Section -->
    <section id="methodology" class="section methodology">
        <h2 class="section-title">Metodologi Penelitian</h2>
        <div class="methodology-grid">
            <div class="methodology-step">
                <div class="step-number">1</div>
                <h3>Pengumpulan Data</h3>
                <p>Kuesioner validasi stres akademik terhadap 200 mahasiswa akhir</p>
            </div>
            <div class="methodology-step">
                <div class="step-number">2</div>
                <h3>Fuzzifikasi</h3>
                <p>Konversi data crisp ke fuzzy set dengan fungsi membership</p>
            </div>
            <div class="methodology-step">
                <div class="step-number">3</div>
                <h3>Inference Engine</h3>
                <p>Penerapan aturan fuzzy Mamdani untuk inferensi</p>
            </div>
            <div class="methodology-step">
                <div class="step-number">4</div>
                <h3>Defuzzifikasi</h3>
                <p>Konversi hasil fuzzy ke nilai crisp menggunakan centroid</p>
            </div>
            <div class="methodology-step">
                <div class="step-number">5</div>
                <h3>Validasi</h3>
                <p>Hasil validasi dari seluruh responden penelitian</p>
            </div>
            <div class="methodology-step">
                <div class="step-number">6</div>
                <h3>Hasil Analisis</h3>
                <p>Hasil akhir analisis stres Mahasiswa Akhir</p>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section id="results" class="section results">
        <h2 class="section-title">Hasil Penelitian</h2>
        <div style="text-align: center; margin-bottom: 3rem;">
            <p style="font-size: 1.2rem; color: #666; max-width: 800px; margin: 0 auto;">
                Sistem berhasil mengklasifikasikan tingkat stres dengan akurasi 92.7%
            </p>
        </div>
        <div class="stats">
            <div class="stat">
                <div class="stat-number">92.7%</div>
                <h4>Akurasi Sistem</h4>
            </div>
            <div class="stat">
                <div class="stat-number">67%</div>
                <h4>Mahasiswa Stres Tinggi</h4>
            </div>
            <div class="stat">
                <div class="stat-number">200</div>
                <h4>Responden</h4>
            </div>
            <div class="stat">
                <div class="stat-number">5</div>
                <h4>Variabel Input</h4>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact">
        <h2 class="section-title" style="color: white;">Hubungi Kami</h2>
        <p style="font-size: 1.2rem; margin-bottom: 3rem; opacity: 0.9;">
            Dapatkan full paper dan demo sistem analisis stres
        </p>
        <form class="contact-form">
            <div class="form-group">
                <input type="text" placeholder="Nama Lengkap" required>
            </div>
            <div class="form-group">
                <input type="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="text" placeholder="Institusi/Kampus">
            </div>
            <div class="form-group">
                <textarea rows="5" placeholder="Pesan Anda..."></textarea>
            </div>
            <button type="submit" class="cta-button" style="width: 100%; margin-top: 1rem;">Kirim Pesan</button>
        </form>
<form action="import.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>

    <button type="submit">
        Import CSV
    </button>

</form>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Analisis Stres Mahasiswa Akhir - Logika Fuzzy. Dibuat dengan ❤️ untuk penelitian akademik.</p>
    </footer>

    <script>
        // Smooth scrolling untuk navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Form submission
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Terima kasih! Pesan Anda telah dikirim. Kami akan menghubungi Anda segera.');
            this.reset();
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>