<?php

namespace App\Filament\Resources;

use App\Enums\ApplicationStatus;
use App\Filament\Concerns\ScopesToConsultant;
use App\Filament\Resources\ApplicationResource\Pages;
use App\Filament\Resources\ApplicationResource\RelationManagers;
use App\Models\Application;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApplicationResource extends Resource
{
    use ScopesToConsultant;

    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Admission';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return static::scopeAssignedQuery(parent::getEloquentQuery(), 'consultant_id');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('application_no')->disabled(),
            Forms\Components\Placeholder::make('student_name')
                ->label('Student')
                ->content(fn (?Application $record): string => $record?->student?->user?->name ?? '-'),
            Forms\Components\Select::make('consultant_id')
                ->label('Consultant')
                ->options(fn () => User::query()->role(['consultant', 'admission_officer', 'admin', 'super_admin'])->pluck('name', 'id'))
                ->searchable(),
            Forms\Components\Select::make('country_id')
                ->relationship('country', 'name_en')
                ->searchable(),
            Forms\Components\Select::make('university_id')
                ->relationship('university', 'name_en')
                ->searchable(),
            Forms\Components\Select::make('course_id')
                ->relationship('course', 'name_en')
                ->searchable(),
            Forms\Components\TextInput::make('intake'),
            Forms\Components\Select::make('status')
                ->options(collect(ApplicationStatus::cases())->mapWithKeys(
                    fn (ApplicationStatus $status) => [$status->value => strtoupper(str_replace('_', ' ', $status->value))]
                ))
                ->required(),
            Forms\Components\TextInput::make('next_action'),
            Forms\Components\TextInput::make('current_step')->numeric(),
            Forms\Components\DateTimePicker::make('submitted_at')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_no')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('student.user.name')->label('Student'),
                Tables\Columns\TextColumn::make('university.name_en')->label('University'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('next_action')->limit(30),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(ApplicationStatus::cases())->mapWithKeys(
                        fn (ApplicationStatus $status) => [$status->value => strtoupper(str_replace('_', ' ', $status->value))]
                    )),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
