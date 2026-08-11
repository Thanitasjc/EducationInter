<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'เรื่องราวความสำเร็จ';

    protected static ?string $modelLabel = 'เรื่องราวความสำเร็จ';

    protected static ?string $pluralModelLabel = 'เรื่องราวความสำเร็จ';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('student_name')->required(),
            Forms\Components\TextInput::make('university_label'),
            Forms\Components\TextInput::make('country_label'),
            Forms\Components\TextInput::make('year')->maxLength(10),
            Forms\Components\Textarea::make('quote_th')->required()->rows(3),
            Forms\Components\Textarea::make('quote_en')->required()->rows(3),
            Forms\Components\FileUpload::make('image_path')
                ->label('รูปปก')
                ->image()
                ->directory('covers/reviews')
                ->disk('public')
                ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_featured')->default(true),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('Cover')->disk('public')->height(40),
                Tables\Columns\TextColumn::make('student_name')->searchable(),
                Tables\Columns\TextColumn::make('university_label'),
                Tables\Columns\TextColumn::make('country_label'),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
