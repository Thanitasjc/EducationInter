<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'พาร์ทเนอร์';

    protected static ?string $modelLabel = 'พาร์ทเนอร์';

    protected static ?string $pluralModelLabel = 'พาร์ทเนอร์';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('url')
                ->label('Website / link')
                ->url()
                ->maxLength(255),
            Forms\Components\FileUpload::make('logo_path')
                ->label('Logo / card image')
                ->image()
                ->directory('partners')
                ->disk('public')
                ->imageEditor()
                ->maxSize(8192)
                ->helperText('อัปโหลดโลโก้หรือการ์ดพาทเนอร์')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('logo_url')
                ->label('Logo URL (optional fallback)')
                ->url()
                ->helperText('ใช้เมื่อต้องการลิงก์รูปภายนอกแทนไฟล์อัปโหลด')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->height(40)
                    ->getStateUsing(function (Partner $record): ?string {
                        $path = $record->logo_path;
                        if (is_string($path) && str_starts_with($path, 'http')) {
                            return $path;
                        }

                        return $path;
                    }),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
