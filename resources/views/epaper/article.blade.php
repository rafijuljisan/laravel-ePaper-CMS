<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>{{ $article->title }} | ePaper</title>
    <meta name="description" content="{{ Str::limit(strip_tags($article->summary ?? $article->content), 150) }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">

    <header class="bg-white shadow-sm border-b-4 border-red-700 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('epaper.index') }}" class="text-2xl font-black text-black tracking-tight">
                Daily<span class="text-red-700">Morning</span>
            </a>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('epaper.index') }}" class="text-sm font-semibold text-gray-600 hover:text-red-700 flex items-center gap-1 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to ePaper
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-8 md:py-12 bg-white mt-6 shadow-sm rounded-lg border border-gray-100">
        
        <div class="flex items-center gap-2 text-sm text-red-700 font-bold mb-4 uppercase tracking-wider">
            <span>{{ $article->category->name ?? 'News' }}</span>
            <span class="text-gray-300">•</span>
            <span class="text-gray-500">Page {{ $article->page_number }}</span>
        </div>

        <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-6">
            {{ $article->title }}
        </h1>

        <div class="flex items-center justify-between border-y border-gray-200 py-3 mb-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold">
                    {{ strtoupper(substr($article->author ?? 'R', 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 text-sm">By {{ $article->author ?? 'Staff Reporter' }}</p>
                    <p class="text-xs text-gray-500">{{ $article->created_at->format('l, F j, Y \a\t h:i A') }}</p>
                </div>
            </div>
        </div>

        @if($article->summary)
            <div class="text-xl text-gray-600 font-medium leading-relaxed mb-8 border-l-4 border-red-700 pl-4 bg-gray-50 py-2 rounded-r">
                {{ $article->summary }}
            </div>
        @endif

        <article class="prose prose-lg md:prose-xl max-w-none text-gray-700 prose-a:text-red-700 hover:prose-a:text-red-800">
            {!! $article->content ?? '<p>This is a breaking news placeholder. Full story coming soon.</p>' !!}
        </article>

    </main>

    <footer class="bg-gray-800 text-white text-center py-6 mt-12">
        <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Daily Morning ePaper. All rights reserved.</p>
    </footer>

</body>
</html>