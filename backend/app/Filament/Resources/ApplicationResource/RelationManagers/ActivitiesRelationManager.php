<?php

namespace App\Filament\Resources\ApplicationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $title = 'Activity log';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')
                ->label('Note')
                ->required()
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('from_status')->label('From'),
                Tables\Columns\TextColumn::make('to_status')->label('To'),
                Tables\Columns\TextColumn::make('body')->wrap()->limit(80),
                Tables\Columns\TextColumn::make('user.name')->label('By'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add note')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['type'] = 'note';
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
