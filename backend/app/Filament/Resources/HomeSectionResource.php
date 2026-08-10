<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSectionResource\Pages;
use App\Models\HomeSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeSectionResource extends Resource
{
    protected static ?string $model = HomeSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Home Sections';

    protected static ?string $modelLabel = 'Home Section';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Section')
                ->schema([
                    Forms\Components\TextInput::make('key')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('e.g. bachelor-pathways, countries, services'),
                    Forms\Components\Select::make('layout')
                        ->options([
                            'pathways_split' => 'Pathways (text + cover)',
                            'program_categories' => 'Program category cards',
                            'cards' => 'Cards grid cover',
                            'banner' => 'Section banner',
                            'cta' => 'CTA banner',
                        ])
                        ->required()
                        ->default('cards'),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    Forms\Components\Toggle::make('is_active')->default(true),
                ])
                ->columns(2),

            Forms\Components\Section::make('Content')
                ->schema([
                    Forms\Components\TextInput::make('title_th'),
                    Forms\Components\TextInput::make('title_en'),
                    Forms\Components\Textarea::make('subtitle_th')->rows(2),
                    Forms\Components\Textarea::make('subtitle_en')->rows(2),
                ])
                ->columns(2),

            Forms\Components\Section::make('Cover image')
                ->schema([
                    Forms\Components\FileUpload::make('cover_path')
                        ->label('รูปปก Section')
                        ->image()
                        ->directory('sections')
                        ->disk('public')
                        ->imageEditor()
                        ->maxSize(5120)
                        ->helperText('Upload cover image for this homepage section'),
                ]),

            Forms\Components\Section::make('Items (pathways or program categories)')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->schema([
                            Forms\Components\TextInput::make('number')
                                ->numeric()
                                ->helperText('Pathways only'),
                            Forms\Components\Textarea::make('text_th')->rows(2)->helperText('Pathways'),
                            Forms\Components\Textarea::make('text_en')->rows(2)->helperText('Pathways'),
                            Forms\Components\TextInput::make('note_th'),
                            Forms\Components\TextInput::make('note_en'),
                            Forms\Components\TextInput::make('title_th')->helperText('Program category'),
                            Forms\Components\TextInput::make('title_en'),
                            Forms\Components\Textarea::make('summary_th')->rows(2),
                            Forms\Components\Textarea::make('summary_en')->rows(2),
                            Forms\Components\TextInput::make('href')->placeholder('/learn-language'),
                            Forms\Components\Toggle::make('external')->default(false),
                            Forms\Components\FileUpload::make('cover_path')
                                ->label('Item cover image')
                                ->image()
                                ->directory('sections/items')
                                ->disk('public')
                                ->imageEditor()
                                ->maxSize(5120)
                                ->helperText('อัปโหลดรูปปกการ์ด หรือปล่อยว่างแล้วใส่ URL ในช่องด้านล่าง')
                                ->columnSpanFull(),
                            Forms\Components\TextInput::make('cover_url')
                                ->label('Cover URL (optional fallback)')
                                ->url()
                                ->helperText('ใช้เมื่อต้องการลิงก์รูปภายนอกแทนไฟล์อัปโหลด')
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->defaultItems(0)
                        ->reorderable(),
                ])
                ->collapsed(),

            Forms\Components\Section::make('CTA')
                ->schema([
                    Forms\Components\TextInput::make('cta_label_th'),
                    Forms\Components\TextInput::make('cta_label_en'),
                    Forms\Components\TextInput::make('cta_url'),
                ])
                ->columns(3)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_path')
                    ->label('Cover')
                    ->disk('public')
                    ->height(48)
                    ->square(),
                Tables\Columns\TextColumn::make('key')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title_th')->limit(30),
                Tables\Columns\TextColumn::make('layout')->badge(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListHomeSections::route('/'),
            'create' => Pages\CreateHomeSection::route('/create'),
            'edit' => Pages\EditHomeSection::route('/{record}/edit'),
        ];
    }
}
