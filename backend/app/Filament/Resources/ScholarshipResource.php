<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipResource\Pages;
use App\Filament\Forms\Components\MediaUpload;
use App\Models\Scholarship;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarship::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?string $navigationLabel = 'ทุนการศึกษา';

    protected static ?string $modelLabel = 'ทุนการศึกษา';

    protected static ?string $pluralModelLabel = 'ทุนการศึกษา';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title_th')->required(),
            Forms\Components\TextInput::make('title_en')->required(),
            Forms\Components\Select::make('country_id')->relationship('country', 'name_en')->searchable(),
            Forms\Components\Select::make('university_id')->relationship('university', 'name_en')->searchable(),
            Forms\Components\TextInput::make('amount_label_th'),
            Forms\Components\TextInput::make('amount_label_en'),
            Forms\Components\DatePicker::make('deadline'),
            MediaUpload::make('cover_path')
                ->label('รูปปก (แสดงหน้า /scholarships และหน้ารายละเอียด)')
                ->image()
                ->directory('covers/scholarships')
                ->imageEditor()
                ->helperText('อัปโหลดรูปปกแต่ละทุน — ถ้าว่าง เว็บจะใช้รูปปกมหาวิทยาลัยแทน')
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_featured')->default(false),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_path')->label('Cover')->height(40),
                Tables\Columns\TextColumn::make('title_en')->searchable(),
                Tables\Columns\TextColumn::make('university.name_en'),
                Tables\Columns\TextColumn::make('deadline')->date(),
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
            'index' => Pages\ListScholarships::route('/'),
            'create' => Pages\CreateScholarship::route('/create'),
            'edit' => Pages\EditScholarship::route('/{record}/edit'),
        ];
    }
}
