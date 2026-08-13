<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'การรับสมัคร';

    protected static ?string $navigationLabel = 'นักเรียน';

    protected static ?string $modelLabel = 'นักเรียน';

    protected static ?string $pluralModelLabel = 'นักเรียน';

    protected static ?int $navigationSort = 0;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('บัญชีผู้ใช้')
                ->schema([
                    Forms\Components\TextInput::make('user_name')
                        ->label('ชื่อ')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('user_email')
                        ->label('อีเมล')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('user_phone')
                        ->label('เบอร์โทร')
                        ->tel()
                        ->maxLength(50),
                    Forms\Components\TextInput::make('user_password')
                        ->label('รหัสผ่าน')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->minLength(8)
                        ->helperText(fn (string $operation): ?string => $operation === 'edit'
                            ? 'เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน'
                            : null),
                    Forms\Components\Select::make('user_locale')
                        ->label('ภาษา')
                        ->options([
                            'th' => 'ไทย',
                            'en' => 'English',
                        ])
                        ->default('th')
                        ->required(),
                    Forms\Components\Toggle::make('user_is_active')
                        ->label('ใช้งานได้')
                        ->default(true),
                ])
                ->columns(2),
            Forms\Components\Section::make('ข้อมูลนักเรียน')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->label('วันเกิด'),
                    Forms\Components\TextInput::make('nationality')
                        ->label('สัญชาติ')
                        ->maxLength(100),
                    Forms\Components\Select::make('education_level')
                        ->label('ระดับการศึกษา')
                        ->options([
                            'High School' => 'มัธยมศึกษา',
                            'Bachelor' => 'ปริญญาตรี',
                            'Master' => 'ปริญญาโท',
                            'PhD' => 'ปริญญาเอก',
                            'Other' => 'อื่นๆ',
                        ])
                        ->searchable(),
                    Forms\Components\Select::make('preferred_locale')
                        ->label('ภาษาที่ต้องการ')
                        ->options([
                            'th' => 'ไทย',
                            'en' => 'English',
                        ])
                        ->default('th')
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('ชื่อ')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('อีเมล')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label('เบอร์โทร')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('สัญชาติ')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('education_level')
                    ->label('ระดับการศึกษา')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('user.is_active')
                    ->label('ใช้งาน')
                    ->boolean(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->counts('applications')
                    ->label('ใบสมัคร'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('สมัครเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('user.is_active')
                    ->label('สถานะใช้งาน')
                    ->queries(
                        true: fn (Builder $query) => $query->whereHas('user', fn (Builder $q) => $q->where('is_active', true)),
                        false: fn (Builder $query) => $query->whereHas('user', fn (Builder $q) => $q->where('is_active', false)),
                    ),
                Tables\Filters\SelectFilter::make('education_level')
                    ->label('ระดับการศึกษา')
                    ->options([
                        'High School' => 'มัธยมศึกษา',
                        'Bachelor' => 'ปริญญาตรี',
                        'Master' => 'ปริญญาโท',
                        'PhD' => 'ปริญญาเอก',
                        'Other' => 'อื่นๆ',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Student $record): void {
                        $record->user?->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function ($records): void {
                            foreach ($records as $record) {
                                $record->user?->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicationsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\AppointmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
