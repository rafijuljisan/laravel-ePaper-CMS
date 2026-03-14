@extends('layouts.app')

@section('title', ($edition ?? $latestEdition)?->title ?? 'ই-পেপার')

@section('extra-styles')
    <style>
        body {
            background: #d6d1c7;
        }

        /* ============================================================
               LAYOUT — 3 columns: left sidebar | reader | right sidebar
            ============================================================ */
        .ep-wrap {
            display: grid;
            grid-template-columns: 140px 1fr 200px;
            grid-template-rows: auto 1fr;
            gap: 10px;
            padding: 10px;
            max-width: 1440px;
            margin: 0 auto;
            /* no height — let it grow naturally */
            box-sizing: border-box;
        }

        /* ============================================================
               TOP PAGINATION BAR (full width, row 1)
            ============================================================ */
        .ep-topbar {
            grid-column: 1 / -1;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ep-page-btns {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .ep-pb {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 6px;
            border: 1px solid #bbb;
            background: #f0f0f0;
            color: #333;
            font-size: 13px;
            border-radius: 3px;
            cursor: pointer;
            font-family: 'Noto Serif Bengali', serif;
            transition: all 0.15s;
            user-select: none;
        }

        .ep-pb:hover:not(.disabled) {
            background: #1a6faf;
            color: #fff;
            border-color: #1a6faf;
        }

        .ep-pb.active {
            background: #1a6faf;
            color: #fff;
            border-color: #1a6faf;
            font-weight: 700;
        }

        .ep-pb.disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }

        .ep-pb.arrow {
            font-weight: 700;
        }

        .ep-topbar-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .ep-action-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 5px 11px;
            border: 1px solid #bbb;
            background: #f5f5f5;
            color: #333;
            font-size: 12px;
            border-radius: 3px;
            cursor: pointer;
            font-family: 'Noto Serif Bengali', serif;
            transition: all 0.15s;
        }

        .ep-action-btn:hover {
            background: #e0eaf5;
            border-color: #1a6faf;
            color: #1a6faf;
        }

        .ep-action-btn.red {
            background: #c0392b;
            color: #fff;
            border-color: #c0392b;
        }

        .ep-action-btn.red:hover {
            background: #a93226;
        }

        /* ============================================================
               LEFT SIDEBAR — page thumbnails
            ============================================================ */
        .ep-left {
            grid-column: 1;
            grid-row: 2;
            display: flex;
            flex-direction: column;
            gap: 0;
            overflow: hidden;
            border-radius: 4px;
            border: 1px solid #bbb;
        }

        .ep-sidebar-hd {
            background: #1a6faf;
            color: #fff;
            text-align: center;
            padding: 7px 6px;
            font-size: 12px;
            font-family: 'Noto Serif Bengali', serif;
            flex-shrink: 0;
        }

        .ep-thumbs {
            flex: 1;
            overflow-y: auto;
            background: #fff;
            scrollbar-width: thin;
            scrollbar-color: #ccc transparent;
        }

        .ep-thumb {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.15s;
            position: relative;
        }

        .ep-thumb:hover {
            background: #e8f0fb;
        }

        .ep-thumb.active {
            background: #ddeaf8;
            border-left: 3px solid #1a6faf;
        }

        .ep-thumb-img {
            width: 90px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 2px;
            display: block;
        }

        .ep-thumb-ph {
            width: 90px;
            height: 120px;
            background: #f0ebe0;
            border: 1px solid #ddd;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: #ccc;
        }

        .ep-thumb-num {
            font-size: 11px;
            color: #555;
            margin-top: 5px;
            font-family: 'Noto Serif Bengali', serif;
            text-align: center;
        }

        /* ============================================================
               MAIN READER — newspaper fits the box, NO scroll/zoom
            ============================================================ */
        .ep-reader {
            grid-column: 2;
            grid-row: 2;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            /* scrollable */
            border-radius: 4px;
            border: 1px solid #bbb;
            background: #888;
            position: relative;
        }

        .ep-reader-inner {
            flex: 1;
            position: relative;
            overflow: visible;
        }

        /* The newspaper image always fits the reader box */
        .ep-newspaper {
            display: block;
            width: 100%;
            /* full width of reader */
            height: auto;
            /* natural height — no cap */
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.4);
            user-select: none;
            -webkit-user-drag: none;
            transition: opacity 0.25s;
        }

        .ep-newspaper.loading {
            opacity: 0;
        }

        /* Hotspot overlay — covers the image exactly */
        .ep-hotspot-layer {
            position: absolute;
            pointer-events: none;
        }

        .ep-hotspot {
            position: absolute;
            border: 2px solid transparent;
            border-radius: 2px;
            cursor: pointer;
            pointer-events: auto;
            transition: all 0.15s;
        }

        .ep-hotspot:hover {
            border-color: rgba(192, 57, 43, 0.85);
            background: rgba(192, 57, 43, 0.12);
        }

        .ep-hotspot-tip {
            position: absolute;
            bottom: calc(100% + 4px);
            left: 0;
            background: #c0392b;
            color: #fff;
            font-size: 10px;
            padding: 3px 7px;
            white-space: nowrap;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: 'Noto Serif Bengali', serif;
            border-radius: 2px;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s;
            z-index: 10;
        }

        .ep-hotspot:hover .ep-hotspot-tip {
            opacity: 1;
        }

        /* Bottom nav inside reader */
        .ep-reader-footer {
            background: #f0ebe0;
            border-top: 1px solid #ccc;
            padding: 6px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ep-prev-next {
            display: flex;
            gap: 6px;
        }

        .ep-pn-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 5px 14px;
            background: #1a6faf;
            color: #fff;
            border: none;
            border-radius: 3px;
            font-size: 12px;
            cursor: pointer;
            font-family: 'Noto Serif Bengali', serif;
            transition: background 0.15s;
        }

        .ep-pn-btn:hover {
            background: #155d96;
        }

        .ep-pn-btn:disabled {
            background: #bbb;
            cursor: not-allowed;
        }

        .ep-page-info-label {
            font-size: 12px;
            color: #666;
            font-family: 'Noto Serif Bengali', serif;
        }

        /* Edition selector in reader footer */
        .ep-edition-sel {
            font-size: 12px;
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 4px 8px;
            background: #fff;
            font-family: 'Noto Serif Bengali', serif;
            color: #333;
            cursor: pointer;
        }

        /* ============================================================
               RIGHT SIDEBAR
            ============================================================ */
        .ep-right {
            grid-column: 3;
            grid-row: 2;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #ccc transparent;
        }

        .ep-widget {
            background: #fff;
            border: 1px solid #bbb;
            border-radius: 4px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .ep-widget-hd {
            background: #1a6faf;
            color: #fff;
            padding: 7px 10px;
            font-size: 12px;
            font-family: 'Noto Serif Bengali', serif;
        }

        .ep-widget-hd.dark {
            background: #1a1008;
        }

        /* Today's pages list */
        .ep-today-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-bottom: 1px solid #f0ebe0;
            cursor: pointer;
            transition: background 0.15s;
            font-size: 12px;
            font-family: 'Noto Serif Bengali', serif;
            color: #333;
        }

        .ep-today-item:last-child {
            border-bottom: none;
        }

        .ep-today-item:hover {
            background: #e8f0fb;
        }

        .ep-today-item .thumb-icon {
            color: #1a6faf;
            font-size: 14px;
        }

        /* ===== CALENDAR ===== */
        .ep-cal-hd {
            background: #1a6faf;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            font-size: 12px;
            font-family: 'Noto Serif Bengali', serif;
        }

        .ep-cal-nav {
            background: none;
            border: none;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            line-height: 1;
            padding: 0 4px;
            opacity: 0.8;
            transition: opacity 0.15s;
        }

        .ep-cal-nav:hover {
            opacity: 1;
        }

        .ep-cal-grid {
            padding: 6px;
        }

        .ep-cal-dow {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            margin-bottom: 3px;
        }

        .ep-cal-dl {
            text-align: center;
            font-size: 9px;
            color: #999;
            padding: 2px 0;
        }

        .ep-cal-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .ep-cal-day {
            text-align: center;
            padding: 4px 1px;
            font-size: 11px;
            border-radius: 3px;
            color: #aaa;
            /* default = no content = grey, unclickable */
            cursor: default;
            user-select: none;
        }

        /* Date that HAS content — highlighted, clickable */
        .ep-cal-day.has-edition {
            color: #fff;
            background: #1a6faf;
            cursor: pointer;
            font-weight: 600;
            border-radius: 3px;
        }

        .ep-cal-day.has-edition:hover {
            background: #155d96;
        }

        /* Today */
        .ep-cal-day.today {
            outline: 2px solid #c0392b;
            outline-offset: -2px;
        }

        .ep-cal-day.today.has-edition {
            background: #c0392b;
        }

        .ep-cal-day.today.has-edition:hover {
            background: #a93226;
        }

        /* Ad box */
        .ep-ad-slot {
            padding: 12px;
            min-height: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            background: #fff5f5;
            text-align: center;
        }

        .ep-ad-slot strong {
            display: block;
            color: #c0392b;
            font-size: 13px;
            line-height: 1.3;
        }

        .ep-ad-slot small {
            color: #999;
            font-size: 11px;
        }

        .ep-ad-slot a {
            font-size: 11px;
            color: #c0392b;
            text-decoration: none;
            font-weight: 700;
            margin-top: 4px;
        }

        /* ============================================================
               HOTSPOT ZOOM / ARTICLE MODAL
            ============================================================ */
        .hz-overlay {
            position: fixed;
            inset: 0;
            z-index: 2000;
            background: rgba(0, 0, 0, 0.85);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            backdrop-filter: blur(3px);
        }

        .hz-overlay.open {
            display: flex;
        }

        .hz-box {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-width: 92vw;
            max-height: 92vh;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            width: auto;   /* ← shrinks to fit content */
            min-width: 300px;
        }

        /* Modal header with toolbar */
        .hz-header {
            background: #1a1a2e;
            color: #fff;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .hz-title {
            flex: 1;
            font-family: 'Noto Serif Bengali', serif;
            font-size: 13px;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .hz-toolbar {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .hz-btn {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 5px 11px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            font-family: 'Noto Serif Bengali', serif;
            transition: background 0.15s;
            white-space: nowrap;
        }

        .hz-btn:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .hz-btn.dl {
            background: #c0392b;
        }

        .hz-btn.dl:hover {
            background: #a93226;
        }

        .hz-btn.close-btn {
            background: #555;
        }

        .hz-btn.close-btn:hover {
            background: #333;
        }

        .hz-zoom-level {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            min-width: 34px;
            text-align: center;
        }

        /* Image container — scrollable */
        .hz-body {
            overflow: auto;
            flex: 1;
            background: #555;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 10px;
        }

        .hz-canvas-wrap {
            position: relative;
            flex-shrink: 0;
            transform-origin: top center;
            transition: transform 0.15s;
        }

        #hzCanvas {
            display: block;
            border-radius: 2px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        /* Article text panel (shown after "বিস্তারিত পড়ুন") */
        .hz-article-panel {
            display: none;
            flex-direction: column;
            overflow: hidden;
            flex: 1;
            border-top: 1px solid #eee;
        }

        .hz-article-panel.open {
            display: flex;
        }

        .hz-article-scroll {
            overflow-y: auto;
            padding: 20px 24px 24px;
            flex: 1;
        }

        .hz-article-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 900;
            color: #1a1008;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .hz-article-meta {
            font-size: 11px;
            color: #888;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .hz-article-summary {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            line-height: 1.7;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid #ede5d0;
        }

        .hz-article-content {
            font-size: 14px;
            line-height: 1.9;
            color: #2a2a2a;
        }

        .hz-article-content p {
            margin-bottom: 12px;
        }

        /* Skeleton */
        .skel {
            height: 14px;
            background: linear-gradient(90deg, #ede5d0 25%, #e0d8c4 50%, #ede5d0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.4s infinite;
            border-radius: 3px;
            margin-bottom: 9px;
        }

        @keyframes shimmer {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* ============================================================
               RESPONSIVE
            ============================================================ */

        /* Tablet ≤ 900px: hide right sidebar */
        @media (max-width: 900px) {
            .ep-wrap {
                grid-template-columns: 120px 1fr;
                grid-template-rows: auto 1fr;
            }

            .ep-topbar {
                grid-column: 1 / -1;
            }

            .ep-left {
                grid-column: 1;
                grid-row: 2;
            }

            .ep-reader {
                grid-column: 2;
                grid-row: 2;
            }

            .ep-right {
                display: none;
            }
        }

        /* Mobile ≤ 640px: hide left sidebar too, reader full width */
        @media (max-width: 640px) {
            .ep-wrap {
                grid-template-columns: 1fr;
                grid-template-rows: auto auto 1fr;
                height: auto;
                padding: 6px;
                gap: 6px;
            }

            .ep-topbar {
                grid-column: 1;
                grid-row: 1;
            }

            .ep-left {
                grid-column: 1;
                grid-row: 2;
                height: 90px;
                flex-direction: row;
                border-radius: 4px;
            }

            .ep-reader {
                grid-column: 1;
                grid-row: 3;
            }

            .ep-right {
                display: none;
            }

            /* Left sidebar goes horizontal on mobile */
            .ep-sidebar-hd {
                display: none;
            }

            .ep-thumbs {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                overflow-y: hidden;
                height: 90px;
            }

            .ep-thumb {
                flex-direction: column;
                flex-shrink: 0;
                padding: 5px 6px;
                border-bottom: none;
                border-right: 1px solid #eee;
                width: 70px;
            }

            .ep-thumb-img {
                width: 52px;
                height: 68px;
            }

            .ep-thumb-ph {
                width: 52px;
                height: 68px;
            }

            /* Topbar wrap */
            .ep-topbar-actions {
                display: none;
            }

            /* hide on very small */
            .ep-page-btns .ep-pb {
                min-width: 28px;
                height: 28px;
                font-size: 12px;
            }

            /* Modal responsive */
            .hz-box {
                max-width: 100%;
                max-height: 95vh;
                border-radius: 8px 8px 0 0;
            }

            .hz-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .hz-title {
                font-size: 12px;
            }

            .hz-btn {
                padding: 5px 8px;
                font-size: 11px;
            }
        }

        @media (max-width: 400px) {
            .ep-page-btns .ep-pb {
                min-width: 24px;
                height: 26px;
                font-size: 11px;
                padding: 0 3px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $activeEdition = $edition ?? $latestEdition;
        $sortedPages = $activeEdition?->pages->sortBy('page_number') ?? collect();
        $firstPage = $sortedPages->first();
        $totalPages = $sortedPages->count();

        // Build set of edition dates for calendar
        $editionDates = \App\Models\Edition::pluck('edition_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Page names in Bengali
        $pageNames = ['প্রথম পাতা', '২য় পাতা', '৩য় পাতা', '৪র্থ পাতা', '৫ম পাতা', '৬ষ্ঠ পাতা', '৭ম পাতা', '৮ম পাতা', '৯ম পাতা', '১০ম পাতা'];
    @endphp

    <div class="ep-wrap">

        {{-- ============================================================
        TOP PAGINATION BAR
        ============================================================ --}}
        <div class="ep-topbar">
            <div class="ep-page-btns">
                <button class="ep-pb arrow" id="btnPrev" onclick="prevPage()">«</button>
                @foreach($sortedPages as $page)
                    <button class="ep-pb {{ $loop->first ? 'active' : '' }}" id="navBtn{{ $loop->iteration }}"
                        data-idx="{{ $loop->iteration - 1 }}" onclick="goToIdx({{ $loop->iteration - 1 }})">
                        {{ $page->page_number }}
                    </button>
                @endforeach
                <button class="ep-pb arrow" id="btnNext" onclick="nextPage()">»</button>
            </div>

            <div class="ep-topbar-actions">
                <button class="ep-action-btn" onclick="printPage()">🖨️ প্রিন্ট</button>
                <button class="ep-action-btn" onclick="copyLink()">🔗 লিংক</button>
                <button class="ep-action-btn red" onclick="downloadPage()">⬇️ ডাউনলোড</button>
            </div>
        </div>

        {{-- ============================================================
        LEFT SIDEBAR — page thumbnails
        ============================================================ --}}
        <aside class="ep-left">
            <div class="ep-sidebar-hd">সকল পাতা</div>
            <div class="ep-thumbs" id="epThumbs">
                @foreach($sortedPages as $page)
                    <div class="ep-thumb {{ $loop->first ? 'active' : '' }}" id="thumb{{ $loop->iteration - 1 }}"
                        onclick="goToIdx({{ $loop->iteration - 1 }})">
                        @if($page->image_path)
                            <img src="{{ asset('storage/' . $page->image_path) }}" alt="পাতা {{ $page->page_number }}"
                                class="ep-thumb-img" loading="lazy">
                        @else
                            <div class="ep-thumb-ph">📄</div>
                        @endif
                        <span class="ep-thumb-num">{{ $page->page_number }}</span>
                    </div>
                @endforeach
            </div>
        </aside>

        {{-- ============================================================
        MAIN READER
        ============================================================ --}}
        <section class="ep-reader" id="epReader">
            <div class="ep-reader-inner" id="epReaderInner">
                @if($firstPage && $firstPage->image_path)
                    <img src="{{ asset('storage/' . $firstPage->image_path) }}" alt="Newspaper" class="ep-newspaper"
                        id="epNewspaper" onload="onImgLoad()">
                    <div class="ep-hotspot-layer" id="epHotspotLayer"></div>
                @else
                    <div style="color:#ccc; text-align:center; padding:40px;">
                        <div style="font-size:48px;">📰</div>
                        <p style="margin-top:12px; font-family:'Noto Serif Bengali',serif;">কোনো পাতা পাওয়া যায়নি</p>
                    </div>
                @endif
            </div>

            <div class="ep-reader-footer">
                <div class="ep-prev-next">
                    <button class="ep-pn-btn" id="btnPrevFoot" onclick="prevPage()">« আগের পাতা</button>
                    <button class="ep-pn-btn" id="btnNextFoot" onclick="nextPage()">পরের পাতা »</button>
                </div>
                <span class="ep-page-info-label" id="pageInfoLabel">
                    @if($firstPage) পাতা {{ $firstPage->page_number }} / {{ $totalPages }} @endif
                </span>
                <select class="ep-edition-sel" onchange="window.location='/edition/'+this.value">
                    @foreach($editions as $ed)
                        <option value="{{ $ed->id }}" {{ $activeEdition?->id == $ed->id ? 'selected' : '' }}>
                            {{ $ed->edition_date?->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </section>

        {{-- ============================================================
        RIGHT SIDEBAR
        ============================================================ --}}
        <aside class="ep-right">

            {{-- Today's pages --}}
            <div class="ep-widget">
                <div class="ep-widget-hd">আজকের পত্রিকা</div>
                @foreach($sortedPages as $page)
                    <div class="ep-today-item" onclick="goToIdx({{ $loop->iteration - 1 }})">
                        <span class="thumb-icon">👍</span>
                        <span>{{ $pageNames[$loop->index] ?? ($page->page_number . '. পাতা') }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Calendar --}}
            <div class="ep-widget">
                <div class="ep-cal-hd">
                    <button class="ep-cal-nav" onclick="calMove(-1)">‹</button>
                    <span id="calLabel">March 2026</span>
                    <button class="ep-cal-nav" onclick="calMove(1)">›</button>
                </div>
                <div class="ep-cal-grid">
                    <div class="ep-cal-dow">
                        @foreach(['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $d)
                            <div class="ep-cal-dl">{{ $d }}</div>
                        @endforeach
                    </div>
                    <div class="ep-cal-days" id="calDays"></div>
                </div>
            </div>

            {{-- Ads --}}
            @foreach([
                    [$settings->sidebar_ad1_image, $settings->sidebar_ad1_url],
                    [$settings->sidebar_ad2_image, $settings->sidebar_ad2_url],
                ] as [$adImg, $adUrl])
                <div class="ep-widget">
                    <div class="ep-ad-slot">
                        @if($adImg)
                            <a href="{{ $adUrl ?? '#' }}" target="_blank">
                                <img src="{{ asset('storage/'.$adImg) }}"
                                    alt="Ad" style="max-width:100%; height:auto;">
                            </a>
                        @else
                            <strong>ADVERTISE YOUR<br>BUSINESS HERE!</strong>
                            <small>250 x 250 px</small>
                            <a href="#">CONTACT US NOW</a>
                        @endif
                    </div>
                </div>
                @endforeach

        </aside>

    </div>

    {{-- ============================================================
    HOTSPOT ZOOM / ARTICLE MODAL
    ============================================================ --}}
    <div class="hz-overlay" id="hzOverlay" onclick="hzBackdrop(event)">
        <div class="hz-box" id="hzBox">

            {{-- Header / toolbar --}}
            <div class="hz-header">
                <span class="hz-title" id="hzTitle">নিবন্ধ</span>
                <div class="hz-toolbar">
                    <button class="hz-btn" onclick="hzZoomOut()">−</button>
                    <span class="hz-zoom-level" id="hzZoomPct">100%</span>
                    <button class="hz-btn" onclick="hzZoomIn()">+</button>
                    <button class="hz-btn" onclick="hzReadFull()">বিস্তারিত পড়ুন</button>
                    <button class="hz-btn dl" onclick="hzDownload()">⬇️ ডাউনলোড</button>
                    <button class="hz-btn close-btn" onclick="hzClose()">✕</button>
                </div>
            </div>

            {{-- Cropped image --}}
            <div class="hz-body" id="hzBody">
                <div class="hz-canvas-wrap" id="hzCanvasWrap">
                    <canvas id="hzCanvas"></canvas>
                </div>
            </div>

            {{-- Article text panel (hidden until "বিস্তারিত পড়ুন") --}}
            <div class="hz-article-panel" id="hzArticlePanel">
                <div class="hz-article-scroll">
                    <div class="hz-article-title" id="hzArtTitle">
                        <div class="skel" style="width:80%"></div>
                        <div class="skel" style="width:55%"></div>
                    </div>
                    <div class="hz-article-meta" id="hzArtMeta"></div>
                    <div class="hz-article-summary" id="hzArtSummary"></div>
                    <div class="hz-article-content" id="hzArtContent">
                        <div class="skel"></div>
                        <div class="skel" style="width:88%"></div>
                        <div class="skel" style="width:72%"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    @php
        $pagesJson = $sortedPages->map(fn($p) => [
            'id' => $p->id,
            'page_number' => $p->page_number,
            'image_url' => $p->image_path ? asset('storage/' . $p->image_path) : null,
            'width' => $p->width ?? 800,
            'height' => $p->height ?? 1200,
            'hotspots' => $p->hotspots->map(fn($h) => [
                'id' => $h->id,
                'article_id' => $h->article_id,
                'title' => $h->article->title ?? 'নিবন্ধ',
                'x' => $h->x,
                'y' => $h->y,
                'w' => $h->width,
                'h' => $h->height,
            ])->values(),
        ])->values();
    @endphp

    <script>
        /* ================================================================
           DATA
        ================================================================ */
        const PAGES = {!! json_encode($pagesJson) !!};
        const ARTICLE_URL = '{{ url("article/__ID__") }}';
        const EDITION_DATES = {!! json_encode($editionDates ?? []) !!}; // ['2026-03-14', ...]
        const EDITION_URL_MAP = {!! json_encode(
        \App\Models\Edition::latest('edition_date')->get()
            ->mapWithKeys(fn($e) => [\Carbon\Carbon::parse($e->edition_date)->format('Y-m-d') => $e->id])
    ) !!};

        /* ================================================================
           STATE
        ================================================================ */
        let curIdx = 0;
        let hzScale = 1;
        let curHotspot = null;
        let curPage = null;
        let artLoaded = false;

        /* ================================================================
           NEWSPAPER VIEWER — fit-to-box, no zoom
        ================================================================ */
        const epImg = document.getElementById('epNewspaper');
        const hotspotLayer = document.getElementById('epHotspotLayer');
        const readerInner = document.getElementById('epReaderInner');
        const pageInfoLabel = document.getElementById('pageInfoLabel');

        function onImgLoad() {
            positionHotspotLayer();
            renderHotspots();
        }

        /* After image renders, size the overlay to match the rendered image */
        function positionHotspotLayer() {
            if (!epImg || !hotspotLayer) return;
            const r = epImg.getBoundingClientRect();
            hotspotLayer.style.width = r.width + 'px';
            hotspotLayer.style.height = r.height + 'px';
            hotspotLayer.style.top = (epImg.offsetTop) + 'px';
            hotspotLayer.style.left = (epImg.offsetLeft) + 'px';
        }

        window.addEventListener('resize', () => {
            positionHotspotLayer();
        });

        /* Render hotspots as % of the overlay */
        function renderHotspots() {
            if (!hotspotLayer || !curPage) return;
            hotspotLayer.innerHTML = '';

            const pageW = curPage.width || 800;
            const pageH = curPage.height || 1200;

            curPage.hotspots.forEach(h => {
                const el = document.createElement('div');
                el.className = 'ep-hotspot';
                el.style.cssText = `
                    left:   ${(h.x / pageW) * 100}%;
                    top:    ${(h.y / pageH) * 100}%;
                    width:  ${(h.w / pageW) * 100}%;
                    height: ${(h.h / pageH) * 100}%;
                `;
                const tip = document.createElement('div');
                tip.className = 'ep-hotspot-tip';
                tip.textContent = h.title;
                el.appendChild(tip);

                el.addEventListener('click', e => {
                    e.stopPropagation();
                    openHotspotZoom(h, curPage);
                });

                hotspotLayer.appendChild(el);
            });
        }

        /* ================================================================
           PAGE NAVIGATION
        ================================================================ */
        function goToIdx(idx) {
            if (idx < 0 || idx >= PAGES.length) return;
            curIdx = idx;
            curPage = PAGES[idx];

            // Update nav buttons
            document.querySelectorAll('.ep-pb[data-idx]').forEach(b => {
                b.classList.toggle('active', parseInt(b.dataset.idx) === idx);
            });

            // Update thumbnails
            document.querySelectorAll('.ep-thumb').forEach((t, i) => {
                t.classList.toggle('active', i === idx);
            });

            // Scroll sidebar thumb into view
            const activeThumb = document.getElementById('thumb' + idx);
            activeThumb?.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });

            // Page info label
            if (pageInfoLabel) pageInfoLabel.textContent = `পাতা ${curPage.page_number} / ${PAGES.length}`;

            // Update prev/next buttons
            document.getElementById('btnPrev').classList.toggle('disabled', idx === 0);
            document.getElementById('btnNext').classList.toggle('disabled', idx === PAGES.length - 1);
            const pf = document.getElementById('btnPrevFoot');
            const nf = document.getElementById('btnNextFoot');
            if (pf) pf.disabled = (idx === 0);
            if (nf) nf.disabled = (idx === PAGES.length - 1);

            // Swap image
            if (!epImg || !curPage.image_url) return;
            epImg.classList.add('loading');
            epImg.src = curPage.image_url;
            epImg.onload = () => {
                epImg.classList.remove('loading');
                positionHotspotLayer();
                renderHotspots();
            };
        }

        window.prevPage = () => goToIdx(curIdx - 1);
        window.nextPage = () => goToIdx(curIdx + 1);

        /* Init */
        if (PAGES.length > 0) {
            curPage = PAGES[0];
            document.getElementById('btnPrev')?.classList.add('disabled');
            if (PAGES.length === 1) document.getElementById('btnNext')?.classList.add('disabled');
        }

        /* ================================================================
           PAGE ACTIONS
        ================================================================ */
        window.printPage = function () {
            if (!curPage?.image_url) return;
            const w = window.open('', '_blank');
            w.document.write(`<html><head><title>পাতা ${curPage.page_number}</title>
            <style>body{margin:0}img{width:100%;height:auto}</style></head>
            <body><img src="${curPage.image_url}" onload="window.print();window.close()"></body></html>`);
            w.document.close();
        };

        window.copyLink = function () {
            navigator.clipboard.writeText(window.location.href)
                .then(() => alert('লিংক কপি হয়েছে!'))
                .catch(() => prompt('লিংক কপি করুন:', window.location.href));
        };

        window.downloadPage = function () {
            if (!curPage?.image_url) return;
            fetch(curPage.image_url)
                .then(r => r.blob())
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = `page-${curPage.page_number}.jpg`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    setTimeout(() => URL.revokeObjectURL(a.href), 3000);
                })
                .catch(() => window.open(curPage.image_url, '_blank'));
        };

        /* ================================================================
           HOTSPOT ZOOM MODAL
        ================================================================ */
        function openHotspotZoom(hotspot, page) {
            curHotspot = hotspot;
            hzScale = 1;
            artLoaded = false;

            document.getElementById('hzTitle').textContent = hotspot.title || 'নিবন্ধ';
            document.getElementById('hzZoomPct').textContent = '100%';

            // Hide article panel
            document.getElementById('hzArticlePanel').classList.remove('open');

            // Reset article content skeletons
            document.getElementById('hzArtTitle').innerHTML =
                '<div class="skel" style="width:80%"></div><div class="skel" style="width:55%"></div>';
            document.getElementById('hzArtSummary').innerHTML = '';
            document.getElementById('hzArtContent').innerHTML =
                '<div class="skel"></div><div class="skel" style="width:88%"></div><div class="skel" style="width:72%"></div>';
            document.getElementById('hzArtMeta').innerHTML = '';

            document.getElementById('hzOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';

            // Load and crop image onto canvas
            const img = new Image();
            img.crossOrigin = 'anonymous';
            // FIND and REPLACE the entire img.onload section:
img.onload = function () {
    const natW = img.naturalWidth;
    const natH = img.naturalHeight;

    // ← KEY FIX: hotspot x,y,w,h are stored as pixels relative to
    // the original image size used in the hotspot mapper.
    // Use the stored page dimensions to scale correctly.
    const pageW = page.width  || natW;
    const pageH = page.height || natH;

    // Scale factor from stored page size to actual image size
    const scX = natW / pageW;
    const scY = natH / pageH;

    const hx = hotspot.x * scX;
    const hy = hotspot.y * scY;
    const hw = hotspot.w * scX;
    const hh = hotspot.h * scY;

    // Small padding — only 10px, not 40px which was pulling in neighbours
    const pad = 8;
    const sx = Math.max(0, hx - pad);
    const sy = Math.max(0, hy - pad);
    const sw = Math.min(hw + pad * 2, natW - sx);
    const sh = Math.min(hh + pad * 2, natH - sy);

    const dpr = window.devicePixelRatio || 1;
    const canvas = document.getElementById('hzCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width  = sw * dpr;
    canvas.height = sh * dpr;
    canvas.style.width  = sw + 'px';
    canvas.style.height = sh + 'px';
    ctx.drawImage(img, sx, sy, sw, sh, 0, 0, sw * dpr, sh * dpr);

    canvas._sw = sw;
    canvas._sh = sh;
    canvas._sx = sx;
    canvas._sy = sy;

    const modalMaxW = Math.min(sw + 32, window.innerWidth * 0.92);
    document.getElementById('hzBox').style.width = modalMaxW + 'px';
    document.getElementById('hzBox').style.maxWidth = '92vw';

    applyHzZoom();
};
            img.src = page.image_url;
        }

        function applyHzZoom() {
            const wrap = document.getElementById('hzCanvasWrap');
            const canvas = document.getElementById('hzCanvas');
            const sw = canvas._sw || 400;
            const sh = canvas._sh || 300;
            canvas.style.width = (sw * hzScale) + 'px';
            canvas.style.height = (sh * hzScale) + 'px';
            document.getElementById('hzZoomPct').textContent = Math.round(hzScale * 100) + '%';
        }

        window.hzZoomIn = () => { hzScale = Math.min(hzScale + 0.25, 4); applyHzZoom(); };
        window.hzZoomOut = () => { hzScale = Math.max(hzScale - 0.25, 0.4); applyHzZoom(); };

        window.hzClose = function() {
            document.getElementById('hzOverlay').classList.remove('open');
            document.body.style.overflow = '';
            document.getElementById('hzBox').style.width = 'auto'; // ← reset
        };

        window.hzBackdrop = function (e) {
            if (e.target === document.getElementById('hzOverlay')) hzClose();
        };

        window.hzDownload = function () {
            const canvas = document.getElementById('hzCanvas');
            try {
                canvas.toBlob(blob => {
                    if (!blob) { window.open(curPage?.image_url, '_blank'); return; }
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = (curHotspot?.title || 'article').replace(/[^a-zA-Z0-9\u0980-\u09FF]/g, '_') + '.png';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    setTimeout(() => URL.revokeObjectURL(a.href), 3000);
                }, 'image/png');
            } catch (e) {
                window.open(curPage?.image_url, '_blank');
            }
        };

        /* Load and show full article text */
        window.hzReadFull = function () {
            const panel = document.getElementById('hzArticlePanel');
            panel.classList.add('open');

            if (artLoaded || !curHotspot?.article_id) return;

            const url = ARTICLE_URL.replace('__ID__', curHotspot.article_id);
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    artLoaded = true;
                    document.getElementById('hzArtTitle').textContent = data.title || curHotspot.title;
                    document.getElementById('hzArtMeta').innerHTML =
                        [data.category ? `<span style="color:#c0392b;font-weight:700">${data.category}</span>` : '',
                        data.author ? `✍️ ${data.author}` : '',
                        data.edition_date || ''].filter(Boolean).join(' &nbsp;·&nbsp; ');
                    const sumEl = document.getElementById('hzArtSummary');
                    sumEl.textContent = data.summary || '';
                    sumEl.style.display = data.summary ? 'block' : 'none';
                    document.getElementById('hzArtContent').innerHTML = data.content || '<p>বিস্তারিত পাওয়া যায়নি।</p>';
                })
                .catch(() => {
                    document.getElementById('hzArtTitle').textContent = curHotspot?.title || 'নিবন্ধ';
                    document.getElementById('hzArtContent').innerHTML = '<p>তথ্য লোড করতে সমস্যা হয়েছে।</p>';
                });
        };

        /* ================================================================
           CALENDAR — highlight only dates with editions
        ================================================================ */
        let calYear = new Date().getFullYear();
        let calMonth = new Date().getMonth();

        const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        function renderCalendar() {
            document.getElementById('calLabel').textContent = MONTH_NAMES[calMonth] + ' ' + calYear;
            const grid = document.getElementById('calDays');
            grid.innerHTML = '';
            const firstDay = new Date(calYear, calMonth, 1).getDay();
            const daysInM = new Date(calYear, calMonth + 1, 0).getDate();
            const today = new Date();

            // Empty cells
            for (let i = 0; i < firstDay; i++) {
                const e = document.createElement('div');
                e.className = 'ep-cal-day';
                grid.appendChild(e);
            }

            for (let d = 1; d <= daysInM; d++) {
                const el = document.createElement('div');
                const dateStr = `${calYear}-${String(calMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const hasEd = EDITION_DATES.includes(dateStr);
                const isToday = (d === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear());

                el.className = 'ep-cal-day' + (hasEd ? ' has-edition' : '') + (isToday ? ' today' : '');
                el.textContent = d;

                if (hasEd) {
                    const edId = EDITION_URL_MAP[dateStr];
                    if (edId) el.onclick = () => window.location = '/edition/' + edId;
                    el.title = 'এই তারিখের সংস্করণ দেখুন';
                }

                grid.appendChild(el);
            }
        }

        window.calMove = function (dir) {
            calMonth += dir;
            if (calMonth < 0) { calMonth = 11; calYear--; }
            if (calMonth > 11) { calMonth = 0; calYear++; }
            renderCalendar();
        };

        /* ================================================================
           KEYBOARD
        ================================================================ */
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') hzClose();
            if (e.key === 'ArrowRight' || e.key === 'PageDown') nextPage();
            if (e.key === 'ArrowLeft' || e.key === 'PageUp') prevPage();
            if (e.key === '+' || e.key === '=') hzZoomIn();
            if (e.key === '-') hzZoomOut();
        });

        /* ================================================================
           INIT
        ================================================================ */
        renderCalendar();

        // Set initial prev/next state
        if (PAGES.length <= 1) {
            document.getElementById('btnPrev')?.classList.add('disabled');
            document.getElementById('btnNext')?.classList.add('disabled');
            const pf = document.getElementById('btnPrevFoot');
            const nf = document.getElementById('btnNextFoot');
            if (pf) pf.disabled = true;
            if (nf) nf.disabled = true;
        }
    </script>
@endpush