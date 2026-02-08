<!-- resources/views/auth/verify-email.blade.php -->
<!DOCTYPE html>
<html lang="id" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - SIAPRIZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-pink: #FF1B8D;
            --primary-blue: #1E40AF;
            --gradient-pink: linear-gradient(135deg, #FF1B8D 0%, #FF6B9D 100%);
            --gradient-blue: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            --gradient-success: linear-gradient(135deg, #10B981 0%, #34D399 100%);
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-700: #334155;
            --success: #10B981;
            --success-light: #D1FAE5;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Sora', sans-serif;
        }

        .verify-wrapper {
            width: 100%;
            max-width: 1000px;
            position: relative;
        }

        /* Language Switcher */
        .language-switcher {
            position: absolute;
            top: -60px;
            right: 0;
            display: flex;
            gap: 0.5rem;
            background: white;
            padding: 0.5rem;
            border-radius: 50px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .lang-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: transparent;
            color: var(--gray-500);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .lang-btn.active {
            background: var(--primary-blue);
            color: white;
        }

        .lang-btn:hover:not(.active) {
            color: var(--primary-blue);
        }

        /* Main Container */
        .verify-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            min-height: 500px;
        }

        /* Left Side - Illustration */
        .verify-left {
            background: var(--gradient-success);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .verify-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .illustration-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        /* Email Icon */
        .email-icon-wrapper {
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            position: relative;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .email-icon-wrapper::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: ripple 2s ease-out infinite;
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .email-icon {
            width: 90px;
            height: 90px;
            color: white;
        }

        .illustration-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }

        .illustration-text {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            max-width: 350px;
            margin: 0 auto;
        }

        /* Right Side - Content */
        .verify-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .verify-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .verify-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .verify-emoji {
            font-size: 2rem;
            animation: wave 1s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }

        .verify-subtitle {
            color: var(--gray-500);
            font-size: 0.95rem;
            line-height: 1.6;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Alert Success */
        .alert-success {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            background: var(--success-light);
            border: 1px solid var(--success);
            color: #047857;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success svg {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
        }

        /* Info Box */
        .info-box {
            background: var(--gray-100);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            width: 24px;
            height: 24px;
            color: var(--primary-blue);
            flex-shrink: 0;
            margin-top: 2px;
        }

        .info-text {
            font-size: 0.9rem;
            color: var(--gray-700);
            line-height: 1.5;
        }

        .info-text strong {
            color: var(--dark);
            font-weight: 600;
        }

        /* Buttons */
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn-resend {
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-resend:hover {
            background: #1E3A8A;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
        }

        .btn-resend:active {
            transform: translateY(0);
        }

        .btn-resend svg {
            width: 20px;
            height: 20px;
        }

        .btn-logout {
            width: 100%;
            padding: 1rem;
            background: transparent;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            border-color: var(--gray-400);
            background: var(--gray-100);
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 2rem 0 1.5rem;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 50%;
            height: 1px;
            background: var(--gray-200);
        }

        .divider-text {
            display: inline-block;
            background: white;
            padding: 0 1rem;
            color: var(--gray-400);
            font-size: 0.875rem;
            position: relative;
            z-index: 1;
        }

        /* Help Text */
        .help-text {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--gray-500);
        }

        .help-text a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .help-text a:hover {
            color: var(--primary-pink);
        }

        /* Responsive */
        @media (max-width: 968px) {
            .verify-container {
                grid-template-columns: 1fr;
            }

            .verify-left {
                display: none;
            }

            .language-switcher {
                top: 20px;
                right: 20px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .verify-right {
                padding: 2rem 1.5rem;
            }

            .verify-title {
                font-size: 1.5rem;
            }

            .language-switcher {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 100;
            }
        }
    </style>
</head>
<body>
    <div class="verify-wrapper">
        <!-- Language Switcher -->
        <div class="language-switcher">
            <button class="lang-btn" onclick="switchLanguage('id')" data-lang="id">ID</button>
            <button class="lang-btn active" onclick="switchLanguage('en')" data-lang="en">EN</button>
            <button class="lang-btn" onclick="switchLanguage('cn')" data-lang="cn">CN</button>
        </div>

        <div class="verify-container">
            <!-- Left Side - Illustration -->
            <div class="verify-left">
                <div class="illustration-content">
                    <div class="email-icon-wrapper">
                        <svg class="email-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    
                    <h3 class="illustration-title" data-translate="illustration_title">
                        Hampir Selesai!
                    </h3>
                    <p class="illustration-text" data-translate="illustration_text">
                        Verifikasi email adalah langkah penting untuk keamanan akun Anda dan memastikan Anda mendapatkan update penting dari SIAPRIZ.
                    </p>
                </div>
            </div>

            <!-- Right Side - Content -->
            <div class="verify-right">
                <!-- Header -->
                <div class="verify-header">
                    <h2 class="verify-title">
                        <span data-translate="verify_title">Cek Email Kamu</span>
                        <span class="verify-emoji">📧</span>
                    </h2>
                    <p class="verify-subtitle" data-translate="verify_subtitle">
                        Kami sudah mengirim link verifikasi ke email kamu. Klik link tersebut untuk mengaktifkan akun SIAPRIZ.
                    </p>
                </div>

                <!-- Alert Success -->
                @if (session('status') == 'verification-link-sent')
                    <div class="alert-success">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span data-translate="alert_success">Link verifikasi berhasil dikirim ulang!</span>
                    </div>
                @endif

                <!-- Info Box -->
                <div class="info-box">
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="info-text" data-translate="info_check_inbox">
                            <strong>Cek folder Inbox</strong> atau folder Spam/Junk email Anda
                        </p>
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="info-text" data-translate="info_wait">
                            <strong>Tunggu beberapa menit</strong> jika email belum muncul
                        </p>
                    </div>
                    <div class="info-item">
                        <svg class="info-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="info-text" data-translate="info_resend">
                            <strong>Tidak menerima email?</strong> Klik tombol kirim ulang di bawah
                        </p>
                    </div>
                </div>

                <!-- Resend Form -->
                <div class="button-group">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn-resend">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span data-translate="btn_resend">Kirim Ulang Email Verifikasi</span>
                        </button>
                    </form>

                    <div class="divider">
                        <span class="divider-text" data-translate="or">ATAU</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout" data-translate="btn_logout">
                            Keluar dan Login dengan Email Lain
                        </button>
                    </form>
                </div>

                <!-- Help Text -->
                <div class="help-text">
                    <span data-translate="help_text">Butuh bantuan?</span>
                    <a href="#" data-translate="help_link">Hubungi Support</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Translation data
        const translations = {
            id: {
                illustration_title: 'Hampir Selesai!',
                illustration_text: 'Verifikasi email adalah langkah penting untuk keamanan akun Anda dan memastikan Anda mendapatkan update penting dari SIAPRIZ.',
                verify_title: 'Cek Email Kamu',
                verify_subtitle: 'Kami sudah mengirim link verifikasi ke email kamu. Klik link tersebut untuk mengaktifkan akun SIAPRIZ.',
                alert_success: 'Link verifikasi berhasil dikirim ulang!',
                info_check_inbox: '<strong>Cek folder Inbox</strong> atau folder Spam/Junk email Anda',
                info_wait: '<strong>Tunggu beberapa menit</strong> jika email belum muncul',
                info_resend: '<strong>Tidak menerima email?</strong> Klik tombol kirim ulang di bawah',
                btn_resend: 'Kirim Ulang Email Verifikasi',
                or: 'ATAU',
                btn_logout: 'Keluar dan Login dengan Email Lain',
                help_text: 'Butuh bantuan?',
                help_link: 'Hubungi Support'
            },
            en: {
                illustration_title: 'Almost Done!',
                illustration_text: 'Email verification is an important step for your account security and ensures you receive important updates from SIAPRIZ.',
                verify_title: 'Check Your Email',
                verify_subtitle: 'We have sent a verification link to your email. Click the link to activate your SIAPRIZ account.',
                alert_success: 'Verification link resent successfully!',
                info_check_inbox: '<strong>Check your Inbox</strong> or Spam/Junk folder',
                info_wait: '<strong>Wait a few minutes</strong> if the email hasn\'t appeared',
                info_resend: '<strong>Didn\'t receive the email?</strong> Click the resend button below',
                btn_resend: 'Resend Verification Email',
                or: 'OR',
                btn_logout: 'Logout and Login with Another Email',
                help_text: 'Need help?',
                help_link: 'Contact Support'
            },
            cn: {
                illustration_title: '快完成了！',
                illustration_text: '电子邮件验证是保护您账户安全的重要步骤，并确保您收到SIAPRIZ的重要更新。',
                verify_title: '检查您的电子邮件',
                verify_subtitle: '我们已向您的电子邮件发送了验证链接。点击链接激活您的SIAPRIZ账户。',
                alert_success: '验证链接重新发送成功！',
                info_check_inbox: '<strong>检查您的收件箱</strong>或垃圾邮件文件夹',
                info_wait: '<strong>等待几分钟</strong>如果邮件尚未出现',
                info_resend: '<strong>没有收到邮件？</strong>点击下面的重新发送按钮',
                btn_resend: '重新发送验证邮件',
                or: '或',
                btn_logout: '登出并使用其他电子邮件登录',
                help_text: '需要帮助？',
                help_link: '联系支持'
            }
        };

        // Get current language from localStorage or default to 'en'
        let currentLang = localStorage.getItem('language') || 'en';

        // Initialize language on page load
        document.addEventListener('DOMContentLoaded', function() {
            switchLanguage(currentLang);
        });

        function switchLanguage(lang) {
            currentLang = lang;
            localStorage.setItem('language', lang);
            
            // Update active button
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.getAttribute('data-lang') === lang) {
                    btn.classList.add('active');
                }
            });

            // Update HTML lang attribute
            document.getElementById('html-root').setAttribute('lang', lang);

            // Update all translatable elements
            document.querySelectorAll('[data-translate]').forEach(element => {
                const key = element.getAttribute('data-translate');
                if (translations[lang][key]) {
                    // Check if translation contains HTML
                    if (translations[lang][key].includes('<strong>')) {
                        element.innerHTML = translations[lang][key];
                    } else {
                        element.textContent = translations[lang][key];
                    }
                }
            });
        }
    </script>
</body>
</html>