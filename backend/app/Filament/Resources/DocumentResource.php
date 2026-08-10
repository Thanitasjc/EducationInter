<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Services\StudentNotifier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationGroup = 'Admission';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('student_name')
                ->label('Student')
                ->content(fn (?Document $record): string => $record?->student?->user?->name ?? '-'),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Select::make('document_type_id')
                ->relationship('type', 'name_en')
                ->searchable(),
            Forms\Components\Select::make('application_id')
                ->relationship('application', 'application_no')
                ->searchable(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required(),
            Forms\Components\Textarea::make('review_note')->rows(3)->columnSpanFull(),
            Forms\Components\Placeholder::make('file_link')
                ->label('File')
                ->content(function (?Document $record): string {
                    if (! $record?->path) {
                        return '-';
                    }

                    return Storage::disk('public')->url($record->path);
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('student.user.name')->label('Student'),
                Tables\Columns\TextColumn::make('type.name_en')->label('Type'),
                Tables\Columns\TextColumn::make('application.application_no')->label('Application'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Document $record): bool => $record->status !== 'approved')
                    ->action(fn (Document $record) => static::review($record, 'approved')),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Document $record): bool => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\Textarea::make('review_note')->required()->rows(2),
                    ])
                    ->action(fn (Document $record, array $data) => static::review($record, 'rejected', $data['review_note'] ?? null)),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function review(Document $record, string $status, ?string $note = null): void
    {
        $record->update([
            'status' => $status,
            'review_note' => $note ?? $record->review_note,
        ]);

        $user = $record->student?->user;
        if ($user) {
            app(StudentNotifier::class)->notify(
                $user,
                $status === 'approved' ? 'Document approved' : 'Document needs revision',
                $status === 'approved'
                    ? "Your document \"{$record->name}\" was approved."
                    : "Your document \"{$record->name}\" was rejected. ".($note ?: 'Please re-upload.'),
                $status === 'approved' ? 'success' : 'warning',
                '/student/documents',
            );
        }

        Notification::make()
            ->title('Document '.$status)
            ->success()
            ->send();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
