<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Admin SIAPRIZ</title>
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
            --gradient-pink: linear-gradient(135deg, #FF1B8D 0%, #FF6B9D 100%);
            --gradient-blue: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            --gradient-hero: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #FF1B8D 100%);
        }

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

        .forgot-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 480px;
            width: 100%;
            padding: 3rem;
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
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
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

        .form-input.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.5rem;
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

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #1E3A8A;
        }

        .info-box {
            background: var(--gray-100);
            border-left: 4px solid var(--primary-blue);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .info-box p {
            font-size: 0.875rem;
            color: var(--gray-700);
            line-height: 1.6;
        }

        @media (max-width: 640px) {
            body { padding: 1rem; }
            .forgot-container { padding: 2rem 1.5rem; }
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
            <h2>Lupa Password</h2>
            <p>Masukkan email Administrator Anda untuk menerima link reset password</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <div class="info-box">
            <p>
                <strong>Catatan:</strong> Link reset password akan dikirim ke email Anda dan berlaku selama 1 jam. 
                Pastikan email yang Anda masukkan adalah email Administrator yang terdaftar.
            </p>
        </div>

        <form action="{{ route('admin.password.send') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Administrator</label>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    required 
                    class="form-input @error('email') error @enderror" 
                    placeholder="admin@example.com"
                    value="{{ old('email') }}"
                    autofocus
                >
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-submit">
                Kirim Link Reset Password
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>