@php
    // Group consecutive services+documents blocks into pairs for 2-col layout
    $blocks = $content ?? [];
    $grouped = [];
    $i = 0;
    while ($i < count($blocks)) {
        $current = $blocks[$i];
        if ($current['type'] === 'services' && isset($blocks[$i+1]) && $blocks[$i+1]['type'] === 'documents') {
            $grouped[] = ['type' => 'services_with_docs', 'services' => $current, 'docs' => $blocks[$i+1]];
            $i += 2;
        } else {
            $grouped[] = $current;
            $i++;
        }
    }
@endphp

@foreach($grouped as $block)
    <div class="block-container relative">
        @switch($block['type'])
            @case('marquee')
                @php
                    $effectClass = [
                        'none' => '',
                        'shadow' => 'marquee-shadow',
                        'glow' => 'marquee-glow',
                        'outline' => 'marquee-outline'
                    ][$block['data']['text_effect'] ?? 'none'] ?? '';
                    
                    $textClasses = implode(' ', [
                        $block['data']['font_size'] ?? 'text-base',
                        $block['data']['font_weight'] ?? 'font-medium',
                        'tracking-wide',
                        $effectClass
                    ]);

                    $speed = $block['data']['speed'] ?? 40;
                    $isNumericSpeed = is_numeric($speed);
                    $duration = $isNumericSpeed ? (1200 / max($speed, 1)) : 30;
                    
                    $direction = $block['data']['direction'] ?? '';
                    $animationClass = $direction === 'reverse' ? 'animate-marquee-reverse' : 'animate-marquee';
                    if (!$isNumericSpeed && str_contains($speed, 'animate-marquee')) {
                        $animationClass = $speed;
                    }

                    $gap = $block['data']['gap'] ?? '5rem';
                @endphp
                <div class="w-full overflow-hidden whitespace-nowrap py-4 shadow-inner relative z-10" 
                     style="background-color: {{ $block['data']['bg_color'] ?? '#1e40af' }}; color: {{ $block['data']['text_color'] ?? '#ffffff' }};">
                    <div class="flex items-center w-max min-w-full {{ $animationClass }} hover:pause-marquee"
                         style="{{ $isNumericSpeed ? "animation-duration: {$duration}s;" : "" }}">
                        {{-- Render items twice for infinite loop --}}
                        @for($i = 0; $i < 2; $i++)
                            <div class="flex items-center flex-shrink-0" style="padding-right: {{ $gap }};">
                                @foreach($block['data']['items'] as $item)
                                    <div class="flex items-center">
                                        @if(!empty($item['link']))
                                            <a href="{{ $item['link'] }}" class="hover:underline {{ $textClasses }}">
                                                <span x-show="currentLang === 'en'">{{ $item['text'] }}</span>
                                                <span x-show="currentLang === 'ml'" x-cloak>{{ $item['text_ml'] ?? $item['text'] }}</span>
                                            </a>
                                        @else
                                            <span class="{{ $textClasses }}">
                                                <span x-show="currentLang === 'en'">{{ $item['text'] }}</span>
                                                <span x-show="currentLang === 'ml'" x-cloak>{{ $item['text_ml'] ?? $item['text'] }}</span>
                                            </span>
                                        @endif
                                        <span class="opacity-40 text-2xl" style="margin-left: calc({{ $gap }} / 2); margin-right: calc({{ $gap }} / 2);">
                                            {{ $block['data']['separator'] ?? '•' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </div>
                @break

            @case('button_link')
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 {{ $block['data']['alignment'] ?? 'text-center' }}">
                    @if(!empty($block['data']['target_page']))
                        <a href="{{ url($block['data']['target_page']) }}" 
                           class="inline-flex items-center gap-2 px-8 py-4 font-bold rounded-2xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-1"
                           style="background-color: {{ $block['data']['button_color'] ?? '#2563eb' }}; color: {{ $block['data']['text_color'] ?? '#ffffff' }};">
                            <span x-show="currentLang === 'en'">{{ $block['data']['button_text'] ?? 'Read More' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['button_text_ml'] ?? $block['data']['button_text'] ?? 'കൂടുതൽ വായിക്കുക' }}</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @endif
                </div>
                @break

            @case('hero')
                @php
                    $heroImage = $block['data']['background_image'] ?? $block['data']['image'] ?? null;
                @endphp
                <section class="relative min-h-[350px] md:h-[450px] flex items-center justify-center bg-gray-50 overflow-hidden">
                    @if($heroImage)
                        <img src="{{ media_url($heroImage) }}" 
                             class="absolute inset-0 w-full h-full object-cover" 
                             alt="{{ $block['data']['heading'] ?? 'Hero Image' }}">
                    @endif
                    <div class="relative z-10 {{ $block['data']['heading_alignment'] ?? 'text-center' }} px-6 md:px-12 py-12 md:py-20 max-w-5xl mx-auto">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 sm:mb-6 drop-shadow-md leading-tight" style="color: {{ $block['data']['heading_color'] ?? '#ffffff' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? '' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? '' }}</span>
                        </h1>
                        <p class="{{ $block['data']['text_size'] ?? 'text-base sm:text-lg md:text-xl' }} font-light leading-relaxed drop-shadow-sm max-w-3xl mx-auto" style="color: {{ $block['data']['subheading_color'] ?? '#dbeafe' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['subheading'] ?? '' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['subheading_ml'] ?? $block['data']['subheading'] ?? '' }}</span>
                        </p>
                        @if(!empty($block['data']['content']) || !empty($block['data']['content_ml']))
                            <div class="mt-8 prose prose-invert prose-lg max-w-4xl mx-auto text-left drop-shadow-sm prose-strong:text-inherit prose-em:text-inherit">
                                @if(!empty($block['data']['content_ml']))
                                    <div lang="ml" x-show="currentLang === 'ml'" x-cloak>
                                        {!! parse_tiptap_html(is_string($block['data']['content_ml']) ? $block['data']['content_ml'] : tiptap_converter()->asHTML($block['data']['content_ml'])) !!}
                                    </div>
                                @endif
                                @if(!empty($block['data']['content']))
                                    <div lang="en" x-show="currentLang === 'en'" x-cloak>
                                        {!! parse_tiptap_html(is_string($block['data']['content']) ? $block['data']['content'] : tiptap_converter()->asHTML($block['data']['content'])) !!}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </section>
                @break

             @case('rich_text')
                @php
                    $anchorId = $block['data']['anchor_id'] ?? null;
                @endphp
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16"
                     @if($anchorId) id="{{ $anchorId }}" @endif>
                    @php
                        $heading = $block['data']['heading'] ?? $block['data']['title'] ?? null;
                        $headingMl = $block['data']['heading_ml'] ?? $block['data']['title_ml'] ?? null;
                        $hasHeading = !empty($heading) || !empty($headingMl);
                    @endphp
                    @if($hasHeading)
                        <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-12">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                <span x-show="currentLang === 'en'">{{ $heading }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $headingMl ?? $heading }}</span>
                                <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                            </h2>
                        </div>
                    @endif
                    <div class="prose prose-sm sm:prose-base md:prose-lg lg:prose-xl prose-blue max-w-none prose-img:rounded-xl prose-img:shadow-lg leading-relaxed md:leading-loose prose-strong:text-inherit prose-em:text-inherit {{ $block['data']['text_size'] ?? 'text-base' }}" style="color: {{ $block['data']['text_color'] ?? '#111827' }}">
                        @if(!empty($block['data']['content_ml']))
                            <div lang="ml" x-show="currentLang === 'ml'" x-cloak>
                                {!! parse_tiptap_html(is_string($block['data']['content_ml']) ? $block['data']['content_ml'] : tiptap_converter()->asHTML($block['data']['content_ml']), $block['data']['table_size'] ?? 'table-md') !!}
                            </div>
                        @endif
                        @if(!empty($block['data']['content']))
                            <div lang="en" x-show="currentLang === 'en'" x-cloak>
                                {!! parse_tiptap_html(is_string($block['data']['content']) ? $block['data']['content'] : tiptap_converter()->asHTML($block['data']['content']), $block['data']['table_size'] ?? 'table-md') !!}
                            </div>
                        @endif
                    </div>
                </div>
                @break

            @case('image')
                @if(!empty($block['data']['image']))
                    <figure class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col items-center">
                        <div class="relative w-full {{ $block['data']['aspect_ratio'] ?? 'aspect-auto' }} overflow-hidden rounded-3xl shadow-2xl hover:shadow-blue-500/20 transition-shadow duration-500"
                             style="max-width: {{ $block['data']['width_percent'] ?? 100 }}%;">
                            <img src="{{ media_url($block['data']['image']) }}" loading="lazy"
                                 class="w-full h-full object-cover"
                                 alt="{{ $block['data']['alt'] ?? '' }}">
                        </div>
                        @if(!empty($block['data']['caption']))
                            <figcaption class="mt-4 text-center text-gray-500 italic font-medium px-4">
                                {{ $block['data']['caption'] }}
                            </figcaption>
                        @endif
                    </figure>
                @endif
                @break

            @case('video')
                <div class="mx-auto px-4 sm:px-6 lg:px-8 py-16" style="max-width: {{ $block['data']['width_percent'] ?? 100 }}%;">
                    <div class="relative aspect-video rounded-3xl overflow-hidden shadow-2xl bg-black group">
                        @if(($block['data']['type'] ?? 'url') === 'url' && !empty($block['data']['url']))
                            @php
                                $embedUrl = $block['data']['url'];
                                if (str_contains($embedUrl, 'youtube.com/watch?v=')) {
                                    $videoId = explode('v=', $embedUrl)[1];
                                    $videoId = explode('&', $videoId)[0];
                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                    $videoId = explode('youtu.be/', $embedUrl)[1];
                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                } elseif (str_contains($embedUrl, 'vimeo.com/')) {
                                    $videoId = explode('vimeo.com/', $embedUrl)[1];
                                    $embedUrl = "https://player.vimeo.com/video/{$videoId}";
                                }
                            @endphp
                            <iframe class="absolute inset-0 w-full h-full border-0" 
                                    src="{{ $embedUrl }}" 
                                    allowfullscreen 
                                    loading="lazy"></iframe>
                        @elseif(!empty($block['data']['file']))
                            <video controls class="absolute inset-0 w-full h-full object-cover" preload="metadata">
                                <source src="{{ media_url($block['data']['file']) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif

                        @if(!empty($block['data']['title']))
                            <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <p class="text-white font-semibold">
                                    <span x-show="currentLang === 'en'">{{ $block['data']['title'] }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['title_ml'] ?? $block['data']['title'] }}</span>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                @break

            @case('gallery')
                @if(!empty($block['data']['images']))
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20" @if(!empty($block['data']['anchor_id'])) id="{{ $block['data']['anchor_id'] }}" @endif>
                        @if(!empty($block['data']['heading']))
                            <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-12">
                                <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                    <span x-show="currentLang === 'en'">{{ $block['data']['heading'] }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] }}</span>
                                    <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                                </h2>
                            </div>
                        @endif
                        @php
                            $cols = $block['data']['columns'] ?? '3';
                            $gridClass = [
                                '2' => 'grid-cols-2',
                                '3' => 'grid-cols-2 md:grid-cols-3',
                                '4' => 'grid-cols-2 md:grid-cols-4',
                            ][$cols] ?? 'grid-cols-2 md:grid-cols-3';
                        @endphp
                        <div class="w-full overflow-hidden py-4">
                                @php 
                                    $galleryImages = collect($block['data']['images'] ?? [])->map(function($item) {
                                        $imagePath = \Illuminate\Support\Arr::first((array)($item['image'] ?? ''));
                                        return [
                                            'src' => $imagePath ? media_url($imagePath) : '',
                                            'type' => 'image',
                                            'title' => $item['label'] ?? ''
                                        ];
                                    })->values()->toArray();
                                    $enableMarquee = $block['data']['enable_marquee'] ?? true;
                                    $marqueeDirection = $block['data']['marquee_direction'] ?? '';
                                    $marqueeClass = $marqueeDirection === 'reverse' ? 'animate-marquee-reverse' : 'animate-marquee';
                                @endphp
                                @if($enableMarquee)
                                    <div class="flex items-stretch w-max min-w-full {{ $marqueeClass }} hover:pause-marquee" style="animation-duration: 30s;">
                                        @for($i = 0; $i < 2; $i++)
                                            <div class="flex items-stretch gap-8 px-4 flex-shrink-0">
                                                @foreach($block['data']['images'] as $index => $item)
                                                    <div class="w-64 md:w-80 flex flex-col space-y-4">
                                                        @php $imagePath = is_array($item) ? ($item['image'] ?? '') : $item; @endphp
                                                        <div class="group relative aspect-[3/4] overflow-hidden rounded-2xl shadow-sm border border-gray-100 cursor-pointer bg-white flex items-center justify-center p-2 flex-grow"
                                                             onclick='openLightbox({{ json_encode($galleryImages) }}, {{ $index }})'>
                                                            <img src="{{ media_url(\Illuminate\Support\Arr::first((array)($imagePath ?? ''))) }}" loading="lazy" 
                                                                 class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" 
                                                                 alt="{{ $item['label'] ?? 'Gallery image' }}">
                                                            <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-colors duration-500 flex items-center justify-center">
                                                                <svg class="h-10 w-10 text-white opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        @if(!empty($item['label']))
                                                            <p class="text-sm font-semibold text-gray-700 text-center">
                                                                <span x-show="currentLang === 'en'">{{ $item['label'] }}</span>
                                                                <span x-show="currentLang === 'ml'" x-cloak>{{ $item['label_ml'] ?? $item['label'] }}</span>
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endfor
                                    </div>
                                @else
                                    <div class="grid {{ $gridClass }} gap-8 px-4">
                                        @foreach($block['data']['images'] as $index => $item)
                                            <div class="flex flex-col space-y-4">
                                                @php $imagePath = is_array($item) ? ($item['image'] ?? '') : $item; @endphp
                                                <div class="group relative aspect-[3/4] overflow-hidden rounded-2xl shadow-sm border border-gray-100 cursor-pointer bg-white flex items-center justify-center p-2 flex-grow"
                                                     onclick='openLightbox({{ json_encode($galleryImages) }}, {{ $index }})'>
                                                    <img src="{{ media_url(\Illuminate\Support\Arr::first((array)($imagePath ?? ''))) }}" loading="lazy" 
                                                         class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105" 
                                                         alt="{{ $item['label'] ?? 'Gallery image' }}">
                                                    <div class="absolute inset-0 bg-blue-600/0 group-hover:bg-blue-600/10 transition-colors duration-500 flex items-center justify-center">
                                                        <svg class="h-10 w-10 text-white opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100 duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                @if(!empty($item['label']))
                                                    <p class="text-sm font-semibold text-gray-700 text-center">
                                                        <span x-show="currentLang === 'en'">{{ $item['label'] }}</span>
                                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $item['label_ml'] ?? $item['label'] }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                        </div>
                    </div>
                @endif
                @break

            @case('video_gallery')
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20" id="{{ $block['data']['anchor_id'] ?? '' }}">
                    @if(!empty($block['data']['heading']))
                        <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-12">
                            <h2 class="text-3xl md:text-4xl font-extrabold" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                <span x-show="currentLang === 'en'">{{ $block['data']['heading'] }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] }}</span>
                            </h2>
                            <div class="w-20 h-1.5 bg-blue-600 mx-auto mt-4 rounded-full"></div>
                        </div>
                    @endif

                    @if(!empty($block['data']['videos']))
                        @php
                            $gridCols = match($block['data']['columns'] ?? '3') {
                                '2' => 'md:grid-cols-2',
                                '4' => 'md:grid-cols-4',
                                default => 'md:grid-cols-3',
                            };
                        @endphp
                        <div class="grid grid-cols-1 {{ $gridCols }} gap-8">
                            @foreach($block['data']['videos'] as $item)
                                <div class="flex flex-col h-full bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 group border border-gray-100">
                                    <div class="aspect-video relative overflow-hidden bg-black">
                                        @if(($item['type'] ?? 'url') === 'url' && !empty($item['url']))
                                            @php
                                                $embedUrl = $item['url'];
                                                if (str_contains($embedUrl, 'youtube.com/watch?v=')) {
                                                    $videoId = explode('v=', $embedUrl)[1];
                                                    $videoId = explode('&', $videoId)[0];
                                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                                } elseif (str_contains($embedUrl, 'youtu.be/')) {
                                                    $videoId = explode('youtu.be/', $embedUrl)[1];
                                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                                } elseif (str_contains($embedUrl, 'vimeo.com/')) {
                                                    $videoId = explode('vimeo.com/', $embedUrl)[1];
                                                    $embedUrl = "https://player.vimeo.com/video/{$videoId}";
                                                }
                                            @endphp
                                            <iframe src="{{ $embedUrl }}" 
                                                    class="absolute inset-0 w-full h-full border-0" 
                                                    allowfullscreen 
                                                    loading="lazy"></iframe>
                                        @elseif(!empty($item['file']))
                                            <video controls class="absolute inset-0 w-full h-full object-cover" preload="metadata">
                                                <source src="{{ media_url($item['file']) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        @endif
                                    </div>
                                    @if(!empty($item['title']))
                                        <div class="p-6 text-center">
                                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                {{ $item['title'] }}
                                            </h3>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @break

            @case('split_content')
                @php
                    $hasImage = !empty($block['data']['image']);
                    $imageWidth = $block['data']['image_width'] ?? 'w-1/2';
                    $textWidthMap = [
                        'w-1/4' => 'sm:w-3/4',
                        'w-1/3' => 'sm:w-2/3',
                        'w-1/2' => 'sm:w-1/2',
                        'w-2/3' => 'sm:w-1/3',
                        'w-3/4' => 'sm:w-1/4',
                    ];
                    $textWidth = $hasImage ? ($textWidthMap[$imageWidth] ?? 'sm:w-1/2') : 'w-full max-w-4xl mx-auto';
                    $flexClasses = $hasImage ? (($block['data']['image_position'] ?? 'right') == 'left' ? 'sm:flex-row-reverse' : 'sm:flex-row') : 'justify-center';
                @endphp
                <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="flex flex-col {{ $flexClasses }} items-center gap-10 md:gap-16">
                        <div class="w-full {{ $textWidth }} {{ $block['data']['heading_alignment'] ?? 'text-left' }}">
                            @if(!empty($block['data']['heading']))
                                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                    <span x-show="currentLang === 'en'">{{ $block['data']['heading'] }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] }}</span>
                                    <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                                </h2>
                            @endif
                            <div class="prose prose-sm md:prose-base prose-blue max-w-none prose-p:my-2 prose-headings:my-2 prose-strong:text-inherit prose-em:text-inherit {{ $hasImage ? '' : 'mx-auto' }}" style="color: {{ $block['data']['text_color'] ?? '#374151' }}; {{ $hasImage ? '' : (($block['data']['heading_alignment'] ?? '') === 'text-center' ? 'text-align: center;' : '') }}">
                                @if(!empty($block['data']['content_ml']))
                                    <div lang="ml" x-show="currentLang === 'ml'" x-cloak>
                                        {!! parse_tiptap_html(is_string($block['data']['content_ml']) ? $block['data']['content_ml'] : tiptap_converter()->asHTML($block['data']['content_ml'])) !!}
                                    </div>
                                @endif
                                @if(!empty($block['data']['content']))
                                    <div lang="en" x-show="currentLang === 'en'" x-cloak>
                                        {!! parse_tiptap_html(is_string($block['data']['content']) ? $block['data']['content'] : tiptap_converter()->asHTML($block['data']['content'])) !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($hasImage)
                            <div class="w-full sm:{{ $imageWidth }} flex justify-center">
                                <div class="relative group w-full {{ $block['data']['aspect_ratio'] ?? 'aspect-auto' }} overflow-hidden rounded-2xl shadow-xl">
                                    <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 to-teal-500 rounded-2xl opacity-10 group-hover:opacity-20 transition duration-500"></div>
                                    <img src="{{ media_url($block['data']['image']) }}" loading="lazy" 
                                         class="relative w-full h-full object-cover">
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
                @break

            @case('split_video_content')
                @php
                    $videoWidth = $block['data']['video_width'] ?? 'w-1/2';
                    $textWidthMap = [
                        'w-1/4' => 'sm:w-3/4',
                        'w-1/3' => 'sm:w-2/3',
                        'w-1/2' => 'sm:w-1/2',
                        'w-2/3' => 'sm:w-1/3',
                        'w-3/4' => 'sm:w-1/4',
                    ];
                    $textWidth = $textWidthMap[$videoWidth] ?? 'sm:w-1/2';
                @endphp
                <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="flex flex-col @if(($block['data']['video_position'] ?? 'right') == 'left') sm:flex-row-reverse @else sm:flex-row @endif items-center gap-10 md:gap-16">
                        <div class="w-full {{ $textWidth }} {{ $block['data']['heading_alignment'] ?? 'text-left' }}">
                            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? '' }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? '' }}</span>
                                <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                            </h2>
                            <div class="prose prose-sm md:prose-base prose-blue max-w-none prose-p:my-2 prose-headings:my-2" style="color: {{ $block['data']['text_color'] ?? '#374151' }}">
                                @if(!empty($block['data']['content_ml']))
                                    <div lang="ml" x-show="currentLang === 'ml'" x-cloak>
                                        {!! parse_tiptap_html(is_string($block['data']['content_ml']) ? $block['data']['content_ml'] : tiptap_converter()->asHTML($block['data']['content_ml'])) !!}
                                    </div>
                                @endif
                                @if(!empty($block['data']['content']))
                                    <div lang="en" x-show="currentLang === 'en'" x-cloak>
                                        {!! parse_tiptap_html(is_string($block['data']['content']) ? $block['data']['content'] : tiptap_converter()->asHTML($block['data']['content'])) !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="w-full sm:{{ $videoWidth }} flex justify-center">
                            <div class="relative group w-full aspect-video">
                                <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 to-teal-500 rounded-2xl opacity-10 group-hover:opacity-20 transition duration-500"></div>
                                <div class="relative w-full h-full rounded-2xl overflow-hidden shadow-xl bg-black">
                                    @if(($block['data']['video_type'] ?? 'url') === 'url' && !empty($block['data']['video_url']))
                                    @php
                                        $videoId = '';
                                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $block['data']['video_url'], $match)) {
                                            $videoId = $match[1];
                                        }
                                    @endphp
                                    @if($videoId)
                                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen></iframe>
                                    @else
                                        <div class="flex items-center justify-center h-full text-white text-sm p-4 text-center">
                                            Invalid video URL
                                        </div>
                                    @endif
                                @elseif(($block['data']['video_type'] ?? 'url') === 'file' && !empty($block['data']['video_file']))
                                    <video class="w-full h-full object-cover" controls>
<source src="{{ media_url(\Illuminate\Support\Arr::first((array)($block['data']['video_file'] ?? ''))) }}" type="video/mp4">                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <div class="flex items-center justify-center h-full text-white text-sm p-4 text-center">
                                        No video provided
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                </section>
                @break

            @case('services')
                @php
                    $items = $block['data']['items'] ?? [];
                    $columns = $block['data']['columns'] ?? '3';
                    $showSidebar = !empty($block['data']['show_sidebar']);
                    $colClass = [
                        '2' => 'md:grid-cols-2',
                        '3' => 'md:grid-cols-3',
                        '4' => 'md:grid-cols-2 lg:grid-cols-4',
                        '5' => 'md:grid-cols-3 lg:grid-cols-5',
                        '6' => 'md:grid-cols-3 lg:grid-cols-6',
                    ][$columns] ?? 'md:grid-cols-3';
                @endphp
                <section id="{{ $block['data']['anchor_id'] ?? '' }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="mb-10 {{ $block['data']['heading_alignment'] ?? 'text-center md:text-left' }}">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? 'Our Services' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? 'നമ്മുടെ സേവനങ്ങൾ' }}</span>
                            <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                        </h2>
                        @if(!empty($block['data']['description']) || !empty($block['data']['description_ml']))
                            <div class="prose prose-sm md:prose-base prose-blue text-gray-600 max-w-3xl leading-relaxed">
                                @if(!empty($block['data']['description_ml']))
                                    <div lang="ml" x-show="currentLang === 'ml'" x-cloak>{!! parse_tiptap_html(is_string($block['data']['description_ml']) ? $block['data']['description_ml'] : tiptap_converter()->asHTML($block['data']['description_ml'])) !!}</div>
                                @endif
                                @if(!empty($block['data']['description']))
                                    <div lang="en" x-show="currentLang === 'en'" x-cloak>{!! parse_tiptap_html(is_string($block['data']['description']) ? $block['data']['description'] : tiptap_converter()->asHTML($block['data']['description'])) !!}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="{{ $showSidebar ? 'flex flex-col lg:flex-row gap-10 items-start' : '' }}">
                        <div class="{{ $showSidebar ? 'flex-1 min-w-0' : 'w-full' }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 {{ $colClass }} gap-6 md:gap-8 items-stretch">
                                @foreach($items as $item)
                                    <div class="group p-6 md:p-8 bg-white border border-gray-100 rounded-2xl md:rounded-3xl shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center flex flex-col items-center border-b-4 border-b-transparent hover:border-b-blue-500">
                                        @if(!empty($item['icon']))
                                            <div class="mb-6 w-full rounded-2xl bg-blue-50 group-hover:bg-blue-100 transition-colors duration-500 overflow-hidden flex items-center justify-center aspect-[4/3]">
                                                <img src="{{ media_url($item['icon']) }}" loading="lazy"
                                                     class="w-full h-full object-cover"
                                                     alt="{{ $item['title'] ?? 'Service' }}">
                                            </div>
                                        @endif
                                        @if(!empty($item['title']))
                                            @php
                                                $c1 = $item['title_color'] ?? '#111827';
                                                $c2 = $item['title_color_2'] ?? '';
                                                $titleStyle = $c2
                                                    ? "background: linear-gradient(135deg, {$c1}, {$c2}); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"
                                                    : "color: {$c1};";
                                            @endphp
                                            <h3 class="text-lg md:text-xl font-bold mb-3 leading-tight transition-colors" style="{{ $titleStyle }}">
                                                <span x-show="currentLang === 'en'">{{ $item['title'] }}</span>
                                                <span x-show="currentLang === 'ml'" x-cloak>{{ $item['title_ml'] ?? $item['title'] }}</span>
                                            </h3>
                                        @endif
                                        @if(!empty($item['description']) || !empty($item['description_ml']))
                                            <div class="text-sm text-gray-600 leading-relaxed prose prose-sm prose-blue max-w-none prose-p:my-1 text-center">
                                                @if(!empty($item['description_ml']))
                                                    <div lang="ml" x-show="currentLang === 'ml'" x-cloak>{!! parse_tiptap_html(is_string($item['description_ml']) ? $item['description_ml'] : tiptap_converter()->asHTML($item['description_ml'])) !!}</div>
                                                @endif
                                                @if(!empty($item['description']))
                                                    <div lang="en" x-show="currentLang === 'en'" x-cloak>{!! parse_tiptap_html(is_string($item['description']) ? $item['description'] : tiptap_converter()->asHTML($item['description'])) !!}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </section>
                @break

            @case('video_gallery')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                    @if(!empty($block['data']['heading']))
                        <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-12">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                <span x-show="currentLang === 'en'">{{ $block['data']['heading'] }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] }}</span>
                                <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                            </h2>
                        </div>
                    @endif
                    @php
                        $cols = $block['data']['columns'] ?? '2';
                        $gridClass = [
                            '1' => 'grid-cols-1',
                            '2' => 'grid-cols-1 md:grid-cols-2',
                            '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                        ][$cols] ?? 'grid-cols-1 md:grid-cols-2';
                    @endphp
                    <div class="grid {{ $gridClass }} gap-8">
                        @foreach($block['data']['videos'] ?? [] as $video)
                            <div class="space-y-4">
                                <div class="relative aspect-video rounded-3xl overflow-hidden shadow-lg bg-black group border border-gray-100">
                                    @if(($video['type'] ?? 'url') === 'url' && !empty($video['url']))
                                        @php
                                            $videoId = '';
                                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video['url'], $match)) {
                                                $videoId = $match[1];
                                            }
                                        @endphp
                                        @if($videoId)
                                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                    frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen></iframe>
                                        @else
                                            <div class="flex items-center justify-center h-full text-white text-sm p-4 text-center">
                                                Invalid video URL: {{ $video['url'] }}
                                            </div>
                                        @endif
                                    @elseif(($video['type'] ?? 'url') === 'file' && !empty($video['file']))
                                        <video class="w-full h-full object-cover" controls>
                                            <source src="{{ media_url(\Illuminate\Support\Arr::first((array)($video['file'] ?? ''))) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <div class="flex items-center justify-center h-full text-white text-sm p-4 text-center">
                                            No video provided
                                        </div>
                                    @endif
                                </div>
                                @if(!empty($video['title']))
                                    <p class="text-lg font-bold text-gray-900 sm:text-center px-2">
                                        <span x-show="currentLang === 'en'">{{ $video['title'] }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $video['title_ml'] ?? $video['title'] }}</span>
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
                @break

            @case('testimonials')
                <section class="py-24 bg-gray-900 overflow-hidden">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-16">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#ffffff' }}">
                                <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? 'What Our Clients Say' }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? 'അവർ എന്താണ് പറയുന്നത്' }}</span>
                                <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#3b82f6' }}"></div>
                            </h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                            @foreach($block['data']['items'] as $item)
                                <div class="p-8 bg-white/5 backdrop-blur-lg border border-white/10 rounded-3xl">
                                    <div class="text-gray-300 italic mb-8 prose prose-sm prose-invert max-w-none">
                                        {!! parse_tiptap_html(is_string($item['quote'] ?? '') ? ($item['quote'] ?? '') : tiptap_converter()->asHTML($item['quote'] ?? '')) !!}
                                    </div>
                                    <div class="flex items-center gap-4">
                                        @if(!empty($item['avatar']))
                                            <img src="{{ media_url(\Illuminate\Support\Arr::first((array)($item['avatar'] ?? ''))) }}" loading="lazy" class="w-12 h-12 rounded-full object-cover border-2 border-blue-500" alt="{{ $item['name'] ?? 'Client' }}">
                                        @endif
                                        <div>
                                            <h4 class="text-white font-bold">{{ $item['name'] ?? 'Client' }}</h4>
                                            <p class="text-gray-500 text-sm">{{ $item['role'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @break

            @case('contact_form')
                <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 bg-white rounded-[2rem] md:rounded-[3rem] shadow-2xl shadow-blue-900/5 mt-12 mb-12 border border-blue-50">
                    <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-12">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-8 inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? 'Contact Us' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? 'ഞങ്ങളെ ബന്ധപ്പെടുക' }}</span>
                            <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                        </h2>
                        <p class="text-gray-500 text-lg max-w-2xl mx-auto">
                            <span x-show="currentLang === 'en'">{{ $block['data']['subheading'] ?? 'We would love to hear from you. Fill out the form below and we will get back to you soon.' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['subheading_ml'] ?? $block['data']['subheading'] ?? 'നിങ്ങളിൽ നിന്ന് കേൾക്കാൻ ഞങ്ങൾ ആഗ്രഹിക്കുന്നു. ഫോം പൂരിപ്പിക്കുക.' }}</span>
                        </p>
                    </div>

                    @if(session('success_en'))
                        <div class="mb-8 p-6 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 text-emerald-800 animate-in fade-in slide-in-from-top duration-500">
                            <div class="p-2 bg-emerald-500 rounded-full text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="font-semibold">
                                <span x-show="currentLang === 'en'">{{ session('success_en') }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ session('success_ml') }}</span>
                            </p>
                        </div>
                    @endif

                    @if(session('error_en'))
                        <div class="mb-8 p-6 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-4 text-red-800 animate-in fade-in slide-in-from-top duration-500">
                            <div class="p-2 bg-red-500 rounded-full text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <p class="font-semibold">
                                <span x-show="currentLang === 'en'">{{ session('error_en') }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ session('error_ml') }}</span>
                            </p>
                        </div>
                    @endif

                    <form action="{{ route('inquiry.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        @csrf
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">
                                <span x-show="currentLang === 'en'">Full Name *</span>
                                <span x-show="currentLang === 'ml'" x-cloak>മുഴുവൻ പേര് *</span>
                            </label>
                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="John C"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('name') border-red-500 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">
                                <span x-show="currentLang === 'en'">Email Address *</span>
                                <span x-show="currentLang === 'ml'" x-cloak>ഇമെയിൽ വിലാസം *</span>
                            </label>
                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="john@example.com"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('email') border-red-500 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">
                                <span x-show="currentLang === 'en'">Phone Number</span>
                                <span x-show="currentLang === 'ml'" x-cloak>ഫോൺ നമ്പർ</span>
                            </label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 1234567890"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">
                                <span x-show="currentLang === 'en'">Subject</span>
                                <span x-show="currentLang === 'ml'" x-cloak>വിഷയം</span>
                            </label>
                            <input type="text" name="subject" value="{{ old('subject') }}" placeholder="How can we help?"
                                   class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none @error('subject') border-red-500 @enderror">
                            @error('subject') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-bold text-gray-700 ml-1">
                                <span x-show="currentLang === 'en'">Message *</span>
                                <span x-show="currentLang === 'ml'" x-cloak>സന്ദേശം *</span>
                            </label>
                            <textarea name="message" required rows="5" placeholder="Tell us more about your inquiry..."
                                      class="w-full px-5 py-4 rounded-xl bg-gray-50 border border-gray-100 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 outline-none resize-none @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                            @error('message') <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2 pt-4">
                            @php
                                $btnColor = $block['data']['button_color'] ?? \App\Models\Setting::get('admin_btn_color', '#2563eb');
                            @endphp
                            <button type="submit" style="background-color: {{ $btnColor }}" class="w-full md:w-auto px-10 py-5 text-white font-black rounded-xl md:rounded-2xl transition-all duration-300 shadow-xl shadow-blue-500/30 hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                                <span x-show="currentLang === 'en'">{{ $block['data']['button_text'] ?? 'Send Message' }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['button_text_ml'] ?? $block['data']['button_text'] ?? 'സന്ദേശം അയക്കുക' }}</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>
                </section>
                @break

            @case('team_members')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-gray-50">
                    @if(!empty($block['data']['heading']) || !empty($block['data']['heading_ml']))
                    <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-16">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative text-blue-900">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? '' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? '' }}</span>
                            <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#3b82f6' }}"></div>
                        </h2>
                    </div>
                    @endif
                    
                    @php
                        $cols = $block['data']['columns'] ?? '3';
                        $gridClass = match((string)$cols) {
                            '2' => 'grid-cols-1 md:grid-cols-2',
                            '3' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                            '4' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
                            default => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                        };
                    @endphp
                    
                    @if(isset($block['data']['members']) && is_array($block['data']['members']))
                        <div class="grid {{ $gridClass }} gap-10">
                            @foreach($block['data']['members'] as $member)
                                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform hover:-translate-y-2 transition-transform duration-300">
                                    @if(!empty($member['image']))
                                        <div class="aspect-square w-full relative">
                                            <img src="{{ media_url($member['image']) }}" alt="{{ $member['name'] ?? 'Team Member' }}" class="absolute inset-0 w-full h-full object-cover object-top" loading="lazy">
                                        </div>
                                    @endif
                                    <div class="p-6 text-center">
                                        <h3 class="text-xl font-bold mb-1" style="color: {{ $block['data']['member_name_color'] ?? '#111827' }}">
                                            <span x-show="currentLang === 'en'">{{ $member['name'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $member['name_ml'] ?? $member['name'] ?? '' }}</span>
                                        </h3>
                                        @if(!empty($member['designation']) || !empty($member['designation_ml']))
                                        <p class="font-semibold mb-3" style="color: {{ $block['data']['member_details_color'] ?? '#2563eb' }}">
                                            <span x-show="currentLang === 'en'">{{ $member['designation'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $member['designation_ml'] ?? $member['designation'] ?? '' }}</span>
                                        </p>
                                        @endif
                                        @if(!empty($member['extra_details']) || !empty($member['extra_details_ml']))
                                        <p class="text-sm" style="color: {{ $block['data']['member_details_color'] ?? '#6b7280' }}">
                                            <span x-show="currentLang === 'en'">{{ $member['extra_details'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $member['extra_details_ml'] ?? $member['extra_details'] ?? '' }}</span>
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
                @break

            @case('stats')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-white shadow-inner">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-12 text-center">
                        @foreach($block['data']['items'] as $item)
                            <div class="stats-item flex flex-col items-center">
                                <div class="text-5xl md:text-6xl font-sans font-black text-[#001a72] mb-3 flex items-center justify-center">
                                    <span class="counter" data-target="{{ $item['number'] }}">0</span>
                                </div>
                                <p class="text-gray-500 font-bold uppercase tracking-[0.2em] text-xs md:text-sm">
                                    <span x-show="currentLang === 'en'">{{ $item['label'] }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $item['label_ml'] ?? $item['label'] }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
                @break

            @case('info_cards')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                        @foreach($block['data']['items'] as $item)
                            <div class="p-6 sm:p-8 md:p-10 rounded-3xl shadow-xl text-center flex flex-col items-center justify-center transform hover:-translate-y-2 transition-all duration-700 overflow-hidden relative group min-h-[200px]" 
                                 style="background-color: {{ $item['bg_color'] ?? '#001a72' }}; color: {{ $item['text_color'] ?? '#ffffff' }};">
                                <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold mb-4 md:mb-6 uppercase tracking-widest leading-tight decoration-white/30">
                                    <span x-show="currentLang === 'en'">{{ $item['title'] }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $item['title_ml'] ?? $item['title'] }}</span>
                                </h2>
                                <p class="text-sm md:text-base lg:text-lg leading-relaxed opacity-90 font-medium tracking-tight max-w-2xl">
                                    <span x-show="currentLang === 'en'">{!! $item['description'] !!}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{!! $item['description_ml'] ?? $item['description'] !!}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                </section>
                @break

            @case('blog_feed')
                @php
                    $limit = $block['data']['limit'] ?? 3;
                    $colsCount = $block['data']['columns'] ?? 3;
                    $gridClasses = ($colsCount == 2) ? 'md:grid-cols-2' : 'md:grid-cols-2 lg:grid-cols-3';
                    $latestPosts = \App\Models\Page::where('type', 'post')
                        ->where('is_published', true)
                        ->orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get();
                @endphp
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                    @if(!empty($block['data']['heading']))
                        <div class="{{ $block['data']['heading_alignment'] ?? 'text-center' }} mb-16">
                            <h2 class="text-3xl md:text-4xl font-extrabold mb-8 tracking-tight inline-block relative" style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                                <span x-show="currentLang === 'en'">{{ $block['data']['heading'] }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] }}</span>
                                <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                            </h2>
                            @if(!empty($block['data']['subheading']))
                                <p class="text-gray-500 max-w-2xl mx-auto text-lg">
                                    <span x-show="currentLang === 'en'">{{ $block['data']['subheading'] }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['subheading_ml'] ?? $block['data']['subheading'] }}</span>
                                </p>
                            @endif
                        </div>
                    @endif
                    <div class="grid grid-cols-1 {{ $gridClasses }} gap-10">
                        @foreach($latestPosts as $post)
                            <article class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full border border-gray-100 group">
                                @if($post->featured_image)
                                    <div class="aspect-video relative overflow-hidden">
                                        <img src="{{ media_url($post->featured_image) }}" loading="lazy" 
                                             alt="{{ $post->title }}" 
                                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    </div>
                                @endif
                                <div class="p-8 flex flex-col flex-grow">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4 line-clamp-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600 transition">
                                            <span x-show="currentLang === 'en'">{{ $post->title }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $post->title_ml ?? $post->title }}</span>
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 mb-6 text-sm line-clamp-3 leading-relaxed">
                                        {{ $post->excerpt ?? '' }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
                @break

            @case('documents')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 standalone-documents">
                    @include('partials.document_sidebar', ['block' => $block])
                </section>
                @break

            @case('services_with_docs')
                <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 flex flex-col lg:flex-row gap-10 md:gap-16">
                    <div class="flex-1">
                        @php 
                            $servicesBlock = $block['services'];
                            $items = $servicesBlock['data']['items'] ?? [];
                        @endphp
                        <div class="mb-10 {{ $servicesBlock['data']['heading_alignment'] ?? 'text-center md:text-left' }}">
                            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-8 tracking-tight inline-block relative">
                                <span x-show="currentLang === 'en'">{{ $servicesBlock['data']['heading'] ?? 'Our Services' }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $servicesBlock['data']['heading_ml'] ?? $servicesBlock['data']['heading'] ?? 'നമ്മുടെ സേവനങ്ങൾ' }}</span>
                                <div class="absolute -bottom-4 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $servicesBlock['data']['underline_color'] ?? '#2563eb' }}"></div>
                            </h2>
                            @if(!empty($servicesBlock['data']['description']) || !empty($servicesBlock['data']['description_ml']))
                                <div class="prose prose-sm md:prose-base prose-blue text-gray-600 max-w-3xl leading-relaxed">
                                    @if(!empty($servicesBlock['data']['description_ml']))
                                        <div lang="ml" x-show="currentLang === 'ml'" x-cloak>{!! parse_tiptap_html(is_string($servicesBlock['data']['description_ml']) ? $servicesBlock['data']['description_ml'] : tiptap_converter()->asHTML($servicesBlock['data']['description_ml'])) !!}</div>
                                    @endif
                                    @if(!empty($servicesBlock['data']['description']))
                                        <div lang="en" x-show="currentLang === 'en'" x-cloak>{!! parse_tiptap_html(is_string($servicesBlock['data']['description']) ? $servicesBlock['data']['description'] : tiptap_converter()->asHTML($servicesBlock['data']['description'])) !!}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-stretch">
                            @foreach($items as $item)
                                <div class="group p-6 bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-500 flex flex-col items-center text-center">
                                    @if(!empty($item['icon']))
                                        <div class="mb-4 w-full rounded-xl bg-blue-50 group-hover:bg-blue-100 transition-colors overflow-hidden flex items-center justify-center aspect-[4/3]">
                                            <img src="{{ media_url(\Illuminate\Support\Arr::first((array)($item['icon'] ?? ''))) }}" loading="lazy" class="w-full h-full object-cover" alt="{{ $item['title'] }}">
                                        </div>
                                    @endif
                                    <h3 class="text-base font-bold text-gray-900 mb-2">
                                        <span x-show="currentLang === 'en'">{{ $item['title'] }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $item['title_ml'] ?? $item['title'] }}</span>
                                    </h3>
                                    <div class="text-xs text-gray-600 line-clamp-3">
                                        @if(!empty($item['description_ml']))
                                            <div lang="ml" x-show="currentLang === 'ml'" x-cloak>{!! parse_tiptap_html(is_string($item['description_ml']) ? $item['description_ml'] : tiptap_converter()->asHTML($item['description_ml'])) !!}</div>
                                        @endif
                                        @if(!empty($item['description']))
                                            <div lang="en" x-show="currentLang === 'en'" x-cloak>{!! parse_tiptap_html(is_string($item['description']) ? $item['description'] : tiptap_converter()->asHTML($item['description'])) !!}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="w-full lg:w-80 flex-shrink-0">
                        @include('partials.document_sidebar', ['block' => $block['docs']])
                    </div>
                </section>
                @break

        @case('application_forms')
            @php
                $afForms  = $block['data']['forms'] ?? [];
                $afAnchor = $block['data']['anchor_id'] ?? '';
            @endphp
            <section id="{{ $afAnchor }}" class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                @if(!empty($block['data']['heading']) || !empty($block['data']['heading_ml']))
                    <div class="{{ $block['data']['heading_alignment'] ?? 'text-left' }} mb-10">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight inline-block relative"
                            style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? 'Application Forms' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? 'അപേക്ഷാ ഫോമുകൾ' }}</span>
                            <div class="absolute -bottom-3 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                        </h2>
                    </div>
                @endif

                @if(count($afForms) > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        @foreach($afForms as $index => $form)
                            @php
                                $pdfPath = is_array($form['file'] ?? '') ? reset($form['file']) : ($form['file'] ?? '');
                                $isLast  = $index === count($afForms) - 1;
                            @endphp
                            <div class="group flex items-center gap-4 px-5 py-4 {{ !$isLast ? 'border-b border-gray-100' : '' }} hover:bg-blue-50 transition-colors duration-200">
                                {{-- Serial number --}}
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-700 text-sm font-bold flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                                    {{ $index + 1 }}
                                </span>

                                {{-- PDF Icon --}}
                                <svg class="flex-shrink-0 w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 13h1.5c1.1 0 1.75.6 1.75 1.5S10.6 16 9.5 16H8.5v1.5H8V13zm1.5 2.5c.55 0 .85-.25.85-.75s-.3-.75-.85-.75h-.5v1.5h.5zm2 .75c0-1 .7-1.75 1.75-1.75H15v.85h-1.5c-.6 0-.95.35-.95.9s.35.9.95.9H15V19h-1.5c-1.05 0-1.75-.75-1.75-1.75zm3.5-2.5h1c1.1 0 1.75.6 1.75 1.75v1c0 1.15-.65 1.75-1.75 1.75h-1V13zm.5 3.5h.5c.55 0 .85-.3.85-.75v-1c0-.45-.3-.75-.85-.75h-.5v2.5z"/>
                                </svg>

                                {{-- Title (clickable link to open PDF in new tab) --}}
                                <div class="flex-1 min-w-0">
                                    @if($pdfPath)
                                        <a href="{{ media_url($pdfPath) }}"
                                           target="_blank"
                                           rel="noopener"
                                           class="text-blue-700 font-semibold text-sm md:text-base hover:text-blue-900 hover:underline transition-colors leading-snug">
                                            <span x-show="currentLang === 'en'">{{ $form['title'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $form['title_ml'] ?? $form['title'] ?? '' }}</span>
                                        </a>
                                    @else
                                        <span class="text-gray-700 font-semibold text-sm md:text-base">
                                            <span x-show="currentLang === 'en'">{{ $form['title'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $form['title_ml'] ?? $form['title'] ?? '' }}</span>
                                        </span>
                                    @endif
                                    @if(!empty($form['description']) || !empty($form['description_ml']))
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            <span x-show="currentLang === 'en'">{{ $form['description'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $form['description_ml'] ?? $form['description'] ?? '' }}</span>
                                        </p>
                                    @endif
                                </div>

                                {{-- Open arrow icon --}}
                                @if($pdfPath)
                                    <a href="{{ media_url($pdfPath) }}"
                                       target="_blank"
                                       rel="noopener"
                                       class="flex-shrink-0 p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-100 transition-all"
                                       title="Open PDF">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400 italic text-sm">No forms available.</p>
                @endif
            </section>
            @break



        @case('schemes')
            @php
                $schItems = $block['data']['items'] ?? [];
                $schCols  = $block['data']['columns'] ?? '3';
                $schGrid  = match((string)$schCols) {
                    '2' => 'md:grid-cols-2',
                    '4' => 'md:grid-cols-2 lg:grid-cols-4',
                    default => 'md:grid-cols-3',
                };
                $schAnchor = $block['data']['anchor_id'] ?? '';
            @endphp
            <section id="{{ $schAnchor }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                @if(!empty($block['data']['heading']) || !empty($block['data']['heading_ml']))
                    <div class="{{ $block['data']['heading_alignment'] ?? 'text-left' }} mb-10">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight inline-block relative"
                            style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? 'Schemes' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? 'പദ്ധതികൾ' }}</span>
                            <div class="absolute -bottom-3 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                        </h2>
                    </div>
                @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 {{ $schGrid }} gap-6">
                    @foreach($schItems as $item)
                        @php $imgPath = is_array($item['image'] ?? '') ? reset($item['image']) : ($item['image'] ?? ''); @endphp
                        <div class="group bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            @if($imgPath)
                                <div class="aspect-[4/3] overflow-hidden bg-gray-100">
                                    <img src="{{ media_url($imgPath) }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                         alt="{{ $item['title'] ?? 'Scheme' }}">
                                </div>
                            @endif
                            @if(!empty($item['title']) || !empty($item['title_ml']) || !empty($item['description']) || !empty($item['description_ml']))
                                <div class="p-5">
                                    @if(!empty($item['title']) || !empty($item['title_ml']))
                                        <h3 class="font-bold text-gray-900 text-base mb-2 group-hover:text-blue-600 transition-colors">
                                            <span x-show="currentLang === 'en'">{{ $item['title'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $item['title_ml'] ?? $item['title'] ?? '' }}</span>
                                        </h3>
                                    @endif
                                    @if(!empty($item['description']) || !empty($item['description_ml']))
                                        <p class="text-sm text-gray-500 line-clamp-3">
                                            <span x-show="currentLang === 'en'">{{ $item['description'] ?? '' }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $item['description_ml'] ?? $item['description'] ?? '' }}</span>
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
            @break

        @case('reports')
            @php
                $repItems  = $block['data']['items'] ?? [];
                $repCols   = $block['data']['columns'] ?? '3';
                $repGrid   = match((string)$repCols) {
                    '2' => 'md:grid-cols-2',
                    '4' => 'md:grid-cols-2 lg:grid-cols-4',
                    default => 'md:grid-cols-3',
                };
                $repAnchor = $block['data']['anchor_id'] ?? '';
                // Group by year for display
                $repByYear = collect($repItems)->groupBy('year');
            @endphp
            <section id="{{ $repAnchor }}" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                @if(!empty($block['data']['heading']) || !empty($block['data']['heading_ml']))
                    <div class="{{ $block['data']['heading_alignment'] ?? 'text-left' }} mb-10">
                        <h2 class="text-3xl md:text-4xl font-extrabold mb-4 tracking-tight inline-block relative"
                            style="color: {{ $block['data']['heading_color'] ?? '#111827' }}">
                            <span x-show="currentLang === 'en'">{{ $block['data']['heading'] ?? 'Annual Reports' }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $block['data']['heading_ml'] ?? $block['data']['heading'] ?? 'വാർഷിക റിപ്പോർട്ടുകൾ' }}</span>
                            <div class="absolute -bottom-3 left-0 w-full h-1.5 rounded-full" style="background-color: {{ $block['data']['underline_color'] ?? '#2563eb' }}"></div>
                        </h2>
                    </div>
                @endif

                @foreach($repByYear as $year => $yearItems)
                    <div class="mb-10">
                        {{-- Year header --}}
                        <div class="flex items-center gap-3 mb-5">
                            <span class="inline-flex items-center px-4 py-1.5 bg-blue-600 text-white text-sm font-bold rounded-full shadow">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $year }}
                            </span>
                            <div class="flex-1 h-px bg-gray-200"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $repGrid }} gap-6">
                            @foreach($yearItems as $report)
                                @php $pdfPath = is_array($report['file'] ?? '') ? reset($report['file']) : ($report['file'] ?? ''); @endphp
                                <div class="group bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col">
                                    <div class="flex items-start gap-4 mb-4">
                                        <div class="flex-shrink-0 w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                                            <svg class="w-7 h-7 text-amber-500" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 4h5v7h7v9H6V4zm3 9h1.5c1.1 0 1.75.6 1.75 1.5S11.6 16 10.5 16H9.5v1.5H9V13zm1.5 2.5c.55 0 .85-.25.85-.75s-.3-.75-.85-.75h-.5v1.5h.5zm2 .75c0-1 .7-1.75 1.75-1.75H15v.85h-1.5c-.6 0-.95.35-.95.9s.35.9.95.9H15V19h-1.5c-1.05 0-1.75-.75-1.75-1.75zm3.5-2.5h1c1.1 0 1.75.6 1.75 1.75v1c0 1.15-.65 1.75-1.75 1.75h-1V13zm.5 3.5h.5c.55 0 .85-.3.85-.75v-1c0-.45-.3-.75-.85-.75h-.5v2.5z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-gray-900 text-base leading-snug group-hover:text-blue-600 transition-colors">
                                                <span x-show="currentLang === 'en'">{{ $report['title'] ?? '' }}</span>
                                                <span x-show="currentLang === 'ml'" x-cloak>{{ $report['title_ml'] ?? $report['title'] ?? '' }}</span>
                                            </h3>
                                            @if(!empty($report['description']) || !empty($report['description_ml']))
                                                <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                                    <span x-show="currentLang === 'en'">{{ $report['description'] ?? '' }}</span>
                                                    <span x-show="currentLang === 'ml'" x-cloak>{{ $report['description_ml'] ?? $report['description'] ?? '' }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-auto pt-4 border-t border-gray-50">
                                        @if($pdfPath)
                                            <a href="{{ media_url($pdfPath) }}"
                                               download
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-all shadow-sm hover:shadow-md w-full justify-center">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                <span x-show="currentLang === 'en'">{{ $report['button_text'] ?? 'Download Report' }}</span>
                                                <span x-show="currentLang === 'ml'" x-cloak>{{ $report['button_text_ml'] ?? $report['button_text'] ?? 'റിപ്പോർട്ട് ഡൗൺലോഡ്' }}</span>
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">No file uploaded</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
            @break

        @endswitch
    </div>
@endforeach
