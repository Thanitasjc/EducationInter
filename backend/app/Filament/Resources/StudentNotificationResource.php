<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentNotificationResource\Pages;
use App\Models\StudentNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentNotificationResource extends Resource
{
    protected static ?string $model = StudentNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'ลูกค้าสัมพันธ์';

    protected static ?string $navigationLabel = 'แจ้งเตือนนักเรียน';

    protected static ?string $modelLabel = 'แจ้งเตือน';

    protected static ?string $pluralModelLabel = 'แจ้งเตือนนักเรียน';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('นักเรียน (User)')
                ->options(fn () => User::query()
                    ->role('student')
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'info' => 'Info',
                    'success' => 'Success',
                    'warning' => 'Warning',
                    'danger' => 'Danger',
                ])
                ->required()
                ->default('info'),
            Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
            Forms\Components\Textarea::make('body')->rows(3)->columnSpanFull(),
            Forms\Components\TextInput::make('link')
                ->label('ลิงก์ในพอร์ทัล')
                ->placeholder('/student/documents')
                ->maxLength(255),
            Forms\Components\DateTimePicker::make('read_at')
                ->label('อ่านเมื่อ')
                ->seconds(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('นักเรียน')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('อ่านแล้ว')
                    ->boolean()
                    ->getStateUsing(fn (StudentNotification $record): bool => filled($record->read_at)),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'info' => 'Info',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'danger' => 'Danger',
                    ]),
                Tables\Filters\TernaryFilter::make('read_at')
                    ->label('สถานะอ่าน')
                    ->nullable()
                    ->trueLabel('อ่านแล้ว')
                    ->falseLabel('ยังไม่อ่าน')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('read_at'),
                        false: fn ($query) => $query->whereNull('read_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListStudentNotifications::route('/'),
            'create' => Pages\CreateStudentNotification::route('/create'),
            'edit' => Pages\EditStudentNotification::route('/{record}/edit'),
        ];
    }
}
