<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kasir - RestoKita POS</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #ccfbf1;
            --body-bg: #f8fafc;
            --dark-color: #0f172a;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: var(--dark-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Topbar Header Konsisten dengan Dashboard */
        .login-topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.35);
        }

        /* Card Modern Style Dashboard */
        .modern-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 8px 30px -4px rgba(15, 23, 42, 0.06);
            transition: all 0.25s ease;
        }

        .btn-teal {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            border: none;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .btn-teal:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #115e59 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.3);
        }

        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border-color: #cbd5e1;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.15);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .input-group .form-control.with-toggle {
            border-radius: 0;
        }

        .btn-password-toggle {
            border-radius: 0 12px 12px 0;
            border-color: #cbd5e1;
            background-color: #f8fafc;
            color: #64748b;
        }

        .btn-password-toggle:hover {
            background-color: #f1f5f9;
            color: #334155;
        }

        /* Card Info Akun Kasir */
        .account-preview-card {
            background: linear-gradient(135deg, #f0fdfa 0%, #e6fffa 100%);
            border: 1px solid #99f6e4;
            border-radius: 16px;
            padding: 1.1rem;
        }

        .btn-autofill {
            background-color: #ffffff;
            border: 1px solid #0d9488;
            color: #0f766e;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.45rem 1rem;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-autofill:hover {
            background-color: #0d9488;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(13, 148, 136, 0.25);
        }
    </style>
</head>
<body>

    <!-- Header Navbar Seragam dengan Dashboard -->
    <header class="login-topbar">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-icon-box">
                <i class="bi bi-shop"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold tracking-tight text-dark">RestoKita</h5>
                <small class="text-teal" style="font-size: 0.75rem; color: #0d9488; font-weight: 600;">
                    UKK Junior Coder 2026 • Point of Sale
                </small>
            </div>
        </div>

        <div class="d-none d-sm-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill border">
            <i class="bi bi-clock text-primary"></i>
            <span id="loginClock" class="small fw-semibold text-secondary">Loading jam...</span>
        </div>
    </header>

    <!-- Main Container Card Login -->
    <main class="container my-auto py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5 col-xl-5">

                <div class="modern-card p-4 p-sm-5">
                    <!-- Heading -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle bg-teal-subtle text-teal mb-3" style="background-color: var(--primary-light); color: var(--primary-dark);">
                            <i class="bi bi-person-badge fs-2"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Masuk ke Sistem Kasir</h4>
                        <p class="text-muted small mb-0">Masukkan email dan password untuk mengakses kasir & dashboard</p>
                    </div>

                    <!-- Flash Message Alerts -->
                    @if(session('info'))
                        <div class="alert alert-info border-0 rounded-4 small py-2 d-flex align-items-center gap-2 mb-3 shadow-sm">
                            <i class="bi bi-info-circle-fill text-info fs-6"></i>
                            <div>{{ session('info') }}</div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-4 small py-2 d-flex align-items-center gap-2 mb-3 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-6"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                    @endif

                    <!-- Form Login -->
                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="emailInput" class="form-control @error('email') is-invalid @enderror" placeholder="kasir@restokita.com" value="{{ old('email', 'kasir@restokita.com') }}" required autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark mb-1">Kata Sandi / Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="passwordInput" class="form-control with-toggle @error('password') is-invalid @enderror" placeholder="••••••••" value="password123" required>
                                <button class="btn btn-password-toggle border border-start-0" type="button" onclick="togglePasswordVisibility()" title="Lihat/Sembunyikan Password">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck" checked>
                                <label class="form-check-label small text-muted" for="rememberCheck">
                                    Ingat sesi kasir ini
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Masuk -->
                        <button type="submit" class="btn btn-teal w-100 py-3 d-flex align-items-center justify-content-center gap-2 mb-4">
                            <i class="bi bi-box-arrow-in-right fs-5"></i> Masuk ke Dashboard
                        </button>
                    </form>

                    <!-- Kartu Informasi Akun Tunggal (Kasir RestoKita) -->
                    <div class="account-preview-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-success rounded-pill px-2 py-1 mb-1" style="font-size: 0.7rem;">
                                    AKUN RESMI
                                </span>
                                <div class="fw-bold text-dark small">Kasir RestoKita</div>
                            </div>
                            <button type="button" class="btn btn-autofill d-flex align-items-center gap-1 shadow-sm" onclick="autoFillAccount()">
                                <i class="bi bi-magic text-teal"></i> Isi Otomatis
                            </button>
                        </div>

                        <div class="small text-secondary pt-1 border-top border-teal-subtle">
                            <div class="d-flex justify-content-between py-1">
                                <span>Email Kasir:</span>
                                <strong class="text-dark">kasir@restokita.com</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Password:</span>
                                <strong class="text-dark">password123</strong>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>

    <!-- Footer Konsisten dengan Dashboard -->
    <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
        RestoKita POS &copy; 2026 • Modul Uji Kompetensi Keahlian (MUK) Pemrogram Junior SMK
    </footer>

    <!-- JavaScript Bundle & AutoFill Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live Clock
        function updateClock() {
            const clockEl = document.getElementById('loginClock');
            if (clockEl) {
                const now = new Date();
                const options = { 
                    weekday: 'short', 
                    day: 'numeric', 
                    month: 'short', 
                    year: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                };
                clockEl.textContent = now.toLocaleDateString('id-ID', options) + ' WIB';
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 1-Click AutoFill
        function autoFillAccount() {
            document.getElementById('emailInput').value = 'kasir@restokita.com';
            document.getElementById('passwordInput').value = 'password123';
        }

        // Toggle Show/Hide Password
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
