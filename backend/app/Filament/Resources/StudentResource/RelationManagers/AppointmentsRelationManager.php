<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Filament\Resources\AppointmentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    protected static ?string $title = 'นัดหมาย';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('title')->limit(40),
                Tables\Columns\TextColumn::make('consultant.name')->label('ที่ปรึกษา'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->url(fn ($record) => AppointmentResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
