@php
    $siteName = \App\Models\Setting::get('site_name', 'Pravasis IT Solution');
    $siteNameMl = \App\Models\Setting::get('site_name_ml', $siteName);
    $siteLogo = \App\Models\Setting::get('logo');
    
    $pages = \App\Models\Page::where('is_published', true)
        ->whereNull('parent_id')
        ->with(['children' => fn($q) => $q->where('is_published', true)])
        ->get();

    $headerMenu = \App\Models\Menu::getHeader();
    $footerMenu = \App\Models\Menu::getFooter();

    $navItems = $headerMenu->count() > 0 ? $headerMenu : $pages->map(function($p) {
        return (object)[
            'label' => $p->title,
            'label_ml' => $p->title_ml ?? $p->title,
            'url' => route('page.show', $p->slug),
            'children' => $p->children->map(function($c) {
                return (object)['label' => $c->title, 'label_ml' => $c->title_ml ?? $c->title, 'url' => route('page.show', $c->slug)];
            })
        ];
    });
    
    $navColor = \App\Models\Setting::get('nav_link_color', '#4b5563');
    $navHoverColor = \App\Models\Setting::get('nav_link_hover_color', '#2563eb');
    $navActiveColor = \App\Models\Setting::get('nav_link_active_color', '#2563eb');
    $adminBtnColor = \App\Models\Setting::get('admin_btn_color', '#1c51c5ff');
    $adminBtnHoverColor = \App\Models\Setting::get('admin_btn_hover_color', '#1d4ed8');
    $headerBgColor = \App\Models\Setting::get('header_bg_color', '#ffffff');
    $headerTextColor = \App\Models\Setting::get('header_text_color', '#111827');
    $topBarBgColor = \App\Models\Setting::get('top_bar_bg_color', '#111827');
    $topBarTextColor = \App\Models\Setting::get('top_bar_text_color', '#ffffff');
    $headerBgImage = \App\Models\Setting::get('header_bg_image');
    $favicon = \App\Models\Setting::get('favicon');
    $siteNameColor = \App\Models\Setting::get('site_name_color', '#111827');
    $siteNameHoverColor = \App\Models\Setting::get('site_name_hover_color', '#1f2937');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if($favicon)
        <link rel="icon" href="{{ media_url($favicon) }}">
    @endif
    <title>{{ isset($page) ? ($page->seo_title ?? $page->title) . ' - ' : '' }}{{ $siteName }}</title>
    <meta name="description" content="{{ isset($page) ? ($page->seo_description ?? $siteName) : $siteName }}">
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        /* Offset anchor targets so they don't hide behind the sticky header */
        [id] { scroll-margin-top: 90px; }
        .prose ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        .prose ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        .prose li { margin-bottom: 0.5rem !important; }
        /* Allow parent block color to be inherited by all children, but inline styles will still win */
        .prose :where(p, h1, h2, h3, h4, li, strong) { color: inherit; }

        /* Custom Responsive Table Wrapper inside Rich Text */
        .prose .table-responsive {
            display: block;
            margin: 2rem auto;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border-collapse: separate;
        }
        
        .prose .table-responsive table {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            margin: 0 !important;
            table-layout: auto !important;
        }

        .prose .table-responsive th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            text-align: left !important;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-right: 1px solid #e2e8f0 !important;
            white-space: nowrap !important;
        }

        .prose .table-responsive th:first-child {
            border-top-left-radius: 11px !important;
        }

        .prose .table-responsive th:last-child {
            border-top-right-radius: 11px !important;
        }

        .prose .table-responsive td {
            border-bottom: 1px solid #e2e8f0 !important;
            border-right: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            vertical-align: middle !important;
        }

        .prose .table-responsive th:last-child,
        .prose .table-responsive td:last-child {
            border-right: none !important;
        }

        .prose .table-responsive tr:last-child td {
            border-bottom: none !important;
        }

        .prose .table-responsive tr:nth-child(even) {
            background-color: #f8fafc !important;
        }

        .prose .table-responsive tr:hover {
            background-color: #f1f5f9 !important;
        }

        .prose .table-responsive table p {
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Table Sizes */
        .prose .table-responsive.table-sm {
            width: max-content !important;
            max-width: 100% !important;
            min-width: 40% !important;
        }
        .prose .table-responsive.table-sm th,
        .prose .table-responsive.table-sm td {
            padding: 6px 10px !important;
            font-size: 0.75rem !important;
        }

        .prose .table-responsive.table-md {
            width: max-content !important;
            max-width: 100% !important;
            min-width: 60% !important;
        }
        .prose .table-responsive.table-md th,
        .prose .table-responsive.table-md td {
            padding: 10px 16px !important;
            font-size: 0.85rem !important;
        }

        .prose .table-responsive.table-lg {
            width: 100% !important;
            max-width: 100% !important;
        }
        .prose .table-responsive.table-lg th,
        .prose .table-responsive.table-lg td {
            padding: 14px 20px !important;
            font-size: 0.95rem !important;
        }

        :root {
            --nav-color: {{ $navColor }};
            --nav-hover: {{ $navHoverColor }};
            --nav-active: {{ $navActiveColor }};
            --admin-btn-bg: {{ $adminBtnColor }};
            --admin-btn-hover: {{ $adminBtnHoverColor }};
            --header-bg: {{ $headerBgColor }};
            --header-text: {{ $headerTextColor }};
            --top-bar-bg: {{ $topBarBgColor }};
            --top-bar-text: {{ $topBarTextColor }};
            --header-bg-image: url('{{ $headerBgImage ? media_url($headerBgImage) : "" }}');
            --site-name-color: {{ $siteNameColor }};
            --site-name-hover: {{ $siteNameHoverColor }};
        }

        .site-name-text { color: var(--site-name-color); transition: color 0.3s ease; }
        a:hover .site-name-text { color: var(--site-name-hover); }

        .nav-link { color: var(--nav-color); transition: color 0.3s ease; }
        .nav-link:hover { color: var(--nav-hover); }
        .nav-link.active { color: var(--nav-active) !important; border-bottom: 2px solid var(--nav-active) !important; }

        .admin-btn {
            background-color: var(--admin-btn-bg);
            color: white;
            transition: all 0.3s ease;
        }
        .admin-btn:hover {
            background-color: var(--admin-btn-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Marquee Animations */
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @keyframes marquee-reverse {
            0% { transform: translateX(-50%); }
            100% { transform: translateX(0); }
        }
        .animate-marquee-normal { animation: marquee 30s linear infinite; }
        .animate-marquee-slow { animation: marquee 60s linear infinite; }
        .animate-marquee-fast { animation: marquee 15s linear infinite; }
        .animate-marquee { animation: marquee linear infinite; }
        .animate-marquee-reverse { animation: marquee-reverse linear infinite; }
        .pause-marquee:hover { animation-play-state: paused; }

        .marquee-shadow { text-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .marquee-glow { text-shadow: 0 0 10px currentColor; }
        .marquee-outline { -webkit-text-stroke: 1px currentColor; color: transparent !important; }
    </style>
</head>
<body class="antialiased bg-gray-50 flex flex-col min-h-screen" x-data="{ mobileMenuOpen: false, currentLang: $persist('ml') }">
    <header class="border-b border-gray-100 sticky top-0 z-50 bg-white w-full">
        <!-- Top Bar -->
        <div class="py-2 hidden sm:block" style="background-color: var(--top-bar-bg); color: var(--top-bar-text);">
            <div class="w-[98%] max-w-[1920px] mx-auto px-2 sm:px-4 lg:px-6 flex justify-between items-center text-xs md:text-sm">
                <div class="flex items-center space-x-4 md:space-x-6">
                    @if($email = \App\Models\Setting::get('email'))
                        <div class="flex items-center">
                            <svg class="h-3.5 w-3.5 mr-1.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ $email }}" class="hover:text-blue-400 transition truncate max-w-[150px] md:max-w-none">{{ $email }}</a>
                        </div>
                    @endif
                    @if($phone = \App\Models\Setting::get('phone'))
                        <div class="flex items-center">
                            <svg class="h-3.5 w-3.5 mr-1.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ $phone }}" class="hover:text-blue-400 transition">{{ $phone }}</a>
                        </div>
                    @endif
                </div>
                @if($workingHours = \App\Models\Setting::get('working_hours'))
                    <div class="hidden lg:flex items-center">
                        <svg class="h-3.5 w-3.5 mr-1.5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $workingHours }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Navigation Bar -->
        <nav class="w-[98%] max-w-[1920px] mx-auto px-2 sm:px-4 lg:px-6 py-4 flex justify-between items-center relative">
            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-black/5" aria-label="Toggle menu">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Logo -->
            <div class="flex items-center z-20 min-w-0 mr-4 md:mr-6">
                <a href="/" class="flex items-center space-x-3 md:space-x-4 transition-transform hover:scale-[1.02] min-w-0">
                    @if($siteLogo)
                        <img src="{{ media_url($siteLogo) }}" alt="{{ $siteName }}" class="h-16 w-auto md:h-24 flex-shrink-0">
                    @endif
                    <span class="font-bold leading-tight flex-1 site-name-text">
                        <span x-show="currentLang === 'en'" class="text-base md:text-2xl lg:text-4xl">{{ $siteName }}</span>
                        <span x-show="currentLang === 'ml'" class="text-base md:text-2xl lg:text-3xl" x-cloak>{{ $siteNameMl }}</span>
                    </span>
                </a>
            </div>

            <!-- Language Toggle & Admin Button -->
            <div class="flex items-center space-x-2 md:space-x-3 flex-shrink-0">
                 <button @click="currentLang = (currentLang === 'ml' ? 'en' : 'ml')" class="admin-btn px-3 py-2 md:px-4 md:py-2.5 text-xs md:text-sm font-semibold rounded-lg shadow-lg flex items-center gap-1.5 whitespace-nowrap">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                    <span x-text="currentLang === 'ml' ? 'English' : 'മലയാളം'"></span>
                 </button>
                 <a href="/admin" class="admin-btn p-2 md:p-2.5 rounded-lg shadow-lg flex items-center justify-center" title="Admin Panel" aria-label="Admin Panel">
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z"/>
                        <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z"/>
                    </svg>Admin Login
                </a>
            </div>

            <!-- Mobile Menu Sidebar -->
            <div x-show="mobileMenuOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full"
                 class="fixed inset-0 z-50 lg:hidden overflow-y-auto bg-white p-6 shadow-2xl w-3/4 max-w-sm"
                 @click.away="mobileMenuOpen = false">
                <div class="flex items-center justify-between mb-8">
                    @if($siteLogo)
                        <img src="{{ media_url($siteLogo) }}" alt="{{ $siteName }}" class="h-8 w-auto">
                    @endif
                    <button @click="mobileMenuOpen = false" class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    @foreach($navItems as $item)
                        @php $hasChildren = isset($item->children) && $item->children->count() > 0; @endphp
                        @if($hasChildren)
                            <div x-data="{ open: false }">
                                {{-- Row: label link + chevron toggle button --}}
                                <div class="flex items-center justify-between w-full">
                                    <a href="{{ $item->url }}" class="text-lg font-semibold text-gray-900 hover:text-blue-600 flex-1"
                                       @click="mobileMenuOpen = false">
                                        <span x-show="currentLang === 'en'">{{ $item->label }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $item->label_ml ?? $item->label }}</span>
                                    </a>
                                    <button @click="open = !open" class="p-1 ml-2 text-gray-500 hover:text-blue-600">
                                        <svg class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                </div>
                                <div x-show="open" class="mt-2 pl-4 space-y-2 border-l-2 border-blue-50">
                                    @foreach($item->children as $child)
                                        <a href="{{ $child->url }}" class="block text-gray-600 hover:text-blue-600 active:text-blue-600"
                                           @click="mobileMenuOpen = false">
                                            <span x-show="currentLang === 'en'">{{ $child->label }}</span>
                                            <span x-show="currentLang === 'ml'" x-cloak>{{ $child->label_ml ?? $child->label }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $item->url }}" class="block text-lg font-semibold text-gray-900 hover:text-blue-600 active:text-blue-600">
                                <span x-show="currentLang === 'en'">{{ $item->label }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $item->label_ml ?? $item->label }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
                
                <div class="mt-10 pt-10 border-t border-gray-100 space-y-6">
                    @if($email)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <a href="mailto:{{ $email }}" class="text-gray-600 text-sm">{{ $email }}</a>
                        </div>
                    @endif
                    @if($phone)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            <a href="tel:{{ $phone }}" class="text-gray-600 text-sm">{{ $phone }}</a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Overlay -->
            <div x-show="mobileMenuOpen" x-cloak @click="mobileMenuOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden" x-transition.opacity></div>
        
        </nav>
        
        <!-- Extra Nav Header for Links -->
        <div class="hidden lg:block border-t border-gray-100 shadow-sm relative z-40 bg-white" style="background-color: var(--header-bg); color: var(--header-text);">
            <!-- Desktop Menu -->
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 hidden lg:flex space-x-6 xl:space-x-12 items-center justify-center py-0">
                @foreach($navItems as $item)
                    @php 
                        $hasChildren = isset($item->children) && $item->children->count() > 0;
                        $itemUrl = url($item->url);
                        $isActive = request()->url() == $itemUrl || request()->is(ltrim(parse_url($itemUrl, PHP_URL_PATH), '/') . '/*');
                    @endphp
                    
                    @if($hasChildren)
                        <div class="relative group h-full flex items-center">
                            {{-- Parent label is a real link; chevron triggers the hover dropdown --}}
                            <a href="{{ $item->url }}" class="nav-link font-medium transition duration-150 py-2 flex items-center gap-1 {{ $isActive ? 'active' : '' }}">
                                <span x-show="currentLang === 'en'">{{ $item->label }}</span>
                                <span x-show="currentLang === 'ml'" x-cloak>{{ $item->label_ml ?? $item->label }}</span>
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </a>
                            <div class="absolute left-0 top-[100%] w-48 bg-white border border-gray-100 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 py-2">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->url }}" 
                                       class="block px-4 py-2 text-sm text-black hover:bg-blue-50 hover:text-blue-600 transition">
                                        <span x-show="currentLang === 'en'">{{ $child->label }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $child->label_ml ?? $child->label }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->url }}" 
                           class="nav-link font-medium transition duration-150 py-2 {{ $isActive ? 'active' : '' }}">
                            <span x-show="currentLang === 'en'">{{ $item->label }}</span>
                            <span x-show="currentLang === 'ml'" x-cloak>{{ $item->label_ml ?? $item->label }}</span>
                        </a>
                    @endif
                @endforeach
            </div>


        </div>

    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 pt-16 pb-8 mt-20 text-white">
        <div class="w-[98%] max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[2fr_1fr_1.5fr_1.5fr] gap-x-10 gap-y-12 mb-16">
                <!-- About Side -->
                <div class="space-y-6">
                    <a href="/" class="flex items-center space-x-3">
                        @if($siteLogo)
                            <img src="{{ media_url($siteLogo) }}" alt="{{ $siteName }}" class="h-20 md:h-28 w-auto">
                        @endif
                        <span class="text-2xl font-bold text-white">
                            {{ $siteName }}
                        </span>
                    </a>
                    <p class="text-gray-400 text-base leading-relaxed max-w-sm">
                        <span x-show="currentLang === 'en'">{{ \App\Models\Setting::get('footer_about_text', 'Leading provider of customized IT solutions and professional consultancy services.') }}</span>
                        <span x-show="currentLang === 'ml'" x-cloak>{{ \App\Models\Setting::get('footer_about_text_ml', \App\Models\Setting::get('footer_about_text', 'Leading provider of customized IT solutions and professional consultancy services.')) }}</span>
                    </p>
                </div>

                <!-- Footer Menu -->
                <div>
                    <h4 class="text-white font-bold mb-6 text-xl">
                        <span x-show="currentLang === 'en'">{{ \App\Models\Setting::get('footer_nav_title', 'Quick Links') }}</span>
                        <span x-show="currentLang === 'ml'" x-cloak>{{ \App\Models\Setting::get('footer_nav_title_ml', 'പ്രധാന ലിങ്കുകൾ') }}</span>
                    </h4>
                    <ul class="space-y-4">
                        @php
                            $selectedMenuIds = \App\Models\Setting::get('footer_selected_menus', []);
                            $customFooterMenu = [];
                            if (is_array($selectedMenuIds) && count($selectedMenuIds) > 0) {
                                // Fetch the selected menus in the order they were selected
                                $placeholders = implode(',', array_fill(0, count($selectedMenuIds), '?'));
                                $customFooterMenu = \App\Models\Menu::whereIn('id', $selectedMenuIds)
                                    ->orderByRaw("FIELD(id, $placeholders)", $selectedMenuIds)
                                    ->get();
                            }
                        @endphp
                        
                        @if(count($customFooterMenu) > 0)
                            @foreach($customFooterMenu as $item)
                                <li>
                                    <a href="{{ $item->url }}" class="text-gray-400 hover:text-blue-400 text-base transition flex items-center gap-2">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full"></span> 
                                        <span x-show="currentLang === 'en'">{{ $item->label }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $item->label_ml ?? $item->label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @elseif($footerMenu->count() > 0)
                            @foreach($footerMenu as $item)
                                <li>
                                    <a href="{{ $item->url }}" class="text-gray-400 hover:text-blue-400 text-base transition flex items-center gap-2">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full"></span> 
                                        <span x-show="currentLang === 'en'">{{ $item->label }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $item->label_ml ?? $item->label }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            @foreach($pages->take(5) as $p)
                                <li>
                                    <a href="{{ route('page.show', $p->slug) }}" class="text-gray-400 hover:text-blue-400 text-base transition flex items-center gap-2">
                                        <span class="w-1 h-1 bg-blue-500 rounded-full"></span> 
                                        <span x-show="currentLang === 'en'">{{ $p->title }}</span>
                                        <span x-show="currentLang === 'ml'" x-cloak>{{ $p->title_ml ?? $p->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-white font-bold mb-6 text-xl">
                        <span x-show="currentLang === 'en'">Contact Us</span>
                        <span x-show="currentLang === 'ml'" x-cloak>ഞങ്ങളെ ബന്ധപ്പെടുക</span>
                    </h4>
                    <ul class="space-y-4 text-base text-gray-400">
                        @if($address = \App\Models\Setting::get('address'))
                            <li class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="leading-relaxed">
                                    <span x-show="currentLang === 'en'">{{ $address }}</span>
                                    <span x-show="currentLang === 'ml'" x-cloak>{{ \App\Models\Setting::get('address_ml', $address) }}</span>
                                </span>
                            </li>
                        @endif
                        @if($email = \App\Models\Setting::get('email'))
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <a href="mailto:{{ $email }}" class="hover:text-blue-400 transition truncate">{{ $email }}</a>
                            </li>
                        @endif
                        @if($phone = \App\Models\Setting::get('phone'))
                            <li class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <a href="tel:{{ $phone }}" class="hover:text-blue-400 transition">{{ $phone }}</a>
                            </li>
                        @endif
                    </ul>
                    
                    <div class="mt-8">
                        <h4 class="text-white font-bold mb-4">
                            <span x-show="currentLang === 'en'">Follow Us</span>
                            <span x-show="currentLang === 'ml'" x-cloak>ഞങ്ങളെ പിന്തുടരുക</span>
                        </h4>
                        <div class="flex flex-wrap gap-3">
                            @php $socials = ['facebook', 'twitter', 'instagram', 'linkedin']; @endphp
                            @foreach($socials as $social)
                                @if($link = \App\Models\Setting::get($social))
                                    <a href="{{ $link }}" class="w-9 h-9 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition group" target="_blank">
                                        <span class="sr-only">{{ ucfirst($social) }}</span>
                                        <span class="text-xs group-hover:text-white font-bold">{{ strtoupper(substr($social, 0, 1)) }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Google Map -->
                <div>
                    <h4 class="text-white font-bold mb-6 text-xl">
                        <span x-show="currentLang === 'en'">Our Location</span>
                        <span x-show="currentLang === 'ml'" x-cloak>ലൊക്കേഷൻ</span>
                    </h4>
                    @php
                        $mapIframe = \App\Models\Setting::get('google_maps_iframe');
                        $address = \App\Models\Setting::get('address');
                    @endphp

                    <div class="rounded-2xl overflow-hidden shadow-2xl border border-gray-800 bg-gray-800 aspect-video sm:aspect-square">
                        @if($mapIframe)
                            {!! $mapIframe !!}
                        @elseif($address)
                            <iframe 
                                width="100%" 
                                height="100%" 
                                frameborder="0" 
                                scrolling="no" 
                                marginheight="0" 
                                marginwidth="0" 
                                src="https://maps.google.com/maps?q={{ urlencode($address) }}&t=&z=15&ie=UTF8&iwloc=&output=embed">
                            </iframe>
                        @else
                            <div class="h-full flex items-center justify-center text-gray-500 text-sm p-4 text-center">
                                Map will appear here once an address or embed code is added in settings.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-800 flex justify-center items-center text-gray-500">
                <p class="text-xs md:text-sm text-center">
                    &copy; {{ date('Y') }} 
                    <span x-show="currentLang === 'en'">{{ $siteName }}</span>
                    <span x-show="currentLang === 'ml'" x-cloak>{{ $siteNameMl }}</span>. All rights reserved.
                    <span class="mx-2 hidden sm:inline">|</span>
                    <br class="sm:hidden">
                    Powered by <a href="https://pravasisit.com" target="_blank" class="hover:text-white transition-colors"><strong>Pravasis IT Solutions</strong></a>
                </p>
            </div>
        </div>
    </footer>
    @stack('scripts')
    <script>
        // Stats Counter Animation
        const counters = document.querySelectorAll('.counter');
        const speed = 200;

        const animateCounter = (counter) => {
            const target = +counter.getAttribute('data-target');
            let count = 0;
            const inc = target / speed;

            const updateCount = () => {
                if (count < target) {
                    count = Math.ceil(count + inc);
                    counter.innerText = count;
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        };

        const observerOptions = { threshold: 0.5 };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        counters.forEach(counter => observer.observe(counter));

        // Basic Lightbox functionality
        function openLightbox(images, index) {
            const lightbox = document.createElement('div');
            lightbox.className = 'fixed inset-0 z-[100] bg-black/95 flex items-center justify-center p-4 backdrop-blur-sm cursor-zoom-out';
            lightbox.onclick = () => lightbox.remove();
            
            const img = document.createElement('img');
            img.src = images[index].src;
            img.className = 'max-w-full max-h-full rounded-lg shadow-2xl animate-in zoom-in duration-300';
            
            const caption = document.createElement('div');
            caption.className = 'absolute bottom-8 left-1/2 -translate-x-1/2 text-white text-center font-bold text-lg';
            caption.innerText = images[index].title || '';
            
            lightbox.appendChild(img);
            lightbox.appendChild(caption);
            document.body.appendChild(lightbox);
        }
    </script>
</body>
</html>
