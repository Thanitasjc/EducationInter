<?php

namespace App\Filament\Resources;

use App\Enums\LeadStatus;
use App\Filament\Concerns\ScopesToConsultant;
use App\Filament\Resources\LeadResource\Pages;
use App\Filament\Resources\LeadResource\RelationManagers;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadPipelineService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class LeadResource extends Resource
{
    use ScopesToConsultant;

    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'ลูกค้าสัมพันธ์';

    protected static ?string $navigationLabel = 'ลีด';

    protected static ?string $modelLabel = 'ลีด';

    protected static ?string $pluralModelLabel = 'ลีด';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return static::scopeAssignedQuery(parent::getEloquentQuery(), 'assigned_to');
    }

    protected static function consultantOptions(): array
    {
        return User::query()
            ->role(['consultant', 'admission_officer', 'admin', 'super_admin'])
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('email')->email(),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\Select::make('source')->options([
                'website' => 'Website',
                'google' => 'Google',
                'facebook' => 'Facebook',
                'line' => 'LINE',
                'phone' => 'Phone',
                'walk-in' => 'Walk-in',
                'referral' => 'Referral',
                'event' => 'Event',
                'campaign' => 'Campaign',
            ])->required(),
            Forms\Components\Select::make('status')
                ->options(collect(LeadStatus::cases())->mapWithKeys(
                    fn (LeadStatus $status) => [$status->value => strtoupper($status->value)]
                ))
                ->required(),
            Forms\Components\Select::make('assigned_to')
                ->label('Consultant')
                ->options(fn () => static::consultantOptions())
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
            Forms\Components\Textarea::make('message')->columnSpanFull(),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
            Forms\Components\DateTimePicker::make('last_contact_at')->disabled()->seconds(false),
            Forms\Components\DateTimePicker::make('next_follow_up_at')
                ->label('Next follow-up')
                ->seconds(false)
                ->visible(fn (): bool => static::hasFollowUpColumn()),
        ]);
    }

    protected static function hasFollowUpColumn(): bool
    {
        try {
            return Schema::hasColumn('leads', 'next_follow_up_at');
        } catch (\Throwable) {
            return false;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('source')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (LeadStatus|string $state): string => match ($state instanceof LeadStatus ? $state->value : $state) {
                        'new' => 'gray',
                        'contacted', 'consultation' => 'info',
                        'interested', 'document', 'application' => 'warning',
                        'submitted', 'offer', 'visa' => 'primary',
                        'success' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')->label('Consultant'),
                Tables\Columns\TextColumn::make('next_follow_up_at')
                    ->label('Follow-up')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($state) => $state && $state <= now() ? 'danger' : null)
                    ->visible(fn (): bool => static::hasFollowUpColumn()),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(LeadStatus::cases())->mapWithKeys(
                        fn (LeadStatus $status) => [$status->value => strtoupper($status->value)]
                    )),
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'website' => 'Website',
                        'google' => 'Google',
                        'facebook' => 'Facebook',
                        'line' => 'LINE',
                    ]),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignee', 'name')
                    ->label('Consultant'),
                Tables\Filters\SelectFilter::make('follow_up')
                    ->label('Follow-up')
                    ->options([
                        'due' => 'Due / overdue',
                        'upcoming' => 'Upcoming (7 days)',
                        'unset' => 'No follow-up set',
                    ])
                    ->visible(fn (): bool => static::hasFollowUpColumn())
                    ->query(function (Builder $query, array $data): Builder {
                        if (! static::hasFollowUpColumn()) {
                            return $query;
                        }

                        return match ($data['value'] ?? null) {
                            'due' => $query->whereNotNull('next_follow_up_at')
                                ->where('next_follow_up_at', '<=', now()->endOfDay()),
                            'upcoming' => $query->whereNotNull('next_follow_up_at')
                                ->whereBetween('next_follow_up_at', [now(), now()->addDays(7)]),
                            'unset' => $query->whereNull('next_follow_up_at'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('convert')
                    ->label('Convert')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->visible(function (Lead $record): bool {
                        if (! filled($record->email)) {
                            return false;
                        }
                        try {
                            if (! Schema::hasColumn('applications', 'lead_id')) {
                                return true;
                            }

                            return ! $record->application()->exists();
                        } catch (\Throwable) {
                            return true;
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Convert lead to application')
                    ->modalDescription('สร้างใบสมัครจากลีดนี้ และแจ้งนักเรียนในพอร์ทัล')
                    ->action(function (Lead $record, LeadPipelineService $pipeline) {
                        try {
                            $application = $pipeline->convertToApplication($record, auth()->user());

                            Notification::make()
                                ->title('Application created')
                                ->body($application->application_no)
                                ->success()
                                ->send();

                            return redirect(ApplicationResource::getUrl('edit', ['record' => $application]));
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Convert failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('schedule')
                    ->label('Schedule')
                    ->icon('heroicon-o-calendar-days')
                    ->form([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->default(fn (Lead $record) => 'Consultation: '.$record->name),
                        Forms\Components\DateTimePicker::make('starts_at')->required()->seconds(false)->default(now()->addDay()->setTime(10, 0)),
                        Forms\Components\DateTimePicker::make('ends_at')->seconds(false),
                        Forms\Components\Select::make('type')
                            ->options([
                                'consultation' => 'Consultation',
                                'document_review' => 'Document review',
                                'interview' => 'Interview',
                                'follow_up' => 'Follow-up',
                                'other' => 'Other',
                            ])
                            ->default('consultation')
                            ->required(),
                        Forms\Components\Select::make('consultant_id')
                            ->label('Consultant')
                            ->options(fn () => static::consultantOptions())
                            ->default(fn (Lead $record) => $record->assigned_to ?? auth()->id())
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('notes')->rows(2),
                    ])
                    ->action(function (Lead $record, array $data, LeadPipelineService $pipeline): void {
                        $appointment = $pipeline->scheduleAppointment($record, $data, auth()->user());

                        Notification::make()
                            ->title('Appointment scheduled')
                            ->body($appointment->starts_at?->format('Y-m-d H:i'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('advance')
                    ->label('Advance')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(collect(LeadStatus::cases())->mapWithKeys(
                                fn (LeadStatus $status) => [$status->value => strtoupper($status->value)]
                            ))
                            ->required(),
                        Forms\Components\DateTimePicker::make('next_follow_up_at')
                            ->label('Next follow-up')
                            ->seconds(false)
                            ->default(now()->addDays(3)),
                        Forms\Components\Textarea::make('note')->rows(2),
                    ])
                    ->action(function (Lead $record, array $data, LeadPipelineService $pipeline): void {
                        $pipeline->changeStatus(
                            $record,
                            $data['status'],
                            auth()->user(),
                            $data['note'] ?? null,
                            $data['next_follow_up_at'] ?? null,
                        );

                        Notification::make()->title('Lead status updated')->success()->send();
                    }),
                Tables\Actions\Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Consultant')
                            ->options(fn () => static::consultantOptions())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (Lead $record, array $data, LeadPipelineService $pipeline): void {
                        $consultant = User::query()->findOrFail($data['assigned_to']);
                        $pipeline->assign($record, $consultant, auth()->user());

                        Notification::make()->title('Lead assigned')->success()->send();
                    }),
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
            RelationManagers\AppointmentsRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
