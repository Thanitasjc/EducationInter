<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Forms\Components\MediaUpload;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?string $navigationLabel = 'กิจกรรม';

    protected static ?string $modelLabel = 'กิจกรรม';

    protected static ?string $pluralModelLabel = 'กิจกรรม';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title_th')->required(),
            Forms\Components\TextInput::make('title_en')->required(),
            Forms\Components\Textarea::make('summary_th')->rows(2),
            Forms\Components\Textarea::make('summary_en')->rows(2),
            Forms\Components\Textarea::make('content_th')->rows(5)->columnSpanFull(),
            Forms\Components\Textarea::make('content_en')->rows(5)->columnSpanFull(),
            MediaUpload::make('cover_path')
                ->label('รูปปก')
                ->image()
                ->directory('covers/events')
                        ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('location'),
            Forms\Components\DateTimePicker::make('starts_at')->required(),
            Forms\Components\DateTimePicker::make('ends_at'),
            Forms\Components\Toggle::make('is_featured')->default(false),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title_en')->searchable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('starts_at', 'desc')
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
