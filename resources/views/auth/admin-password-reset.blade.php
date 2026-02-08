<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .email-header img {
            height: 50px;
            width: auto;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }
        .email-header h1 {
            color: white;
            font-size: 24px;
            margin: 0;
            font-weight: 700;
        }
        .email-body {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .email-body h2 {
            color: #1E40AF;
            font-size: 20px;
            margin-bottom: 20px;
        }
        .email-body p {
            margin-bottom: 16px;
            font-size: 15px;
        }
        .user-info {
            background: #F1F5F9;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .user-info p {
            margin: 8px 0;
            font-size: 14px;
        }
        .user-info strong {
            color: #1E40AF;
        }
        .reset-button {
            display: inline-block;
            background: #1E40AF;
            color: white !important;
            text-decoration: none !important;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            margin: 20px 0;
            transition: background 0.3s ease;
        }
        .reset-button:hover {
            background: #1E3A8A;
        }
        .info-box {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #92400E;
        }
        .footer {
            background: #F8FAFC;
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #64748B;
            border-top: 1px solid #E2E8F0;
        }
        .footer p {
            margin: 8px 0;
        }
        .footer a {
            color: #1E40AF;
            text-decoration: none;
        }
        .security-note {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .security-note p {
            margin: 0;
            font-size: 14px;
            color: #7F1D1D;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Reset Password Administrator</h1>
        </div>

        <div class="email-body">
            <h2>Halo, {{ $user->nama_lengkap }}</h2>
            
            <p>
                Kami menerima permintaan untuk mereset password akun Administrator Anda di sistem SIAPRIZ.
            </p>

            <div class="user-info">
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Username:</strong> {{ $user->username }}</p>
                <p><strong>Role:</strong> Administrator</p>
            </div>

            <p>
                Untuk melanjutkan proses reset password, silakan klik tombol di bawah ini:
            </p>

            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button" 
                    style="color: white !important; text-decoration: none !important;">
                    Reset Password Sekarang
                </a>
            </div>

            <div class="info-box">
                <p>
                    <strong>⏱️ Link berlaku selama 1 jam</strong><br>
                    Link reset password akan kadaluarsa dalam 60 menit sejak email ini dikirim.
                </p>
            </div>

            <p style="font-size: 14px; color: #64748B;">
                Jika tombol tidak berfungsi, copy dan paste link berikut ke browser Anda:
            </p>
            <p style="font-size: 13px; word-break: break-all; color: #1E40AF; background: #F1F5F9; padding: 12px; border-radius: 6px;">
                {{ $resetUrl }}
            </p>

            <div class="security-note">
                <p>
                    <strong>🔒 Catatan Keamanan:</strong><br>
                    Jika Anda tidak meminta reset password, abaikan email ini atau hubungi tim support kami segera. 
                    Akun Anda tetap aman dan tidak ada perubahan yang akan terjadi.
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong>SIAPRIZ</strong></p>
            <p>Sistem Informasi Akuntansi Penjualan</p>
            <p style="margin-top: 16px;">
                Email ini dikirim otomatis, mohon tidak membalas email ini.<br>
                Untuk bantuan, hubungi: <a href="mailto:siapriz.official@gmail.com">siapriz.official@gmail.com</a>
            </p>
            <p style="margin-top: 16px; color: #94A3B8; font-size: 12px;">
                © {{ date('Y') }} SIAPRIZ. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>