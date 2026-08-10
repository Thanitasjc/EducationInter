<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('university_id')
                ->relationship('university', 'name_en')
                ->searchable()
                ->required(),
            Forms\Components\Select::make('course_category_id')
                ->relationship('category', 'name_en')
                ->searchable(),
            Forms\Components\TextInput::make('slug')->required(),
            Forms\Components\TextInput::make('name_th')->required(),
            Forms\Components\TextInput::make('name_en')->required(),
            Forms\Components\Select::make('degree_level')->options([
                'foundation' => 'Foundation',
                'bachelor' => 'Bachelor',
                'master' => 'Master',
                'mba' => 'MBA',
                'phd' => 'PhD',
                'language' => 'Language',
            ]),
            Forms\Components\TextInput::make('duration_months')->numeric(),
            Forms\Components\TextInput::make('tuition')->numeric(),
            Forms\Components\TextInput::make('currency')->default('GBP'),
            Forms\Components\Textarea::make('summary_th')->rows(3),
            Forms\Components\Textarea::make('summary_en')->rows(3),
            Forms\Components\FileUpload::make('cover_path')
                ->label('รูปปก')
                ->image()
                ->directory('covers/courses')
                ->disk('public')
                ->imageEditor()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_popular')->default(false),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name_en')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('university.name_en')->label('University'),
                Tables\Columns\TextColumn::make('degree_level')->badge(),
                Tables\Columns\TextColumn::make('tuition'),
                Tables\Columns\IconColumn::make('is_popular')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('university_id')->relationship('university', 'name_en'),
                Tables\Filters\SelectFilter::make('degree_level')->options([
                    'bachelor' => 'Bachelor',
                    'master' => 'Master',
                    'mba' => 'MBA',
                ]),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
