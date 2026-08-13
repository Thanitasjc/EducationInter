<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Enums\ApplicationStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';

    protected static ?string $title = 'ใบสมัคร';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('application_no')->searchable(),
                Tables\Columns\TextColumn::make('university.name_en')->label('University'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('next_action')->limit(30),
                Tables\Columns\TextColumn::make('submitted_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(ApplicationStatus::cases())->mapWithKeys(
                        fn (ApplicationStatus $status) => [$status->value => strtoupper(str_replace('_', ' ', $status->value))]
                    )),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->url(fn ($record) => \App\Filament\Resources\ApplicationResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }
}
