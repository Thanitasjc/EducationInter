<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required(),
            Forms\Components\Textarea::make('review_note')->rows(2),
        ]);
    }

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
                    ->action(fn (Document $record) => DocumentResource::review($record, 'approved')),
                Tables\Actions\Action::make('reject')
                    ->color('danger')
                    ->form([Forms\Components\Textarea::make('review_note')->required()])
                    ->action(fn (Document $record, array $data) => DocumentResource::review($record, 'rejected', $data['review_note'] ?? null)),
                Tables\Actions\EditAction::make(),
            ]);
    }
}
