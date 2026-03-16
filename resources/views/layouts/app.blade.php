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
            font-size: 14px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .topbar-social a:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .topbar-date {
            font-size: 14px;
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
            font-size: 14px;
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
            font-size: 14px;
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
            font-size: 14px;
        }

        .nav-date-badge {
            background: var(--accent);
            color: #fff;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 14px;
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
            font-size: 14px;
            color: #555;
            border-top: 1px solid #1e1a08;
            font-family: 'Noto Serif Bengali', serif;
        }

        /* ===== MAIN ===== */
        main {
            min-height: 60vh;
        }

        /* ===== FOOTER ===== */
        /* ===== FOOTER ===== */
.site-footer {
    background: var(--ink);
    border-top: 3px solid var(--accent);
    padding: 32px 20px 0;
    color: rgba(255, 255, 255, 0.55);
    font-size: 14px;
}

.footer-main {
    display: grid;
    grid-template-columns: 1.4fr 1fr 1fr;
    gap: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-brand .footer-logo-text {
    font-family: 'Noto Serif Bengali', serif;
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    letter-spacing: -0.5px;
    line-height: 1;
    display: block;
    margin-bottom: 6px;
}

.footer-brand .footer-tagline {
    font-size: 14px;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.35);
    font-family: 'Playfair Display', serif;
    display: block;
    margin-bottom: 14px;
}

.footer-brand p {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.4);
    line-height: 1.7;
    margin: 0;
    max-width: 240px;
}

.footer-col-title {
    font-size: 14px;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.3);
    font-family: 'Playfair Display', serif;
    margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-contact-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.footer-contact-list li {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    color: rgba(255, 255, 255, 0.55);
    font-size: 14px;
    line-height: 1.5;
}

.footer-contact-list .fc-icon {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    margin-top: 2px;
    opacity: 0.5;
}

.footer-social-row {
    display: flex;
    gap: 8px;
    margin-top: 4px;
}

.footer-social-row a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.5);
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s;
}

.footer-social-row a:hover {
    border-color: var(--accent);
    color: #fff;
    background: rgba(192, 57, 43, 0.15);
}

.footer-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.45);
    flex-wrap: wrap;
    gap: 6px;
}

.footer-bar a {
    color: rgba(255, 255, 255, 0.75);  /* was 0.3 */
    text-decoration: none;
    font-size: 14px;                 /* was inherited 11px */
    font-weight: 500;
}

.footer-bar a:hover {
    color: #fff;                       /* was 0.6 */
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 900px) {
    .footer-main {
        grid-template-columns: 1fr 1fr;
    }
    .footer-brand {
        grid-column: 1 / -1;
    }
}

@media (max-width: 640px) {
    .site-footer {
        padding: 24px 14px 0;
    }
    .footer-main {
        grid-template-columns: 1fr;
        gap: 24px;
        padding-bottom: 20px;
    }
    .footer-brand {
        grid-column: auto;
    }
    .footer-brand p {
        max-width: 100%;
    }
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
                font-size: 13px;
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
                font-size: 11px;
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
    <div class="site-container">

        <div class="footer-main">

            {{-- Brand column --}}
            <div class="footer-brand">
                <span class="footer-logo-text">{{ $settings->site_name }}</span>
                <span class="footer-tagline">{{ $settings->site_tagline ?? 'সংবাদ ও তথ্যের নির্ভরযোগ্য উৎস' }}</span>
                <p>আপনার বিশ্বস্ত সংবাদ মাধ্যম। সত্য, নিরপেক্ষ ও দায়িত্বশীল সাংবাদিকতায় আমরা প্রতিশ্রুতিবদ্ধ।</p>
            </div>

            {{-- Contact column --}}
            <div class="footer-col">
                <p class="footer-col-title">যোগাযোগ</p>
                <ul class="footer-contact-list">
                    @if($settings->editor_name)
                        <li>
                            <svg class="fc-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span>বার্তা সম্পাদক: {{ $settings->editor_name }}</span>
                        </li>
                    @endif
                    @if($settings->site_email)
                        <li>
                            <svg class="fc-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <span>{{ $settings->site_email }}</span>
                        </li>
                    @endif
                    @if($settings->site_phone)
                        <li>
                            <svg class="fc-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 6.75z" />
                            </svg>
                            <span>{{ $settings->site_phone }}</span>
                        </li>
                    @endif
                    @if($settings->site_address)
                        <li>
                            <svg class="fc-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            <span>{{ $settings->site_address }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Social column --}}
            <div class="footer-col">
                <p class="footer-col-title">আমাদের অনুসরণ করুন</p>
                <div class="footer-social-row">
                    @if($settings->facebook_url)
                        <a href="{{ $settings->facebook_url }}" target="_blank" rel="noopener" aria-label="Facebook">f</a>
                    @endif
                    @if($settings->twitter_url)
                        <a href="{{ $settings->twitter_url }}" target="_blank" rel="noopener" aria-label="Twitter">𝕏</a>
                    @endif
                    @if($settings->youtube_url)
                        <a href="{{ $settings->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube">▶</a>
                    @endif
                </div>
            </div>

        </div>

        {{-- Bottom bar --}}
        {{-- Bottom bar --}}
        <div class="footer-bar">
            <span>© {{ date('Y') }} {{ $settings->site_name }} — সর্বস্বত্ব সংরক্ষিত</span>
            <span>Developed by <a href="https://jisan.openwindowbd.com" target="_blank" rel="noopener">Md Jisan Sheikh</a></span>
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