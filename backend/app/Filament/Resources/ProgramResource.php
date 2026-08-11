<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Models\Program;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'โปรแกรมภาษา';

    protected static ?string $modelLabel = 'โปรแกรมภาษา';

    protected static ?string $pluralModelLabel = 'โปรแกรมภาษา';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title_th')->required(),
            Forms\Components\TextInput::make('title_en')->required(),
            Forms\Components\Textarea::make('summary_th')->rows(2),
            Forms\Components\Textarea::make('summary_en')->rows(2),
            Forms\Components\Textarea::make('content_th')->rows(4)->columnSpanFull(),
            Forms\Components\Textarea::make('content_en')->rows(4)->columnSpanFull(),
            Forms\Components\TextInput::make('age_min')->numeric()->minValue(1)->maxValue(99),
            Forms\Components\TextInput::make('age_max')->numeric()->minValue(1)->maxValue(99),
            Forms\Components\TextInput::make('duration_label_th'),
            Forms\Components\TextInput::make('duration_label_en'),
            Forms\Components\Select::make('language')->options([
                'english' => 'English',
                'japanese' => 'Japanese',
                'korean' => 'Korean',
                'chinese' => 'Chinese',
                'french' => 'French',
                'german' => 'German',
                'spanish' => 'Spanish',
            ])->searchable(),
            Forms\Components\TagsInput::make('destinations')
                ->placeholder('uk, usa, australia...')
                ->helperText('Country slugs e.g. uk, usa, australia'),
            Forms\Components\FileUpload::make('cover_path')
                ->label('รูปปก')
                ->image()
                ->directory('covers/programs')
                ->disk('public')
                ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('cta_label_th'),
            Forms\Components\TextInput::make('cta_label_en'),
            Forms\Components\TextInput::make('cta_url')->default('/contact'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_featured')->default(true),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_path')->label('Cover')->disk('public')->height(40),
                Tables\Columns\TextColumn::make('title_en')->searchable(),
                Tables\Columns\TextColumn::make('age_label')->label('Age'),
                Tables\Columns\TextColumn::make('language')->badge(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
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
            'index' => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit' => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
