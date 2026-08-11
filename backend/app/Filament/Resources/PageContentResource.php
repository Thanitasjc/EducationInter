<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageContentResource\Pages;
use App\Filament\Forms\Components\MediaUpload;
use App\Models\PageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'เนื้อหาเพจ';

    protected static ?string $modelLabel = 'เนื้อหาเพจ';

    protected static ?string $pluralModelLabel = 'เนื้อหาเพจ';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Page')
                ->schema([
                    Forms\Components\Select::make('key')
                        ->label('Page key')
                        ->options([
                            'hero' => 'Home hero',
                            'about' => 'About page',
                            'learn-language' => 'Learn language listing',
                            'academic-year' => 'Academic Year landing',
                        ])
                        ->searchable()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->live()
                        ->helperText('เลือกหน้าที่จะแก้ไขเนื้อหา'),
                ]),

            Forms\Components\Section::make('Home hero')
                ->visible(fn (Get $get): bool => $get('key') === 'hero')
                ->schema([
                    Forms\Components\TextInput::make('value.headline_th')->label('Headline (TH)'),
                    Forms\Components\TextInput::make('value.headline_en')->label('Headline (EN)'),
                    Forms\Components\Textarea::make('value.subheadline_th')->label('Subheadline (TH)')->rows(2),
                    Forms\Components\Textarea::make('value.subheadline_en')->label('Subheadline (EN)')->rows(2),
                    Forms\Components\TextInput::make('value.cta_primary_th')->label('Primary CTA (TH)'),
                    Forms\Components\TextInput::make('value.cta_primary_en')->label('Primary CTA (EN)'),
                    Forms\Components\TextInput::make('value.cta_secondary_th')->label('Secondary CTA (TH)'),
                    Forms\Components\TextInput::make('value.cta_secondary_en')->label('Secondary CTA (EN)'),
                    Forms\Components\TextInput::make('value.cta_primary_url')
                        ->label('Primary CTA URL')
                        ->placeholder('/contact')
                        ->helperText('ค่าเริ่มต้น: /contact'),
                    Forms\Components\TextInput::make('value.cta_secondary_url')
                        ->label('Secondary CTA URL')
                        ->placeholder('/universities')
                        ->helperText('ค่าเริ่มต้น: /universities'),
                    Forms\Components\TextInput::make('value.slide_interval_ms')
                        ->label('Slide interval (ms)')
                        ->numeric()
                        ->default(5500)
                        ->helperText('ความเร็วสไลด์ เช่น 5500 = 5.5 วินาที')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Home hero slides')
                ->visible(fn (Get $get): bool => $get('key') === 'hero')
                ->schema([
                    Forms\Components\Repeater::make('value.slides')
                        ->label('Slides')
                        ->schema([
                            MediaUpload::make('image')
                                ->label('Cover image')
                                ->image()
                                ->directory('pages/hero')
                                                        ->imageEditor()
                                ->maxSize(8192)
                                ->helperText('อัปโหลดรูปปกสไลด์')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('image_url')
                                ->label('Cover URL (optional fallback)')
                                ->url()
                                ->helperText('ใช้เมื่อต้องการลิงก์รูปภายนอกแทนไฟล์อัปโหลด')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('headline_th')->label('Slide headline (TH)')->helperText('เว้นว่าง = ใช้ headline หลัก'),
                            Forms\Components\TextInput::make('headline_en')->label('Slide headline (EN)'),
                            Forms\Components\Textarea::make('subheadline_th')->label('Slide subheadline (TH)')->rows(2),
                            Forms\Components\Textarea::make('subheadline_en')->label('Slide subheadline (EN)')->rows(2),
                            Forms\Components\TextInput::make('link')
                                ->label('Slide link (optional)')
                                ->placeholder('/universities')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->cloneable()
                        ->defaultItems(0)
                        ->itemLabel(fn (array $state): ?string => $state['headline_th'] ?? $state['headline_en'] ?? 'Slide')
                        ->columnSpanFull(),
                ])
                ->collapsed(false),

            Forms\Components\Section::make('About page')
                ->visible(fn (Get $get): bool => $get('key') === 'about')
                ->schema([
                    Forms\Components\TextInput::make('value.title_th')->label('Title (TH)'),
                    Forms\Components\TextInput::make('value.title_en')->label('Title (EN)'),
                    Forms\Components\Textarea::make('value.body_th')->label('Body (TH)')->rows(6)->columnSpanFull(),
                    Forms\Components\Textarea::make('value.body_en')->label('Body (EN)')->rows(6)->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Learn language listing')
                ->visible(fn (Get $get): bool => $get('key') === 'learn-language')
                ->schema([
                    Forms\Components\TextInput::make('value.title_th')->label('Title (TH)'),
                    Forms\Components\TextInput::make('value.title_en')->label('Title (EN)'),
                    Forms\Components\Textarea::make('value.subtitle_th')->label('Subtitle (TH)')->rows(2),
                    Forms\Components\Textarea::make('value.subtitle_en')->label('Subtitle (EN)')->rows(2),
                    Forms\Components\Textarea::make('value.intro_th_1')->label('Intro 1 (TH)')->rows(3),
                    Forms\Components\Textarea::make('value.intro_en_1')->label('Intro 1 (EN)')->rows(3),
                    Forms\Components\Textarea::make('value.intro_th_2')->label('Intro 2 (TH)')->rows(3),
                    Forms\Components\Textarea::make('value.intro_en_2')->label('Intro 2 (EN)')->rows(3),
                ])
                ->columns(2),

            Forms\Components\Section::make('Academic Year — Hero & SEO')
                ->visible(fn (Get $get): bool => $get('key') === 'academic-year')
                ->schema([
                    Forms\Components\TextInput::make('value.title_th')->label('Hero title (TH)')->required(),
                    Forms\Components\TextInput::make('value.title_en')->label('Hero title (EN)')->required(),
                    Forms\Components\Textarea::make('value.subtitle_th')->label('Hero subtitle (TH)')->rows(3),
                    Forms\Components\Textarea::make('value.subtitle_en')->label('Hero subtitle (EN)')->rows(3),
                    Forms\Components\TextInput::make('value.meta_title_th')->label('Meta title (TH)'),
                    Forms\Components\TextInput::make('value.meta_title_en')->label('Meta title (EN)'),
                    Forms\Components\Textarea::make('value.meta_description_th')->label('Meta description (TH)')->rows(2),
                    Forms\Components\Textarea::make('value.meta_description_en')->label('Meta description (EN)')->rows(2),
                    Forms\Components\TextInput::make('value.promo_banner_th')->label('Promo banner (TH)')->columnSpanFull(),
                    Forms\Components\TextInput::make('value.promo_banner_en')->label('Promo banner (EN)')->columnSpanFull(),
                    MediaUpload::make('value.hero_image')
                        ->label('Hero image')
                        ->image()
                        ->directory('pages/academic-year')
                                        ->imageEditor()
                        ->maxSize(5120)
                        ->helperText('อัปโหลดรูป หรือใส่ URL ภายนอกในช่องด้านล่างถ้ายังไม่อัปโหลด')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('value.hero_image_url')
                        ->label('Hero image URL (optional fallback)')
                        ->url()
                        ->helperText('ใช้เมื่อต้องการลิงก์รูปภายนอกแทนไฟล์อัปโหลด')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Academic Year — Why / USP')
                ->visible(fn (Get $get): bool => $get('key') === 'academic-year')
                ->schema([
                    Forms\Components\TextInput::make('value.why_title_th')->label('Why section title (TH)'),
                    Forms\Components\TextInput::make('value.why_title_en')->label('Why section title (EN)'),
                    Forms\Components\Repeater::make('value.usps')
                        ->label('USP items')
                        ->schema([
                            Forms\Components\TextInput::make('title_th')->label('Title (TH)')->required(),
                            Forms\Components\TextInput::make('title_en')->label('Title (EN)')->required(),
                            Forms\Components\Textarea::make('body_th')->label('Body (TH)')->rows(2),
                            Forms\Components\Textarea::make('body_en')->label('Body (EN)')->rows(2),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed(),

            Forms\Components\Section::make('Academic Year — FAQ')
                ->visible(fn (Get $get): bool => $get('key') === 'academic-year')
                ->schema([
                    Forms\Components\TextInput::make('value.faq_title_th')->label('FAQ title (TH)'),
                    Forms\Components\TextInput::make('value.faq_title_en')->label('FAQ title (EN)'),
                    Forms\Components\Textarea::make('value.faq_body_th')->label('FAQ intro (TH)')->rows(2),
                    Forms\Components\Textarea::make('value.faq_body_en')->label('FAQ intro (EN)')->rows(2),
                    Forms\Components\Repeater::make('value.faqs')
                        ->label('FAQ items')
                        ->schema([
                            Forms\Components\TextInput::make('question_th')->label('Question (TH)')->required(),
                            Forms\Components\TextInput::make('question_en')->label('Question (EN)')->required(),
                            Forms\Components\Textarea::make('answer_th')->label('Answer (TH)')->rows(3),
                            Forms\Components\Textarea::make('answer_en')->label('Answer (EN)')->rows(3),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->reorderable()
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed(),

            Forms\Components\Section::make('Raw JSON (advanced)')
                ->visible(fn (Get $get): bool => filled($get('key')) && ! in_array($get('key'), [
                    'hero',
                    'about',
                    'learn-language',
                    'academic-year',
                ], true))
                ->schema([
                    Forms\Components\KeyValue::make('value')
                        ->label('Value')
                        ->keyLabel('Field')
                        ->valueLabel('Content')
                        ->columnSpanFull(),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'hero' => 'Home hero',
                        'about' => 'About',
                        'learn-language' => 'Learn language',
                        'academic-year' => 'Academic Year',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('value.title_th')
                    ->label('Title (TH)')
                    ->limit(40)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('value.headline_th')
                    ->label('Headline')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('key')
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPageContents::route('/'),
            'create' => Pages\CreatePageContent::route('/create'),
            'edit' => Pages\EditPageContent::route('/{record}/edit'),
        ];
    }
}
