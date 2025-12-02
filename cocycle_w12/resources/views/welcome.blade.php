<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoCycle</title>
    
    {{-- Ubah link CSS agar membaca dari folder public/css --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <header>
        <div class="logo" style="padding-left: 2%;">
            {{-- Ubah src gambar agar membaca dari folder public/assets --}}
            <img src="{{ asset('assets/logo_cocycle.png') }}" alt="logo" width="60">
        </div>
        <nav id="navMenu">
            <a href="#home">Home</a>
            <a href="#about">About Us</a>
            <a href="#community">Community</a>
            <a href="#faq">FAQ</a>
            
            {{-- Opsional: Tambahkan tombol Login jika user belum login --}}
            @if (Route::has('login'))
                <div class="auth-links" style="display:inline-block; margin-left: 20px;">
                    @auth
                        <a href="{{ url('/home') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                    @endauth
                </div>
            @endif
        </nav>
    </header>

    <section class="hero" id="home">
        <video autoplay muted loop playsinline class="bg-video">
            {{-- Ubah source video --}}
            <source src="{{ asset('assets/vid/veg_vid.mp4') }}" type="video/mp4">
            Browser kamu tidak mendukung video.
        </video>
        <div class="hero-content">
            <h1 class="fw-bold" style="color: #FFC145; text-shadow: 1px 1px 3px #000;">
                <i>Eat Smart, Waste Less!</i>
            </h1>
            <p>With <strong>CoCycle</strong>, every bite counts for the planet</p>
        </div>
    </section>

    <section class="sign">
        <div class="cards">
            {{-- Ubah Link Sign Up Customer --}}
            {{-- Asumsi: route register bawaan laravel, kita kirim parameter role --}}
            <div class="card" style="background-color: #FFC145; cursor: pointer;"
                onclick="location.href='{{ route('register', ['role' => 'customer']) }}';">
                
                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" fill="white" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                </svg>
                <div class="card-body">
                    <h5 class="sign-up">Sign up as a customer</h5>
                </div>
            </div>

            {{-- Ubah Link Sign Up Partner --}}
            <div class="card" style="background-color: #5CB338; cursor: pointer;"
                onclick="location.href='{{ route('register', ['role' => 'partner']) }}';">
                
                <svg xmlns="http://www.w3.org/2000/svg" width="90" height="90" fill="white" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                </svg>
                <div class="card-body">
                    <h5 class="sign-up">Sign up as a partner</h5>
                </div>
            </div>
        </div>
    </section>

    <section class="impact" id="about">
        <h2>About Us</h2>
        {{-- Ubah src gambar --}}
        <img class="img2" src="{{ asset('assets/roti.jpg') }}" alt="about us image" width="55%">

        <p class="content" style="padding: 0 5%; text-align: center;">
            <strong style="color: #5e2703; font-style: italic;font-size: larger;">CoCycle</strong> adalah gerakan
            menuju masa depan berkelanjutan. Terinspirasi dari kelapa yang tidak menyisakan limbah, kami menghadirkan
            platform yang memanfaatkan makanan berlebih agar tidak berakhir sebagai sampah. Dengan membeli lewat
            <strong style="color: #5e2703; font-style: italic;font-size: larger;">CoCycle</strong>, kamu tidak hanya
            menghemat, tetapi juga ikut mendukung misi global mengurangi food waste dan kelaparan. <br />
        </p>

        <div class="impact-items" style="color: #2a3b30;">
            <div class="impact-item">1000+ Pengguna</div>
            <div class="impact-item">10+ Komunitas Lingkungan</div>
            <div class="impact-item">100+ Mitra Bisnis</div>
        </div>
    </section>

    <section class="community" id="community">
        <h2>Our Community</h2>
        {{-- Ubah src gambar --}}
        <img class="img2" src="{{ asset('assets/community.jpg') }}" alt="community image" width="55%">

        <p class="content" style="padding: 0 5%; text-align: center;">
            Bekerja sama dengan berbagai komunitas lingkungan demi mewujudkan bumi hijau dan lestari.
        </p>

        <div class="community-items" style="color: #2a3b30;">
            <div class="community-item">Bank Sampah</div>
            <div class="community-item">Komunitas Kompos</div>
            <div class="community-item">Zero Hunger</div>
        </div>
    </section>

    <div class="faq-container" id="faq">
        <h2 style="text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.4);">FAQ</h2>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                Bagaimana cara memastikan kualitas? <span class="toggle">+</span>
            </div>
            <div class="faq-answer">Kami memiliki standar ketat untuk memastikan makanan yang dijual layak konsumsi dan higienis.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                Di area mana saja layanan ini tersedia? <span class="toggle">+</span>
            </div>
            <div class="faq-answer">Layanan kami saat ini tersedia di Jabodetabek dan akan segera hadir di kota besar lainnya.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleFAQ(this)">
                Bagaimana cara melaporkan masalah? <span class="toggle">+</span>
            </div>
            <div class="faq-answer">Anda dapat melaporkan masalah melalui email support kami atau fitur bantuan di aplikasi.</div>
        </div>
    </div>

    {{-- Ubah src script JS --}}
    {{-- Pastikan script1.js ada di folder public/js atau public --}}
    <script src="{{ asset('js/script1.js') }}"></script> 
    {{-- Kalau file js kamu ada di luar folder js (langsung di public), hapus bagian 'js/' --}}
</body>

</html>