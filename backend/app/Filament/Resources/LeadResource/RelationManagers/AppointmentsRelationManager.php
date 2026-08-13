<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    protected static ?string $title = 'นัดหมาย';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('consultant_id')
                ->label('Consultant')
                ->options(fn () => User::query()
                    ->role(['consultant', 'admission_officer', 'admin', 'super_admin'])
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->default(fn () => $this->getOwnerRecord()->assigned_to ?? auth()->id()),
            Forms\Components\Select::make('type')
                ->options([
                    'consultation' => 'Consultation',
                    'document_review' => 'Document review',
                    'interview' => 'Interview',
                    'follow_up' => 'Follow-up',
                    'other' => 'Other',
                ])
                ->required()
                ->default('consultation'),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\DateTimePicker::make('starts_at')->required()->seconds(false),
            Forms\Components\DateTimePicker::make('ends_at')->seconds(false),
            Forms\Components\Select::make('status')
                ->options([
                    'scheduled' => 'Scheduled',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'no_show' => 'No show',
                ])
                ->required()
                ->default('scheduled'),
            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('consultant.name')->label('Consultant'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->defaultSort('starts_at', 'desc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['student_id'] = $this->getOwnerRecord()->student_id;

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
