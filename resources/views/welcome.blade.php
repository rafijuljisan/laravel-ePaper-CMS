<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ই-পেপার — {{ ($edition ?? $latestEdition)?->title ?? 'সংবাদপত্র' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Noto+Serif+Bengali:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --ink: #1a1008;
            --paper: #f5f0e8;
            --paper-dark: #ede5d0;
            --accent: #c0392b;
            --accent-light: #e74c3c;
            --gold: #b8860b;
            --sidebar-w: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--ink);
            font-family: 'Noto Serif Bengali', serif;
            color: var(--ink);
            overflow: hidden;
            height: 100vh;
        }

        /* ===== LAYOUT ===== */
        .epaper-layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-w);
            background: #111007;
            border-right: 1px solid #2a2510;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 20px 18px 16px;
            border-bottom: 1px solid #2a2510;
        }

        .masthead {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 900;
            color: var(--paper);
            letter-spacing: 2px;
            text-transform: uppercase;
            line-height: 1;
        }

        .masthead span {
            display: block;
            color: var(--accent);
            font-size: 10px;
            letter-spacing: 3px;
            margin-top: 4px;
            font-weight: 400;
        }

        .edition-date {
            margin-top: 10px;
            font-size: 11px;
            color: #666;
            letter-spacing: 0.5px;
        }

        /* Page Thumbnails */
        .pages-label {
            padding: 14px 18px 8px;
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #444;
            font-family: 'Playfair Display', serif;
        }

        .pages-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 0 12px 12px;
            scrollbar-width: thin;
            scrollbar-color: #2a2510 transparent;
        }

        .page-thumb {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
            margin-bottom: 4px;
        }

        .page-thumb:hover {
            background: #1a1608;
            border-color: #2a2510;
        }

        .page-thumb.active {
            background: #1e1a08;
            border-color: var(--gold);
        }

        .page-thumb-img {
            width: 42px;
            height: 56px;
            object-fit: cover;
            border-radius: 3px;
            border: 1px solid #2a2510;
            flex-shrink: 0;
        }

        .page-thumb-placeholder {
            width: 42px;
            height: 56px;
            background: #1a1608;
            border-radius: 3px;
            border: 1px solid #2a2510;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #333;
            flex-shrink: 0;
        }

        .page-thumb-info {
            flex: 1;
            min-width: 0;
        }

        .page-thumb-num {
            font-size: 12px;
            color: var(--paper);
            font-weight: 600;
            font-family: 'Playfair Display', serif;
        }

        .page-thumb-count {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* Editions dropdown */
        .editions-section {
            padding: 12px;
            border-top: 1px solid #2a2510;
        }

        .editions-label {
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #444;
            font-family: 'Playfair Display', serif;
            margin-bottom: 8px;
        }

        .editions-select {
            width: 100%;
            background: #1a1608;
            border: 1px solid #2a2510;
            color: var(--paper);
            padding: 8px 10px;
            font-size: 12px;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Noto Serif Bengali', serif;
        }

        /* ===== MAIN READER ===== */
        .reader-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #0e0c06;
            overflow: hidden;
        }

        /* Toolbar */
        .reader-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background: #0a0803;
            border-bottom: 1px solid #1a1508;
            flex-shrink: 0;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-btn {
            background: #1a1608;
            border: 1px solid #2a2510;
            color: #999;
            width: 34px;
            height: 34px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 14px;
        }

        .toolbar-btn:hover {
            background: #2a2510;
            color: var(--paper);
            border-color: #3a3520;
        }

        .zoom-level {
            font-size: 12px;
            color: #666;
            font-family: 'Playfair Display', serif;
            min-width: 40px;
            text-align: center;
        }

        .toolbar-center {
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            color: #555;
            letter-spacing: 1px;
        }

        .toolbar-center strong {
            color: #888;
        }

        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hotspot-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #555;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 5px;
            border: 1px solid #1a1508;
            transition: all 0.15s;
        }

        .hotspot-toggle:hover {
            border-color: #2a2510;
            color: #888;
        }

        .hotspot-toggle .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
        }

        /* Newspaper Canvas */
        .reader-canvas {
            flex: 1;
            overflow: hidden;
            position: relative;
            cursor: grab;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reader-canvas.grabbing {
            cursor: grabbing;
        }

        .newspaper-wrapper {
            position: relative;
            transform-origin: center center;
            transition: transform 0.1s ease-out;
            will-change: transform;
            line-height: 0;
        }

        .newspaper-img {
            display: block;
            max-width: 100%;
            height: auto;
            box-shadow: 0 0 80px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.05);
            border-radius: 2px;
        }

        /* Hotspots */
        .hotspot-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .hotspot {
            position: absolute;
            border: 2px solid transparent;
            border-radius: 2px;
            cursor: pointer;
            pointer-events: auto;
            transition: all 0.2s;
            background: transparent;
        }

        .hotspots-visible .hotspot {
            border-color: rgba(192, 57, 43, 0.5);
            background: rgba(192, 57, 43, 0.05);
        }

        .hotspot:hover {
            border-color: var(--accent) !important;
            background: rgba(192, 57, 43, 0.15) !important;
            z-index: 10;
        }

        .hotspot-label {
            position: absolute;
            top: -1px;
            left: -1px;
            background: var(--accent);
            color: white;
            font-size: 9px;
            padding: 2px 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
            font-family: 'Noto Serif Bengali', serif;
            opacity: 0;
            transition: opacity 0.15s;
            pointer-events: none;
        }

        .hotspot:hover .hotspot-label {
            opacity: 1;
        }

        /* Click ripple */
        .hotspot::after {
            content: '';
            position: absolute;
            inset: -4px;
            border: 2px solid var(--accent);
            border-radius: 4px;
            opacity: 0;
            transform: scale(1);
            transition: all 0.3s;
        }

        .hotspot:active::after {
            opacity: 1;
            transform: scale(1.05);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            color: #333;
            padding: 60px 20px;
        }

        .empty-state .icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .empty-state p {
            font-size: 14px;
            color: #444;
        }

        /* ===== ARTICLE MODAL ===== */
        .article-overlay {
            position: fixed;
            inset: 0;
            background: rgba(10, 8, 3, 0.92);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s;
            backdrop-filter: blur(4px);
        }

        .article-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .article-modal {
            background: var(--paper);
            width: 100%;
            max-width: 720px;
            max-height: 90vh;
            border-radius: 4px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: translateY(20px) scale(0.98);
            transition: transform 0.25s;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.8);
            position: relative;
        }

        .article-overlay.open .article-modal {
            transform: translateY(0) scale(1);
        }

        /* Decorative top border */
        .article-modal::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent);
        }

        .article-modal-header {
            padding: 28px 32px 20px;
            border-bottom: 1px solid var(--paper-dark);
            flex-shrink: 0;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .article-category {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--accent);
            font-family: 'Playfair Display', serif;
        }

        .article-meta-divider {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #bbb;
        }

        .article-edition-info {
            font-size: 11px;
            color: #888;
        }

        .article-title {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 900;
            color: var(--ink);
            line-height: 1.25;
            margin-bottom: 10px;
        }

        .article-author {
            font-size: 12px;
            color: #666;
            font-style: italic;
        }

        .article-modal-body {
            padding: 24px 32px 32px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--paper-dark) transparent;
        }

        .article-summary {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            line-height: 1.7;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--paper-dark);
        }

        .article-content {
            font-size: 15px;
            line-height: 1.9;
            color: #2a2a2a;
        }

        .article-content p {
            margin-bottom: 16px;
        }

        .article-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            margin: 24px 0 12px;
            color: var(--ink);
        }

        .article-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            margin: 20px 0 10px;
        }

        .article-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            background: var(--paper-dark);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 16px;
            transition: all 0.15s;
            z-index: 10;
        }

        .article-close:hover {
            background: var(--ink);
            color: var(--paper);
        }

        /* Loading skeleton */
        .skeleton-line {
            height: 16px;
            background: linear-gradient(90deg, var(--paper-dark) 25%, #e8e0cc 50%, var(--paper-dark) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .article-modal {
                max-width: 100%;
                border-radius: 12px 12px 0 0;
            }

            .article-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .article-title {
                font-size: 20px;
            }

            .article-modal-header {
                padding: 20px 20px 16px;
            }

            .article-modal-body {
                padding: 16px 20px 24px;
            }
        }
    </style>
</head>

<body>

    @php
        $activeEdition = $edition ?? $latestEdition;
        $firstPage = $activeEdition?->pages->sortBy('page_number')->first();
    @endphp

    <div class="epaper-layout">

        {{-- ===== SIDEBAR ===== --}}
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="masthead">
                    {{ config('app.name', 'দৈনিক সংবাদ') }}
                    <span>E-PAPER</span>
                </div>
                @if($activeEdition)
                    <div class="edition-date">
                        📅 {{ $activeEdition->edition_date?->format('d F, Y') }}
                    </div>
                @endif
            </div>

            @if($activeEdition && $activeEdition->pages->count() > 0)
                <div class="pages-label">পাতাসমূহ</div>
                <div class="pages-scroll">
                    @foreach($activeEdition->pages->sortBy('page_number') as $page)
                        <div class="page-thumb {{ $loop->first ? 'active' : '' }}" onclick="loadPage({{ $page->id }}, this)"
                            data-page-id="{{ $page->id }}">
                            @if($page->image_path)
                                <img src="{{ asset('storage/' . $page->image_path) }}" alt="Page {{ $page->page_number }}"
                                    class="page-thumb-img">
                            @else
                                <div class="page-thumb-placeholder">📄</div>
                            @endif
                            <div class="page-thumb-info">
                                <div class="page-thumb-num">পাতা {{ $page->page_number }}</div>
                                <div class="page-thumb-count">
                                    {{ $page->hotspots->count() }} নিবন্ধ
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="editions-section">
                <div class="editions-label">সংস্করণ</div>
                <select class="editions-select" onchange="window.location='/epaper/'+this.value">
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}" {{ $activeEdition?->id == $ed->id ? 'selected' : '' }}>
                            {{ $ed->title }} — {{ $ed->edition_date?->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </aside>

        {{-- ===== MAIN READER ===== --}}
        <main class="reader-main">

            {{-- Toolbar --}}
            <div class="reader-toolbar">
                <div class="toolbar-left">
                    <button class="toolbar-btn" onclick="zoomOut()" title="ছোট করুন">−</button>
                    <span class="zoom-level" id="zoomLevel">100%</span>
                    <button class="toolbar-btn" onclick="zoomIn()" title="বড় করুন">+</button>
                    <button class="toolbar-btn" onclick="resetZoom()" title="রিসেট" style="font-size:11px;">↺</button>
                </div>

                <div class="toolbar-center">
                    @if($activeEdition)
                        <strong>{{ $activeEdition->title }}</strong>
                        &nbsp;·&nbsp;
                        <span id="currentPageLabel">পাতা {{ $firstPage?->page_number ?? '—' }}</span>
                    @else
                        কোনো সংস্করণ নেই
                    @endif
                </div>

                <div class="toolbar-right">
                    <div class="hotspot-toggle" onclick="toggleHotspots(this)" id="hotspotToggle">
                        <div class="dot"></div>
                        <span>নিবন্ধ হাইলাইট</span>
                    </div>
                </div>
            </div>

            {{-- Canvas --}}
            <div class="reader-canvas" id="readerCanvas">
                @if($activeEdition && $firstPage)
                    <div class="newspaper-wrapper" id="newspaperWrapper">
                        <img src="{{ asset('storage/' . $firstPage->image_path) }}" alt="Newspaper Page"
                            class="newspaper-img" id="newspaperImg" onload="onImageLoad(this)">

                        <div class="hotspot-overlay" id="hotspotOverlay"></div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="icon">📰</div>
                        <p>কোনো ই-পেপার উপলব্ধ নেই।</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

    {{-- ===== ARTICLE MODAL ===== --}}
    <div class="article-overlay" id="articleOverlay" onclick="closeArticle(event)">
        <div class="article-modal">
            <button class="article-close" onclick="closeArticleModal()">✕</button>

            <div class="article-modal-header" id="articleHeader">
                <div class="article-meta">
                    <span class="article-category" id="articleCategory"></span>
                    <span class="article-meta-divider"></span>
                    <span class="article-edition-info" id="articleEditionInfo"></span>
                </div>
                <div class="article-title" id="articleTitle">
                    <div class="skeleton-line" style="width:80%"></div>
                    <div class="skeleton-line" style="width:60%"></div>
                </div>
                <div class="article-author" id="articleAuthor"></div>
            </div>

            <div class="article-modal-body">
                <div class="article-summary" id="articleSummary">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line" style="width:85%"></div>
                </div>
                <div class="article-content" id="articleContent">
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line" style="width:90%"></div>
                    <div class="skeleton-line" style="width:70%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Page data from server --}}
    @php
        $pagesJson = ($activeEdition?->pages->sortBy('page_number')->map(fn($p) => [
            'id' => $p->id,
            'page_number' => $p->page_number,
            'image_path' => $p->image_path ? asset('storage/' . $p->image_path) : null,
            'width' => $p->width ?? 800,
            'height' => $p->height ?? 1200,
            'hotspots' => $p->hotspots->map(fn($h) => [
                'id' => $h->id,
                'article_id' => $h->article_id,
                'title' => $h->article->title ?? 'নিবন্ধ',
                'x' => $h->x,
                'y' => $h->y,
                'width' => $h->width,
                'height' => $h->height,
            ])->values()
        ])->values()) ?? collect();
    @endphp
    {{-- Hotspot Zoom Modal --}}
    <div id="hotspotZoomOverlay" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.85); align-items:center; justify-content:center;">
        <div style="
        position:relative; background:#fff; border-radius:12px;
        max-width:90vw; max-height:90vh; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.5);">

            {{-- Header --}}
            <div style="
            background:#1a1a2e; color:#fff; padding:12px 20px;
            display:flex; align-items:center; justify-content:between; gap:10px;">
                <span id="hotspotZoomTitle"
                    style="font-family:'Noto Serif Bengali',serif; font-size:14px; flex:1;"></span>
                <div style="display:flex; gap:8px; margin-left:auto;">
                    <button onclick="zoomHotspotIn()"
                        style="background:#fff2; border:none; color:#fff; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:16px;">+</button>
                    <button onclick="zoomHotspotOut()"
                        style="background:#fff2; border:none; color:#fff; padding:4px 10px; border-radius:6px; cursor:pointer; font-size:16px;">−</button>
                    <button onclick="closeHotspotZoom()"
                        style="background:#e74c3c; border:none; color:#fff; padding:4px 12px; border-radius:6px; cursor:pointer; font-size:16px;">✕</button>
                </div>
            </div>

            {{-- Zoomed image canvas --}}
            <div id="hotspotZoomContainer" style="overflow:auto; max-height:80vh; max-width:90vw;">
                <canvas id="hotspotZoomCanvas"></canvas>
            </div>
        </div>
    </div>

    <script>
        const PAGES_DATA = {!! json_encode($pagesJson) !!};
        const ARTICLE_URL = '{{ url("article/__ID__") }}';
    </script>

    <script>
        (function () {
            // ===== STATE =====
            let scale = 1;
            let minScale = 0.3;
            let maxScale = 4;
            let translateX = 0;
            let translateY = 0;
            let isDragging = false;
            let lastX = 0, lastY = 0;
            let activePageData = PAGES_DATA[0] || null;
            let showHotspots = true;
            let imgNaturalW = 0, imgNaturalH = 0;

            const canvas = document.getElementById('readerCanvas');
            const wrapper = document.getElementById('newspaperWrapper');
            const img = document.getElementById('newspaperImg');
            const overlay = document.getElementById('hotspotOverlay');
            const zoomEl = document.getElementById('zoomLevel');
            const pageLabel = document.getElementById('currentPageLabel');

            // ===== ZOOM =====
            function applyTransform() {
                if (!wrapper) return;
                wrapper.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
                zoomEl.textContent = Math.round(scale * 100) + '%';
            }

            window.zoomIn = () => { scale = Math.min(maxScale, scale + 0.25); applyTransform(); };
            window.zoomOut = () => { scale = Math.max(minScale, scale - 0.25); applyTransform(); };
            window.resetZoom = () => { scale = 1; translateX = 0; translateY = 0; applyTransform(); };

            // Mouse wheel zoom
            canvas?.addEventListener('wheel', (e) => {
                e.preventDefault();
                const delta = e.deltaY > 0 ? -0.1 : 0.1;
                scale = Math.min(maxScale, Math.max(minScale, scale + delta));
                applyTransform();
            }, { passive: false });

            // ===== DRAG (Pan) =====
            canvas?.addEventListener('mousedown', (e) => {
                if (e.target.classList.contains('hotspot')) return;
                isDragging = true;
                lastX = e.clientX;
                lastY = e.clientY;
                canvas.classList.add('grabbing');
            });

            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                translateX += e.clientX - lastX;
                translateY += e.clientY - lastY;
                lastX = e.clientX;
                lastY = e.clientY;
                applyTransform();
            });

            document.addEventListener('mouseup', () => {
                isDragging = false;
                canvas?.classList.remove('grabbing');
            });

            // Touch pan
            let lastTouchX = 0, lastTouchY = 0, lastPinchDist = 0;
            canvas?.addEventListener('touchstart', (e) => {
                if (e.touches.length === 1) {
                    lastTouchX = e.touches[0].clientX;
                    lastTouchY = e.touches[0].clientY;
                } else if (e.touches.length === 2) {
                    lastPinchDist = Math.hypot(
                        e.touches[0].clientX - e.touches[1].clientX,
                        e.touches[0].clientY - e.touches[1].clientY
                    );
                }
            }, { passive: true });

            canvas?.addEventListener('touchmove', (e) => {
                e.preventDefault();
                if (e.touches.length === 1) {
                    translateX += e.touches[0].clientX - lastTouchX;
                    translateY += e.touches[0].clientY - lastTouchY;
                    lastTouchX = e.touches[0].clientX;
                    lastTouchY = e.touches[0].clientY;
                    applyTransform();
                } else if (e.touches.length === 2) {
                    const dist = Math.hypot(
                        e.touches[0].clientX - e.touches[1].clientX,
                        e.touches[0].clientY - e.touches[1].clientY
                    );
                    scale = Math.min(maxScale, Math.max(minScale, scale * (dist / lastPinchDist)));
                    lastPinchDist = dist;
                    applyTransform();
                }
            }, { passive: false });

            // ===== IMAGE LOAD =====
            window.onImageLoad = function (imgEl) {
                imgNaturalW = imgEl.naturalWidth;
                imgNaturalH = imgEl.naturalHeight;
                renderHotspots();
            };

            // ===== HOTSPOT RENDERING =====
            function renderHotspots() {
                if (!overlay || !activePageData) return;
                overlay.innerHTML = '';

                const pageW = activePageData.width || imgNaturalW || 800;
                const pageH = activePageData.height || imgNaturalH || 1200;

                activePageData.hotspots.forEach(h => {
                    const div = document.createElement('div');
                    div.className = 'hotspot';
                    div.style.cssText = `
                left:   ${(h.x / pageW) * 100}%;
                top:    ${(h.y / pageH) * 100}%;
                width:  ${(h.width / pageW) * 100}%;
                height: ${(h.height / pageH) * 100}%;
            `;

                    const label = document.createElement('div');
                    label.className = 'hotspot-label';
                    label.textContent = h.title;
                    div.appendChild(label);

                    div.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openHotspotZoom(h, activePageData);
                    });

                    overlay.appendChild(div);
                });

                if (showHotspots) overlay.classList.add('hotspots-visible');
            }

            // ===== TOGGLE HOTSPOTS =====
            window.toggleHotspots = function (btn) {
                showHotspots = !showHotspots;
                overlay?.classList.toggle('hotspots-visible', showHotspots);
                btn.style.opacity = showHotspots ? '1' : '0.4';
            };

            // ===== LOAD PAGE =====
            window.loadPage = function (pageId, thumbEl) {
                activePageData = PAGES_DATA.find(p => p.id === pageId) || null;
                if (!activePageData || !activePageData.image_path) return;

                // Update thumbnail active state
                document.querySelectorAll('.page-thumb').forEach(t => t.classList.remove('active'));
                thumbEl?.classList.add('active');

                // Update page label
                if (pageLabel) pageLabel.textContent = 'পাতা ' + (activePageData.page_number || '—');

                // Reset zoom
                scale = 1; translateX = 0; translateY = 0;

                // Swap image
                if (img) {
                    img.style.opacity = '0';
                    img.src = activePageData.image_path;
                    img.onload = function () {
                        imgNaturalW = img.naturalWidth;
                        imgNaturalH = img.naturalHeight;
                        img.style.transition = 'opacity 0.3s';
                        img.style.opacity = '1';
                        renderHotspots();
                    };
                }

                applyTransform();
            };

            // ===== ARTICLE MODAL =====
            window.openArticle = function (articleId, fallbackTitle) {
                const modal = document.getElementById('articleOverlay');
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';

                // Reset to skeleton
                document.getElementById('articleTitle').innerHTML =
                    '<div class="skeleton-line" style="width:80%"></div><div class="skeleton-line" style="width:60%"></div>';
                document.getElementById('articleSummary').innerHTML =
                    '<div class="skeleton-line"></div><div class="skeleton-line" style="width:85%"></div>';
                document.getElementById('articleContent').innerHTML =
                    '<div class="skeleton-line"></div><div class="skeleton-line" style="width:90%"></div><div class="skeleton-line" style="width:70%"></div>';
                document.getElementById('articleAuthor').textContent = '';
                document.getElementById('articleCategory').textContent = '';
                document.getElementById('articleEditionInfo').textContent = '';

                // Fetch article
                const url = ARTICLE_URL.replace('__ID__', articleId);
                fetch(url)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('articleTitle').textContent = data.title || fallbackTitle;
                        document.getElementById('articleSummary').textContent = data.summary || '';
                        document.getElementById('articleContent').innerHTML = data.content || '<p>বিস্তারিত তথ্য পাওয়া যায়নি।</p>';
                        document.getElementById('articleAuthor').textContent = data.author ? '✍️ ' + data.author : '';
                        document.getElementById('articleCategory').textContent = data.category || '';
                        document.getElementById('articleEditionInfo').textContent =
                            [data.edition, data.edition_date, data.page_number ? 'পাতা ' + data.page_number : ''].filter(Boolean).join(' · ');

                        // Hide summary div if empty
                        const sumEl = document.getElementById('articleSummary');
                        sumEl.style.display = data.summary ? 'block' : 'none';
                    })
                    .catch(() => {
                        document.getElementById('articleTitle').textContent = fallbackTitle || 'নিবন্ধ';
                        document.getElementById('articleContent').innerHTML = '<p>তথ্য লোড করতে সমস্যা হয়েছে।</p>';
                    });
            };

            window.closeArticle = function (e) {
                if (e.target === document.getElementById('articleOverlay')) closeArticleModal();
            };

            window.closeArticleModal = function () {
                document.getElementById('articleOverlay').classList.remove('open');
                document.body.style.overflow = '';
            };

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closeArticleModal();
                if (e.key === '+' || e.key === '=') zoomIn();
                if (e.key === '-') zoomOut();
                if (e.key === '0') resetZoom();
            });

            // Init
            applyTransform();
        })();


        let hotspotZoomScale = 1;

        window.openHotspotZoom = function (hotspot, page) {
            if (!page || !page.image_path) return;

            const overlay = document.getElementById('hotspotZoomOverlay');
            const canvas = document.getElementById('hotspotZoomCanvas');
            const ctx = canvas.getContext('2d');
            const title = document.getElementById('hotspotZoomTitle');

            hotspotZoomScale = 1;
            title.textContent = hotspot.title || 'নিবন্ধ';
            overlay.style.display = 'flex';

            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = function () {
                // Natural image dimensions
                const natW = img.naturalWidth;
                const natH = img.naturalHeight;

                // Hotspot is stored as % of rendered size — convert to px
                const scaleX = natW / (page.width || natW);
                const scaleY = natH / (page.height || natH);

                const hx = hotspot.x * scaleX;
                const hy = hotspot.y * scaleY;
                const hw = hotspot.width * scaleX;
                const hh = hotspot.height * scaleY;

                // Add padding around the hotspot
                const pad = 30;
                const sx = Math.max(0, hx - pad);
                const sy = Math.max(0, hy - pad);
                const sw = Math.min(hw + pad * 2, natW - sx);
                const sh = Math.min(hh + pad * 2, natH - sy);

                // Draw cropped area onto canvas at 2× for sharpness
                const drawScale = 2;
                canvas.width = sw * drawScale;
                canvas.height = sh * drawScale;
                canvas.style.width = sw + 'px';
                canvas.style.height = sh + 'px';

                ctx.drawImage(img, sx, sy, sw, sh, 0, 0, sw * drawScale, sh * drawScale);

                // Store for zoom
                canvas.dataset.sx = sx;
                canvas.dataset.sy = sy;
                canvas.dataset.sw = sw;
                canvas.dataset.sh = sh;
                canvas._img = img;
            };
            img.src = page.image_path;
        };

        window.zoomHotspotIn = function () {
            hotspotZoomScale = Math.min(hotspotZoomScale + 0.25, 4);
            applyHotspotZoom();
        };

        window.zoomHotspotOut = function () {
            hotspotZoomScale = Math.max(hotspotZoomScale - 0.25, 0.5);
            applyHotspotZoom();
        };

        function applyHotspotZoom() {
            const canvas = document.getElementById('hotspotZoomCanvas');
            const sw = parseFloat(canvas.dataset.sw);
            const sh = parseFloat(canvas.dataset.sh);
            canvas.style.width = (sw * hotspotZoomScale) + 'px';
            canvas.style.height = (sh * hotspotZoomScale) + 'px';
        }

        window.closeHotspotZoom = function () {
            document.getElementById('hotspotZoomOverlay').style.display = 'none';
        };

        // Close on backdrop click
        document.getElementById('hotspotZoomOverlay').addEventListener('click', function (e) {
            if (e.target === this) closeHotspotZoom();
        });

        // Keyboard ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeHotspotZoom();
        });
    </script>
</body>

</html>