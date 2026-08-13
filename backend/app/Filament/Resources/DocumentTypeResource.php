<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'การรับสมัคร';

    protected static ?string $navigationLabel = 'ประเภทเอกสาร';

    protected static ?string $modelLabel = 'ประเภทเอกสาร';

    protected static ?string $pluralModelLabel = 'ประเภทเอกสาร';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(100),
            Forms\Components\TextInput::make('name_th')->label('ชื่อ (ไทย)')->required(),
            Forms\Components\TextInput::make('name_en')->label('ชื่อ (EN)')->required(),
            Forms\Components\Toggle::make('is_required')->label('จำเป็น')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('name_th')->label('ไทย')->searchable(),
                Tables\Columns\TextColumn::make('name_en')->label('EN')->searchable(),
                Tables\Columns\IconColumn::make('is_required')->label('จำเป็น')->boolean(),
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
