<!-- resources/views/emails/verify-email.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Verifikasi Email - SIAPRIZ</title>
    <style>
        /* Reset Styles */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        
        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* Base Styles */
        .email-wrapper {
            background-color: #F8FAFC;
            padding: 40px 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            max-width: 160px;
            height: auto;
        }
        
        /* Body */
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 28px;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 16px 0;
        }
        .intro-text {
            font-size: 16px;
            line-height: 1.6;
            color: #64748B;
            margin: 0 0 30px 0;
        }
        
        /* Button */
        .button-wrapper {
            text-align: center;
            margin: 40px 0;
        }
        .verify-button {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 14px rgba(30, 64, 175, 0.4);
        }
        
        /* Info Box */
        .info-box {
            background: #F1F5F9;
            border-left: 4px solid #1E40AF;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }
        .info-box p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
        }
        .info-box strong {
            color: #0F172A;
            font-weight: 600;
        }
        
        /* Link Box */
        .link-box {
            background: #FFFFFF;
            border: 2px dashed #CBD5E1;
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
            word-break: break-all;
        }
        .link-text {
            font-size: 12px;
            color: #1E40AF;
            font-family: monospace;
        }
        
        /* Features */
        .features {
            margin: 30px 0;
        }
        .feature-item {
            margin-bottom: 16px;
            display: table;
            width: 100%;
        }
        .feature-icon {
            display: table-cell;
            width: 40px;
            vertical-align: top;
            padding-right: 12px;
        }
        .feature-icon img {
            width: 24px;
            height: 24px;
        }
        .feature-content {
            display: table-cell;
            vertical-align: top;
        }
        .feature-title {
            font-size: 16px;
            font-weight: 600;
            color: #0F172A;
            margin: 0 0 4px 0;
        }
        .feature-text {
            font-size: 14px;
            color: #64748B;
            margin: 0;
            line-height: 1.5;
        }
        
        /* Footer */
        .email-footer {
            background: #F8FAFC;
            padding: 30px;
            text-align: center;
        }
        .footer-text {
            font-size: 14px;
            color: #64748B;
            margin: 0 0 8px 0;
            line-height: 1.6;
        }
        .footer-links {
            margin: 20px 0;
        }
        .footer-link {
            color: #1E40AF;
            text-decoration: none;
            font-size: 14px;
            margin: 0 10px;
        }
        .copyright {
            font-size: 12px;
            color: #94A3B8;
            margin: 20px 0 0 0;
        }
        
        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px !important;
            }
            .email-header {
                padding: 30px 20px !important;
            }
            .email-body {
                padding: 30px 20px !important;
            }
            .email-footer {
                padding: 20px !important;
            }
            .greeting {
                font-size: 24px !important;
            }
            .verify-button {
                padding: 14px 30px !important;
                font-size: 15px !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table role="presentation" class="email-container" width="100%" cellspacing="0" cellpadding="0" border="0">
            
            <!-- Body -->
            <tr>
                <td class="email-body">
                    <h1 class="greeting">🎉 Selamat Datang di SIAPRIZ!</h1>
                    
                    <p class="intro-text">
                        Terima kasih telah mendaftar di SIAPRIZ. Kami senang Anda bergabung! 
                        Untuk mulai menggunakan platform kami, silakan verifikasi alamat email Anda dengan mengklik tombol di bawah ini:
                    </p>
                    
                    <!-- Verify Button -->
                    <div class="button-wrapper">
                        <a href="{{ $url }}" class="verify-button">
                            ✓ Verifikasi Email Saya
                        </a>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="info-box">
                        <p>
                            <strong>⏰ Link verifikasi ini berlaku selama 60 menit.</strong><br>
                            Jika Anda tidak melakukan pendaftaran ini, abaikan email ini.
                        </p>
                    </div>
                    
                    <!-- Alternative Link -->
                    <p style="font-size: 14px; color: #64748B; margin: 30px 0 10px 0;">
                        Jika tombol di atas tidak berfungsi, salin dan tempel link berikut ke browser Anda:
                    </p>
                    <div class="link-box">
                        <a href="{{ $url }}" class="link-text">{{ $url }}</a>
                    </div>
                    
                    <!-- Features Section -->
                    <div class="features">
                        <h3 style="font-size: 18px; font-weight: 700; color: #0F172A; margin: 0 0 20px 0;">
                            Yang Bisa Anda Lakukan Setelah Verifikasi:
                        </h3>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <span style="font-size: 24px;">🛍️</span>
                            </div>
                            <div class="feature-content">
                                <p class="feature-title">Kelola Multi-Marketplace</p>
                                <p class="feature-text">Integrasikan dan kelola semua toko online Anda dari satu dashboard</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <span style="font-size: 24px;">📊</span>
                            </div>
                            <div class="feature-content">
                                <p class="feature-title">Analytics Real-Time</p>
                                <p class="feature-text">Pantau performa penjualan dengan data yang selalu up-to-date</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <span style="font-size: 24px;">🔒</span>
                            </div>
                            <div class="feature-content">
                                <p class="feature-title">Data Aman & Terlindungi</p>
                                <p class="feature-text">Sistem keamanan tingkat enterprise untuk melindungi data bisnis Anda</p>
                            </div>
                        </div>
                    </div>
                    
                    <p style="font-size: 16px; color: #0F172A; margin: 30px 0 0 0;">
                        Salam hangat,<br>
                        <strong>Tim SIAPRIZ</strong>
                    </p>
                </td>
            </tr>
            
            <!-- Footer -->
            <tr>
                <td class="email-footer">
                    <p class="footer-text">
                        <strong>SIAPRIZ</strong> - Sistem Penjualan Pintar untuk Bisnis Multi-Marketplace
                    </p>
                    
                    <div class="footer-links">
                        <a href="#" class="footer-link">Bantuan</a>
                        <a href="#" class="footer-link">Privacy Policy</a>
                        <a href="#" class="footer-link">Terms of Service</a>
                    </div>
                    
                    <p class="footer-text" style="font-size: 12px;">
                        Email ini dikirim otomatis, mohon tidak membalas email ini.<br>
                        Jika Anda memiliki pertanyaan, hubungi kami di <a href="mailto:support@siapriz.com" style="color: #1E40AF;">support@siapriz.com</a>
                    </p>
                    
                    <p class="copyright">
                        © {{ date('Y') }} SIAPRIZ. All rights reserved.
                    </p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>