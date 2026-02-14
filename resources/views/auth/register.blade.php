<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SIAPRIZ</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon-siapriz.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #1E40AF;
            --primary-pink: #FF1B8D;
            --dark: #0F172A;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-700: #334155;
            --gradient-pink: linear-gradient(135deg, #FF1B8D 0%, #FF6B9D 100%);
            --gradient-blue: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            --gradient-hero: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #FF1B8D 100%);

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-hero);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        h1, h2 { font-family: 'Sora', sans-serif; }

        .register-wrapper {
            width: 100%;
            max-width: 1200px;
            position: relative;
        }

        .register-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 650px;
            width: 100%;
            padding: 3rem;
            margin: 0 auto;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-img {
            height: 60px;
            width: auto;
            margin-bottom: 1.5rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 1.5rem;
            color: var(--gray-500);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            color: var(--primary-blue);
        }

        .back-btn svg {
            width: 18px;
            height: 18px;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
        }

        .form-header p {
            color: var(--gray-500);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.9rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #16a34a;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }

        .alert svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.95rem;
            color: var(--dark);
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        /* Style untuk select dropdown */
        .form-input select,
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.25rem;
            padding-right: 2.5rem;
            cursor: pointer;
        }

        select.form-input option {
            padding: 0.5rem;
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            padding: 0.25rem;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: var(--primary-blue);
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.3);
        }

        .btn-submit:hover {
            background: #1E3A8A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--gray-700);
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-pink);
        }

        @media (max-width: 640px) {
            body { padding: 1rem; }
            .register-container { padding: 2rem 1.5rem; }
            .form-header h2 { font-size: 1.5rem; }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-container">
            <a href="{{ url('/login') }}" class="back-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Kembali</span>
            </a>

            <div class="logo-section">
                <img src="{{ asset('images/logo-siapriz.png') }}" alt="SIAPRIZ Logo" class="logo-img">
            </div>

            <div class="form-header">
                <h2>Daftar Akun SIAPRIZ</h2>
                <p>Mulai kelola bisnis multi-marketplace Anda bersama SIAPRIZ</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan</label>
                    <input 
                        id="nama_perusahaan" 
                        name="nama_perusahaan" 
                        type="text" 
                        required 
                        class="form-input @error('nama_perusahaan') error @enderror" 
                        placeholder="Masukkan nama perusahaan"
                        value="{{ old('nama_perusahaan') }}"
                        autofocus
                    >
                    @error('nama_perusahaan')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bidang Usaha (Dropdown) -->
                <div class="form-group">
                    <label for="bidang_usaha" class="form-label">Bidang Usaha</label>
                    <select 
                        id="bidang_usaha" 
                        name="bidang_usaha" 
                        required 
                        class="form-input @error('bidang_usaha') error @enderror"
                    >
                        <option value="">Pilih Bidang Usaha</option>
                        <option value="Jasa" {{ old('bidang_usaha') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                        <option value="Dagang" {{ old('bidang_usaha') == 'Dagang' ? 'selected' : '' }}>Dagang</option>
                        <option value="Manufaktur" {{ old('bidang_usaha') == 'Manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                        <option value="Lainnya" {{ old('bidang_usaha') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('bidang_usaha')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Usaha (Text Input) -->
                <div class="form-group">
                    <label for="jenis_usaha" class="form-label">Jenis Usaha</label>
                    <input 
                        id="jenis_usaha" 
                        name="jenis_usaha" 
                        type="text" 
                        required 
                        class="form-input @error('jenis_usaha') error @enderror" 
                        placeholder="Contoh: Elektronik, Fashion, Makanan, dll"
                        value="{{ old('jenis_usaha') }}"
                    >
                    @error('jenis_usaha')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Bisnis</label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            required 
                            class="form-input @error('email') error @enderror" 
                            placeholder="email@perusahaan.com"
                            value="{{ old('email') }}"
                        >
                        @error('email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            required 
                            class="form-input @error('username') error @enderror" 
                            placeholder="Masukkan username"
                            value="{{ old('username') }}"
                        >
                        @error('username')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-wrapper">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="form-input @error('password') error @enderror" 
                                placeholder="Minimal 8 karakter"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <svg id="eye-icon-password" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Ulangi Password</label>
                        <div class="input-wrapper">
                            <input 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                type="password" 
                                required 
                                class="form-input" 
                                placeholder="Masukkan ulang password"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation')">
                                <svg id="eye-icon-password_confirmation" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    Daftar
                </button>
            </form>

            <div class="login-link">
                <span>Sudah punya akun?</span>
                <a href="/login">Masuk</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-icon-' + fieldId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }
    </script>
</body>
</html>