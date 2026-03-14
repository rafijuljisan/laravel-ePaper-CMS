<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $edition ? $edition->title . ' - ePaper' : 'ePaper Archive' }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-200 h-screen flex flex-col overflow-hidden text-gray-800">

    @if(!$edition || $edition->pages->isEmpty())
        <div class="flex-1 flex items-center justify-center">
            <h2 class="text-2xl font-bold text-gray-500">No published editions available.</h2>
        </div>
    @else
        <header class="bg-white shadow-md z-20 p-3 flex flex-col md:flex-row justify-between items-center border-b-4 border-red-700 gap-3">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl md:text-2xl font-bold text-black">{{ $edition->title }}</h1>
                <span class="text-gray-500 text-sm hidden md:inline">{{ $edition->edition_date->format('l, j F Y') }}</span>
            </div>
            
            <div class="flex items-center space-x-2 w-full md:w-auto">
                <input type="text" id="datePicker" class="border border-gray-300 p-2 rounded w-full md:w-40 text-center cursor-pointer bg-white" placeholder="Select Date" value="{{ $edition->edition_date->format('Y-m-d') }}" readonly>
                
                <a href="{{ route('epaper.index') }}" class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-700 whitespace-nowrap text-sm md:text-base">Today</a>
            </div>
        </header>

        <div class="flex-1 flex flex-col md:flex-row overflow-hidden" 
             x-data="{ 
                activePage: 1, 
                pages: {{ json_encode($edition->pages->map(fn($p) => ['num' => $p->page_number, 'img' => asset('storage/'.$p->image_path)])) }},
                get activeImage() { return this.pages.find(p => parseInt(p.num) === parseInt(this.activePage))?.img; },
                nextPage() { if(this.activePage < this.pages.length) this.activePage++; },
                prevPage() { if(this.activePage > 1) this.activePage--; }
             }">

            <div class="md:hidden bg-white p-2 border-b flex justify-between items-center">
                <span class="text-sm font-bold text-gray-600">Page Navigation:</span>
                <select x-model.number="activePage" class="border border-gray-300 rounded p-1 text-sm bg-gray-50">
                    <template x-for="page in pages" :key="page.num">
                        <option :value="page.num" x-text="'Page ' + page.num"></option>
                    </template>
                </select>
            </div>

            <aside class="hidden md:flex w-48 bg-gray-100 border-r border-gray-300 flex-col overflow-y-auto no-scrollbar pb-10">
                <div class="bg-red-800 text-white text-center py-2 font-bold sticky top-0 z-10">All Pages</div>
                <template x-for="page in pages" :key="page.num">
                    <div @click="activePage = page.num" 
                         class="p-3 cursor-pointer border-b border-gray-300 hover:bg-white transition"
                         :class="activePage === page.num ? 'bg-white border-l-4 border-l-red-600' : ''">
                        <div class="relative w-full h-48 bg-gray-300 shadow-sm flex items-center justify-center overflow-hidden">
                            <img :src="page.img" class="w-full h-full object-cover">
                            <span class="absolute top-1 right-1 bg-black text-white text-xs px-2 py-1 rounded shadow" x-text="page.num"></span>
                        </div>
                    </div>
                </template>
            </aside>

            <main class="flex-1 relative bg-gray-300 flex justify-center items-center overflow-auto p-2 md:p-4 shadow-inner">
                
                <button @click="prevPage()" x-show="activePage > 1" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-90 transition z-30 shadow-lg">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                
                <div class="relative w-full h-full flex justify-center items-center p-2 md:p-6">
                    
                    <div class="relative inline-block shadow-2xl bg-white min-w-[300px] min-h-[400px]">
                        <img :src="activeImage" class="max-w-full max-h-[80vh] md:max-h-[90vh] transition-opacity duration-300 ease-in-out object-contain block">
                        
                        <template x-for="hotspot in activeHotspots" :key="hotspot.id">
                            <a :href="'/article/' + hotspot.slug"
                               :title="hotspot.title"
                               class="absolute z-20 bg-blue-500/10 hover:bg-blue-600/30 border border-transparent hover:border-blue-500 transition-all duration-200 cursor-pointer rounded"
                               :style="`
                                    left: ${(hotspot.x / activePageData.width) * 100}%; 
                                    top: ${(hotspot.y / activePageData.height) * 100}%; 
                                    width: ${(hotspot.w / activePageData.width) * 100}%; 
                                    height: ${(hotspot.h / activePageData.height) * 100}%;
                               `">
                            </a>
                        </template>
                    </div>

                </div>
                
                <button @click="nextPage()" x-show="activePage < pages.length" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-60 text-white p-3 rounded-full hover:bg-opacity-90 transition z-30 shadow-lg">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>

            </main>

            <aside class="hidden lg:block w-64 bg-white border-l border-gray-300 overflow-y-auto shadow-lg z-10">
                <div class="bg-gray-800 text-white text-center py-2 font-bold sticky top-0">Page Index</div>
                <ul class="divide-y divide-gray-200">
                    <template x-for="page in pages" :key="page.num">
                        <li>
                            <button @click="activePage = page.num" 
                                    class="w-full text-left px-4 py-3 hover:bg-gray-100 transition"
                                    :class="activePage === page.num ? 'font-bold text-red-700 bg-gray-50 border-l-4 border-red-700' : 'text-gray-700'">
                                <span x-text="'Page ' + page.num"></span>
                            </button>
                        </li>
                    </template>
                </ul>
            </aside>
        </div>

    @endif

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if(document.getElementById('datePicker')) {
                flatpickr("#datePicker", {
                    enable: @json($availableDates ?? []), // Only allow dates from our database array
                    dateFormat: "Y-m-d",
                    disableMobile: "true", // Forces the nice UI on mobile devices
                    onChange: function(selectedDates, dateStr, instance) {
                        window.location.href = '/archive/' + dateStr;
                    }
                });
            }
        });
    </script>
</body>
</html>