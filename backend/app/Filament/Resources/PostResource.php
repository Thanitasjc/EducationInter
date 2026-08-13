<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Forms\Components\MediaUpload;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'เนื้อหาเว็บ';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'บล็อก';

    protected static ?string $modelLabel = 'บทความ';

    protected static ?string $pluralModelLabel = 'บทความ';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('post_category_id')
                ->relationship('category', 'name_en')
                ->searchable(),
            Forms\Components\TextInput::make('title_th')->required(),
            Forms\Components\TextInput::make('title_en')->required(),
            Forms\Components\Textarea::make('excerpt_th')->rows(2),
            Forms\Components\Textarea::make('excerpt_en')->rows(2),
            Forms\Components\Textarea::make('content_th')->rows(6)->columnSpanFull(),
            Forms\Components\Textarea::make('content_en')->rows(6)->columnSpanFull(),
            MediaUpload::make('cover_path')
                ->label('รูปปก (แสดงหน้า /blog และหน้ารายละเอียด)')
                ->image()
                ->directory('covers/posts')
                ->imageEditor()
                ->helperText('อัปโหลดรูปปกแต่ละบทความ — ใช้บนรายการบล็อกและหน้าอ่านบทความ')
                ->columnSpanFull(),
            Forms\Components\DateTimePicker::make('published_at'),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_path')->label('Cover')->height(40),
                Tables\Columns\TextColumn::make('title_en')->searchable(),
                Tables\Columns\TextColumn::make('category.name_en')->label('Category'),
                Tables\Columns\TextColumn::make('published_at')->dateTime(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
