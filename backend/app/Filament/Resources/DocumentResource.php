<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Support\Media;
use App\Services\StudentNotifier;
use Filament\Forms;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationGroup = 'การรับสมัคร';

    protected static ?string $navigationLabel = 'เอกสาร';

    protected static ?string $modelLabel = 'เอกสาร';

    protected static ?string $pluralModelLabel = 'เอกสาร';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->label('นักเรียน')
                ->relationship(
                    name: 'student',
                    titleAttribute: 'id',
                    modifyQueryUsing: fn (Builder $query) => $query->with('user'),
                )
                ->getOptionLabelFromRecordUsing(fn ($record): string => $record->user?->name.' ('.$record->user?->email.')')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\Select::make('application_id')
                ->label('ใบสมัคร')
                ->relationship('application', 'application_no')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('document_type_id')
                ->label('ประเภท')
                ->relationship('type', 'name_en')
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->required()
                ->default('pending'),
            Forms\Components\Textarea::make('review_note')->rows(3)->columnSpanFull(),
            Forms\Components\FileUpload::make('path')
                ->label('ไฟล์เอกสาร')
                ->disk(Media::diskName())
                ->directory('documents')
                ->visibility('public')
                ->acceptedFileTypes([
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                ])
                ->maxSize(10240)
                ->downloadable()
                ->openable()
                ->helperText('อัปโหลด PDF หรือรูป (สูงสุด 10MB)')
                ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                    try {
                        if (! $file->exists()) {
                            return null;
                        }
                    } catch (\League\Flysystem\UnableToCheckFileExistence $exception) {
                        return null;
                    }

                    $disk = Media::diskName();
                    $directory = $component->getDirectory() ?? 'documents';
                    $name = $component->getUploadedFileNameForStorage($file);
                    $path = $file->storeAs($directory, $name, $disk);

                    return Media::url($path) ?? Storage::disk($disk)->url($path);
                })
                ->getUploadedFileUsing(function (BaseFileUpload $component, string $file, string|array|null $storedFileNames): ?array {
                    $url = Media::url($file);
                    if (! $url) {
                        return null;
                    }

                    $name = ($component->isMultiple() ? ($storedFileNames[$file] ?? null) : $storedFileNames)
                        ?? basename(parse_url($url, PHP_URL_PATH) ?: $file);

                    return [
                        'name' => is_string($name) ? $name : basename($file),
                        'size' => 0,
                        'type' => null,
                        'url' => $url,
                    ];
                })
                ->columnSpanFull(),
            Forms\Components\Placeholder::make('file_link')
                ->label('ลิงก์ไฟล์')
                ->content(function (?Document $record): string {
                    $url = Media::url($record?->path);

                    return $url ?: '-';
                })
                ->visibleOn('edit'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('student.user.name')->label('Student')->searchable(),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
