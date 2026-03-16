<x-filament-panels::page>

    {{-- Edition & Page selectors --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
        <div>
            <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #374151; margin-bottom: 0.25rem;">1. Select Edition</label>
            <select wire:model.live="editionId" style="width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem;">
                <option value="">-- Choose an Edition --</option>
                @foreach($this->editions as $edition)
                    <option value="{{ $edition->id }}">{{ $edition->title }} ({{ $edition->edition_date->format('d M Y') }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display: block; font-size: 0.875rem; font-weight: bold; color: #374151; margin-bottom: 0.25rem;">2. Select Page</label>
            <select wire:model.live="pageId" style="width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem;" @if(!$editionId) disabled @endif>
                <option value="">-- Choose a Page --</option>
                @foreach($this->pages as $page)
                    <option value="{{ $page->id }}">Page {{ $page->page_number }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($this->activePage)
    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 1rem; align-items: flex-start;">

        {{-- Image Canvas --}}
        <div style="flex: 1; min-width: 60%; background: #f3f4f6; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: auto;">
            <p style="font-size: 0.875rem; color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">🔍 Draw boxes on the image. You can draw multiple before saving.</p>

            <div x-data="{
                    isDrawing: false,
                    startX: 0, startY: 0,
                    currentX: 0, currentY: 0,
                    startDraw(e) {
                        let rect = this.$refs.image.getBoundingClientRect();
                        this.isDrawing = true;
                        this.startX = e.clientX - rect.left;
                        this.startY = e.clientY - rect.top;
                        this.currentX = this.startX;
                        this.currentY = this.startY;
                    },
                    draw(e) {
                        if(!this.isDrawing) return;
                        let rect = this.$refs.image.getBoundingClientRect();
                        this.currentX = e.clientX - rect.left;
                        this.currentY = e.clientY - rect.top;
                    },
                    stopDraw(e) {
                        if(!this.isDrawing) return;
                        this.isDrawing = false;
                        let nw = this.$refs.image.naturalWidth;
                        let nh = this.$refs.image.naturalHeight;
                        let scaleX = nw / this.$refs.image.clientWidth;
                        let scaleY = nh / this.$refs.image.clientHeight;
                        let left = Math.min(this.startX, this.currentX) * scaleX;
                        let top = Math.min(this.startY, this.currentY) * scaleY;
                        let width = Math.abs(this.currentX - this.startX) * scaleX;
                        let height = Math.abs(this.currentY - this.startY) * scaleY;
                        if(width > 20 && height > 20) {
                            $wire.draftHotspot(left, top, width, height, nw, nh);
                        }
                    }
                 }"
                 style="position: relative; display: inline-block; cursor: crosshair; user-select: none; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);"
                 @mousedown="startDraw"
                 @mousemove.window="draw"
                 @mouseup.window="stopDraw"
                 @dragstart.prevent>

                <img x-ref="image"
                     src="{{ asset('storage/' . $this->activePage->image_path) }}"
                     style="display: block; max-width: 100%; height: auto; pointer-events: none;">

                {{-- Saved hotspots (blue) --}}
                @foreach($this->activePage->hotspots as $hotspot)
                <div wire:key="hotspot-{{ $hotspot->id }}"
                     style="position: absolute; border: 2px solid #3b82f6; background-color: rgba(59,130,246,0.2); pointer-events: none; z-index: 10;
                        {{ 'left:'.($hotspot->x/($this->activePage->width??800)*100).'%;'.
                           'top:'.($hotspot->y/($this->activePage->height??1200)*100).'%;'.
                           'width:'.($hotspot->width/($this->activePage->width??800)*100).'%;'.
                           'height:'.($hotspot->height/($this->activePage->height??1200)*100).'%;' }}">
                    <span style="position:absolute;top:0;left:0;background:#2563eb;color:white;font-size:10px;padding:0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;">
                        {{ $hotspot->article->title ?? 'Unknown' }}
                    </span>
                    <button wire:click="deleteHotspot({{ $hotspot->id }})"
                            style="pointer-events:auto;position:absolute;top:4px;right:4px;background:#dc2626;color:white;border-radius:9999px;width:20px;height:20px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;">✕</button>
                </div>
                @endforeach

                {{-- Pending hotspots (orange) --}}
                @foreach($pendingHotspots as $ph)
                <div wire:key="pending-{{ $ph['tempId'] }}"
                     style="position: absolute; border: 2px solid #f97316; background-color: rgba(249,115,22,0.25); pointer-events: none; z-index: 20;
                        {{ 'left:'.($ph['x']/($this->activePage->width??800)*100).'%;'.
                           'top:'.($ph['y']/($this->activePage->height??1200)*100).'%;'.
                           'width:'.($ph['width']/($this->activePage->width??800)*100).'%;'.
                           'height:'.($ph['height']/($this->activePage->height??1200)*100).'%;' }}">
                    <span style="position:absolute;top:0;left:0;background:#ea580c;color:white;font-size:10px;padding:0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:100%;">
                        ⏳ {{ $ph['articleTitle'] }}
                    </span>
                </div>
                @endforeach

                {{-- Current draft (green) --}}
                @if($draftX)
                <div style="position:absolute;border:3px solid #22c55e;background-color:rgba(34,197,94,0.3);z-index:30;
                    {{ 'left:'.($draftX/($this->activePage->width??800)*100).'%;'.
                       'top:'.($draftY/($this->activePage->height??1200)*100).'%;'.
                       'width:'.($draftW/($this->activePage->width??800)*100).'%;'.
                       'height:'.($draftH/($this->activePage->height??1200)*100).'%;' }}">
                </div>
                @endif

                {{-- Drawing preview (red) --}}
                <div x-show="isDrawing"
                     style="position:absolute;border:2px solid #ef4444;background-color:rgba(239,68,68,0.3);"
                     :style="`left:${Math.min(startX,currentX)}px;top:${Math.min(startY,currentY)}px;width:${Math.abs(currentX-startX)}px;height:${Math.abs(currentY-startY)}px;`">
                </div>

            </div>
        </div>

        {{-- Right Panel --}}
        <div style="width: 320px; background: white; padding: 1.5rem; border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; position: sticky; top: 1rem;">

            {{-- Current draft form --}}
            @if($draftX)
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: bold; color: #1f2937; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #e5e7eb;">
                    🟢 New Hotspot
                </h3>
                <div style="background:#eff6ff;padding:0.75rem;border-radius:0.25rem;font-size:0.875rem;color:#1e40af;margin-bottom:1rem;">
                    Box drawn! Select an article then click <strong>Add to Queue</strong>.
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <label style="display:block;font-size:0.875rem;font-weight:bold;color:#374151;margin-bottom:0.25rem;">Select Article</label>
                    <select wire:model="selectedArticleId" style="width:100%;border:1px solid #d1d5db;border-radius:0.375rem;padding:0.5rem;">
                        <option value="">-- No Article (optional) --</option>
                        @foreach($this->articles as $article)
                            <option value="{{ $article->id }}">{{ $article->title }}</option>
                        @endforeach
                    </select>
                    @error('selectedArticleId') <span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;font-size:0.75rem;color:#6b7280;background:#f9fafb;padding:0.5rem;border-radius:0.25rem;margin-bottom:0.75rem;">
                    <p>X: {{ round($draftX) }}px</p>
                    <p>Y: {{ round($draftY) }}px</p>
                    <p>W: {{ round($draftW) }}px</p>
                    <p>H: {{ round($draftH) }}px</p>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <x-filament::button wire:click="addToPending" color="warning" style="flex:1;">
                        + Add to Queue
                    </x-filament::button>
                    <x-filament::button type="button" color="gray" wire:click="$set('draftX', null)">
                        Cancel
                    </x-filament::button>
                </div>
            </div>
            @endif

            {{-- Pending queue --}}
            @if(count($pendingHotspots) > 0)
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: bold; color: #92400e; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid #fde68a;">
                    🟠 Queue ({{ count($pendingHotspots) }})
                </h3>
                <div style="display:flex;flex-direction:column;gap:0.5rem;margin-bottom:1rem;max-height:200px;overflow-y:auto;">
                    @foreach($pendingHotspots as $index => $ph)
                    <div style="display:flex;align-items:center;justify-content:space-between;background:#fff7ed;border:1px solid #fed7aa;border-radius:0.375rem;padding:0.5rem 0.75rem;font-size:0.8rem;">
                        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#374151;">{{ $ph['articleTitle'] }}</span>
                        <button wire:click="removePending({{ $index }})" style="color:#ef4444;background:none;border:none;cursor:pointer;font-size:1rem;margin-left:0.5rem;">✕</button>
                    </div>
                    @endforeach
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <x-filament::button wire:click="saveAllHotspots" color="success" style="flex:1;">
                        💾 Save All ({{ count($pendingHotspots) }})
                    </x-filament::button>
                    <x-filament::button wire:click="clearPending" color="danger">
                        🗑
                    </x-filament::button>
                </div>
            </div>
            @endif

            {{-- Empty state --}}
            @if(!$draftX && count($pendingHotspots) === 0)
            <div style="text-align:center;color:#9ca3af;padding:2rem 0;">
                <svg style="width:3rem;height:3rem;margin:0 auto 0.5rem;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/>
                </svg>
                <p style="font-size:0.875rem;">Draw boxes on the image to start mapping hotspots.</p>
            </div>
            @endif

        </div>
    </div>
    @endif

</x-filament-panels::page>