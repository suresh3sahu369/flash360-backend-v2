<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// 👇 Ye Imports add kiye hain (Columns aur Actions ke liye)
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    // Icon change kiya hai taaki 'Chat' jaisa dikhe
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Agar kabhi Admin se Comment Edit karna ho
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->label('Comment Content')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. User ka Naam (Relationship: user.name)
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                // 2. Kis News par comment kiya (Relationship: news.title)
                TextColumn::make('news.title')
                    ->label('News Article')
                    ->limit(30) // Title lamba ho toh chhota karega
                    ->searchable(),

                // 3. Comment kya hai
                TextColumn::make('content')
                    ->label('Comment')
                    ->limit(50) // Sirf shuru ke 50 words dikhayega
                    ->searchable(),

                // 4. Kab kiya
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // 👇 Delete Button Yahan Add Kiya Hai
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}