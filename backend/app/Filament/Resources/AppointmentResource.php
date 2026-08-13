<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\ScopesToConsultant;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentResource extends Resource
{
    use ScopesToConsultant;

    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'ลูกค้าสัมพันธ์';

    protected static ?string $navigationLabel = 'นัดหมาย';

    protected static ?string $modelLabel = 'นัดหมาย';

    protected static ?string $pluralModelLabel = 'นัดหมาย';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return static::scopeAssignedQuery(parent::getEloquentQuery(), 'consultant_id');
    }

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
                ->preload(),
            Forms\Components\Select::make('lead_id')
                ->label('ลีด')
                ->relationship('lead', 'name')
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('consultant_id')
                ->label('ที่ปรึกษา')
                ->options(fn () => User::query()
                    ->role(['consultant', 'admission_officer', 'admin', 'super_admin'])
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('type')
                ->label('ประเภท')
                ->options([
                    'consultation' => 'Consultation',
                    'document_review' => 'Document review',
                    'interview' => 'Interview',
                    'follow_up' => 'Follow-up',
                    'other' => 'Other',
                ])
                ->required()
                ->default('consultation'),
            Forms\Components\TextInput::make('title')
                ->label('หัวข้อ')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\DateTimePicker::make('starts_at')
                ->label('เริ่ม')
                ->required()
                ->seconds(false),
            Forms\Components\DateTimePicker::make('ends_at')
                ->label('สิ้นสุด')
                ->seconds(false),
            Forms\Components\Select::make('status')
                ->label('สถานะ')
                ->options([
                    'scheduled' => 'Scheduled',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'no_show' => 'No show',
                ])
                ->required()
                ->default('scheduled'),
            Forms\Components\Textarea::make('notes')
                ->label('หมายเหตุ')
                ->rows(3)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('starts_at')->label('เริ่ม')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('หัวข้อ')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('student.user.name')->label('นักเรียน')->toggleable(),
                Tables\Columns\TextColumn::make('lead.name')->label('ลีด')->toggleable(),
                Tables\Columns\TextColumn::make('consultant.name')->label('ที่ปรึกษา'),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'cancelled', 'no_show' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->defaultSort('starts_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No show',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'consultation' => 'Consultation',
                        'document_review' => 'Document review',
                        'interview' => 'Interview',
                        'follow_up' => 'Follow-up',
                        'other' => 'Other',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
