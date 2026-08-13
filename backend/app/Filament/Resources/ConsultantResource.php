<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultantResource\Pages;
use App\Models\Consultant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
class ConsultantResource extends Resource
{
    protected static ?string $model = Consultant::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'ลูกค้าสัมพันธ์';

    protected static ?string $navigationLabel = 'ที่ปรึกษา';

    protected static ?string $modelLabel = 'ที่ปรึกษา';

    protected static ?string $pluralModelLabel = 'ที่ปรึกษา';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('บัญชีที่ปรึกษา')
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
                    Forms\Components\Toggle::make('user_is_active')
                        ->label('ใช้งานได้')
                        ->default(true),
                ])
                ->columns(2),
            Forms\Components\Section::make('โปรไฟล์ที่ปรึกษา')
                ->schema([
                    Forms\Components\TextInput::make('employee_code')
                        ->label('รหัสพนักงาน')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),
                    Forms\Components\TextInput::make('max_leads')
                        ->label('รับลีดสูงสุด')
                        ->numeric()
                        ->default(40)
                        ->required(),
                    Forms\Components\Toggle::make('is_available')
                        ->label('พร้อมรับงาน')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_code')->label('รหัส')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('ชื่อ')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('อีเมล')->copyable(),
                Tables\Columns\TextColumn::make('user.phone')->label('เบอร์')->toggleable(),
                Tables\Columns\TextColumn::make('max_leads')->label('Max leads'),
                Tables\Columns\IconColumn::make('is_available')->label('พร้อม')->boolean(),
                Tables\Columns\IconColumn::make('user.is_active')->label('ใช้งาน')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Consultant $record): void {
                        $user = $record->user;
                        $record->delete();
                        $user?->delete();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function ($records): void {
                            foreach ($records as $record) {
                                $user = $record->user;
                                $record->delete();
                                $user?->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function mutateUserData(array $data): array
    {
        return [
            'name' => $data['user_name'],
            'email' => $data['user_email'],
            'phone' => $data['user_phone'] ?? null,
            'is_active' => (bool) ($data['user_is_active'] ?? true),
            'locale' => 'th',
            'password' => filled($data['user_password'] ?? null)
                ? $data['user_password']
                : null,
        ];
    }

    public static function syncConsultantUser(Consultant $consultant, array $data, bool $creating): void
    {
        $userPayload = static::mutateUserData($data);
        if (! $creating && blank($userPayload['password'])) {
            unset($userPayload['password']);
        }

        if ($creating) {
            $user = User::query()->create($userPayload);
            $user->assignRole('consultant');
            $consultant->user_id = $user->id;
            $consultant->save();

            return;
        }

        $consultant->user?->update($userPayload);
        if ($consultant->user && ! $consultant->user->hasRole('consultant')) {
            $consultant->user->assignRole('consultant');
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsultants::route('/'),
            'create' => Pages\CreateConsultant::route('/create'),
            'edit' => Pages\EditConsultant::route('/{record}/edit'),
        ];
    }
}
