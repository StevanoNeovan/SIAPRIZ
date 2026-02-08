{{-- resources/views/auth/ceo-forgot-password.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - CEO SIAPRIZ</title>
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
            --dark: #0F172A;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-700: #334155;
            --gradient-hero: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #FF1B8D 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-hero);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        h1, h2 { font-family: 'Sora', sans-serif; }

        .forgot-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 3rem;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo-img {
            height: 60px;
            width: auto;
            margin-bottom: 1rem;
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--gray-500);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.5rem;
        }

        .alert {
            padding: 0.875rem;
            border-radius: 12px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.875rem;
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
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert ul {
            margin: 0;
            padding-left: 1.25rem;
        }

        .alert li {
            margin-bottom: 0.25rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.4rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.9rem;
            color: var(--dark);
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.8rem;
            margin-top: 0.4rem;
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.3);
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background: #1E3A8A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .back-link {
            text-align: center;
            margin-top: 1.25rem;
        }

        .back-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #1E3A8A;
        }

        .info-box {
            background: var(--gray-100);
            border-left: 4px solid var(--primary-blue);
            padding: 0.875rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
        }

        .info-box p {
            font-size: 0.825rem;
            color: var(--gray-700);
            line-height: 1.5;
            margin: 0;
        }

        .info-box strong {
            color: var(--dark);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
        }

        @media (max-width: 640px) {
            body { padding: 1rem; }
            .forgot-container { 
                padding: 2rem 1.5rem;
                max-width: 100%;
            }
            .form-header h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <div class="logo-section">
            <img src="{{ asset('images/logo-siapriz.png') }}" alt="SIAPRIZ Logo" class="logo-img">
        </div>

        <div class="form-header">
            <h2>Reset Password CEO</h2>
            <p>Masukkan email, secret key, dan password baru untuk mereset password CEO Anda</p>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="info-box">
            <p>
                <strong>Catatan:</strong> Secret key adalah kunci rahasia CEO yang telah ditetapkan sebelumnya. 
                Pastikan email dan secret key yang Anda masukkan sudah benar. Password baru minimal 8 karakter.
            </p>
        </div>

        <form action="{{ route('ceo.password.reset') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="form-label">Email CEO</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        required 
                        class="form-input @error('email') error @enderror" 
                        placeholder="ceo@example.com"
                        value="{{ old('email') }}"
                        autofocus
                    >
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="secret" class="form-label">Secret Key CEO</label>
                    <input 
                        id="secret" 
                        name="secret" 
                        type="password" 
                        required 
                        class="form-input @error('secret') error @enderror" 
                        placeholder="Masukkan secret key"
                    >
                    @error('secret')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password" class="form-label">Password Baru</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        class="form-input @error('password') error @enderror" 
                        placeholder="Minimal 8 karakter"
                    >
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        class="form-input @error('password_confirmation') error @enderror" 
                        placeholder="Konfirmasi password"
                    >
                    @error('password_confirmation')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Reset Password
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>