<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'เอกสาร';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type.name_en')->label('Type'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->color('success')
                    ->visible(fn (Document $record): bool => $record->status !== 'approved')
                    ->action(fn (Document $record) => DocumentResource::review($record, 'approved')),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->visible(fn (Document $record): bool => $record->status !== 'rejected')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('review_note')->required(),
                    ])
                    ->action(fn (Document $record, array $data) => DocumentResource::review($record, 'rejected', $data['review_note'] ?? null)),
                Tables\Actions\Action::make('open')
                    ->url(fn (Document $record) => DocumentResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
