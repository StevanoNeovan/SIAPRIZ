<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIAPRIZ - Smart Sales Performance System for Multi-Marketplace Businesses</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon-siapriz.png') }}">
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
            --gradient-hero: linear-gradient(135deg, #1E40AF 0%, #3B82F6 50%, #FF1B8D 100%);
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-700: #334155;
            --gray-800: #1E293B;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            background: var(--light);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Sora', sans-serif;
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: transparent;
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.4s ease;
        }

        nav.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-bottom: 1px solid var(--gray-200);
        }


        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .nav-container {
            max-width: 1250px;
            margin: 0 auto;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--gradient-pink);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            font-size: 0.95rem;
            border: none;
        }

        .btn-outline {
            background: var(--primary-blue);
            color: white;
            border: 2px solid var(--primary-blue);
        }

        .btn-outline:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.3);
        }

        .btn-primary {
            background: var(--gradient-pink);
            color: white;
            border: none;
            box-shadow: 0 10px 30px rgba(255, 27, 141, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(255, 27, 141, 0.4);
        }

        .logo-img {
            height: 40px;
            width: auto;
            display: block;
        }


        /* Hero Section */
        .hero {
            padding: 8rem 2rem 6rem;
            background: var(--gradient-hero);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 27, 141, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.2) 0%, transparent 50%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            color: white;
            font-weight: 600;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

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

        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.95);
            max-width: 800px;
            margin: 0 auto 3rem;
            line-height: 1.7;
            font-weight: 400;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .hero-cta {
            display: flex;
            gap: 1.2rem;
            justify-content: flex-start;   /* geser ke kiri */
            align-items: center;
            margin-top: 1.8rem;
        }


        .btn-hero {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .btn-white {
            background: white;
            color: var(--primary-blue);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .btn-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        /* Section Styles */
        section {
            padding: 6rem 2rem;
            position: relative;
        }

        .section-light {
            background: var(--light);
        }

        .section-gray {
            background: var(--gray-100);
        }

        .section-dark {
            background: var(--dark);
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-tag {
            display: inline-block;
            padding: 0.5rem 1.2rem;
            background: rgba(255, 27, 141, 0.1);
            color: var(--primary-pink);
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--gray-700);
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* Problem Section */
        .problem-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .problem-visual {
            background: var(--gradient-blue);
            padding: 3rem;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(30, 64, 175, 0.3);
        }

        .problem-visual::before {
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

        .problem-icons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .problem-icon-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
            color: white;
            transition: transform 0.3s ease;
        }

        .problem-icon-card:hover {
            transform: translateY(-5px);
        }

        .problem-icon-card svg {
            width: 50px;
            height: 50px;
            margin-bottom: 1rem;
        }

        .problem-list {
            list-style: none;
        }

        .problem-list li {
            padding: 1.2rem 0;
            border-bottom: 1px solid var(--gray-200);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .problem-list li:last-child {
            border-bottom: none;
        }

        .problem-list li::before {
            content: '✕';
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: rgba(255, 27, 141, 0.1);
            color: var(--primary-pink);
            border-radius: 50%;
            font-weight: 700;
            flex-shrink: 0;
        }

        .problem-conclusion {
            margin-top: 2rem;
            padding: 1.5rem;
            background: rgba(255, 27, 141, 0.05);
            border-left: 4px solid var(--primary-pink);
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-pink);
        }

        /* Solution Section */
        .solution-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .solution-text h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary-blue);
        }

        .solution-text p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--gray-700);
            margin-bottom: 1rem;
        }

        .solution-highlight {
            background: linear-gradient(135deg, rgba(255, 27, 141, 0.1) 0%, rgba(30, 64, 175, 0.1) 100%);
            padding: 2rem;
            border-radius: 20px;
            margin-top: 2rem;
            border: 2px solid rgba(255, 27, 141, 0.2);
        }

        .solution-highlight p {
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        /* Benefits Grid */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .benefit-card {
            background: white;
            padding: 2.5rem;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-pink);
        }

        .benefit-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-pink);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .benefit-card h4 {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .benefit-card p {
            color: var(--gray-700);
            line-height: 1.7;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12);
        }

        .feature-number {
            width: 50px;
            height: 50px;
            background: var(--gradient-blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.3rem;
            margin: 0 auto 1.5rem;
        }

        .feature-card h4 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--gray-700);
            line-height: 1.7;
        }

        /* Steps Section */
        .steps-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            position: relative;
        }

        .steps-container::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 12%;
            right: 12%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue) 0%, var(--primary-pink) 100%);
            z-index: 0;
        }

        .step-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .step-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: var(--gradient-pink);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(255, 27, 141, 0.3);
        }

        .step-card h4 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            color: var(--dark);
        }

        .step-card p {
            color: var(--gray-700);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Target Audience */
        .audience-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .audience-card {
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.05) 0%, rgba(255, 27, 141, 0.05) 100%);
            padding: 2.5rem;
            border-radius: 25px;
            border: 2px solid rgba(255, 27, 141, 0.2);
            transition: all 0.3s ease;
        }

        .audience-card:hover {
            transform: scale(1.03);
            border-color: var(--primary-pink);
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.1) 0%, rgba(255, 27, 141, 0.1) 100%);
        }

        .audience-card h4 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .audience-card h4::before {
            content: '✓';
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            background: var(--gradient-pink);
            color: white;
            border-radius: 50%;
            font-weight: 700;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-hero);
            text-align: center;
            padding: 5rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 70%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-section h2 {
            font-size: 3rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .cta-section p {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer Updated */
            footer {
            background: var(--dark);
            color: white;
            padding: 4rem 2rem 2rem;
        }

        .footer-main {
            margin-bottom: 3rem;
        }

        .footer-columns {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
            margin-bottom: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-column h4 {
            font-size: 0.85rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 1.5rem;
            letter-spacing: 0.5px;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.8rem;
        }

        .footer-column ul li a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .footer-column ul li a:hover {
            color: var(--primary-pink);
        }

        .footer-locations {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .location h5 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            color: white;
        }

        .location p {
            font-size: 0.85rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-logo-img {
            height: 50px;
            width: auto;
        }

        .footer-social {
            display: flex;
            gap: 1.5rem;
        }

        .footer-social a {
            color: rgba(255, 255, 255, 0.6);
            transition: color 0.3s ease;
        }

        .footer-social a:hover {
            color: var(--primary-pink);
        }

        .footer-social svg {
            width: 24px;
            height: 24px;
        }

        .footer-copyright {
            text-align: center;
            padding-top: 2rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        /* Responsive Footer */
        @media (max-width: 1024px) {
            .footer-columns,
            .footer-locations {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
                .footer-columns,
                .footer-locations {
                grid-template-columns: 1fr;
            }
    
            .footer-bottom {
                flex-direction: column;
                gap: 2rem;
                text-align: center;
            }
        }


        /* Responsive */
        @media (max-width: 1024px) {
            .hero h1 {
                font-size: 3rem;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .steps-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .steps-container::before {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .nav-container {
                padding: 1rem;
            }

            .hero {
                padding: 8rem 1.5rem 4rem;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-cta {
                flex-direction: column;
                gap: 1rem;
            }

            .problem-content,
            .solution-content {
                grid-template-columns: 1fr;
            }

            .benefits-grid,
            .features-grid,
            .audience-grid {
                grid-template-columns: 1fr;
            }

            .steps-container {
                grid-template-columns: 1fr;
            }

            .section-title {
                font-size: 2rem;
            }

            .cta-section h2 {
                font-size: 2rem;
            }
        }

        


        /* Scroll animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .hero-split {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 4rem;
            align-items: center;
            text-align: left;
        }

        .hero-text h1 {
            font-size: 3.8rem;
        }

        .hero-image img {
            width: 100%;
            max-width: none;
            border-radius: 0;     /* hilangkan sudut melengkung */
            box-shadow: none;    /* hilangkan bayangan */
            animation: float 6s ease-in-out infinite; /* boleh tetap pakai */
        }


        @keyframes float {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .hero-split {
            grid-template-columns: 1fr;
            text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo-container">
                <img src="{{ asset('images/logo-siapriz.png') }}" alt="SIAPRIZ Logo" class="logo-img">
            </div>
            <div class="nav-buttons">
                <a href="/login" class="btn btn-outline">Sign In</a>
                <a href="/register" class="btn btn-primary">Registrasi</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container hero-split">
    <div class="hero-text">
        <div class="hero-badge">🚀 Smart Sales Performance System</div>
        <h1>SIAPRIZ</h1>
        <p class="hero-subtitle">
            Kelola, analisis, dan pahami kinerja penjualan dari berbagai marketplace dalam satu dashboard yang simpel, cepat, dan siap pakai.
        </p>
        <div class="hero-cta">
            <a href="/login" class="btn btn-hero btn-white">Mulai Sekarang</a>
            <a href="#masalah-utama" class="btn btn-hero btn-primary">Pelajari Lebih Lanjut</a>
        </div>
    </div>

    <div class="hero-image">
        <img src="{{ asset('images/hero.png') }}" alt="Dashboard SIAPRIZ">
    </div>

    </section>

    <!-- Problem Section -->
    <section id="masalah-utama" class="section-light">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">Masalah Utama</span>
                <h2 class="section-title">Data Penjualan Banyak, Tapi Tidak Pernah "Bicara"</h2>
                <p class="section-subtitle">Berjualan di banyak marketplace itu bagus. Tapi datanya sering bermasalah.</p>
            </div>
            <div class="problem-content">
                <div class="problem-visual fade-in">
                    <div class="problem-icons">
                        <div class="problem-icon-card">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>File Tersebar</div>
                        </div>
                        <div class="problem-icon-card">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                            <div>Format Berbeda</div>
                        </div>
                        <div class="problem-icon-card">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <div>Sulit Dibandingkan</div>
                        </div>
                        <div class="problem-icon-card">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>Memakan Waktu</div>
                        </div>
                    </div>
                </div>
                <div class="fade-in">
                    <ul class="problem-list">
                        <li>Tersebar di banyak file</li>
                        <li>Format berbeda-beda</li>
                        <li>Sulit dibandingkan</li>
                        <li>Memakan waktu untuk dianalisis</li>
                    </ul>
                    <div class="problem-conclusion">
                        Akhirnya, keputusan bisnis dibuat berdasarkan feeling, bukan data.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Solution Section -->
    <section class="section-gray">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">Solusi Kami</span>
                <h2 class="section-title">SIAPRIZ Mengubah File Penjualan Jadi Insight Bisnis</h2>
            </div>
            <div class="solution-content">
                <div class="solution-text fade-in">
                    <h3>Sistem Informasi Akuntansi Penjualan</h3>
                    <p>SIAPRIZ adalah aplikasi Sistem Informasi Akuntansi Penjualan yang membantu bisnis mengolah data penjualan dari berbagai marketplace menjadi laporan dan dashboard kinerja yang mudah dipahami.</p>
                    <div class="solution-highlight">
                        <p>💡 Tanpa integrasi rumit. Cukup upload data transaksi, dan SIAPRIZ mengerjakan sisanya.</p>
                    </div>
                    <br><br>
                    <h3>Kenapa SIAPRIZ?</h3>
                    <p>Lebih Praktis. Lebih Cepat. Lebih Siap Digunakan. SIAPRIZ dirancang agar siapa pun bisa memahami kinerja penjualannya sendiri.</p>
                </div>
                <div class="fade-in">
                    <div class="benefits-grid">
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h4>Praktis</h4>
                            <p>Tidak perlu koneksi API marketplace yang rumit</p>
                        </div>
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h4>Aman</h4>
                            <p>Data tetap di tangan Anda, privasi terjaga</p>
                        </div>
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <h4>Mudah</h4>
                            <p>Tanpa latar belakang teknis, siapa pun bisa pakai</p>
                        </div>
                        <div class="benefit-card">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h4>Cocok UMKM</h4>
                            <p>Dirancang untuk bisnis digital dan tim kecil</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section-light">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">Fitur Unggulan</span>
                <h2 class="section-title">Semua yang Anda Butuhkan untuk Mengukur Penjualan</h2>
            </div>
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-number">1</div>
                    <h4>Upload Multi-Marketplace</h4>
                    <p>Unggah file transaksi dari berbagai marketplace dengan format yang disesuaikan</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-number">2</div>
                    <h4>Dashboard Kinerja</h4>
                    <p>Pantau omzet, jumlah transaksi, produk terlaris, dan tren penjualan dalam satu tampilan</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-number">3</div>
                    <h4>Laporan Otomatis</h4>
                    <p>Dapatkan laporan penjualan periodik tanpa rekap manual</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-number">4</div>
                    <h4>Analisis Kinerja</h4>
                    <p>Bandingkan performa penjualan antar marketplace dan antar periode</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-number">5</div>
                    <h4>Pencatatan Akuntansi</h4>
                    <p>Data lebih rapi, konsisten, dan siap dianalisis kapan saja</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-number">6</div>
                    <h4>Insight Tren</h4>
                    <p>Temukan pola penjualan dan peluang pertumbuhan bisnis Anda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section-gray">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">Cara Kerja</span>
                <h2 class="section-title">Sesederhana 4 Langkah</h2>
                <p class="section-subtitle">Tanpa instalasi rumit. Tanpa proses teknis yang memusingkan.</p>
            </div>
            <div class="steps-container">
                <div class="step-card fade-in">
                    <div class="step-number">1</div>
                    <h4>Download Data</h4>
                    <p>Download data penjualan dari marketplace favorit Anda</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">2</div>
                    <h4>Upload File</h4>
                    <p>Upload file ke SIAPRIZ dengan sekali klik</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">3</div>
                    <h4>Proses Otomatis</h4>
                    <p>Data diproses otomatis oleh sistem kami</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">4</div>
                    <h4>Lihat Dashboard</h4>
                    <p>Lihat dashboard & laporan kinerja penjualan real-time</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="section-dark">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag" style="background: rgba(255, 255, 255, 0.2); color: white;">Manfaat Nyata</span>
                <h2 class="section-title">Bukan Sekadar Data, Tapi Keputusan Lebih Tepat</h2>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4>Hemat Waktu</h4>
                    <p>Otomatisasi pengolahan data menghemat waktu hingga 80% dibanding rekap manual</p>
                </div>
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4>Minim Kesalahan</h4>
                    <p>Sistem terstandar mengurangi human error dalam pencatatan dan perhitungan</p>
                </div>
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h4>Transparansi Performa</h4>
                    <p>Lihat dengan jelas marketplace dan produk mana yang paling menguntungkan</p>
                </div>
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h4>Evaluasi Strategi</h4>
                    <p>Data akurat menjadi dasar untuk evaluasi dan perbaikan strategi bisnis</p>
                </div>
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <h4>Siap Scale-Up</h4>
                    <p>Infrastruktur data yang kuat memudahkan ekspansi dan digitalisasi bisnis</p>
                </div>
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg fill="none" stroke="white" viewBox="0 0 24 24" style="width: 30px; height: 30px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h4>Pengambilan Keputusan Lebih Cepat</h4>
                    <p>Insight real-time membantu merespons tren penjualan tanpa menunggu laporan manual</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience -->
    <section class="section-light">
        <div class="container">
            <div class="section-header fade-in">
                <span class="section-tag">Cocok untuk Siapa?</span>
                <h2 class="section-title">Dibangun untuk Bisnis yang Ingin Tumbuh</h2>
            </div>
            <div class="audience-grid">
                <div class="audience-card fade-in">
                    <h4>UMKM & Brand Lokal</h4>
                    <p>Sempurna untuk bisnis kecil dan menengah yang berjualan di berbagai platform online dan membutuhkan kontrol penuh atas data penjualan mereka.</p>
                </div>
                <div class="audience-card fade-in">
                    <h4>Seller Multi-Marketplace</h4>
                    <p>Ideal untuk seller yang aktif di Tokopedia, Shopee, Lazada, Bukalapak, dan marketplace lainnya yang ingin melihat performa secara menyeluruh.</p>
                </div>
                <div class="audience-card fade-in">
                    <h4>Inkubator & Pendamping UMKM</h4>
                    <p>Cocok untuk lembaga yang mendampingi UMKM dalam digitalisasi bisnis dan membutuhkan tools sederhana namun powerful.</p>
                </div>
                <div class="audience-card fade-in">
                    <h4>Program Digitalisasi Bisnis</h4>
                    <p>Mendukung program-program pemerintah atau swasta yang fokus pada transformasi digital UMKM Indonesia.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Siap Mengubah Data Jadi Keputusan Bisnis?</h2>
            <p>Bergabunglah dengan ribuan bisnis yang sudah mengoptimalkan penjualan mereka dengan SIAPRIZ</p>
            <div class="footer-cta">
                <a href="/login" class="btn btn-hero btn-white">Mulai Gratis Sekarang</a>
                <a href="#" class="btn btn-hero btn-primary">Hubungi Sales</a>
            </div>
        </div>
    </section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-main">
            <div class="footer-columns">
                <!-- Kolom 1: Fitur -->
                <div class="footer-column">
                    <h4>FITUR</h4>
                    <ul>
                        <li><a href="#">Upload Multi-Marketplace</a></li>
                        <li><a href="#">Dashboard Kinerja</a></li>
                        <li><a href="#">Laporan Otomatis</a></li>
                        <li><a href="#">Analisis Kinerja</a></li>
                        <li><a href="#">Pencatatan Akuntansi</a></li>
                        <li><a href="#">Insight Tren</a></li>
                    </ul>
                </div>

                <!-- Kolom 2: Solusi Bisnis -->
                <div class="footer-column">
                    <h4>SOLUSI BISNIS</h4>
                    <ul>
                        <li><a href="#">Solusi untuk Startup</a></li>
                        <li><a href="#">Solusi untuk UMKM</a></li>
                        <li><a href="#">Solusi untuk Large Enterprise</a></li>
                        <li><a href="#">Solusi untuk Seller Multi-Marketplace</a></li>
                        <li><a href="#">Solusi untuk Inkubator</a></li>
                        <li><a href="#">Solusi untuk Program Digitalisasi</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Insights -->
                <div class="footer-column">
                    <h4>INSIGHTS</h4>
                    <ul>
                        <li><a href="#">Artikel & Blog</a></li>
                        <li><a href="#">Studi Kasus</a></li>
                        <li><a href="#">E-book & Whitepaper</a></li>
                        <li><a href="#">Resources & Panduan</a></li>
                    </ul>
                </div>

                <!-- Kolom 4: Perusahaan -->
                <div class="footer-column">
                    <h4>PERUSAHAAN</h4>
                    <ul>
                        <li><a href="#">Tentang SIAPRIZ</a></li>
                        <li><a href="#">We're Hiring</a></li>
                        <li><a href="#">Referral Program</a></li>
                        <li><a href="#">Hubungi Support</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                    </ul>
                </div>
            </div>

            <!-- Lokasi Kantor -->
            <div class="footer-locations">
                <div class="location">
                    <h5>Jakarta</h5>
                    <p>Mid Plaza 2, Jl. Jenderal Sudirman No.4, Kel. Karet Tengsin, Kec. Tanah Abang, Kota Jakarta Pusat, Jakarta 10220</p>
                </div>
                <div class="location">
                    <h5>Surabaya</h5>
                    <p>Jl. Ngagel Jaya Selatan No.158, Kel. Pucang Sewu, Kec. Gubeng, Kota Surabaya, Jawa Timur 60284</p>
                </div>
                <div class="location">
                    <h5>Bandung</h5>
                    <p>Jl. A. Yani No.271A, Cihapit, Kec. Bandung Wetan, Kota Bandung, Jawa Barat 40114</p>
                </div>
                <div class="location">
                    <h5>Medan</h5>
                    <p>Jl. KH. Zainul Arifin No.152, Kel. Madras Hulu, Kec. Medan Polonia, Kota Medan, Sumatera Utara 20151</p>
                </div>
            </div>
        </div>

        <!-- Logo dan Social Media -->
        <div class="footer-bottom">
            <div class="footer-logo">
                <img src="{{ asset('images/logo-siapriz-white.png') }}" alt="SIAPRIZ Logo" class="footer-logo-img">
            </div>
            
            <div class="footer-social">
                <a href="#" aria-label="Instagram"><svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                <a href="#" aria-label="LinkedIn"><svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
                <a href="#" aria-label="Facebook"><svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                <a href="#" aria-label="YouTube"><svg fill="currentColor" viewBox="0 0 24 24" width="24" height="24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
            </div>
        </div>

        <div class="footer-copyright">
            © 2026 SIAPRIZ. All rights reserved.
        </div>
    </div>
</footer>

    <!-- Scroll Animation Script -->
    <script>
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>

<script>
    const nav = document.querySelector('nav');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 80) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });
</script>
