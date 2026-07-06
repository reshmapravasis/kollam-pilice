<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Page Editor')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('title')
                                                ->label('Title (English)')
                                                ->required()
                                                ->maxLength(255)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                            Forms\Components\TextInput::make('title_ml')
                                                ->label('Title (Malayalam)')
                                                ->maxLength(255),
                                        ]),
                                        Forms\Components\Select::make('type')
                                            ->options([
                                                'page' => 'Page',
                                                'post' => 'Blog Post',
                                            ])
                                            ->default('page')
                                            ->required()
                                            ->live(),
                                        Forms\Components\Select::make('parent_id')
                                            ->label('Parent Page')
                                            ->relationship('parent', 'title')
                                            ->placeholder('Select a parent page (optional)')
                                            ->searchable(),
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Published')
                                            ->default(true),
                                        Forms\Components\FileUpload::make('featured_image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('pages')
                                            ->visible(fn (Forms\Get $get) => $get('type') === 'post'),
                                        Forms\Components\Textarea::make('excerpt')
                                            ->rows(2)
                                            ->visible(fn (Forms\Get $get) => $get('type') === 'post'),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Content')
                            ->icon('heroicon-o-pencil-square')
                            ->schema([
                                Forms\Components\Builder::make('content')
                                    ->blocks([
                                        Forms\Components\Builder\Block::make('marquee')
                                            ->label(fn (?array $state) => '📢 News Ticker'.($state ? ': '.Str::limit($state['items'][0]['text'] ?? '', 20) : ''))
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('text')->label('Text (English)')->required(),
                                                            Forms\Components\TextInput::make('text_ml')->label('Text (Malayalam)')->required(),
                                                        ])->columnSpanFull(),
                                                        Forms\Components\Select::make('link')
                                                            ->label('Link to Page')
                                                            ->options(fn () => Page::where('is_published', true)->pluck('title', 'slug')->toArray())
                                                            ->searchable()
                                                            ->nullable(),
                                                    ])
                                                    ->minItems(1)
                                                    ->label('Marquee Items'),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('speed')
                                                            ->numeric()
                                                            ->default(40)
                                                            ->helperText('Lower is slower, higher is faster (default: 40)'),
                                                        Forms\Components\Select::make('direction')
                                                            ->options([
                                                                '' => 'Right to Left (Default)',
                                                                'reverse' => 'Left to Right',
                                                            ])
                                                            ->default(''),
                                                        Forms\Components\TextInput::make('gap')
                                                            ->default('5rem')
                                                            ->helperText('Gap between items (e.g., 5rem, 50px)'),
                                                        Forms\Components\TextInput::make('separator')
                                                            ->default('•')
                                                            ->placeholder('e.g. • or | or **'),
                                                        Forms\Components\ColorPicker::make('bg_color')->default('#1e40af'),
                                                        Forms\Components\ColorPicker::make('text_color')->default('#ffffff'),
                                                        Forms\Components\Select::make('text_effect')
                                                            ->options([
                                                                'none' => 'None',
                                                                'shadow' => 'Shadow',
                                                                'glow' => 'Glow',
                                                                'outline' => 'Outline',
                                                            ])->default('none'),
                                                        Forms\Components\Select::make('font_size')
                                                            ->options([
                                                                'text-xs' => 'Extra Small',
                                                                'text-sm' => 'Small',
                                                                'text-base' => 'Normal',
                                                                'text-lg' => 'Large',
                                                                'text-xl' => 'Extra Large',
                                                            ])->default('text-base'),
                                                        Forms\Components\Select::make('font_weight')
                                                            ->options([
                                                                'font-normal' => 'Normal',
                                                                'font-medium' => 'Medium',
                                                                'font-bold' => 'Bold',
                                                                'font-black' => 'Black',
                                                            ])->default('font-medium'),
                                                    ]),
                                            ]),
                                        Forms\Components\Builder\Block::make('rich_text')
                                            ->label(fn (?array $state) => '📝 Rich Text'.($state ? ': '.($state['title'] ?? $state['heading'] ?? Str::limit(strip_tags($state['content'] ?? ''), 20)) : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('heading')->label('Heading (English)'),
                                                    Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)'),
                                                ]),
                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-center'),

                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-center'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Grid::make(2)->schema([
                                                    TiptapEditor::make('content_ml')->label('Malayalam Content')->output(TiptapOutput::Html)->profile('default'),
                                                    TiptapEditor::make('content')->label('English Content')->output(TiptapOutput::Html)->profile('default'),
                                                ])->columnSpanFull(),
                                                Forms\Components\Select::make('text_size')
                                                    ->options([
                                                        'text-sm' => 'Small',
                                                        'text-base' => 'Normal',
                                                        'text-lg' => 'Medium',
                                                        'text-xl' => 'Large',
                                                    ])
                                                    ->default('text-base'),
                                                Forms\Components\Select::make('table_size')
                                                    ->label('Table Size (If Table Exists)')
                                                    ->options([
                                                        'table-sm' => 'Small Table',
                                                        'table-md' => 'Medium Table',
                                                        'table-lg' => 'Large Table (Full Width)',
                                                    ])
                                                    ->default('table-md'),
                                                Forms\Components\TextInput::make('anchor_id')
                                                    ->label('Anchor ID (for sub-menu links)')
                                                    ->placeholder('e.g. loan-table')
                                                    ->helperText('Set this to link to this section from sub-menus. Use lowercase with hyphens, no spaces.')
                                                    ->alphaDash(),
                                            ]),
                                        Forms\Components\Builder\Block::make('button_link')
                                            ->label(fn (?array $state) => '🔘 Action Button'.($state ? ': '.($state['button_text'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(3)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('button_text')->label('Button Text (English)')->default('Read More')->required(),
                                                        Forms\Components\TextInput::make('button_text_ml')->label('Button Text (Malayalam)')->default('കൂടുതൽ വായിക്കുക'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('target_page')
                                                        ->options(fn () => Page::where('is_published', true)->pluck('title', 'slug')->toArray())
                                                        ->searchable()
                                                        ->required(),
                                                    Forms\Components\Select::make('alignment')
                                                        ->options([
                                                            'text-left' => 'Left',
                                                            'text-center' => 'Center',
                                                            'text-right' => 'Right',
                                                        ])->default('text-center'),
                                                    Forms\Components\ColorPicker::make('button_color')->default('#2563eb'),
                                                    Forms\Components\ColorPicker::make('text_color')->default('#ffffff'),
                                                ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('hero')
                                            ->label(fn (?array $state) => '🚀 Hero Sections'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('heading')->label('Heading (English)'),
                                                    Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)'),
                                                ]),
                                                Forms\Components\ColorPicker::make('heading_color')->default('#ffffff'),

                                                Forms\Components\ColorPicker::make('heading_color')->default('#ffffff'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-center'),

                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-center'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('subheading')->label('Subheading (English)'),
                                                    Forms\Components\TextInput::make('subheading_ml')->label('Subheading (Malayalam)'),
                                                ]),
                                                Forms\Components\ColorPicker::make('subheading_color')->default('#dbeafe'),
                                                Forms\Components\Select::make('text_size')
                                                    ->options([
                                                        'text-xl' => 'Small',
                                                        'text-2xl' => 'Normal',
                                                        'text-3xl' => 'Large',
                                                        'text-4xl' => 'Extra Large',
                                                    ])->default('text-2xl'),
                                                Forms\Components\Grid::make(2)->schema([
                                                    TiptapEditor::make('content_ml')->label('Malayalam Content')->output(TiptapOutput::Html),
                                                    TiptapEditor::make('content')->label('English Content')->output(TiptapOutput::Html),
                                                ])->columnSpanFull(),
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('button_text')->label('Button Text (English)'),
                                                    Forms\Components\TextInput::make('button_text_ml')->label('Button Text (Malayalam)'),
                                                ]),
                                                Forms\Components\TextInput::make('button_link'),
                                                Forms\Components\FileUpload::make('background_image')
                                                    ->label('Background Image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('hero-images'),
                                            ]),

                                        Forms\Components\Builder\Block::make('image')
                                            ->label(fn (?array $state) => '🖼️ Image'.($state ? ': '.($state['alt'] ?? ($state['caption'] ?? '')) : ''))
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')
                                                    ->image()
                                                    ->disk('public')
                                                    ->directory('images')
                                                    ->required(),
                                                Forms\Components\Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('alt'),
                                                        Forms\Components\TextInput::make('caption'),
                                                        Forms\Components\Select::make('width_percent')
                                                            ->label('Display Width')
                                                            ->options([
                                                                '100' => 'Full Width',
                                                                '80' => 'Large (80%)',
                                                                '60' => 'Medium (60%)',
                                                                '50' => 'Half (50%)',
                                                                '40' => 'Compact (40%)',
                                                                '25' => 'Small (25%)',
                                                            ])
                                                            ->default('100'),
                                                    ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('video')
                                            ->label(fn (?array $state) => '🎥 Single Video'.($state ? ': '.($state['title'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Select::make('type')
                                                    ->options([
                                                        'url' => 'Video URL (YouTube/Vimeo)',
                                                        'file' => 'Video File (Upload)',
                                                    ])->default('url')->live(),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('YouTube/Vimeo URL')
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'url'),
                                                Forms\Components\FileUpload::make('file')
                                                    ->label('Video File')
                                                    ->disk('public')
                                                    ->directory('videos')
                                                    ->visible(fn (Forms\Get $get) => $get('type') === 'file'),
                                                Forms\Components\Grid::make(2)
                                                    ->schema([
                                                        Forms\Components\TextInput::make('title'),
                                                        Forms\Components\Select::make('width_percent')
                                                            ->label('Display Width')
                                                            ->options([
                                                                '100' => 'Full Width',
                                                                '80' => 'Large (80%)',
                                                                '60' => 'Medium (60%)',
                                                                '50' => 'Half (50%)',
                                                                '40' => 'Compact (40%)',
                                                                '25' => 'Small (25%)',
                                                            ])
                                                            ->default('100'),
                                                    ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('split_content')
                                            ->label(fn (?array $state) => '↔️ Split: Text & Image'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(4)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-left'),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        TiptapEditor::make('content_ml')->label('Malayalam Content')->output(TiptapOutput::Html),
                                                        TiptapEditor::make('content')->label('English Content')->output(TiptapOutput::Html),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('text_color')->default('#374151'),
                                                    Forms\Components\FileUpload::make('image')
                                                        ->disk('public')
                                                        ->directory('content'),
                                                    Forms\Components\Select::make('image_position')->options(['left' => 'Left', 'right' => 'Right', 'center' => 'Center'])->default('Left'),
                                                    Forms\Components\Select::make('image_width')
                                                        ->options([
                                                            'w-1/4' => 'Small (25%)',
                                                            'w-1/3' => 'Medium (33%)',
                                                            'w-1/2' => 'Half (50%)',
                                                            'w-2/3' => 'Large (66%)',
                                                            'w-3/4' => 'Huge (75%)',
                                                        ])
                                                        ->default('w-1/2'),
                                                    Forms\Components\Select::make('aspect_ratio')
                                                        ->label('Image Aspect Ratio')
                                                        ->options([
                                                            'aspect-auto' => 'Original',
                                                            'aspect-square' => 'Square (1:1)',
                                                            'aspect-video' => 'Video (16:9)',
                                                            'aspect-[4/3]' => 'Standard (4:3)',
                                                            'aspect-[3/4]' => 'Portrait (3:4)',
                                                        ])
                                                        ->default('aspect-auto'),
                                                    Forms\Components\TextInput::make('anchor_id')->label('Anchor ID (for jumping to section)')->placeholder('e.g. about-us'),
                                                ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('split_video_content')
                                            ->label(fn (?array $state) => '↔️ Split: Text & Video'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(4)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-left'),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        TiptapEditor::make('content_ml')->label('Malayalam Content')->output(TiptapOutput::Html),
                                                        TiptapEditor::make('content')->label('English Content')->output(TiptapOutput::Html),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('text_color')->default('#374151'),
                                                    Forms\Components\Select::make('video_type')->options(['url' => 'URL', 'file' => 'Upload'])->default('url')->live(),
                                                    Forms\Components\TextInput::make('video_url')->visible(fn (Forms\Get $get) => $get('video_type') === 'url'),
                                                    Forms\Components\FileUpload::make('video_file')
                                                        ->disk('public')
                                                        ->directory('videos')
                                                        ->visible(fn (Forms\Get $get) => $get('video_type') === 'file'),
                                                    Forms\Components\Select::make('video_position')->options(['left' => 'Left', 'right' => 'Right'])->default('right'),
                                                    Forms\Components\Select::make('video_width')
                                                        ->options([
                                                            'w-1/4' => 'Small (25%)',
                                                            'w-1/3' => 'Medium (33%)',
                                                            'w-1/2' => 'Half (50%)',
                                                            'w-2/3' => 'Large (66%)',
                                                            'w-3/4' => 'Huge (75%)',
                                                        ])
                                                        ->default('w-1/2'),
                                                    Forms\Components\TextInput::make('anchor_id'),
                                                ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('services')
                                            ->label(fn (?array $state) => '🛠️ Services Grid'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(4)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)')->default('Our Services'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)')->default('നമ്മുടെ സേവനങ്ങൾ'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-center'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\Select::make('columns')
                                                        ->options([
                                                            '2' => '2 Columns',
                                                            '3' => '3 Columns',
                                                            '4' => '4 Columns',
                                                            '5' => '5 Columns',
                                                            '6' => '6 Columns',
                                                        ])->default('3'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\Select::make('view_all_link')
                                                        ->label('Target Page')
                                                        ->options(fn () => Page::where('is_published', true)->pluck('title', 'slug')->toArray())
                                                        ->searchable()
                                                        ->placeholder('Select a page...'),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('view_all_text')->label('View All Button Text (English)')->default('View All'),
                                                        Forms\Components\TextInput::make('view_all_text_ml')->label('View All Button Text (Malayalam)')->default('കൂടുതൽ കാണുക'),
                                                    ])->columnSpanFull(),
                                                ]),
                                                Forms\Components\Grid::make(2)->schema([
                                                    TiptapEditor::make('description_ml')->label('Malayalam Description')->output(TiptapOutput::Html),
                                                    TiptapEditor::make('description')->label('English Description')->output(TiptapOutput::Html),
                                                ])->columnSpanFull(),
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('title')->label('Title (English)'),
                                                            Forms\Components\TextInput::make('title_ml')->label('Title (Malayalam)'),
                                                        ])->columnSpanFull(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\ColorPicker::make('title_color')->label('Title Color 1')->default('#111827'),
                                                            Forms\Components\ColorPicker::make('title_color_2')->label('Title Color 2 (Gradient)')->helperText('Leave empty for solid color'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            TiptapEditor::make('description_ml')->label('Malayalam Description')->output(TiptapOutput::Html),
                                                            TiptapEditor::make('description')->label('English Description')->output(TiptapOutput::Html),
                                                        ])->columnSpanFull(),
                                                        Forms\Components\FileUpload::make('icon')
                                                            ->disk('public')
                                                            ->directory('services'),
                                                    ])->grid(3),
                                                Forms\Components\TextInput::make('anchor_id'),
                                            ]),

                                        Forms\Components\Builder\Block::make('testimonials')
                                            ->label(fn (?array $state) => '💬 Testimonials'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)')->default('What They Say'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)')->default('അവർ എന്താണ് പറയുന്നത്'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-center'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                ]),
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\Grid::make(3)->schema([
                                                            Forms\Components\Grid::make(2)->schema([
                                                                Forms\Components\TextInput::make('name')->label('Name (English)'),
                                                                Forms\Components\TextInput::make('name_ml')->label('Name (Malayalam)'),
                                                            ]),
                                                            Forms\Components\Grid::make(2)->schema([
                                                                Forms\Components\TextInput::make('role')->label('Role/Designation (English)'),
                                                                Forms\Components\TextInput::make('role_ml')->label('Role/Designation (Malayalam)'),
                                                            ]),
                                                            Forms\Components\FileUpload::make('avatar')
                                                                ->image()
                                                                ->disk('public')
                                                                ->directory('testimonials'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            TiptapEditor::make('quote')->label('Quote (English)')->output(TiptapOutput::Html),
                                                            TiptapEditor::make('quote_ml')->label('Quote (Malayalam)')->output(TiptapOutput::Html),
                                                        ]),
                                                    ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('contact_form')
                                            ->label(fn (?array $state) => '📧 Contact Form'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)')->default('Contact Us'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)')->default('ഞങ്ങളെ ബന്ധപ്പെടുക'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-center'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('button_text')->label('Button Text (English)')->default('Send Message'),
                                                        Forms\Components\TextInput::make('button_text_ml')->label('Button Text (Malayalam)')->default('സന്ദേശം അയക്കുക'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\ColorPicker::make('button_color')->label('Button Color')->default('#2563eb'),
                                                ]),
                                            ]),

                                        Forms\Components\Builder\Block::make('video_gallery')
                                            ->label(fn (?array $state) => '🎥 Video Gallery'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(4)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)')->default('Video Gallery'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)')->default('വീഡിയോ ഗാലറി'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-center'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\Select::make('columns')
                                                        ->options([
                                                            '2' => '2 Columns',
                                                            '3' => '3 Columns',
                                                            '4' => '4 Columns',
                                                        ])->default('3'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                ]),
                                                Forms\Components\Repeater::make('videos')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('title')->label('Title (English)'),
                                                            Forms\Components\TextInput::make('title_ml')->label('Title (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Select::make('type')
                                                            ->options([
                                                                'url' => 'Video URL (YouTube/Vimeo)',
                                                                'file' => 'Video File (Upload)',
                                                            ])->default('url')->live(),
                                                        Forms\Components\TextInput::make('url')
                                                            ->label('Video URL')
                                                            ->placeholder('https://www.youtube.com/watch?v=...')
                                                            ->visible(fn (Forms\Get $get) => $get('type') === 'url'),
                                                        Forms\Components\FileUpload::make('file')
                                                            ->label('Video File')
                                                            ->disk('public')
                                                            ->directory('videos')
                                                            ->visible(fn (Forms\Get $get) => $get('type') === 'file'),
                                                    ])->grid(2),
                                                Forms\Components\TextInput::make('anchor_id')->placeholder('e.g. video-gallery'),
                                            ]),

                                        Forms\Components\Builder\Block::make('gallery')
                                            ->label(fn (?array $state) => '🖼️ Photo Gallery'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(3)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-center'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                ]),
                                                Forms\Components\Select::make('columns')
                                                    ->options([
                                                        '2' => '2 Columns',
                                                        '3' => '3 Columns',
                                                        '4' => '4 Columns',
                                                    ])->default('3'),
                                                Forms\Components\Toggle::make('enable_marquee')
                                                    ->label('Enable Moving Effect (Marquee)')
                                                    ->default(true),
                                                Forms\Components\Select::make('marquee_direction')
                                                    ->label('Movement Direction')
                                                    ->options([
                                                        '' => 'Right to Left (Default)',
                                                        'reverse' => 'Left to Right',
                                                    ])
                                                    ->default(''),
                                                Forms\Components\Repeater::make('images')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->disk('public')
                                                            ->directory('gallery')->required(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('label')->label('Label (English)'),
                                                            Forms\Components\TextInput::make('label_ml')->label('Label (Malayalam)'),
                                                        ]),
                                                    ])->grid(2),
                                                Forms\Components\TextInput::make('anchor_id')
                                                    ->label('Anchor ID (for sub-menu links)')
                                                    ->placeholder('e.g. photo-gallery')
                                                    ->helperText('Set this to link to this section from sub-menus. Use lowercase with hyphens, no spaces.')
                                                    ->alphaDash(),
                                            ]),

                                        Forms\Components\Builder\Block::make('team_members')
                                            ->label(fn (?array $state) => '👥 Team Members'.($state ? ': '.($state['heading'] ?? '') : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(3)->schema([
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('heading')->label('Heading (English)'),
                                                        Forms\Components\TextInput::make('heading_ml')->label('Heading (Malayalam)'),
                                                    ])->columnSpanFull(),
                                                    Forms\Components\Select::make('heading_alignment')
                                                        ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                        ->default('text-center'),
                                                    Forms\Components\ColorPicker::make('heading_color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                    Forms\Components\ColorPicker::make('member_name_color')->label('Name Color')->default('#111827'),
                                                    Forms\Components\ColorPicker::make('member_details_color')->label('Details Color')->default('#4b5563'),
                                                ]),
                                                Forms\Components\Select::make('columns')
                                                    ->options([
                                                        '2' => '2 Columns',
                                                        '3' => '3 Columns',
                                                        '4' => '4 Columns',
                                                    ])->default('3'),
                                                Forms\Components\Repeater::make('members')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->disk('public')
                                                            ->directory('team')
                                                            ->required(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('name')->label('Name (English)')->required(),
                                                            Forms\Components\TextInput::make('name_ml')->label('Name (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('designation')->label('Designation (English)'),
                                                            Forms\Components\TextInput::make('designation_ml')->label('Designation (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('extra_details')->label('Extra Details (e.g. Phone/Email) (English)'),
                                                            Forms\Components\TextInput::make('extra_details_ml')->label('Extra Details (Malayalam)'),
                                                        ]),
                                                    ])->grid(2),
                                            ]),

                                        Forms\Components\Builder\Block::make('stats')
                                            ->label(fn (?array $state) => '📊 Stats Overview'.($state ? ': '.count($state['items'] ?? []).' items' : ''))
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('label')->label('Label (English)')->placeholder('e.g. Clients'),
                                                            Forms\Components\TextInput::make('label_ml')->label('Label (Malayalam)'),
                                                        ]),
                                                        Forms\Components\TextInput::make('number')->placeholder('e.g. 500+'),
                                                    ])->grid(2),
                                            ]),

                                        Forms\Components\Builder\Block::make('info_cards')
                                            ->label(fn (?array $state) => '🗂️ Info Cards'.($state ? ': '.count($state['items'] ?? []).' items' : ''))
                                            ->schema([
                                                Forms\Components\Repeater::make('items')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('title')->label('Title (English)'),
                                                            Forms\Components\TextInput::make('title_ml')->label('Title (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\Textarea::make('description')->label('Description (English)'),
                                                            Forms\Components\Textarea::make('description_ml')->label('Description (Malayalam)'),
                                                        ]),
                                                        Forms\Components\ColorPicker::make('bg_color')->default('#001a72'),
                                                        Forms\Components\ColorPicker::make('text_color')->default('#ffffff'),
                                                    ])->grid(2),
                                            ]),

                                        // ─── DOWNLOADS PAGE BLOCKS ───────────────────────────

                                        Forms\Components\Builder\Block::make('application_forms')
                                            ->label(fn (?array $state) => '📄 Application Forms'.($state ? ': '.count($state['forms'] ?? []).' forms' : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('heading')
                                                        ->label('Section Heading (English)')
                                                        ->default('Application Forms'),
                                                    Forms\Components\TextInput::make('heading_ml')
                                                        ->label('Section Heading (Malayalam)')
                                                        ->default('അപേക്ഷാ ഫോമുകൾ'),
                                                ]),
                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-left'),
                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Select::make('columns')
                                                    ->options(['2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns'])
                                                    ->default('3'),
                                                Forms\Components\Repeater::make('forms')
                                                    ->label('Application Forms')
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('Form Title (English)')
                                                                ->required(),
                                                            Forms\Components\TextInput::make('title_ml')
                                                                ->label('Form Title (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\Textarea::make('description')
                                                                ->label('Description (English)')
                                                                ->rows(2),
                                                            Forms\Components\Textarea::make('description_ml')
                                                                ->label('Description (Malayalam)')
                                                                ->rows(2),
                                                        ]),
                                                        Forms\Components\FileUpload::make('file')
                                                            ->label('PDF File')
                                                            ->disk('public')
                                                            ->directory('downloads/forms')
                                                            ->acceptedFileTypes(['application/pdf'])
                                                            ->extraAttributes(['class' => 'pdf-upload-field'])
                                                            ->required(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('button_text')
                                                                ->label('Download Button Text (English)')
                                                                ->default('Download'),
                                                            Forms\Components\TextInput::make('button_text_ml')
                                                                ->label('Download Button Text (Malayalam)')
                                                                ->default('ഡൗൺലോഡ്'),
                                                        ]),
                                                    ])->grid(2),
                                                Forms\Components\TextInput::make('anchor_id')
                                                    ->label('Anchor ID')
                                                    ->placeholder('e.g. application-forms')
                                                    ->helperText('Used for navigation links'),
                                            ]),

                                        Forms\Components\Builder\Block::make('schemes')
                                            ->label(fn (?array $state) => '🗺️ Schemes'.($state ? ': '.count($state['items'] ?? []).' items' : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('heading')
                                                        ->label('Section Heading (English)')
                                                        ->default('Schemes'),
                                                    Forms\Components\TextInput::make('heading_ml')
                                                        ->label('Section Heading (Malayalam)')
                                                        ->default('പദ്ധതികൾ'),
                                                ]),
                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-left'),
                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Select::make('columns')
                                                    ->options(['2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns'])
                                                    ->default('3'),
                                                Forms\Components\Repeater::make('items')
                                                    ->label('Scheme Items')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->label('Scheme Image')
                                                            ->image()
                                                            ->disk('public')
                                                            ->directory('downloads/schemes')
                                                            ->required(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('Title (English)'),
                                                            Forms\Components\TextInput::make('title_ml')
                                                                ->label('Title (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\Textarea::make('description')
                                                                ->label('Description (English)')
                                                                ->rows(2),
                                                            Forms\Components\Textarea::make('description_ml')
                                                                ->label('Description (Malayalam)')
                                                                ->rows(2),
                                                        ]),
                                                    ])->grid(2),
                                                Forms\Components\TextInput::make('anchor_id')
                                                    ->label('Anchor ID')
                                                    ->placeholder('e.g. schemes')
                                                    ->helperText('Used for navigation links'),
                                            ]),

                                        Forms\Components\Builder\Block::make('reports')
                                            ->label(fn (?array $state) => '📊 Reports'.($state ? ': '.count($state['items'] ?? []).' items' : ''))
                                            ->schema([
                                                Forms\Components\Grid::make(2)->schema([
                                                    Forms\Components\TextInput::make('heading')
                                                        ->label('Section Heading (English)')
                                                        ->default('Annual Reports'),
                                                    Forms\Components\TextInput::make('heading_ml')
                                                        ->label('Section Heading (Malayalam)')
                                                        ->default('വാർഷിക റിപ്പോർട്ടുകൾ'),
                                                ]),
                                                Forms\Components\Select::make('heading_alignment')
                                                    ->options(['text-left' => 'Left', 'text-center' => 'Center', 'text-right' => 'Right'])
                                                    ->default('text-left'),
                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'),

                                                Forms\Components\ColorPicker::make('heading_color')->default('#111827'), Forms\Components\ColorPicker::make('underline_color')->label('Underline Color')->default('#2563eb'),
                                                Forms\Components\Select::make('columns')
                                                    ->options(['2' => '2 Columns', '3' => '3 Columns', '4' => '4 Columns'])
                                                    ->default('3'),
                                                Forms\Components\Repeater::make('items')
                                                    ->label('Report Items')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('year')
                                                            ->label('Year / Period')
                                                            ->placeholder('e.g. 2024-25')
                                                            ->required(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('title')
                                                                ->label('Report Title (English)')
                                                                ->required(),
                                                            Forms\Components\TextInput::make('title_ml')
                                                                ->label('Report Title (Malayalam)'),
                                                        ]),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\Textarea::make('description')
                                                                ->label('Description (English)')
                                                                ->rows(2),
                                                            Forms\Components\Textarea::make('description_ml')
                                                                ->label('Description (Malayalam)')
                                                                ->rows(2),
                                                        ]),
                                                        Forms\Components\FileUpload::make('file')
                                                            ->label('PDF Report')
                                                            ->disk('public')
                                                            ->directory('downloads/reports')
                                                            ->acceptedFileTypes(['application/pdf'])
                                                            ->extraAttributes(['class' => 'pdf-upload-field'])
                                                            ->required(),
                                                        Forms\Components\Grid::make(2)->schema([
                                                            Forms\Components\TextInput::make('button_text')
                                                                ->label('Download Button Text (English)')
                                                                ->default('Download Report'),
                                                            Forms\Components\TextInput::make('button_text_ml')
                                                                ->label('Download Button Text (Malayalam)')
                                                                ->default('റിപ്പോർട്ട് ഡൗൺലോഡ്'),
                                                        ]),
                                                    ])->grid(2),
                                                Forms\Components\TextInput::make('anchor_id')
                                                    ->label('Anchor ID')
                                                    ->placeholder('e.g. reports')
                                                    ->helperText('Used for navigation links'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->cloneable()
                                    ->blockNumbers(false)
                                    ->extraItemActions([
                                        Action::make('preview')
                                            ->icon('heroicon-m-eye')
                                            ->label('Preview')
                                            ->modalHeading('Section Preview')
                                            ->modalWidth('7xl')
                                            ->modalSubmitAction(false)
                                            ->modalCancelAction(false)
                                            ->modalContent(fn (array $state, array $arguments, Forms\Components\Builder $component): View => (function () use ($state, $arguments) {
                                                $itemKey = $arguments['item'];
                                                $selectedItem = $state[$itemKey] ?? null;

                                                if (! $selectedItem) {
                                                    return view('filament.forms.components.block-preview', ['blockType' => 'unknown']);
                                                }

                                                return view(
                                                    'filament.forms.components.block-preview',
                                                    array_merge(
                                                        ['blockType' => $selectedItem['type'] ?? 'unknown'],
                                                        $selectedItem['data'] ?? []
                                                    )
                                                );
                                            })()),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('SEO')
                            ->icon('heroicon-o-presentation-chart-line')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('seo_title')->label('SEO Title (English)'),
                                    Forms\Components\TextInput::make('seo_title_ml')->label('SEO Title (Malayalam)'),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Textarea::make('seo_description')->label('SEO Description (English)'),
                                    Forms\Components\Textarea::make('seo_description_ml')->label('SEO Description (Malayalam)'),
                                ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'page' => 'gray',
                        'post' => 'success',
                    }),
                Tables\Columns\TextColumn::make('parent.title')->label('Parent Page'),
                Tables\Columns\ToggleColumn::make('is_published'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
