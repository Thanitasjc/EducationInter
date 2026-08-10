<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UniversityResource\Pages;
use App\Models\University;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UniversityResource extends Resource
{
    protected static ?string $model = University::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('country_id')
                ->relationship('country', 'name_en')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('city_id')
                ->relationship('city', 'name_en')
                ->searchable(),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name_th')->required(),
            Forms\Components\TextInput::make('name_en')->required(),
            Forms\Components\TextInput::make('type'),
            Forms\Components\TextInput::make('ranking_qs')->numeric(),
            Forms\Components\TextInput::make('tuition_min')->numeric(),
            Forms\Components\TextInput::make('tuition_max')->numeric(),
            Forms\Components\TextInput::make('currency')->default('GBP'),
            Forms\Components\Textarea::make('about_th')->rows(4)->columnSpanFull(),
            Forms\Components\Textarea::make('about_en')->rows(4)->columnSpanFull(),
            Forms\Components\FileUpload::make('cover_path')
                ->label('รูปปก')
                ->image()
                ->directory('covers/universities')
                ->disk('public')
                ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('logo_path')
                ->label('โลโก้')
                ->image()
                ->directory('covers/universities/logos')
                ->disk('public')
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_featured')->default(false),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_path')->label('Cover')->disk('public')->height(40),
                Tables\Columns\TextColumn::make('name_en')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('country.name_en')->label('Country'),
                Tables\Columns\TextColumn::make('ranking_qs')->label('QS'),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('country_id')->relationship('country', 'name_en'),
            ])
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
            'index' => Pages\ListUniversities::route('/'),
            'create' => Pages\CreateUniversity::route('/create'),
            'edit' => Pages\EditUniversity::route('/{record}/edit'),
        ];
    }
}
