<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'দৈনিক সংবাদ') . ' — ই-পেপার')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Noto+Serif+Bengali:wght@400;600;700&display=swap"
        rel="stylesheet">

    @if($settings->site_favicon)
        <link rel="icon" href="{{ asset('storage/' . $settings->site_favicon) }}">
    @endif
    <style>
        :root {
            --ink: #1a1008;
            --paper: #f5f0e8;
            --paper-dark: #ede5d0;
            --accent: #c0392b;
            --accent-light: #e74c3c;
            --gold: #b8860b;
            --blue: #1a6faf;
        }

        /* Constrain all header/nav/footer content to same width as page */
        .site-container {
            max-width: 1440px;
            /* match ep-wrap max-width in index.blade.php */
            margin: 0 auto;
            width: 100%;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Serif Bengali', serif;
            color: var(--ink);
            background: #f0ebe0;
        }

        /* ===== TOP BAR ===== */
        .topbar {
            background: var(--blue);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            height: 36px;
            overflow: hidden;
        }

        .topbar-social {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 11px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .topbar-social a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .topbar-date {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.85);
            font-family: 'Noto Serif Bengali', serif;
            white-space: nowrap;
        }

        /* ===== HEADER ===== */
        .site-header {
            background: #fff;
            border-bottom: 3px solid var(--accent);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .header-logo a {
            text-decoration: none;
        }

        .logo-name {
            font-family: 'Noto Serif Bengali', serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
            letter-spacing: -1px;
        }

        .logo-subtitle {
            font-size: 10px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #888;
            font-family: 'Playfair Display', serif;
            margin-top: 4px;
        }

        .header-ad-banner {
            flex: 1;
            max-width: 468px;
            height: 64px;
            background: #e8e0cc;
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 12px;
            border-radius: 4px;
        }

        /* ===== NAV BAR ===== */
        .site-nav {
            background: var(--ink);
            padding: 0 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .nav-links {
            display: flex;
            align-items: center;
            flex: 1;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .nav-links::-webkit-scrollbar {
            display: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 14px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 13px;
            font-family: 'Noto Serif Bengali', serif;
            text-decoration: none;
            white-space: nowrap;
            border-bottom: 2px solid transparent;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .nav-item:hover,
        .nav-item.active {
            color: #fff;
            border-bottom-color: var(--accent);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-icon {
            font-size: 13px;
        }

        .nav-date-badge {
            background: var(--accent);
            color: #fff;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 11px;
            white-space: nowrap;
            flex-shrink: 0;
            margin-left: 8px;
        }

        /* ===== HAMBURGER ===== */
        .nav-hamburger {
            display: none;
            flex-direction: column;
            justify-content: center;
            gap: 5px;
            width: 38px;
            height: 38px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 7px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .nav-hamburger span {
            display: block;
            height: 2px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 2px;
            transition: all 0.25s;
        }

        .nav-hamburger.open span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }

        .nav-hamburger.open span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }

        .nav-hamburger.open span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }


        /* ===== MOBILE DRAWER ===== */
        .nav-drawer {
            display: none;
            position: absolute;
            top: 100%;
            left: -20px;
            /* ← offset the nav padding */
            right: -20px;
            background: #111007;
            z-index: 500;
            flex-direction: column;
            border-top: 1px solid #2a2510;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
        }

        .nav-drawer.open {
            display: flex;
        }

        .nav-drawer .nav-item {
            padding: 13px 20px;
            border-bottom: 1px solid #1e1a08;
            border-left: 3px solid transparent;
        }

        .nav-drawer .nav-item:hover,
        .nav-drawer .nav-item.active {
            background: #1e1a08;
            border-bottom-color: #1e1a08;
            border-left-color: var(--accent);
            padding-left: 17px;
        }

        .nav-drawer-date {
            padding: 10px 20px;
            font-size: 11px;
            color: #555;
            border-top: 1px solid #1e1a08;
            font-family: 'Noto Serif Bengali', serif;
        }

        /* ===== MAIN ===== */
        main {
            min-height: 60vh;
        }

        /* ===== FOOTER ===== */
        .site-footer {
            background: var(--ink);
            color: rgba(255, 255, 255, 0.6);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 12px;
            border-top: 3px solid var(--accent);
            flex-wrap: wrap;
        }

        .footer-left {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .footer-left strong {
            color: rgba(255, 255, 255, 0.9);
            font-family: 'Noto Serif Bengali', serif;
            font-size: 13px;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .footer-contact {
            color: rgba(255, 255, 255, 0.5);
        }

        /* ============================================================
           RESPONSIVE BREAKPOINTS
        ============================================================ */

        /* Tablet (≤ 900px) */
        @media (max-width: 900px) {
            .logo-name {
                font-size: 28px;
            }

            .header-ad-banner {
                max-width: 300px;
                height: 52px;
            }

            .nav-item {
                padding: 10px 10px;
                font-size: 12px;
            }
        }

        /* Mobile (≤ 640px) */
        @media (max-width: 640px) {

            /* Topbar */
            .topbar {
                padding: 0 14px;
            }

            .topbar-date {
                font-size: 10px;
            }

            /* Header — hide ad, shrink logo */
            .site-header {
                padding: 10px 14px;
            }

            .logo-name {
                font-size: 22px;
                letter-spacing: 0;
            }

            .logo-subtitle {
                font-size: 9px;
                letter-spacing: 1.5px;
            }

            .header-ad-banner {
                display: none;
            }

            /* Nav — show hamburger, hide desktop links */
            .site-nav {
                padding: 0 14px;
            }

            .nav-links {
                display: none;
            }

            .nav-hamburger {
                display: flex;
            }

            .nav-date-badge {
                display: none;
            }

            /* Footer */
            .site-footer {
                flex-direction: column;
                align-items: flex-start;
                padding: 14px;
                gap: 8px;
            }

            .footer-right {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
        }

        /* Extra small (≤ 380px) */
        @media (max-width: 380px) {
            .logo-name {
                font-size: 19px;
            }

            .topbar-social a {
                width: 20px;
                height: 20px;
            }

            .topbar-date {
                display: none;
            }

            /* very tight — hide date */
        }

        @yield('extra-styles')
    </style>

    @stack('head-scripts')
</head>

<body>

    {{-- ===== TOP BAR ===== --}}
    <div class="topbar">
        <div class="site-container" style="display:flex;align-items:center;justify-content:space-between;height:100%;">
            <div class="topbar-social">
                @if($settings->facebook_url)
                    <a href="{{ $settings->facebook_url }}" target="_blank" title="Facebook">f</a>
                @endif
                @if($settings->twitter_url)
                    <a href="{{ $settings->twitter_url }}" target="_blank" title="Twitter">t</a>
                @endif
                @if($settings->youtube_url)
                    <a href="{{ $settings->youtube_url }}" target="_blank" title="YouTube">▶</a>
                @endif
            </div>
            <span class="topbar-date">
                📅 {{ \Carbon\Carbon::now()->locale('bn')->isoFormat('dddd, D MMMM YYYY') }}
            </span>
        </div>
    </div>

    {{-- ===== HEADER ===== --}}
    <header class="site-header">
        <div class="site-container" style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <div class="header-logo">
                <a href="{{ url('/') }}">
                    @if($settings->site_logo)
                        <img src="{{ asset('storage/' . $settings->site_logo) }}" alt="{{ $settings->site_name }}"
                            style="max-height:60px; width:auto;">
                    @else
                        <div class="logo-name">{{ $settings->site_name }}</div>
                        <div class="logo-subtitle">{{ $settings->site_tagline }}</div>
                    @endif
                </a>
            </div>
            <div class="header-ad-banner">
                @if($settings->header_ad_image)
                    <a href="{{ $settings->header_ad_url ?? '#' }}" target="_blank">
                        <img src="{{ asset('storage/' . $settings->header_ad_image) }}" alt="Advertisement"
                            style="max-height:64px; width:auto;">
                    </a>
                @else
                    <span>বিজ্ঞাপন — 468 × 60</span>
                @endif
            </div>
        </div>
    </header>

    {{-- ===== NAV ===== --}}
    <nav class="site-nav">
        <div class="site-container" style="display:flex;align-items:center;position:relative;">

            {{-- Desktop links --}}
            <div class="nav-links">
                <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span> প্রচ্ছদ
                </a>
                <a href="{{ url('/') }}" class="nav-item {{ request()->is('edition*') ? 'active' : '' }}">
                    <span class="nav-icon">📰</span> অনলাইন সংস্করণ
                </a>

                @if(isset($activeEdition))
                    <span class="nav-date-badge">
                        📅 {{ $activeEdition->edition_date?->format('d F Y') ?? '' }}
                    </span>
                @endif
            </div>

            {{-- Hamburger button --}}
            <button class="nav-hamburger" id="navHamburger" aria-label="মেনু খুলুন" aria-expanded="false"
                onclick="toggleMobileNav()">
                <span></span>
                <span></span>
                <span></span>
            </button>

            {{-- Mobile drawer --}}
            <div class="nav-drawer" id="navDrawer" aria-hidden="true">
                <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}"
                    onclick="closeMobileNav()">
                    <span class="nav-icon">🏠</span> প্রচ্ছদ
                </a>
                <a href="{{ url('/') }}" class="nav-item {{ request()->is('edition*') ? 'active' : '' }}"
                    onclick="closeMobileNav()">
                    <span class="nav-icon">📰</span> অনলাইন সংস্করণ
                </a>

                @if(isset($activeEdition))
                    <div class="nav-drawer-date">
                        📅 {{ $activeEdition->edition_date?->format('d F Y') ?? '' }}
                    </div>
                @endif
            </div>

        </div>
    </nav>

    {{-- ===== PAGE CONTENT ===== --}}
    <main>
        @yield('content')
    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="site-footer">
        <div class="site-container"
            style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
            <div class="footer-left">
                <strong>{{ $settings->site_name }}</strong>
                @if($settings->editor_name)
                    <span>বার্তা সম্পাদক: {{ $settings->editor_name }}</span>
                @endif
                @if($settings->site_address)
                    <span>{{ $settings->site_address }}</span>
                @endif
            </div>
            <div class="footer-right">
                @if($settings->site_email)
                    <span class="footer-contact">✉️ {{ $settings->site_email }}</span>
                @endif
                @if($settings->site_phone)
                    <span class="footer-contact">📞 {{ $settings->site_phone }}</span>
                @endif
                <span class="footer-contact">© {{ date('Y') }} সর্বস্বত্ব সংরক্ষিত</span>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileNav() {
            const btn = document.getElementById('navHamburger');
            const drawer = document.getElementById('navDrawer');
            const isOpen = drawer.classList.toggle('open');
            btn.classList.toggle('open', isOpen);
            btn.setAttribute('aria-expanded', isOpen);
            drawer.setAttribute('aria-hidden', !isOpen);
        }

        function closeMobileNav() {
            const btn = document.getElementById('navHamburger');
            const drawer = document.getElementById('navDrawer');
            btn.classList.remove('open');
            drawer.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
            drawer.setAttribute('aria-hidden', 'true');
        }

        // Close drawer when clicking outside nav
        document.addEventListener('click', function (e) {
            const nav = document.querySelector('.site-nav');
            if (nav && !nav.contains(e.target)) {
                closeMobileNav();
            }
        });

        // Close drawer on resize back to desktop
        window.addEventListener('resize', function () {
            if (window.innerWidth > 640) closeMobileNav();
        });
    </script>

    @stack('scripts')
</body>

</html>